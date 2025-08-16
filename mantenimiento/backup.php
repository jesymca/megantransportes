<?php

/**
 * GESTOR DE RESPALDOS DE BASE DE DATOS (Multiplataforma)
 *
 * Un script robusto y seguro para crear, listar, descargar y eliminar
 * respaldos de una base de datos MySQL. Diseñado para funcionar
 * de manera confiable en entornos Windows y Linux.
 */

// --- CONFIGURACIÓN Y ARRANQUE SEGURO ---

// 1. Iniciar la sesión de forma segura.
//    Evita el error "Ignoring session_start() because a session is already active".
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. Inclusión de archivos.
//    Es una buena práctica usar `require_once` para archivos críticos
//    para evitar errores si no se encuentran.
//    - db.php: Debe contener las credenciales de la base de datos ($host, $user, $password, $database).
//    - auth.php: Debe manejar la lógica de autenticación y permisos de usuario.
//    - header.php: Puede contener el encabezado común de la página (si aplica).
require_once '../includes/db.php';
require_once '../includes/auth.php';
require_once '../includes/header.php'; // Opcional, si tienes un encabezado común.

// --- DEFINICIÓN DE CONSTANTES Y VARIABLES ---

// Define el directorio de copias de seguridad usando la constante DIRECTORY_SEPARATOR
// para máxima compatibilidad entre Windows ('\') y Linux ('/').
define('BACKUP_DIR', __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'backups' . DIRECTORY_SEPARATOR);

// Variables para mensajes al usuario.
$message = '';
$error = '';

// --- VERIFICACIÓN DEL DIRECTORIO DE RESPALDOS ---

// Se asegura de que el directorio de respaldos exista y tenga los permisos correctos.
// Usamos 0755 en lugar de 0777 por seguridad:
// - Propietario: leer, escribir, ejecutar.
// - Grupo y otros: leer, ejecutar.
if (!is_dir(BACKUP_DIR)) {
    if (!mkdir(BACKUP_DIR, 0755, true)) {
        // Si no se puede crear, termina la ejecución con un error claro.
        die("Error Crítico: No se pudo crear el directorio de respaldos en '" . BACKUP_DIR . "'. Verifique los permisos del servidor web.");
    }
}

// --- LÓGICA DE NEGOCIO ---

/**
 * Busca la ruta del ejecutable de mysqldump.
 * Intenta primero con el comando simple (si está en el PATH del sistema),
 * luego busca en rutas comunes para Windows y Linux.
 *
 * @return string|null La ruta al ejecutable o null si no se encuentra.
 */
function find_mysqldump() {
    // 1. Intenta ejecutar el comando directamente. Funciona si está en el PATH.
    exec('which mysqldump', $output, $return_var); // Comando para Linux/Mac
    if ($return_var === 0 && !empty($output[0])) {
        return $output[0];
    }
    exec('where mysqldump', $output, $return_var); // Comando para Windows
    if ($return_var === 0 && !empty($output[0])) {
        return $output[0];
    }
    
    // 2. Si no está en el PATH, busca en rutas comunes.
    $common_paths = [
        'C:\\xampp\\mysql\\bin\\mysqldump.exe', // XAMPP en Windows
        'C:\\wamp\\bin\\mysql\\mysql*\\bin\\mysqldump.exe', // WAMP en Windows (con comodín)
        'C:\\laragon\\bin\\mysql\\*\\bin\\mysqldump.exe', // Laragon
        '/usr/bin/mysqldump', // Ruta estándar en Debian/Ubuntu
        '/usr/local/bin/mysqldump', // Ruta común en otras distros de Linux/macOS
    ];

    foreach ($common_paths as $path) {
        // Soporte para comodines (ej. para versiones de MySQL en WAMP)
        $glob_paths = glob($path);
        if ($glob_paths && file_exists($glob_paths[0])) {
            return $glob_paths[0];
        }
    }

    return null; // No se encontró
}


/**
 * Maneja la creación de un nuevo respaldo de la base de datos.
 */
function handle_backup_creation() {
    global $message, $error, $host, $user, $password, $database;

    $mysqldump_path = find_mysqldump();
    if (!$mysqldump_path) {
        $error = "Error: No se pudo encontrar el ejecutable 'mysqldump'. Asegúrese de que MySQL esté instalado y que la ruta a 'mysqldump' esté en el PATH del sistema o configúrela manualmente en el script.";
        return;
    }

    $filename = 'backup_' . $database . '_' . date('Y-m-d_H-i-s') . '.sql';
    $filepath = BACKUP_DIR . $filename;

    // MEJORA DE SEGURIDAD: Usar una variable de entorno para la contraseña
    // evita que aparezca en la lista de procesos del sistema.
    putenv("MYSQL_PWD=$password");

    // Comando para ejecutar mysqldump.
    // - escapeshellarg() protege contra inyección de comandos.
    // - --single-transaction: Garantiza una copia consistente para tablas InnoDB.
    // - 2>&1: Redirige la salida de error a la salida estándar para poder capturarla.
    $command = sprintf(
        '%s --host=%s --user=%s --single-transaction %s > %s 2>&1',
        escapeshellarg($mysqldump_path),
        escapeshellarg($host),
        escapeshellarg($user),
        escapeshellarg($database),
        escapeshellarg($filepath)
    );

    exec($command, $output, $return_var);

    // Limpiar la variable de entorno por seguridad.
    putenv('MYSQL_PWD=');

    if ($return_var === 0 && file_exists($filepath) && filesize($filepath) > 0) {
        $message = "Respaldo de la base de datos creado exitosamente: " . htmlspecialchars($filename);
    } else {
        // Si el respaldo falla, se elimina el archivo vacío que se pudo haber creado.
        if (file_exists($filepath)) {
            unlink($filepath);
        }
        $error = "Error al crear el respaldo. Código de retorno: $return_var.";
        // Muestra la salida del comando para facilitar la depuración.
        if (!empty($output)) {
            $error .= "<br><strong>Detalles:</strong> <pre>" . htmlspecialchars(implode("\n", $output)) . "</pre>";
        }
    }
}


/**
 * Maneja la eliminación de un archivo de respaldo existente.
 */
function handle_backup_deletion() {
    global $message, $error;

    $file_to_delete = basename($_GET['delete_file']);
    $filepath_to_delete = BACKUP_DIR . $file_to_delete;

    // Verificación de seguridad para evitar ataques de "Directory Traversal".
    // realpath() resuelve rutas simbólicas y '..' para obtener la ruta canónica.
    if (file_exists($filepath_to_delete) && strpos(realpath($filepath_to_delete), realpath(BACKUP_DIR)) === 0) {
        
        // CORRECCIÓN PARA WINDOWS: Un pequeño retraso puede ayudar a liberar
        // bloqueos de archivo causados por antivirus u otros procesos.
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            sleep(1);
        }
        
        if (unlink($filepath_to_delete)) {
            $message = "Archivo '" . htmlspecialchars($file_to_delete) . "' eliminado exitosamente.";
        } else {
            $error = "Error al eliminar el archivo '" . htmlspecialchars($file_to_delete) . "'. Verifique los permisos.";
        }
    } else {
        $error = "Archivo no encontrado o la ruta es inválida.";
    }
}


// --- ENRUTADOR DE ACCIONES ---

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['backup_db'])) {
    handle_backup_creation();
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['delete_file'])) {
    handle_backup_deletion();
}


// --- OBTENCIÓN DE DATOS PARA LA VISTA ---

$backup_files = [];
if (is_dir(BACKUP_DIR)) {
    // scandir() con SCANDIR_SORT_DESCENDING es más eficiente que scandir + arsort.
    $files = scandir(BACKUP_DIR, SCANDIR_SORT_DESCENDING);
    foreach ($files as $file) {
        // Filtrar solo archivos .sql y excluir '.' y '..'
        if (is_file(BACKUP_DIR . $file) && pathinfo($file, PATHINFO_EXTENSION) === 'sql') {
            $backup_files[] = $file;
        }
    }
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Respaldos de Base de Datos</title>
    <!-- Dependencias de Bootstrap (CSS y JS) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        }
        .container {
            max-width: 960px;
        }
        .card {
            border: none;
            border-radius: 0.75rem;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1);
        }
        .card-header {
            background-color: #343a40;
            color: white;
            border-radius: 0.75rem 0.75rem 0 0;
            padding: 1.25rem;
        }
        .table-responsive {
            word-break: break-all;
        }
        .btn-action {
            width: 120px;
            margin-bottom: 5px;
        }
        .alert pre {
            white-space: pre-wrap;
            word-break: break-all;
            background-color: #e9ecef;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ced4da;
        }
    </style>
</head>
<body>

<div class="container mt-5 mb-5">
    <div class="card">
        <div class="card-header">
            <h4 class="mb-0"><i class="bi bi-database-check me-2"></i> Gestión de Respaldos</h4>
        </div>
        <div class="card-body p-4">
            
            <!-- Zona de notificaciones -->
            <?php if ($message): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i> <?php echo $message; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i> <?php echo $error; // Se usa echo directamente porque el HTML ya fue escapado en la lógica ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <!-- Formulario para crear respaldo -->
            <form method="post" action="" class="mb-4">
                <button type="submit" name="backup_db" class="btn btn-primary btn-lg">
                    <i class="bi bi-plus-circle me-2"></i> Crear Nuevo Respaldo
                </button>
            </form>

            <hr>

            <!-- Listado de respaldos existentes -->
            <h5 class="mt-4">Respaldos Disponibles</h5>
            <?php if (empty($backup_files)): ?>
                <div class="alert alert-info mt-3">
                    <i class="bi bi-info-circle me-2"></i> No hay archivos de respaldo disponibles.
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Nombre del Archivo</th>
                                <th>Tamaño</th>
                                <th>Fecha de Creación</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($backup_files as $file):
                                $filepath = BACKUP_DIR . $file;
                                // Usar @ para suprimir errores si el archivo es eliminado entre el escaneo y el renderizado
                                $file_size = @filesize($filepath);
                                $file_date = @filemtime($filepath);
                            ?>
                                <tr>
                                    <td><i class="bi bi-file-earmark-zip me-2"></i> <?php echo htmlspecialchars($file); ?></td>
                                    <td><?php echo $file_size ? round($file_size / 1024, 2) . ' KB' : 'N/A'; ?></td>
                                    <td><?php echo $file_date ? date('d/m/Y H:i:s', $file_date) : 'N/A'; ?></td>
                                    <td class="text-center">
                                        <a href="../assets/backups/<?php echo urlencode($file); ?>" class="btn btn-success btn-sm btn-action" download>
                                            <i class="bi bi-download"></i> Descargar
                                        </a>
                                        <a href="?delete_file=<?php echo urlencode($file); ?>" class="btn btn-danger btn-sm btn-action" onclick="return confirm('¿Está seguro de que desea eliminar este archivo de respaldo? Esta acción no se puede deshacer.');">
                                            <i class="bi bi-trash"></i> Eliminar
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
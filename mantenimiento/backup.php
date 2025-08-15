<?php

// 1. Iniciar la sesión de forma segura
// Esto evita el error "Ignoring session_start() because a session is already active".
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Incluye tus archivos de configuración y autenticación
include '../includes/db.php';
include '../includes/auth.php'; 
include '../includes/header.php';

// Define el directorio de copias de seguridad
$backup_dir = __DIR__ . '/../assets/backups/';

// 2. CORRECCIÓN: Manejar permisos y verificar que el directorio existe
// En Windows, los permisos pueden ser problemáticos. Aseguramos que el directorio
// se pueda crear y tenga los permisos adecuados para que el usuario de PHP pueda escribir y borrar.
if (!is_dir($backup_dir)) {
    if (!mkdir($backup_dir, 0777, true)) {
        die("Error: No se pudo crear el directorio de backups. Verifique los permisos.");
    }
}

$message = '';
$error = '';

// Manejar la creación de la copia de seguridad de la base de datos
if (isset($_POST['backup_db'])) {
    $filename = 'megan_backup_' . date('Ymd_His') . '.sql';
    $filepath = $backup_dir . $filename;

    $db_host = $host;
    $db_user = $user;
    $db_password = $password;
    $db_name = $database;

    $mysqldump_path = '';
    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
        $mysqldump_path = 'C:\\xampp\\mysql\\bin\\mysqldump.exe';
    } else {
        $mysqldump_path = '/usr/bin/mysqldump';
    }

    if (empty($error)) {
        // 3. MEJORA DE SEGURIDAD: Usar una variable de entorno para la contraseña.
        // Esto evita exponer la contraseña en la lista de procesos del sistema.
        putenv("MYSQL_PWD=$db_password");

        // Comando para ejecutar mysqldump (sin la contraseña visible)
        $command = sprintf(
            '%s --opt -h%s -u%s %s > %s',
            escapeshellarg($mysqldump_path),
            escapeshellarg($db_host),
            escapeshellarg($db_user),
            escapeshellarg($db_name),
            escapeshellarg($filepath)
        );

        exec($command, $output, $return_var);
        
        // Limpiar la variable de entorno para mayor seguridad
        putenv('MYSQL_PWD=');

        if ($return_var === 0) {
            $message = "Respaldo de la base de datos creado exitosamente: " . htmlspecialchars($filename);
        } else {
            $error = "Error al crear el respaldo de la base de datos. Código de error: " . $return_var;
            $error .= "<br>Salida del comando: " . htmlspecialchars(implode("\n", $output));
        }
    }
}

// Manejar la eliminación de archivos
if (isset($_GET['delete_file'])) {
    $file_to_delete = basename($_GET['delete_file']);
    $filepath_to_delete = $backup_dir . $file_to_delete;

    // Verificación de seguridad para evitar la eliminación de archivos fuera del directorio de backups
    if (file_exists($filepath_to_delete) && strpos(realpath($filepath_to_delete), realpath($backup_dir)) === 0) {
        
        // 4. CORRECCIÓN DEL ERROR: Añadir un pequeño retraso antes de borrar en Windows.
        // Esto le da tiempo al sistema operativo y otros programas (como antivirus) a liberar el archivo.
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            sleep(1); // Pausa la ejecución por 1 segundo
        }
        
        if (unlink($filepath_to_delete)) {
            $message = "Archivo '" . htmlspecialchars($file_to_delete) . "' eliminado exitosamente.";
        } else {
            $error = "Error al eliminar el archivo '" . htmlspecialchars($file_to_delete) . "'. Verifique los permisos.";
        }
    } else {
        $error = "Archivo no encontrado o ruta inválida.";
    }
}

// Obtener la lista de archivos de respaldo, ordenados del más nuevo al más antiguo
$backup_files = [];
if (is_dir($backup_dir)) {
    $files = array_diff(scandir($backup_dir), ['.', '..']);
    arsort($files); // Ordena los archivos de forma descendente por nombre (el nombre incluye la fecha)
    foreach ($files as $file) {
        if (pathinfo($file, PATHINFO_EXTENSION) === 'sql') {
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
    <title>Respaldo y Gestión de Base de Datos</title>
    <!-- Incluir Bootstrap para un estilo rápido y responsive -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Incluir Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .card {
            border-radius: 1rem;
            box-shadow: 0 4px 6px rgba(0,0,0,.1);
        }
        .card-header {
            border-radius: 1rem 1rem 0 0;
        }
        .btn-primary {
            background-color: #0d6efd;
            border-color: #0d6efd;
        }
        .btn-primary:hover {
            background-color: #0b5ed7;
            border-color: #0a58ca;
        }
        .btn-danger {
            background-color: #dc3545;
            border-color: #dc3545;
        }
        .btn-danger:hover {
            background-color: #c82333;
            border-color: #bd2130;
        }
    </style>
</head>
<body>

<div class="container mt-5">
    <div class="card">
        <div class="card-header bg-dark text-white">
            <h5>Respaldo y Gestión de Base de Datos</h5>
        </div>
        <div class="card-body">
            <?php if ($message): ?>
                <div class="alert alert-success" role="alert">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div class="alert alert-danger" role="alert">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <form method="post" action="">
                <button type="submit" name="backup_db" class="btn btn-primary mb-3">
                    <i class="bi bi-cloud-download me-2"></i> Realizar Respaldo Ahora
                </button>
            </form>

            <h6 class="mt-4">Archivos de Respaldo Existentes:</h6>
            <?php if (empty($backup_files)): ?>
                <p>No hay archivos de respaldo disponibles.</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>Nombre del Archivo</th>
                                <th>Tamaño</th>
                                <th>Fecha de Creación</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($backup_files as $file):
                                $filepath = $backup_dir . $file;
                                $file_size = file_exists($filepath) ? round(filesize($filepath) / 1024, 2) : 0; // Tamaño en KB
                                $file_date = file_exists($filepath) ? date('d/m/Y H:i:s', filemtime($filepath)) : 'N/A';
                            ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($file); ?></td>
                                    <td><?php echo $file_size; ?> KB</td>
                                    <td><?php echo $file_date; ?></td>
                                    <td>
                                        <a href="../assets/backups/<?php echo urlencode($file); ?>" class="btn btn-success btn-sm" download>
                                            <i class="bi bi-download"></i> Descargar
                                        </a>
                                        <a href="?delete_file=<?php echo urlencode($file); ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Está seguro de que desea eliminar este archivo de respaldo?');">
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

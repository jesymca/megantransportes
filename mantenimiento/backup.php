<?php
session_start();
include '../includes/db.php';
include '../includes/auth.php'; // Validar sesión activa
include '../includes/header.php';

// Define the backup directory
$backup_dir = __DIR__ . '/../assets/backups/';

// Create the backup directory if it doesn't exist
if (!is_dir($backup_dir)) {
    mkdir($backup_dir, 0777, true); // Create recursively with full permissions
}

$message = '';
$error = '';

// Handle database backup
if (isset($_POST['backup_db'])) {
    $filename = 'megan_backup_' . date('Ymd_His') . '.sql';
    $filepath = $backup_dir . $filename;

    // Get database credentials from db.php
    $db_host = $host;
    $db_user = $user;
    $db_password = $password;
    $db_name = $database;

    // Determine the mysqldump path based on the operating system
    $mysqldump_path = '';
    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
        // Windows operating system
        // You might need to adjust this path based on your XAMPP/WAMP installation
        $mysqldump_path = 'C:\\xampp\\mysql\\bin\\mysqldump.exe'; 
        // Fallback or common path for other Windows setups if the above doesn't work
        // $mysqldump_path = 'mysqldump'; // If it's in system PATH
    } else {
        // Linux or macOS operating system
        // Common paths for Linux/macOS
        $mysqldump_path = '/usr/bin/mysqldump';
        if (!file_exists($mysqldump_path)) {
            $mysqldump_path = '/usr/local/bin/mysqldump';
        }
        // Fallback to just 'mysqldump' if it's in the system's PATH
        if (!file_exists($mysqldump_path)) {
            $mysqldump_path = 'mysqldump';
        }
    }

    // Check if mysqldump executable exists at the determined path (if it's an absolute path)
    if (strpos($mysqldump_path, '/') === 0 || strpos($mysqldump_path, ':') === 1) { // Check for absolute path (Linux/Windows)
        if (!file_exists($mysqldump_path)) {
            $error = "Error: mysqldump no encontrado en la ruta especificada: " . htmlspecialchars($mysqldump_path) . ". Por favor, verifique la configuración.";
        }
    }

    if (empty($error)) {
        // Command to execute mysqldump
        $command = sprintf(
            '%s --opt -h%s -u%s -p%s %s > %s',
            escapeshellarg($mysqldump_path),
            escapeshellarg($db_host),
            escapeshellarg($db_user),
            escapeshellarg($db_password),
            escapeshellarg($db_name),
            escapeshellarg($filepath)
        );

        // Execute the command
        exec($command, $output, $return_var);

        if ($return_var === 0) {
            $message = "Respaldo de la base de datos creado exitosamente: " . $filename;
        } else {
            $error = "Error al crear el respaldo de la base de datos. Código de error: " . $return_var;
            // You can log $output for more details if needed
            $error .= "<br>Detalles del comando: " . htmlspecialchars($command);
            $error .= "<br>Salida del comando: " . htmlspecialchars(implode("\n", $output));
        }
    }
}

// Handle file deletion
if (isset($_GET['delete_file'])) {
    $file_to_delete = basename($_GET['delete_file']); // Use basename to prevent directory traversal
    $filepath_to_delete = $backup_dir . $file_to_delete;

    if (file_exists($filepath_to_delete) && strpos(realpath($filepath_to_delete), realpath($backup_dir)) === 0) {
        // Ensure the file is within the backup directory
        if (unlink($filepath_to_delete)) {
            $message = "Archivo '" . htmlspecialchars($file_to_delete) . "' eliminado exitosamente.";
        } else {
            $error = "Error al eliminar el archivo '" . htmlspecialchars($file_to_delete) . "'.";
        }
    } else {
        $error = "Archivo no encontrado o ruta inválida.";
    }
}

// Get list of backup files
$backup_files = [];
if (is_dir($backup_dir)) {
    $files = scandir($backup_dir);
    foreach ($files as $file) {
        if ($file !== '.' && $file !== '..' && pathinfo($file, PATHINFO_EXTENSION) === 'sql') {
            $backup_files[] = $file;
        }
    }
}
?>

<div class="card mt-4">
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
                        $file_size = round(filesize($filepath) / 1024, 2); // Size in KB
                        $file_date = date('d/m/Y H:i:s', filemtime($filepath));
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
        <?php endif; ?>
    </div>
</div>

<?php include '../includes/footer.php'; ?>

<?php
session_start();
include '../includes/db.php';
include '../includes/auth.php'; // Validar sesión activa
include '../includes/header.php';

$message = '';
$error = '';

if (isset($_POST['optimize_db'])) {
    try {
        // Fetch all table names
        $tables_result = $conn->query("SHOW TABLES");
        if ($tables_result) {
            $tables = [];
            while ($row = $tables_result->fetch_row()) {
                $tables[] = $row[0];
            }

            if (!empty($tables)) {
                $optimized_tables = [];
                foreach ($tables as $table) {
                    // Optimize each table
                    $optimize_sql = "OPTIMIZE TABLE `" . $conn->real_escape_string($table) . "`";
                    if ($conn->query($optimize_sql)) {
                        $optimized_tables[] = $table;
                    } else {
                        $error .= "Error al optimizar la tabla '" . htmlspecialchars($table) . "': " . $conn->error . "<br>";
                    }
                }
                if (empty($error)) {
                    $message = "Base de datos optimizada exitosamente. Tablas optimizadas: " . implode(', ', $optimized_tables);
                } else {
                    $message = "Optimización completada con algunos errores.";
                }
            } else {
                $message = "No se encontraron tablas para optimizar.";
            }
        } else {
            $error = "Error al obtener la lista de tablas: " . $conn->error;
        }
    } catch (Exception $e) {
        $error = "Ocurrió un error inesperado durante la optimización: " . $e->getMessage();
    }
}
?>

<div class="card mt-4">
    <div class="card-header bg-dark text-white">
        <h5>Mantenimiento y Optimización de Base de Datos</h5>
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

        <p>Haga clic en el botón a continuación para realizar una optimización de la base de datos. Esto puede ayudar a mejorar el rendimiento de su aplicación.</p>
        <form method="post" action="">
            <button type="submit" name="optimize_db" class="btn btn-info">
                <i class="bi bi-tools me-2"></i> Optimizar Base de Datos Ahora
            </button>
        </form>

        <h6 class="mt-4">Notas sobre la optimización:</h6>
        <ul>
            <li>La optimización de tablas puede liberar espacio no utilizado y reorganizar los datos para un acceso más rápido.</li>
            <li>Es una buena práctica realizar esta operación periódicamente, especialmente después de muchas eliminaciones o actualizaciones.</li>
            <li>El tiempo de ejecución dependerá del tamaño y la cantidad de tablas en su base de datos.</li>
        </ul>
    </div>
</div>

<?php include '../includes/footer.php'; ?>

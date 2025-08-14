<?php
// error_reporting(E_ALL);
// ini_set('display_errors', '1');

session_start();
include 'includes/db.php';
include 'includes/auth.php'; // Validar sesión activa
include 'includes/header.php';
?>

<div class="row">
    <!-- Tarjeta de Inventario Total -->
    <div class="col-md-4 mb-4">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <h5 class="card-title">Total en Inventario</h5>
                <?php
                $sql = "SELECT COUNT(id) as total FROM inventario";
                $result = $conn->query($sql);
                $row = $result->fetch_assoc();
                echo "<h2>" . $row['total'] . "</h2>";
                ?>
                <a href="inventario/entradas.php" class="text-white">Ver Detalles</a>
            </div>
        </div>
    </div>

    <!-- Tarjeta de Salidas Recientes -->
    <div class="col-md-4 mb-4">
        <div class="card bg-warning text-dark">
            <div class="card-body">
                <h5 class="card-title">Salidas Totales</h5>
                <?php
                $sql = "SELECT COUNT(id) as total FROM salidas_inventario";
                $result = $conn->query($sql);
                $row = $result->fetch_assoc();
                echo "<h2>" . $row['total'] . "</h2>";
                ?>
                <a href="inventario/salidas.php" class="text-dark">Ver Detalles</a>
            </div>
        </div>
    </div>

    <!-- Tarjeta de Usuarios Registrados -->
    <div class="col-md-4 mb-4">
        <div class="card bg-success text-white">
            <div class="card-body">
                <h5 class="card-title">Usuarios Activos</h5>
                <?php
                $sql = "SELECT COUNT(id) as total FROM usuarios";
                $result = $conn->query($sql);
                $row = $result->fetch_assoc();
                echo "<h2>" . $row['total'] . "</h2>";
                ?>
                <a href="admin/usuarios/" class="text-white">Ver Detalles</a>
            </div>
        </div>
    </div>
</div>

<!-- Tabla de Últimas Salidas -->
<div class="card mt-4">
    <div class="card-header bg-dark text-white">
        <h5>Últimas 5 Salidas de Inventario</h5>
    </div>
    <div class="card-body">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Técnico</th>
                    <th>Cantidad</th>
                    <th>Fecha</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql = "SELECT i.nombre as item, u.nombre as tecnico, s.cantidad, s.fecha_salida 
                        FROM salidas_inventario s
                        JOIN inventario i ON s.id_item = i.id
                        JOIN usuarios u ON s.id_usuario = u.id
                        ORDER BY s.fecha_salida DESC LIMIT 5";
                $result = $conn->query($sql);
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                            <td>" . $row['item'] . "</td>
                            <td>" . $row['tecnico'] . "</td>
                            <td>" . $row['cantidad'] . "</td>
                            <td>" . $row['fecha_salida'] . "</td>
                          </tr>";
                }
                ?>
            </tbody>
        </table>
        <a href="inventario/salidas.php" class="btn btn-primary">Ver Todas</a>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

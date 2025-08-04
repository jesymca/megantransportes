<?php
session_start();
include '../includes/db.php';
include '../includes/auth.php';
include '../includes/header.php';

// Variables para mensajes
$exito = $error = $alerta_bajo_stock = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_item = filter_input(INPUT_POST, 'id_item', FILTER_VALIDATE_INT);
    $cantidad = filter_input(INPUT_POST, 'cantidad', FILTER_VALIDATE_INT);
    $motivo = trim($_POST['motivo']);
    $id_usuario = $_SESSION['usuario_id'];

    // Validaciones básicas
    if (!$id_item || !$cantidad || empty($motivo)) {
        $error = "Por favor complete todos los campos correctamente.";
    } elseif ($cantidad <= 0) {
        $error = "La cantidad debe ser mayor a cero.";
    } else {
        // Obtener información completa del item
        $sql_stock = "SELECT i.id, i.nombre, i.cantidad, c.nombre as categoria 
                      FROM inventario i
                      LEFT JOIN categorias c ON i.categoria_id = c.id
                      WHERE i.id = ?";
        $stmt = $conn->prepare($sql_stock);
        $stmt->bind_param("i", $id_item);
        $stmt->execute();
        $result = $stmt->get_result();
        $item = $result->fetch_assoc();

        if ($item) {
            // Validar stock disponible
            if ($item['cantidad'] < $cantidad) {
                $error = "No hay suficiente stock disponible. Stock actual: {$item['cantidad']} unidades.";
            } else {
                // Registrar salida
                $sql = "INSERT INTO salidas_inventario (id_item, id_usuario, cantidad, motivo) 
                        VALUES (?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("iiis", $id_item, $id_usuario, $cantidad, $motivo);

                if ($stmt->execute()) {
                    // Actualizar stock
                    $new_stock = $item['cantidad'] - $cantidad;
                    $sql_update = "UPDATE inventario SET cantidad = ? WHERE id = ?";
                    $stmt = $conn->prepare($sql_update);
                    $stmt->bind_param("ii", $new_stock, $id_item);
                    $stmt->execute();

                    $exito = "¡Salida registrada correctamente!";
                    
                    // Verificar si el stock restante es bajo (menos de 3 unidades)
                    if ($new_stock < 3) {
                        // Registrar alerta para administración
                        $mensaje_alerta = "ALERTA: El producto {$item['nombre']} (Categoría: {$item['categoria']}) tiene bajo stock: {$new_stock} unidades restantes.";
                        
                        $sql_alerta = "INSERT INTO alertas_stock (id_item, cantidad_restante, mensaje, fecha_alerta)
                                      VALUES (?, ?, ?, NOW())";
                        $stmt = $conn->prepare($sql_alerta);
                        $stmt->bind_param("iis", $id_item, $new_stock, $mensaje_alerta);
                        $stmt->execute();
                        
                        $alerta_bajo_stock = "¡Atención! Este producto ahora tiene bajo stock ({$new_stock} unidades). Se ha generado una alerta para administración.";
                    }
                } else {
                    $error = "Error al registrar salida: " . $conn->error;
                }
            }
        } else {
            $error = "El producto seleccionado no existe.";
        }
    }
}
?>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-warning text-dark">
                <h4>Registrar Salida de Inventario</h4>
            </div>
            <div class="card-body">
             <?php if (!empty($exito)): ?>
    <div class="alert alert-success alert-dismissible fade show">
        <?php echo $exito; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if (!empty($error)): ?>
    <div class="alert alert-danger alert-dismissible fade show">
        <?php echo $error; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if (!empty($alerta_bajo_stock)): ?>
    <div class="alert alert-warning alert-dismissible fade show">
        <?php echo $alerta_bajo_stock; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>


                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Item</label>
                        <select name="id_item" class="form-select" required>
                            <option value="">Seleccione un producto</option>
                            <?php
                            $sql = "SELECT i.id, i.nombre, i.cantidad, c.nombre as categoria 
                                    FROM inventario i
                                    LEFT JOIN categorias c ON i.categoria_id = c.id
                                    WHERE i.cantidad > 0
                                    ORDER BY i.nombre";
                            $result = $conn->query($sql);
                            while ($row = $result->fetch_assoc()) {
                                $selected = (isset($id_item) && $id_item == $row['id']) ? 'selected' : '';
                                $stock_info = " (Stock: {$row['cantidad']})";
                                echo "<option value='{$row['id']}' $selected>{$row['nombre']} - {$row['categoria']}$stock_info</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Cantidad</label>
                        <input type="number" name="cantidad" class="form-control" 
                               value="<?php echo isset($cantidad) ? htmlspecialchars($cantidad) : ''; ?>" 
                               min="1" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Motivo (Técnico/Mecánico)</label>
                        <textarea name="motivo" class="form-control" required><?php echo isset($motivo) ? htmlspecialchars($motivo) : ''; ?></textarea>
                    </div>
                    <button type="submit" class="btn btn-warning w-100">
                        <i class="fas fa-box-open"></i> Registrar Salida
                    </button>
                </form>
            </div>
        </div>

        <!-- Tabla de Historial de Salidas -->
        <div class="card mt-4">
            <div class="card-header bg-dark text-white">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Historial de Salidas</h5>
                    <a href="/megantransportes/reportes/generar.php?tipo_reporte=salidas" class="btn btn-sm btn-light">
                        <i class="fas fa-file-pdf"></i> Generar Reporte
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr class="table-primary">
                                <th>Item</th>
                                <th>Categoría</th>
                                <th>Cantidad</th>
                                <th>Motivo</th>
                                <th>Fecha</th>
                                <th>Registrado por</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sql = "SELECT i.nombre as item, c.nombre as categoria, s.cantidad, s.motivo, 
                                    s.fecha_salida, u.nombre as usuario
                                    FROM salidas_inventario s
                                    JOIN inventario i ON s.id_item = i.id
                                    LEFT JOIN categorias c ON i.categoria_id = c.id
                                    JOIN usuarios u ON s.id_usuario = u.id
                                    ORDER BY s.fecha_salida DESC
                                    LIMIT 50";
                            $result = $conn->query($sql);
                            
                            if ($result && $result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    echo "<tr>
                                            <td>".htmlspecialchars($row['item'])."</td>
                                            <td>".htmlspecialchars($row['categoria'])."</td>
                                            <td>".$row['cantidad']."</td>
                                            <td>".htmlspecialchars($row['motivo'])."</td>
                                            <td>".date('d/m/Y H:i', strtotime($row['fecha_salida']))."</td>
                                            <td>".htmlspecialchars($row['usuario'])."</td>
                                          </tr>";
                                }
                            } else {
                                echo "<tr><td colspan='6' class='text-center'>No hay registros de salidas</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
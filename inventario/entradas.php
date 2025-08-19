<?php
session_start();
include '../includes/db.php';
include '../includes/auth.php'; // Solo usuarios autenticados
include '../includes/header.php';

// Manejo del formulario de entrada
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = trim($_POST['nombre']);
    $categoria_id = filter_input(INPUT_POST, 'categoria_id', FILTER_VALIDATE_INT);
    $cantidad = filter_input(INPUT_POST, 'cantidad', FILTER_VALIDATE_INT);
    $descripcion = trim($_POST['descripcion']);

    // Validaciones
    if (empty($nombre)) {
        $error = "Por favor, ingrese un nombre válido para el item.";
    } elseif ($categoria_id === false || $categoria_id === null) {
        $error = "Por favor, seleccione una categoría válida.";
    } elseif ($cantidad === false || $cantidad === null || $cantidad <= 0) {
        $error = "Por favor, ingrese una cantidad válida (mayor que 0).";
    } else {
        // Verificar que la categoría exista
        $check_cat_sql = "SELECT id FROM categorias WHERE id = ?";
        $check_stmt = $conn->prepare($check_cat_sql);
        $check_stmt->bind_param("i", $categoria_id);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();

        if ($check_result->num_rows == 1) {
            $sql = "INSERT INTO inventario (nombre, categoria_id, cantidad, descripcion) 
                    VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("siis", $nombre, $categoria_id, $cantidad, $descripcion);

            if ($stmt->execute()) {
                $exito = "¡Item registrado en el inventario correctamente!";
                // Limpiar variables para evitar reenvío
                $nombre = $descripcion = '';
                $categoria_id = $cantidad = null;
            } else {
                $error = "Error al registrar el item: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $error = "La categoría seleccionada no es válida.";
        }
        $check_stmt->close();
    }
}

// Obtener categorías disponibles para el select
$categorias_disponibles = [];
$sql_get_categorias = "SELECT id, nombre FROM categorias ORDER BY nombre ASC";
$result_categorias = $conn->query($sql_get_categorias);
if ($result_categorias && $result_categorias->num_rows > 0) {
    while ($row_cat = $result_categorias->fetch_assoc()) {
        $categorias_disponibles[] = $row_cat;
    }
}


$proveedores_disponibles = [];
$sql_get_proveedores = "SELECT id, nombre FROM proveedores ORDER BY nombre ASC";
$result_proveedores = $conn->query($sql_get_proveedores);
if ($result_proveedores && $result_proveedores->num_rows > 0) {
    while ($row_prov = $result_proveedores->fetch_assoc()) {
        $proveedores_disponibles[] = $row_prov;
    }
}
?>
<div class="row">
    <div class="col-md-12">
        <div class="card mb-4">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h4 class="mb-0">
                    <i class="bi bi-box-arrow-in-down me-2"></i>Registrar Entrada en Inventario
                </h4>
            </div>
            <div class="card-body">
                <?php if (!empty($exito)): ?>
                    <div class="alert alert-success alert-dismissible fade show">
                        <i class="bi bi-check-circle-fill me-2"></i><?php echo $exito; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php elseif (!empty($error)): ?>
                    <div class="alert alert-danger alert-dismissible fade show">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i><?php echo $error; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
                
                <form method="POST">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">
                                <i class="bi bi-tag me-1"></i>Nombre del Item
                            </label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-box"></i></span>
                                <input type="text" name="nombre" class="form-control" 
                                       value="<?php echo isset($nombre) ? htmlspecialchars($nombre) : ''; ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="categoria_id" class="form-label">
                                <i class="bi bi-bookmark me-1"></i>Categoría
                            </label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-list-ul"></i></span>
                                <select name="categoria_id" id="categoria_id" class="form-select" required>
                                    <option value="">Seleccione una categoría</option>
                                    <?php if (!empty($categorias_disponibles)): ?>
                                        <?php foreach ($categorias_disponibles as $categoria_item): ?>
                                            <option value="<?php echo htmlspecialchars($categoria_item['id']); ?>"
                                                <?php echo (isset($categoria_id) && $categoria_id == $categoria_item['id']) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($categoria_item['nombre']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <option value="" disabled>No hay categorías disponibles</option>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>




<div class="col-md-6 mb-3">
    <label for="proveedor_id" class="form-label">
        <i class="bi bi-truck me-1"></i>Proveedor
    </label>
    <div class="input-group">
        <span class="input-group-text"><i class="bi bi-person-badge"></i></span>
        <select name="proveedor_id" id="proveedor_id" class="form-select" required>
            <option value="">Seleccione un proveedor</option>
            <?php if (!empty($proveedores_disponibles)): ?>
                <?php foreach ($proveedores_disponibles as $proveedor_item): ?>
                    <option value="<?php echo htmlspecialchars($proveedor_item['id']); ?>"
                        <?php echo (isset($proveedor_id) && $proveedor_id == $proveedor_item['id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($proveedor_item['nombre']); ?>
                    </option>
                <?php endforeach; ?>
            <?php else: ?>
                <option value="" disabled>No hay proveedores disponibles</option>
            <?php endif; ?>
        </select>
    </div>
</div>

</div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">
                                <i class="bi bi-123 me-1"></i>Cantidad
                            </label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-boxes"></i></span>
                                <input type="number" name="cantidad" class="form-control" 
                                       value="<?php echo isset($cantidad) ? htmlspecialchars($cantidad) : ''; ?>" 
                                       min="1" required>
                            </div>
                        </div>
                        <div class="col-md-8 mb-3">
                            <label class="form-label">
                                <i class="bi bi-card-text me-1"></i>Descripción
                            </label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-text-paragraph"></i></span>
                                <input type="text" name="descripcion" class="form-control" 
                                       value="<?php echo isset($descripcion) ? htmlspecialchars($descripcion) : ''; ?>" 
                                       required>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-save-fill me-2"></i>Guardar Entrada
                    </button>
                </form>
            </div>
        </div>

        <!-- Tabla de Inventario Actual -->
        <div class="card">
            <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                <h4 class="mb-0">
                    <i class="bi bi-clipboard-data me-2"></i>Inventario Actual
                </h4>
                <a href="/megantransportes/reportes/generar.php?tipo_reporte=inventario" class="btn btn-sm btn-light">
                    <i class="bi bi-file-earmark-pdf me-1"></i>Generar PDF
                </a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th><i class="bi bi-hash me-1"></i>ID</th>
                                <th><i class="bi bi-tag me-1"></i>Nombre</th>
                                <th><i class="bi bi-bookmark me-1"></i>Categoría</th>
                                <th><i class="bi bi-box-seam me-1"></i>Cantidad</th>
                                <th><i class="bi bi-card-text me-1"></i>Descripción</th>
                                <th><i class="bi bi-calendar-event me-1"></i>Fecha Ingreso</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sql = "SELECT i.*, c.nombre as categoria_nombre 
                                    FROM inventario i
                                    LEFT JOIN categorias c ON i.categoria_id = c.id
                                    ORDER BY i.fecha_ingreso DESC";
                            $result = $conn->query($sql);
                            
                            if ($result && $result->num_rows > 0) {
                                while($row = $result->fetch_assoc()) {
                                    echo "<tr>
                                            <td>".$row['id']."</td>
                                            <td>".htmlspecialchars($row['nombre'])."</td>
                                            <td>".htmlspecialchars($row['categoria_nombre'] ?? 'Sin categoría')."</td>
                                            <td>
                                                <span class='badge bg-".($row['cantidad'] < 3 ? 'danger' : 'primary')."'>
                                                    ".$row['cantidad']."
                                                </span>
                                            </td>
                                            <td>".htmlspecialchars($row['descripcion'])."</td>
                                            <td>".date('d/m/Y', strtotime($row['fecha_ingreso']))."</td>
                                          </tr>";
                                }
                            } else {
                                echo "<tr><td colspan='6' class='text-center'>No hay items en el inventario</td></tr>";
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
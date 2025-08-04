<?php
session_start();
include '../includes/db.php';
include '../includes/auth.php'; // Asegura que el usuario esté autenticado
include '../includes/header.php';

// Inicialización de variables
$mensaje = '';
$tipo_mensaje = ''; // 'success' o 'danger'

// ==============================================
// PROCESAMIENTO DE ACCIONES (AÑADIR, EDITAR, ELIMINAR)
// ==============================================
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    $nombre_categoria = isset($_POST['nombre_categoria']) ? trim($_POST['nombre_categoria']) : '';
    $id_categoria = isset($_POST['id_categoria']) ? intval($_POST['id_categoria']) : 0;

    // --------------------------
    // ACCIÓN: AÑADIR CATEGORÍA
    // --------------------------
    if ($_POST['action'] == 'add' && !empty($nombre_categoria)) {
        try {
            $sql = "INSERT INTO categorias (nombre) VALUES (?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $nombre_categoria);
            
            if ($stmt->execute()) {
                $mensaje = "Categoría '$nombre_categoria' añadida correctamente.";
                $tipo_mensaje = 'success';
            } else {
                $mensaje = "Error al añadir la categoría: " . $stmt->error;
                $tipo_mensaje = 'danger';
            }
            $stmt->close();
        } catch (mysqli_sql_exception $e) {
            if ($e->getCode() == 1062) { // Código de error para entrada duplicada
                $mensaje = "Error: La categoría '$nombre_categoria' ya existe.";
            } else {
                $mensaje = "Error de base de datos: " . $e->getMessage();
            }
            $tipo_mensaje = 'danger';
        }
    }
    
    // --------------------------
    // ACCIÓN: EDITAR CATEGORÍA
    // --------------------------
    elseif ($_POST['action'] == 'edit' && $id_categoria > 0 && !empty($nombre_categoria)) {
        try {
            $sql = "UPDATE categorias SET nombre = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("si", $nombre_categoria, $id_categoria);
            
            if ($stmt->execute()) {
                $mensaje = "Categoría actualizada a '$nombre_categoria'.";
                $tipo_mensaje = 'success';
            } else {
                $mensaje = "Error al actualizar la categoría: " . $stmt->error;
                $tipo_mensaje = 'danger';
            }
            $stmt->close();
        } catch (mysqli_sql_exception $e) {
            if ($e->getCode() == 1062) {
                $mensaje = "Error: Ya existe otra categoría con el nombre '$nombre_categoria'.";
            } else {
                $mensaje = "Error de base de datos: " . $e->getMessage();
            }
            $tipo_mensaje = 'danger';
        }
    }
    
    // --------------------------
    // ACCIÓN: ELIMINAR CATEGORÍA
    // --------------------------
    elseif ($_POST['action'] == 'delete' && $id_categoria > 0) {
        // Verificar si la categoría está en uso en la tabla 'inventario'
        $sql_check = "SELECT COUNT(*) as count FROM inventario WHERE categoria_id = ?";
        $stmt_check = $conn->prepare($sql_check);
        $stmt_check->bind_param("i", $id_categoria);
        $stmt_check->execute();
        $result_check = $stmt_check->get_result();
        $row_check = $result_check->fetch_assoc();
        $stmt_check->close();

        if ($row_check['count'] > 0) {
            $mensaje = "Error: La categoría no se puede eliminar porque está siendo utilizada por ".$row_check['count']." ítem(s) del inventario.";
            $tipo_mensaje = 'danger';
        } else {
            $sql = "DELETE FROM categorias WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $id_categoria);
            
            if ($stmt->execute()) {
                $mensaje = "Categoría eliminada correctamente.";
                $tipo_mensaje = 'success';
            } else {
                $mensaje = "Error al eliminar la categoría: " . $stmt->error;
                if (strpos($stmt->error, "foreign key constraint fails") !== false) {
                    $mensaje = "Error: La categoría no se puede eliminar porque está siendo utilizada por ítems del inventario.";
                }
                $tipo_mensaje = 'danger';
            }
            $stmt->close();
        }
    }
}

// ==============================================
// OBTENCIÓN DE CATEGORÍAS PARA MOSTRAR EN TABLA
// ==============================================
$categorias = [];
$sql_select = "SELECT id, nombre, fecha_creacion FROM categorias ORDER BY nombre ASC";
$result = $conn->query($sql_select);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $categorias[] = $row;
    }
}
?>

<!-- ============================================== -->
<!-- SECCIÓN HTML - INTERFAZ DE USUARIO -->
<!-- ============================================== -->

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <!-- Encabezado de la tarjeta -->
            <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                <h4 class="mb-0">Gestionar Categorías de Inventario</h4>
                <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#addCategoriaModal">
                    <i class="bi bi-plus-circle-fill"></i> Nueva Categoría
                </button>
            </div>
            
            <!-- Cuerpo de la tarjeta -->
            <div class="card-body">
                <?php if (!empty($mensaje)): ?>
                    <div class="alert alert-<?php echo $tipo_mensaje; ?> alert-dismissible fade show" role="alert">
                        <?php echo htmlspecialchars($mensaje); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <?php if (empty($categorias)): ?>
                    <p class="text-center">No hay categorías registradas. ¡Añade la primera!</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre</th>
                                    <th>Fecha Creación</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($categorias as $cat): ?>
                               <tr>
    <td><?php echo $cat['id']; ?></td>
    <td><?php echo htmlspecialchars($cat['nombre']); ?></td>
    <td><?php echo date('d/m/Y H:i', strtotime($cat['fecha_creacion'])); ?></td>
    <td>
        <div class="btn-group" role="group">
            <!-- Botón Editar -->
            <button type="button" class="btn btn-warning btn-sm btn-edit" 
                    data-bs-toggle="modal" data-bs-target="#editCategoriaModal"
                    data-id="<?php echo $cat['id']; ?>" 
                    data-nombre="<?php echo htmlspecialchars($cat['nombre']); ?>"
                    title="Editar">
               <i class="bi bi-pen"></i> Editar
            </button>
            
            <!-- Botón Eliminar -->
            <button type="button" class="btn btn-danger btn-sm btn-delete ms-2" 
                    data-bs-toggle="modal" data-bs-target="#deleteCategoriaModal"
                    data-id="<?php echo $cat['id']; ?>"
                    data-nombre="<?php echo htmlspecialchars($cat['nombre']); ?>"
                    title="Eliminar">
                <i class="bi bi-trash"></i> Eliminar
            </button>
        </div>
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
</div>

<!-- ============================================== -->
<!-- MODALES -->
<!-- ============================================== -->

<!-- Modal Añadir Categoría -->
<div class="modal fade" id="addCategoriaModal" tabindex="-1" aria-labelledby="addCategoriaModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="gestionar_categorias.php">
                <input type="hidden" name="action" value="add">
                <div class="modal-header">
                    <h5 class="modal-title" id="addCategoriaModalLabel">Añadir Nueva Categoría</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="add_nombre_categoria" class="form-label">Nombre de la Categoría</label>
                        <input type="text" class="form-control" id="add_nombre_categoria" name="nombre_categoria" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="bi bi-x-octagon"></i> Cancelar</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-floppy"></i> Guardar Categoría</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Editar Categoría -->
<div class="modal fade" id="editCategoriaModal" tabindex="-1" aria-labelledby="editCategoriaModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="gestionar_categorias.php">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="id_categoria" id="edit_id_categoria">
                <div class="modal-header">
                    <h5 class="modal-title" id="editCategoriaModalLabel">Editar Categoría</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_nombre_categoria" class="form-label">Nombre de la Categoría</label>
                        <input type="text" class="form-control" id="edit_nombre_categoria" name="nombre_categoria" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Eliminar Categoría -->
<div class="modal fade" id="deleteCategoriaModal" tabindex="-1" aria-labelledby="deleteCategoriaModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="gestionar_categorias.php">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="id_categoria" id="delete_id_categoria">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteCategoriaModalLabel">Confirmar Eliminación</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>¿Estás seguro de que deseas eliminar la categoría "<strong id="delete_nombre_categoria_display"></strong>"?</p>
                    <p class="text-danger small">Esta acción no se puede deshacer. Si la categoría está en uso, la eliminación podría fallar o los items asociados podrían quedar sin categoría.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger">Eliminar Categoría</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ============================================== -->
<!-- SCRIPTS JAVASCRIPT -->
<!-- ============================================== -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Configuración del modal de Editar
    var editCategoriaModal = document.getElementById('editCategoriaModal');
    if (editCategoriaModal) {
        editCategoriaModal.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget;
            var id = button.getAttribute('data-id');
            var nombre = button.getAttribute('data-nombre');

            document.getElementById('edit_id_categoria').value = id;
            document.getElementById('edit_nombre_categoria').value = nombre;
        });
    }

    // Configuración del modal de Eliminar
    var deleteCategoriaModal = document.getElementById('deleteCategoriaModal');
    if (deleteCategoriaModal) {
        deleteCategoriaModal.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget;
            var id = button.getAttribute('data-id');
            var nombre = button.getAttribute('data-nombre');

            document.getElementById('delete_id_categoria').value = id;
            document.getElementById('delete_nombre_categoria_display').textContent = nombre;
        });
    }

    // Configuración de alertas para que se puedan cerrar
    var alertList = document.querySelectorAll('.alert-dismissible');
    alertList.forEach(function (alert) {
        new bootstrap.Alert(alert);
    });
});
</script>

<?php include '../includes/footer.php'; ?>
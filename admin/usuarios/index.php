<?php
session_start();
include '../../includes/db.php';
include '../../includes/auth.php';

// Solo administradores pueden acceder
if ($_SESSION['usuario_rol'] != 'admin') {
    header("Location: ../../index.php");
    exit();
}

// Eliminar usuario (si recibe ID por GET)
if (isset($_GET['eliminar'])) {
    $id = $_GET['eliminar'];
    $sql = "DELETE FROM usuarios WHERE id = ? AND id != ?"; // Evitar auto-eliminación
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $id, $_SESSION['usuario_id']);
    $stmt->execute();
    if ($stmt->affected_rows > 0) {
        $exito = "Usuario eliminado correctamente.";
    } else {
        $error = "No se pudo eliminar el usuario.";
    }
}
?>

<?php include '../../includes/header.php'; ?>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                <h4 class="mb-0">
                    <i class="bi bi-people-fill me-2"></i>Gestión de Usuarios
                </h4>
                <a href="../registro.php" class="btn btn-success btn-sm">
                    <i class="bi bi-person-plus me-1"></i>Nuevo Usuario
                </a>
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

                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th><i class="bi bi-person-badge me-1"></i> Nombre</th>
                                <th><i class="bi bi-envelope me-1"></i> Email</th>
                                <th><i class="bi bi-shield me-1"></i> Rol</th>
                                <th><i class="bi bi-gear me-1"></i> Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sql = "SELECT id, nombre, email, rol FROM usuarios";
                            $result = $conn->query($sql);
                            while ($row = $result->fetch_assoc()) {
                                echo "
                                <tr>
                                    <td>" . htmlspecialchars($row['nombre']) . "</td>
                                    <td>" . htmlspecialchars($row['email']) . "</td>
                                    <td><span class='badge bg-" . ($row['rol'] == 'admin' ? 'primary' : 'secondary') . "'>" . ucfirst(htmlspecialchars($row['rol'])) . "</span></td>
                                    <td>
                                        <a href='editar.php?id=" . $row['id'] . "' class='btn btn-sm btn-warning me-1'>
                                            <i class='bi bi-pencil-square me-1'></i>Editar
                                        </a>
                                        <a href='?eliminar=" . $row['id'] . "' class='btn btn-sm btn-danger' onclick='return confirm(\"¿Estás seguro de eliminar este usuario?\")'>
                                            <i class='bi bi-trash me-1'></i>Eliminar
                                        </a>
                                    </td>
                                </tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>
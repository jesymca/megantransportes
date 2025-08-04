<?php
session_start();
include '../../includes/db.php';
include '../../includes/auth.php';

if ($_SESSION['usuario_rol'] != 'admin') {
    header("Location: ../../index.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$id = $_GET['id'];

// Obtener datos del usuario
$sql = "SELECT * FROM usuarios WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$usuario = $result->fetch_assoc();

if (!$usuario) {
    header("Location: index.php");
    exit();
}

// Actualizar datos
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    $rol = $_POST['rol'];

    $sql = "UPDATE usuarios SET nombre = ?, email = ?, rol = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $nombre, $email, $rol, $id);

    if ($stmt->execute()) {
        $exito = "Usuario actualizado correctamente.";
    } else {
        $error = "Error al actualizar: " . $conn->error;
    }
}
?>

<?php include '../../includes/header.php'; ?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-warning text-dark">
                <h4>Editar Usuario</h4>
            </div>
            <div class="card-body">
                <?php if (isset($exito)): ?>
                    <div class="alert alert-success"><?php echo $exito; ?></div>
                <?php elseif (isset($error)): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>

                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Nombre</label>
                        <input type="text" name="nombre" class="form-control" value="<?php echo $usuario['nombre']; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" value="<?php echo $usuario['email']; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Rol</label>
                        <select name="rol" class="form-select" required>
                            <option value="admin" <?php if ($usuario['rol'] == 'admin') echo 'selected'; ?>>Administrador</option>
                            <option value="tecnico" <?php if ($usuario['rol'] == 'tecnico') echo 'selected'; ?>>Técnico</option>
                            <option value="mecanico" <?php if ($usuario['rol'] == 'mecanico') echo 'selected'; ?>>Mecánico</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-warning w-100">Guardar Cambios</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>
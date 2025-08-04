<?php
session_start();
include '../includes/db.php';
include '../includes/header.php';

// Solo accesible por administradores
if ($_SESSION['usuario_rol'] != 'admin') {
    header("Location: ../index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $rol = $_POST['rol'];
    $pregunta = $_POST['pregunta'];
    $respuesta = $_POST['respuesta'];

    $sql = "INSERT INTO usuarios (nombre, email, password, rol, pregunta_seguridad, respuesta_seguridad) 
            VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssss", $nombre, $email, $password, $rol, $pregunta, $respuesta);

    if ($stmt->execute()) {
        $exito = "Usuario registrado correctamente.";
    } else {
        $error = "Error al registrar: " . $conn->error;
    }
}
?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-success text-white">
                <h4 class="mb-0">Registrar Nuevo Usuario</h4>
            </div>
            <div class="card-body">
                <?php if (isset($exito)): ?>
                    <div class="alert alert-success"><?php echo $exito; ?></div>
                <?php elseif (isset($error)): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                <form method="POST">
                    <div class="mb-3">
                        <label for="nombre" class="form-label">Nombre Completo</label>
                        <input type="text" class="form-control" id="nombre" name="nombre" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Correo Electrónico</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Contraseña</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <div class="mb-3">
                        <label for="rol" class="form-label">Rol</label>
                        <select class="form-select" id="rol" name="rol" required>
                            <option value="admin">Administrador</option>
                            <option value="tecnico">Técnico</option>
                            <option value="mecanico">Mecánico</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="pregunta" class="form-label">Pregunta de Seguridad</label>
                        <input type="text" class="form-control" id="pregunta" name="pregunta" required>
                    </div>
                    <div class="mb-3">
                        <label for="respuesta" class="form-label">Respuesta</label>
                        <input type="text" class="form-control" id="respuesta" name="respuesta" required>
                    </div>
                    <button type="submit" class="btn btn-success w-100">Registrar</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
<?php
session_start();
include '../includes/db.php';
include '../includes/header.php';

if (!isset($_SESSION['reset_email'])) {
    header("Location: recuperar.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $email = $_SESSION['reset_email'];

    $sql = "UPDATE usuarios SET password = ? WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $password, $email);

    if ($stmt->execute()) {
        unset($_SESSION['reset_email']);
        $exito = "Contraseña actualizada. <a href='index.php'>Iniciar Sesión</a>";
    } else {
        $error = "Error al actualizar: " . $conn->error;
    }
}
?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-info text-white">
                <h4 class="mb-0">Cambiar Contraseña</h4>
            </div>
            <div class="card-body">
                <?php if (isset($exito)): ?>
                    <div class="alert alert-success"><?php echo $exito; ?></div>
                <?php elseif (isset($error)): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                <form method="POST">
                    <div class="mb-3">
                        <label for="password" class="form-label">Nueva Contraseña</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <button type="submit" class="btn btn-info w-100">Actualizar</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
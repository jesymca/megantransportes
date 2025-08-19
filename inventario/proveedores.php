<?php
session_start();
include '../includes/db.php';
include '../includes/auth.php';
include '../includes/header.php';

// Inicializar variables para el formulario
$rif = $nombre = $direccion = $tlf = $correo = $web = $social = '';
$editando = false;

// Editar proveedor: cargar datos en el formulario
if (isset($_GET['editar'])) {
    $editando = true;
    $id_editar = intval($_GET['editar']);
    $stmt = $conn->prepare("SELECT * FROM proveedores WHERE id = ?");
    $stmt->bind_param("i", $id_editar);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res && $prov = $res->fetch_assoc()) {
        $rif = $prov['rif'];
        $nombre = $prov['nombre'];
        $direccion = $prov['direccion'];
        $tlf = $prov['tlf'];
        $correo = $prov['correo'];
        $web = $prov['web'];
        $social = $prov['social'];
    }
    $stmt->close();
}

// Guardar cambios de edición
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['guardar_edicion'])) {
    $id_editar = intval($_POST['id']);
    $rif = trim($_POST['rif']);
    $nombre = trim($_POST['nombre']);
    $direccion = trim($_POST['direccion']);
    $tlf = trim($_POST['tlf']);
    $correo = trim($_POST['correo']);
    $web = trim($_POST['web']);
    $social = trim($_POST['social']);

    if ($nombre !== '' && $rif !== '') {
        $stmt = $conn->prepare("UPDATE proveedores SET rif=?, nombre=?, direccion=?, tlf=?, correo=?, web=?, social=? WHERE id=?");
        $stmt->bind_param("sssssssi", $rif, $nombre, $direccion, $tlf, $correo, $web, $social, $id_editar);
        if ($stmt->execute()) {
            $exito = "Proveedor actualizado correctamente.";
        } else {
            $error = "Error al actualizar proveedor: " . $stmt->error;
        }
        $stmt->close();
        // Limpiar formulario
        $editando = false;
        $rif = $nombre = $direccion = $tlf = $correo = $web = $social = '';
    } else {
        $error = "El RIF y el nombre del proveedor no pueden estar vacíos.";
        $editando = true;
    }
}

// Agregar proveedor nuevo
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['agregar'])) {
    $rif = trim($_POST['rif']);
    $nombre = trim($_POST['nombre']);
    $direccion = trim($_POST['direccion']);
    $tlf = trim($_POST['tlf']);
    $correo = trim($_POST['correo']);
    $web = trim($_POST['web']);
    $social = trim($_POST['social']);

    if ($nombre !== '' && $rif !== '') {
        $stmt = $conn->prepare("INSERT INTO proveedores (rif, nombre, direccion, tlf, correo, web, social) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssss", $rif, $nombre, $direccion, $tlf, $correo, $web, $social);
        if ($stmt->execute()) {
            $exito = "Proveedor agregado correctamente.";
        } else {
            $error = "Error al agregar proveedor: " . $stmt->error;
        }
        $stmt->close();
        // Limpiar formulario
        $rif = $nombre = $direccion = $tlf = $correo = $web = $social = '';
    } else {
        $error = "El RIF y el nombre del proveedor no pueden estar vacíos.";
    }
}

// Eliminar proveedor
if (isset($_GET['eliminar'])) {
    $id = intval($_GET['eliminar']);
    $stmt = $conn->prepare("DELETE FROM proveedores WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    $exito = "Proveedor eliminado correctamente.";
}

// Listar proveedores
$proveedores = [];
$result = $conn->query("SELECT * FROM proveedores ORDER BY fecha DESC");
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $proveedores[] = $row;
    }
}
?>

<div class="row">
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <i class="bi bi-truck me-2"></i><?php echo $editando ? 'Editar Proveedor' : 'Agregar Proveedor'; ?>
            </div>
            <div class="card-body">
                <?php if (!empty($exito)): ?>
                    <div class="alert alert-success"><?php echo $exito; ?></div>
                <?php elseif (!empty($error)): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                <form method="POST">
                    <?php if ($editando): ?>
                        <input type="hidden" name="id" value="<?php echo htmlspecialchars($id_editar); ?>">
                    <?php endif; ?>
                    <div class="mb-3">
                        <label class="form-label">RIF</label>
                        <input type="text" name="rif" class="form-control" required value="<?php echo htmlspecialchars($rif); ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nombre</label>
                        <input type="text" name="nombre" class="form-control" required value="<?php echo htmlspecialchars($nombre); ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Dirección</label>
                        <textarea name="direccion" class="form-control" rows="2"><?php echo htmlspecialchars($direccion); ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Teléfono</label>
                        <input type="text" name="tlf" class="form-control" value="<?php echo htmlspecialchars($tlf); ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Correo</label>
                        <input type="email" name="correo" class="form-control" value="<?php echo htmlspecialchars($correo); ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Web</label>
                        <input type="text" name="web" class="form-control" value="<?php echo htmlspecialchars($web); ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Red Social</label>
                        <input type="text" name="social" class="form-control" value="<?php echo htmlspecialchars($social); ?>">
                    </div>
                    <?php if ($editando): ?>
                        <button type="submit" name="guardar_edicion" class="btn btn-warning">
                            <i class="bi bi-pencil-square me-1"></i>Guardar Cambios
                        </button>
                        <a href="proveedores.php" class="btn btn-secondary ms-2">Cancelar</a>
                    <?php else: ?>
                        <button type="submit" name="agregar" class="btn btn-success">
                            <i class="bi bi-plus-circle me-1"></i>Agregar
                        </button>
                    <?php endif; ?>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header bg-secondary text-white">
                <i class="bi bi-list-ul me-2"></i>Lista de Proveedores
            </div>
            <div class="card-body table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>RIF</th>
                            <th>Nombre</th>
                            <th>Teléfono</th>
                            <th>Correo</th>
                            <th>Web</th>
                            <th>Red Social</th>
                            <th>Fecha</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($proveedores)): ?>
                            <?php foreach ($proveedores as $prov): ?>
                                <tr>
                                    <td><?php echo $prov['id']; ?></td>
                                    <td><?php echo htmlspecialchars($prov['rif']); ?></td>
                                    <td><?php echo htmlspecialchars($prov['nombre']); ?></td>
                                    <td><?php echo htmlspecialchars($prov['tlf']); ?></td>
                                    <td><?php echo htmlspecialchars($prov['correo']); ?></td>
                                    <td><?php echo htmlspecialchars($prov['web']); ?></td>
                                    <td><?php echo htmlspecialchars($prov['social']); ?></td>
                                    <td><?php echo htmlspecialchars($prov['fecha']); ?></td>
                                    <td>
                                        <a href="?editar=<?php echo $prov['id']; ?>" class="btn btn-warning btn-sm">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        <a href="?eliminar=<?php echo $prov['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Eliminar este proveedor?');">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="9" class="text-center">No hay proveedores registrados.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
<?php
session_start();
include '../includes/db.php';
include '../includes/header_index.php'; // Esto está bien aquí para la carga de la página

$pregunta_seguridad_display = null; // Renombrado para evitar confusión
$email_ingresado = '';
$mostrar_pregunta = false; // Determina si los campos de pregunta/respuesta son visibles inicialmente

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $respuesta = $_POST['respuesta']; // Asumimos que siempre se envía si el formulario está visible
    $email_ingresado = htmlspecialchars($email);

    // Verificar respuesta de seguridad
    // Es importante obtener la pregunta de seguridad aquí de nuevo
    // para mostrarla si la respuesta es incorrecta, o mejor,
    // guardar la pregunta en un campo oculto cuando se carga inicialmente.
    // Por simplicidad, la volvemos a obtener.

    $sql_verif = "SELECT id, pregunta_seguridad FROM usuarios WHERE email = ? AND respuesta_seguridad = ?";
    $stmt_verif = $conn->prepare($sql_verif);
    if (!$stmt_verif) {
        die("Error en la preparación de la consulta de verificación: " . $conn->error);
    }
    $stmt_verif->bind_param("ss", $email, $respuesta);
    $stmt_verif->execute();
    $result_verif = $stmt_verif->get_result();

    if ($result_verif->num_rows == 1) {
        $_SESSION['reset_email'] = $email;
        header("Location: cambiar_password.php");
        exit();
    } else {
        $error = "Respuesta incorrecta o el usuario no existe.";
        $mostrar_pregunta = true; // Mantenemos visible la pregunta

        // Obtener pregunta para mostrarla nuevamente si la respuesta fue incorrecta
        $sql_preg = "SELECT pregunta_seguridad FROM usuarios WHERE email = ?";
        $stmt_preg = $conn->prepare($sql_preg);
        if (!$stmt_preg) {
            die("Error en la preparación de la consulta de pregunta: " . $conn->error);
        }
        $stmt_preg->bind_param("s", $email);
        $stmt_preg->execute();
        $result_preg = $stmt_preg->get_result();

        if ($result_preg->num_rows == 1) {
            $usuario = $result_preg->fetch_assoc();
            $pregunta_seguridad_display = htmlspecialchars($usuario['pregunta_seguridad']);
        } else {
            // Si el email no existe al momento del POST, la pregunta no se encontrará.
            // $error ya cubre el caso de "usuario no existe" implícitamente.
            // Podrías añadir un mensaje más específico si lo deseas.
            $pregunta_seguridad_display = "No se pudo cargar la pregunta.";
        }
        $stmt_preg->close();
    }
    $stmt_verif->close();
}
// YA NO NECESITAS EL BLOQUE elseif (isset($_GET['email'])) aquí
// porque esa lógica está en ajax_obtener_pregunta.php
?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-warning text-white">
                <h4 class="mb-0">Recuperar Contraseña</h4>
            </div>
            <div class="card-body">
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>

                <form method="POST" id="formRecuperar" action="recuperar.php">
                    <div class="mb-3">
                        <label for="email" class="form-label">Correo Electrónico</label>
                        <input type="email" class="form-control" id="email" name="email"
                               value="<?php echo $email_ingresado; ?>" required
                               onchange="obtenerPregunta()">
                    </div>

                    <div class="mb-3" id="preguntaContainer"
                         style="<?php echo $mostrar_pregunta ? '' : 'display: none;' ?>">
                        <label class="form-label">Pregunta de Seguridad</label>
                        <div class="alert alert-info" id="preguntaSeguridad">
                            <?php echo $pregunta_seguridad_display; // Mostrar la pregunta si hubo un error POST ?>
                        </div>
                    </div>

                    <div class="mb-3" id="respuestaContainer"
                         style="<?php echo $mostrar_pregunta ? '' : 'display: none;' ?>">
                        <label for="respuesta" class="form-label">Respuesta de Seguridad</label>
                        <input type="text" class="form-control" id="respuesta" name="respuesta" <?php echo $mostrar_pregunta ? 'required' : ''; ?>>
                    </div>

                    <button type="submit" class="btn btn-warning w-100"
                            id="btnVerificar" <?php echo $mostrar_pregunta ? '' : 'disabled'; ?>>
                        Verificar
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Tu función obtenerPregunta() ya modificada va aquí
function obtenerPregunta() {
    const email = document.getElementById('email').value;
    const preguntaContainer = document.getElementById('preguntaContainer');
    const respuestaContainer = document.getElementById('respuestaContainer');
    const preguntaSeguridadDiv = document.getElementById('preguntaSeguridad');
    const btnVerificar = document.getElementById('btnVerificar');
    const respuestaInput = document.getElementById('respuesta'); // Para el atributo required

    if (email) {
        fetch(`ajax_obtener_pregunta.php?email=${encodeURIComponent(email)}`)
            .then(response => response.text())
            .then(pregunta => {
                if (pregunta.trim() !== "") {
                    preguntaSeguridadDiv.textContent = pregunta;
                    preguntaContainer.style.display = 'block';
                    respuestaContainer.style.display = 'block';
                    respuestaInput.required = true; // Hacer el campo de respuesta requerido
                    btnVerificar.disabled = false;
                } else {
                    preguntaSeguridadDiv.textContent = '';
                    preguntaContainer.style.display = 'none';
                    respuestaContainer.style.display = 'none';
                    respuestaInput.required = false; // No requerido si no hay pregunta
                    btnVerificar.disabled = true;
                    // alert('No se encontró un usuario con ese correo electrónico o no tiene pregunta de seguridad configurada.');
                    // Podrías mostrar este mensaje en un div en lugar de un alert
                    // Por ejemplo, añadir un <div id="emailErrorMsg" class="text-danger small mt-1"></div>
                    // y luego: document.getElementById('emailErrorMsg').textContent = 'Mensaje de error';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                preguntaSeguridadDiv.textContent = '';
                preguntaContainer.style.display = 'none';
                respuestaContainer.style.display = 'none';
                respuestaInput.required = false;
                btnVerificar.disabled = true;
                alert('Ocurrió un error al obtener la pregunta de seguridad.');
            });
    } else {
        preguntaSeguridadDiv.textContent = '';
        preguntaContainer.style.display = 'none';
        respuestaContainer.style.display = 'none';
        respuestaInput.required = false;
        btnVerificar.disabled = true;
    }
}

// Para asegurar que al cargar la página, si $mostrar_pregunta es true
// (por un error en el POST previo), el botón esté habilitado y el campo de respuesta sea 'required'.
document.addEventListener('DOMContentLoaded', function() {
    const mostrarPreguntaPHP = <?php echo json_encode($mostrar_pregunta); ?>;
    if (mostrarPreguntaPHP) {
        document.getElementById('btnVerificar').disabled = false;
        document.getElementById('respuesta').required = true;
    }
});
</script>

<?php include '../includes/footer.php'; ?>
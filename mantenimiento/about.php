<?php
session_start();
include '../includes/db.php';
include '../includes/auth.php'; // Validar sesión activa
include '../includes/header.php';
?>

<div class="card mt-4">
    <div class="card-header bg-dark text-white">
        <h5>Acerca de MEGAN TRANSPORTES C.A. - Sistema de Inventario</h5>
    </div>
    <div class="card-body">
        <p>Este sistema de gestión de inventario ha sido desarrollado para MEGAN TRANSPORTES C.A. con el objetivo de optimizar el control y seguimiento de los activos y materiales de la empresa.</p>

        <h6>Versión del Sistema:</h6>
        <p><strong>1.0.0</strong> (Julio 2025)</p>

        <h6>Características Principales:</h6>
        <ul>
            <li>Gestión de entradas y salidas de inventario.</li>
            <li>Organización de artículos por categorías.</li>
            <li>Generación de reportes detallados.</li>
            <li>Administración de usuarios con diferentes niveles de acceso.</li>
            <li>Funcionalidades de mantenimiento y respaldo de base de datos.</li>
        </ul>

        <h6>Desarrollado por:</h6>
        <p>
            COLINA JOEL<br>
            PERDOMO LUIS <br>
            SISTEMA DE CONTROL INVENTARIO PARA LA EMPRESA MEGAN TRANSPORTES C.A. EN PUERTO CABELLO ESTADO CARABOBO.
        </p>

        <h6>Agradecimientos:</h6>
        <p>Agradecemos a MEGAN TRANSPORTES C.A. por la oportunidad de desarrollar esta herramienta que esperamos sea de gran utilidad para sus operaciones diarias.</p>

        <p class="mt-4 text-muted">
            &copy; <?php echo date("Y"); ?> MEGAN TRANSPORTES C.A. Todos los derechos reservados.
        </p>
    </div>
</div>

<?php include '../includes/footer.php'; ?>

<?php
session_start();
include '../includes/db.php';
include '../includes/auth.php'; // Validar sesión activa
include '../includes/header.php';
?>

<div class="card mt-4">
    <div class="card-header bg-dark text-white">
        <h5>Manual de Uso del Sistema MEGAN TRANSPORTES C.A.</h5>
    </div>
    <div class="card-body">
        <p>Bienvenido al manual de uso del sistema de gestión de inventario para MEGAN TRANSPORTES C.A. Aquí encontrará guías detalladas sobre cómo utilizar las diferentes funcionalidades del sistema.</p>

        <h6>1. Inicio de Sesión y Acceso</h6>
        <p>Para acceder al sistema, ingrese sus credenciales (nombre de usuario y contraseña) en la página de inicio de sesión. Si tiene problemas para acceder, contacte al administrador del sistema.</p>

        <h6>2. Gestión de Inventarios</h6>
        <ul>
            <li><strong>Entradas de Inventario:</strong> Utilice esta sección para registrar la entrada de nuevos artículos o la reposición de existencias. Asegúrese de ingresar la cantidad correcta y seleccionar la categoría adecuada.</li>
            <li><strong>Salidas de Inventario:</strong> Registre la salida de artículos del inventario, indicando la cantidad y el técnico o proyecto al que se asigna el material.</li>
            <li><strong>Categorías:</strong> Administre las categorías de los artículos para mantener el inventario organizado. Puede añadir, editar o eliminar categorías según sea necesario.</li>
        </ul>

        <h6>3. Generación de Reportes</h6>
        <p>En la sección de "Reportes" puede generar informes personalizados sobre el estado del inventario, las entradas, las salidas y el historial de movimientos. Utilice los filtros disponibles para obtener la información deseada.</p>

        <h6>4. Administración de Usuarios</h6>
        <p>Solo los usuarios con permisos de administrador pueden acceder a esta sección. Aquí puede gestionar las cuentas de usuario, añadir nuevos usuarios, modificar sus roles y permisos, o deshabilitar cuentas.</p>

        <h6>5. Área de Mantenimiento</h6>
        <ul>
            <li><strong>Respaldo de BD:</strong> Permite crear una copia de seguridad de toda la base de datos. Es crucial realizar respaldos periódicamente para proteger su información. También puede descargar y eliminar respaldos antiguos.</li>
            <li><strong>Optimizar BD:</strong> Realiza una optimización de las tablas de la base de datos, lo que puede mejorar el rendimiento general del sistema.</li>
            <li><strong>Manual de Uso:</strong> (Usted está aquí) Acceso a este manual.</li>
            <li><strong>Acerca De:</strong> Información sobre la versión del sistema y los desarrolladores.</li>
        </ul>

        <h6 class="mt-4">Consejos Importantes:</h6>
        <ul>
            <li>Guarde sus credenciales de acceso en un lugar seguro.</li>
            <li>Realice respaldos de la base de datos con regularidad.</li>
            <li>Reporte cualquier problema o error al equipo de soporte técnico.</li>
        </ul>

        <p>Este manual se actualizará con nuevas funcionalidades y mejoras. ¡Gracias por usar nuestro sistema!</p>
    </div>
</div>

<?php include '../includes/footer.php'; ?>

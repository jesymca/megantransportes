<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MEGAN TRANSPORTES C.A. - Inventario</title>
    <link href="/megantransportes/node_modules/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/megantransportes/node_modules/bootstrap-icons/font/bootstrap-icons.min.css">
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
        <a class="navbar-brand" href="/megantransportes/index.php">
            <img src="/megantransportes/assets/img/logo.png" alt="Logo" width="80" class="d-inline-block ">
            <img src="/megantransportes/assets/img/logo.jpg" alt="Logo" width="80" class="d-inline-block ">
            MEGAN TRANSPORTES C.A.
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-box-seam me-1"></i> Inventarios
                    </a>
                  <ul class="dropdown-menu">
    <li>
        <a class="dropdown-item" href="/megantransportes/inventario/entradas.php">
            <i class="bi bi-box-arrow-in-down me-2"></i> Entradas
        </a>
    </li>
    <li>
        <a class="dropdown-item" href="/megantransportes/inventario/salidas.php">
            <i class="bi bi-box-arrow-up me-2"></i> Salidas
        </a>
    </li>
    <li>
        <a class="dropdown-item" href="/megantransportes/inventario/gestionar_categorias.php">
            <i class="bi bi-tags me-2"></i> Categorías
        </a>
    </li>
    <li>
        <a class="dropdown-item" href="/megantransportes/inventario/proveedores.php">
            <i class="bi bi-truck me-2"></i> Proveedores
        </a>
    </li>
</ul>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="/megantransportes/reportes/generar.php">
                        <i class="bi bi-file-earmark-text me-1"></i> Reportes
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/megantransportes/admin/usuarios/">
                        <i class="bi bi-people me-1"></i> Usuarios
                    </a>
                </li>
                
                <!-- Nuevo Dropdown para Mantenimiento -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-gear me-1"></i> Mantenimiento
                    </a>
                    <ul class="dropdown-menu">
                        <li>
                            <a class="dropdown-item" href="/megantransportes/mantenimiento/backup.php">
                                <i class="bi bi-database me-2"></i> Respaldo de BD
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="/megantransportes/mantenimiento/optimize.php">
                                <i class="bi bi-speedometer2 me-2"></i> Optimizar BD
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="/megantransportes/mantenimiento/manual.php">
                                <i class="bi bi-book me-2"></i> Manual de Uso
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="/megantransportes/mantenimiento/about.php">
                                <i class="bi bi-info-circle me-2"></i> Acerca De
                            </a>
                        </li>
                    </ul>
                </li>
                <!-- Fin del Nuevo Dropdown -->

                <li class="nav-item">
                    <a class="nav-link" href="/megantransportes/login/logout.php">
                        <i class="bi bi-box-arrow-right me-1"></i> Cerrar Sesión
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>
    <div class="container mt-4">

<?php
session_start();
include '../includes/db.php';
include '../includes/auth.php';
include '../includes/header.php';

// Obtener categorías disponibles para el select
$categorias_disponibles = [];
$sql_get_categorias = "SELECT id, nombre FROM categorias ORDER BY nombre ASC";
$result_categorias = $conn->query($sql_get_categorias);
if ($result_categorias && $result_categorias->num_rows > 0) {
    while ($row_cat = $result_categorias->fetch_assoc()) {
        $categorias_disponibles[] = $row_cat;
    }
}
?>
<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card">
            <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                <h4 class="mb-0">
                    <i class="bi bi-file-earmark-text me-2"></i>Generar Reporte PDF
                </h4>
            </div>
            <div class="card-body">
                <form id="formReporte" action="pdf/generar_reporte.php" method="POST" target="_blank">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">
                                <i class="bi bi-card-list me-1"></i>Tipo de Reporte
                            </label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-filter"></i></span>
                                <select name="tipo_reporte" id="tipoReporte" class="form-select" required>
                                    <option value="inventario">
                                        <i class="bi bi-box-seam me-1"></i>Inventario Actual
                                    </option>
                                    <option value="salidas">
                                        <i class="bi bi-box-arrow-up me-1"></i>Salidas de Inventario
                                    </option>
                                    <option value="usuarios">
                                        <i class="bi bi-people me-1"></i>Usuarios Registrados
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3" id="categoriaContainer">
                            <label for="categoria" class="form-label">
                                <i class="bi bi-tags me-1"></i>Categoría
                            </label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-bookmark"></i></span>
                                <select name="categoria" id="categoria" class="form-select">
                                    <option value="todas">
                                        <i class="bi bi-collection me-1"></i>Todas las categorías
                                    </option>
                                    <?php if (!empty($categorias_disponibles)): ?>
                                        <?php foreach ($categorias_disponibles as $categoria_item): ?>
                                            <option value="<?php echo htmlspecialchars($categoria_item['id']); ?>">
                                                <i class="bi bi-tag me-1"></i><?php echo htmlspecialchars($categoria_item['nombre']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <option value="todas" disabled>
                                            <i class="bi bi-exclamation-triangle me-1"></i>No hay categorías disponibles
                                        </option>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row" id="rangoFechasContainer" style="display: none;">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">
                                <i class="bi bi-calendar-date me-1"></i>Fecha Inicio
                            </label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-calendar3"></i></span>
                                <input type="date" name="fecha_inicio" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">
                                <i class="bi bi-calendar-check me-1"></i>Fecha Fin
                            </label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-calendar3"></i></span>
                                <input type="date" name="fecha_fin" class="form-control">
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-info w-100 mt-3">
                        <i class="bi bi-file-earmark-pdf-fill me-2"></i>Generar PDF
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const tipoReporte = document.getElementById('tipoReporte');
    const categoriaContainer = document.getElementById('categoriaContainer');
    const rangoFechasContainer = document.getElementById('rangoFechasContainer');
    const categoriaSelect = document.getElementById('categoria');

    function actualizarCampos() {
        const valor = tipoReporte.value;
        
        // Mostrar categoría solo para inventario y salidas
        if (valor === 'inventario' || valor === 'salidas') {
            categoriaContainer.style.display = 'block';
            categoriaSelect.setAttribute('required', '');
        } else {
            categoriaContainer.style.display = 'none';
            categoriaSelect.removeAttribute('required');
        }
        
        // Mostrar rango de fechas solo para salidas
        if (valor === 'salidas') {
            rangoFechasContainer.style.display = 'flex';
            document.querySelector('[name="fecha_inicio"]').setAttribute('required', '');
            document.querySelector('[name="fecha_fin"]').setAttribute('required', '');
        } else {
            rangoFechasContainer.style.display = 'none';
            document.querySelector('[name="fecha_inicio"]').removeAttribute('required');
            document.querySelector('[name="fecha_fin"]').removeAttribute('required');
        }
    }

    tipoReporte.addEventListener('change', actualizarCampos);
    actualizarCampos();
});
</script>

<?php include '../includes/footer.php'; ?>
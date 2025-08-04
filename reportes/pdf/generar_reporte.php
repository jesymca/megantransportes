<?php
session_start();
require __DIR__ . '/../../vendor/autoload.php';
use Dompdf\Dompdf;
include __DIR__ . '/../../includes/db.php';
include __DIR__ . '/../../includes/auth.php';

// Verificar si hay errores en las consultas SQL
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    // Recibir filtros del formulario
    $tipo_reporte = $_POST['tipo_reporte'] ?? 'inventario';
    $categoria_id = $_POST['categoria'] ?? 'todas';
    $fecha_inicio = $_POST['fecha_inicio'] ?? '';
    $fecha_fin = $_POST['fecha_fin'] ?? '';

    // Consulta según el tipo de reporte
    switch ($tipo_reporte) {
        case 'inventario':
            // Modificado para usar JOIN con la tabla categorias
            $sql = "SELECT i.*, c.nombre as categoria_nombre 
                    FROM inventario i
                    LEFT JOIN categorias c ON i.categoria_id = c.id";
            
            if ($categoria_id != 'todas') {
                $sql .= " WHERE i.categoria_id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $categoria_id);
            } else {
                $stmt = $conn->prepare($sql);
            }
            $titulo = "Reporte de Inventario";
            break;

        case 'salidas':
            // Modificado para incluir el nombre de la categoría
            $sql = "SELECT s.*, i.nombre as item, u.nombre as tecnico, c.nombre as categoria_nombre
                    FROM salidas_inventario s
                    JOIN inventario i ON s.id_item = i.id
                    JOIN usuarios u ON s.id_usuario = u.id
                    LEFT JOIN categorias c ON i.categoria_id = c.id";
            
            if ($fecha_inicio && $fecha_fin) {
                $sql .= " WHERE DATE(s.fecha_salida) BETWEEN ? AND ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ss", $fecha_inicio, $fecha_fin);
            } else {
                $stmt = $conn->prepare($sql);
            }
            $titulo = "Reporte de Salidas";
            break;

        case 'usuarios':
            $sql = "SELECT * FROM usuarios";
            $stmt = $conn->prepare($sql);
            $titulo = "Reporte de Usuarios";
            break;

        default:
            throw new Exception("Tipo de reporte no válido");
    }

    $stmt->execute();
    $result = $stmt->get_result();

    // Generar HTML para el PDF
    $html = "
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset='UTF-8'>
        <title>$titulo</title>
        <style>
            body { font-family: Arial, sans-serif; margin: 20px; }
            h1 { color: #0066cc; text-align: center; }
            table { width: 100%; border-collapse: collapse; margin-top: 20px; }
            th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
            th { background-color: #f2f2f2; }
            .header { margin-bottom: 20px; }
            .footer { margin-top: 30px; text-align: center; font-size: 0.8em; }
        </style>
    </head>
    <body>
        <div class='header'>
            <h1>$titulo - MEGAN TRANSPORTES C.A.</h1>
            <p><strong>Fecha del reporte:</strong> " . date('d/m/Y') . "</p>";

    // Mostrar filtros aplicados solo si son relevantes
    if (($tipo_reporte == 'inventario' || $tipo_reporte == 'salidas') && $categoria_id != 'todas') {
        // Obtener nombre de la categoría para mostrar en el filtro
        $sql_cat = "SELECT nombre FROM categorias WHERE id = ?";
        $stmt_cat = $conn->prepare($sql_cat);
        $stmt_cat->bind_param("i", $categoria_id);
        $stmt_cat->execute();
        $cat_result = $stmt_cat->get_result();
        $cat_row = $cat_result->fetch_assoc();
        $categoria_nombre = $cat_row['nombre'] ?? 'Desconocida';
        
        $html .= "<p><strong>Categoría:</strong> " . htmlspecialchars(ucfirst($categoria_nombre)) . "</p>";
    }
    
    if ($tipo_reporte == 'salidas' && $fecha_inicio && $fecha_fin) {
        $html .= "<p><strong>Rango de fechas:</strong> " . htmlspecialchars($fecha_inicio) . " al " . htmlspecialchars($fecha_fin) . "</p>";
    }

    $html .= "</div>
        <table>
            <thead>
                <tr>";

    // Cabeceras de tabla dinámicas
    if ($tipo_reporte == 'inventario') {
        $html .= "<th>Nombre</th><th>Categoría</th><th>Cantidad</th>";
    } elseif ($tipo_reporte == 'salidas') {
        $html .= "<th>Item</th><th>Categoría</th><th>Técnico</th><th>Cantidad</th><th>Fecha</th>";
    } else {
        $html .= "<th>Nombre</th><th>Email</th><th>Rol</th>";
    }

    $html .= "
                </tr>
            </thead>
            <tbody>";

    // Llenar tabla con datos
    while ($row = $result->fetch_assoc()) {
        $html .= "<tr>";
        if ($tipo_reporte == 'inventario') {
            $html .= "<td>" . htmlspecialchars($row['nombre']) . "</td>
                      <td>" . htmlspecialchars(ucfirst($row['categoria_nombre'] ?? 'Sin categoría')) . "</td>
                      <td>" . htmlspecialchars($row['cantidad']) . "</td>";
        } elseif ($tipo_reporte == 'salidas') {
            $html .= "<td>" . htmlspecialchars($row['item']) . "</td>
                      <td>" . htmlspecialchars(ucfirst($row['categoria_nombre'] ?? 'Sin categoría')) . "</td>
                      <td>" . htmlspecialchars($row['tecnico']) . "</td>
                      <td>" . htmlspecialchars($row['cantidad']) . "</td>
                      <td>" . htmlspecialchars($row['fecha_salida']) . "</td>";
        } else {
            $html .= "<td>" . htmlspecialchars($row['nombre']) . "</td>
                      <td>" . htmlspecialchars($row['email']) . "</td>
                      <td>" . htmlspecialchars(ucfirst($row['rol'])) . "</td>";
        }
        $html .= "</tr>";
    }

    $html .= "
            </tbody>
        </table>
        <div class='footer'>
            <p>Generado por: " . htmlspecialchars($_SESSION['usuario_nombre']) . "</p>
            <p>Sistema de Gestión de Inventario - MEGAN TRANSPORTES C.A.</p>
        </div>
    </body>
    </html>";

    // Configurar Dompdf
    $dompdf = new Dompdf();
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    
    // Renderizar PDF
    $dompdf->render();

    // Descargar PDF automáticamente
    $dompdf->stream("reporte_" . date('Ymd_His') . ".pdf", [
        "Attachment" => true
    ]);

} catch (Exception $e) {
    error_log("Error al generar PDF: " . $e->getMessage());
    die("Ocurrió un error al generar el reporte. Por favor, intente nuevamente.");
}
?>
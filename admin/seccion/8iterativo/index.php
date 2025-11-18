<?php
include("../../bd.php");
include("../../templates/header.php");

function obtenerPlatosPopulares($conn, $limite = 10) {
    $sql = "\n        SELECT \n            m.id, \n            m.nombre, \n            m.foto, \n            m.ingredientes, \n            m.precio, \n            COUNT(dp.id_producto) as veces_pedido\n        FROM `detalle_pedido` dp\n        JOIN `menu` m ON dp.id_producto = m.id\n        GROUP BY dp.id_producto\n        ORDER BY veces_pedido DESC\n        LIMIT :limite;\n    ";

    try {
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':limite', $limite, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error al obtener platos populares: " . $e->getMessage());
        return [];
    }
}

$platos_populares = obtenerPlatosPopulares($conn);

?>

<div class="container mt-5">
    <h1 class="text-center mb-4">Estadísticas de Platos Más Pedidos</h1>

    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Top 10 Platos Populares</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Plato</th>
                                    <th scope="col">Veces Pedido</th>
                                    <th scope="col">Precio Unitario</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($platos_populares)): ?>
                                    <tr>
                                        <td colspan="4" class="text-center">No hay datos de pedidos para mostrar.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($platos_populares as $index => $plato): ?>
                                        <tr>
                                            <th scope="row"><?= $index + 1 ?></th>
                                            <td>
                                                <img src="../../../images/<?= htmlspecialchars($plato['foto']) ?>" alt="<?= htmlspecialchars($plato['nombre']) ?>" class="me-2" style="width: 50px; height: 50px; object-fit: cover; border-radius: 8px;">
                                                <?= htmlspecialchars($plato['nombre']) ?>
                                            </td>
                                            <td>
                                                <span class="badge bg-success fs-6"><?= htmlspecialchars($plato['veces_pedido']) ?></span>
                                            </td>
                                            <td>$<?= number_format($plato['precio'], 2) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer text-muted">
                    Análisis de los platos más solicitados por los clientes.
                </div>
            </div>
        </div>
    </div>
</div>

<?php include("../../templates/footer.php"); ?>
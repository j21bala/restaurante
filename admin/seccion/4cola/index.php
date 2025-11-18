<?php include("../../templates/header.php"); ?>
<!doctype html>
<html lang="en">
    <head>
        <title>Pedidos - Gestión de Cola Estricta</title>
        <meta charset="utf-8" />
        <meta
            name="viewport"
            content="width=device-width, initial-scale=1, shrink-to-fit=no"
        />
        <link
            href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
            rel="stylesheet"
            integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN"
            crossorigin="anonymous"
        />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    </head>
    <body>
<?php
include("../../bd.php");

function cambiarEstado($pedido_id, $nuevo_estado, $conn) {
    $sql = "UPDATE pedidos SET estado = :nuevo_estado WHERE id = :pedido_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':nuevo_estado', $nuevo_estado);
    $stmt->bindParam(':pedido_id', $pedido_id);
    $stmt->execute();
}

// --- QUEUE OPERATION: DEQUEUE (Start Preparation) ---
// Mueve el pedido más antiguo de 'en_cola' a 'en_proceso'.
if (isset($_GET['accion']) && $_GET['accion'] == 'dequeue') {
    $sql = "SELECT id FROM pedidos WHERE estado = 'en_cola' ORDER BY fecha ASC LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $pedido_a_procesar = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($pedido_a_procesar) {
        cambiarEstado($pedido_a_procesar['id'], 'en_proceso', $conn);
    }
    header("Location: index.php");
    exit;
}

// --- QUEUE OPERATION: COMPLETE FIFO (Finish Preparation) ---
// Mueve el pedido más antiguo de 'en_proceso' a 'completado'.
if (isset($_GET['accion']) && $_GET['accion'] == 'complete_fifo') {
    $sql = "SELECT id FROM pedidos WHERE estado = 'en_proceso' ORDER BY fecha ASC LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $pedido_a_completar = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($pedido_a_completar) {
        cambiarEstado($pedido_a_completar['id'], 'completado', $conn);
    }
    header("Location: index.php");
    exit;
}

function getPedidosPorEstado($estado, $conn) {
    $sql = "SELECT * FROM pedidos WHERE estado = :estado ORDER BY fecha ASC";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':estado', $estado);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$pedidos_en_cola = getPedidosPorEstado('en_cola', $conn);
$pedidos_en_proceso = getPedidosPorEstado('en_proceso', $conn);
$pedidos_completados = getPedidosPorEstado('completado', $conn);

// Peek: Obtener el primer pedido de la cola sin modificarlo para resaltarlo
$primer_pedido_encola = !empty($pedidos_en_cola) ? $pedidos_en_cola[0] : null;
$primer_pedido_enproceso = !empty($pedidos_en_proceso) ? $pedidos_en_proceso[0] : null;
?>

<div class="container-fluid mt-4">
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="d-grid gap-2">
                <a href="?accion=dequeue" class="btn btn-primary btn-lg <?php echo empty($pedidos_en_cola) ? 'disabled' : ''; ?>">
                    <i class="fas fa-arrow-right"></i> PROCESAR SIGUIENTE EN COLA (DEQUEUE)
                </a>
            </div>
        </div>
        <div class="col-md-6">
            <div class="d-grid gap-2">
                <a href="?accion=complete_fifo" class="btn btn-success btn-lg <?php echo empty($pedidos_en_proceso) ? 'disabled' : ''; ?>">
                    <i class="fas fa-check-circle"></i> COMPLETAR SIGUIENTE EN PROCESO (FIFO)
                </a>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
                    <h3 class="text-center mb-0"><i class="fas fa-hourglass-start"></i> En Cola</h3>
                    <span class="badge bg-light text-dark"><?php echo count($pedidos_en_cola); ?></span>
                </div>
                <div class="card-body">
                    <?php if (empty($pedidos_en_cola)): ?>
                        <p class="text-center text-muted">La cola está vacía.</p>
                    <?php else: ?>
                        <?php foreach ($pedidos_en_cola as $pedido): ?>
                            <div class="card mb-3 <?php echo ($pedido['id'] == ($primer_pedido_encola['id'] ?? null)) ? 'border-primary border-2' : ''; ?>">
                                <div class="card-body">
                                    <p><strong>Cliente:</strong> <?php echo $pedido['nombre_cliente']; ?></p>
                                    <p><strong>Mesa:</strong> <?php echo $pedido['numero_mesa']; ?></p>
                                    <p><strong>Plato:</strong> <?php echo $pedido['plato']; ?></p>
                                    <?php if ($pedido['id'] == ($primer_pedido_encola['id'] ?? null)): ?>
                                        <div class="alert alert-info py-1 mt-2 text-center small fw-bold">Próximo a procesar (FIFO)</div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h3 class="text-center mb-0"><i class="fas fa-cogs"></i> En Proceso</h3>
                    <span class="badge bg-light text-dark"><?php echo count($pedidos_en_proceso); ?></span>
                </div>
                <div class="card-body">
                     <?php if (empty($pedidos_en_proceso)): ?>
                        <p class="text-center text-muted">No hay pedidos en proceso.</p>
                    <?php else: ?>
                        <?php foreach ($pedidos_en_proceso as $pedido): ?>
                            <div class="card mb-3 <?php echo ($pedido['id'] == ($primer_pedido_enproceso['id'] ?? null)) ? 'border-success border-2' : ''; ?>">
                                <div class="card-body">
                                    <p><strong>Cliente:</strong> <?php echo $pedido['nombre_cliente']; ?></p>
                                    <p><strong>Mesa:</strong> <?php echo $pedido['numero_mesa']; ?></p>
                                    <p><strong>Plato:</strong> <?php echo $pedido['plato']; ?></p>
                                    <?php if ($pedido['id'] == ($primer_pedido_enproceso['id'] ?? null)): ?>
                                        <div class="alert alert-success py-1 mt-2 text-center small fw-bold">Próximo a completar (FIFO)</div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                    <h3 class="text-center mb-0"><i class="fas fa-check-circle"></i> Completado</h3>
                    <span class="badge bg-light text-dark"><?php echo count($pedidos_completados); ?></span>
                </div>
                <div class="card-body">
                    <?php if (empty($pedidos_completados)): ?>
                        <p class="text-center text-muted">No hay pedidos completados.</p>
                    <?php else: ?>
                        <?php foreach ($pedidos_completados as $pedido): ?>
                            <div class="card mb-3">
                                <div class="card-body">
                                    <p><strong>Cliente:</strong> <?php echo $pedido['nombre_cliente']; ?></p>
                                    <p><strong>Mesa:</strong> <?php echo $pedido['numero_mesa']; ?></p>
                                    <p><strong>Plato:</strong> <?php echo $pedido['plato']; ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

        <script
            src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
            integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r"
            crossorigin="anonymous"
        ></script>
        <script
            src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"
            integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+"
            crossorigin="anonymous"
        ></script>
    </body>
</html>
<?php include("../../templates/footer.php"); ?>
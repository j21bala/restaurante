<?php 
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['presupuesto'])) {
    $presupuesto = floatval($_POST['presupuesto']);
    $sugerencia = obtenerSugerenciaCombo($lista_menu, $presupuesto);
} else {
    $presupuesto = null;
}
?>

<section id="sugerencia-voraz" class="py-5 bg-light">
    <div class="container">
        <h2 class="text-center mb-4">¿No sabes qué pedir? ¡Te ayudamos!</h2>
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Crea tu combo ideal</h5>
                        <p class="card-text">Dinos tu presupuesto y nuestro algoritmo voraz elegirá la mejor combinación de platos para ti, ¡maximizando el sabor sin pasarse de tu límite!</p>
                        
                        <form method="POST" action="index.php#sugerencia-voraz" class="mb-4">
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" class="form-control" name="presupuesto" placeholder="Ingresa tu presupuesto aquí" step="0.01" min="0" required value="<?= htmlspecialchars($presupuesto ?? ''); ?>">
                                <button class="btn btn-primary" type="submit">Sugerir Combo</button>
                            </div>
                        </form>

                        <?php if (isset($sugerencia)): ?>
                            <div id="resultado-sugerencia" class="mt-4 animate-fade-in">
                                <?php if (!empty($sugerencia['combo'])): ?>
                                    <h5 class="mb-3">🎉 ¡Aquí tienes tu combo sugerido!</h5>
                                    <div class="list-group">
                                        <?php foreach ($sugerencia['combo'] as $item): ?>
                                            <a href="#" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center" data-bs-toggle="modal" data-bs-target="#platoDetalleModal-<?= htmlspecialchars($item['id']); ?>">
                                                <div>
                                                    <h6 class="mb-1"><?= htmlspecialchars($item['nombre']); ?></h6>
                                                    <small class="text-muted"><?= htmlspecialchars($item['ingredientes']); ?></small>
                                                </div>
                                                <span class="badge bg-success rounded-pill">$<?= number_format($item['precio'], 2); ?></span>
                                            </a>
                                        <?php endforeach; ?>
                                    </div>
                                    <div class="alert alert-success mt-3 d-flex justify-content-between align-items-center">
                                        <strong>Costo Total del Combo:</strong>
                                        <strong class="fs-5">$<?= number_format($sugerencia['costo'], 2); ?></strong>
                                    </div>
                                    <p class="text-center mt-3 small text-muted">Haz clic en un plato para ver más detalles o añadirlo a tu orden.</p>
                                <?php else: ?>
                                    <div class="alert alert-warning">
                                        <h5 class="alert-heading">Lo sentimos...</h5>
                                        No hemos podido encontrar ningún combo con tu presupuesto de <strong>$<?= number_format($presupuesto, 2); ?></strong>. Intenta con un monto mayor.
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
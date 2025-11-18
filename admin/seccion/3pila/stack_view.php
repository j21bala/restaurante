<?php
// Recibe: $visitas_stack (VisitasRecientesStack)
//         $lista_menu (array de todos los platos)
?>
<section id="stack-vistas" class="container my-5">
    <div class="row justify-content-center">
        <div class="col-12 col-md-10 col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Platos Vistos Recientemente (Pila LIFO)</h5>
                    <span class="badge bg-primary rounded-pill">Total: <?= $visitas_stack->size() ?></span>
                </div>
                <div class="card-body p-4">
                    <?php if ($visitas_stack->isEmpty()): ?>
                        <div class="text-center text-muted">
                            <i class="bi bi-eye-slash fs-2"></i>
                            <p class="mt-2">Aún no has visto ningún plato. ¡Explora nuestro menú!</p>
                        </div>
                    <?php else: ?>
                        <p class="text-muted text-center mb-4">
                            El último plato que viste (la cima de la pila) es: <strong class="text-dark">"<?= htmlspecialchars(get_nombre_plato($lista_menu, $visitas_stack->peek())) ?>"</strong>. 
                        </p>
                        
                        <div class="d-grid gap-2 mb-4">
                            <a href="admin/seccion/3pila/pop_vista.php" class="btn btn-danger btn-lg <?= $visitas_stack->isEmpty() ? 'disabled' : '' ?>">
                                <i class="bi bi-trash"></i> Quitar Último Visto (POP)
                            </a>
                        </div>
                        
                        <ul class="list-group list-group-flush border-top pt-3">
                            <?php 
                            $stack_data = $visitas_stack->getStack();
                            foreach ($stack_data as $index => $plato_id): 
                                $nombre_plato = get_nombre_plato($lista_menu, $plato_id);
                            ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center animate-fade-in">
                                    <span class="fw-bold">
                                        <?= htmlspecialchars($nombre_plato) ?>
                                    </span>
                                    <?php if ($index === 0): // Marca visualmente la cima de la pila ?>
                                        <span class="badge bg-warning text-dark ms-2">Cima de la Pila</span>
                                    <?php endif; ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
function get_nombre_plato($menu, $id) {
    foreach ($menu as $plato) {
        if ($plato['id'] == $id) {
            return $plato['nombre'];
        }
    }
    return 'Plato Desconocido';
}
?>
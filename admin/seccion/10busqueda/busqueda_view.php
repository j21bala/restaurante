<?php
// Esta vista es incluida dentro de index.php, por lo que tiene acceso a las variables definidas allí.

// Obtenemos el término de búsqueda de la URL (si existe).
$termino_buscado = isset($_GET['q']) ? trim($_GET['q']) : '';

// Realizamos la búsqueda solo si el usuario ha escrito algo.
$platos_encontrados = [];
if (!empty($termino_buscado)) {
    // $lista_menu es la variable que ya contiene todos los platos en index.php
    $platos_encontrados = busqueda_lineal_platos($lista_menu, $termino_buscado);
}
?>

<section id="busqueda-menu" class="py-5">
    <div class="container">
        <h2 class="text-center mb-4">Busca en Nuestro Menú</h2>
        
        <!-- Formulario de Búsqueda -->
        <form action="index.php#busqueda-menu" method="GET" class="mb-5">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="input-group">
                        <input type="text" name="q" class="form-control" placeholder="Ej: Sushi, pollo, arroz..." value="<?= htmlspecialchars($termino_buscado) ?>" aria-label="Término de búsqueda">
                        <button class="btn btn-primary" type="submit">Buscar</button>
                    </div>
                </div>
            </div>
        </form>

        <!-- Resultados de la Búsqueda -->
        <?php if (!empty($termino_buscado)): ?>
            <div id="resultados-busqueda">
                <h4 class="mb-4">Resultados para "<?= htmlspecialchars($termino_buscado) ?>":</h4>
                
                <?php if (empty($platos_encontrados)): ?>
                    <div class="alert alert-warning text-center" role="alert">
                        No se encontraron platos que coincidan con tu búsqueda.
                    </div>
                <?php else: ?>
                    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4">
                        <?php foreach($platos_encontrados as $plato): ?>
                            <div class="col">
                                <div class="card h-100 shadow-sm">
                                    <img src="images/<?= htmlspecialchars($plato['foto']); ?>" class="card-img-top" alt="<?= htmlspecialchars($plato['nombre']); ?>" style="height: 200px; object-fit: cover;">
                                    <div class="card-body">
                                        <h5 class="card-title"><?= htmlspecialchars($plato['nombre']); ?></h5>
                                        <p class="card-text small"><?= htmlspecialchars($plato['ingredientes']); ?></p>
                                        <p class="card-text"><strong>Precio: $<?= number_format($plato['precio'], 2); ?></strong></p>
                                    </div>
                                    <div class="card-footer">
                                        <button type="button" class="btn btn-primary btn-sm w-100" data-bs-toggle="modal" data-bs-target="#platoDetalleModal-<?= htmlspecialchars($plato['id']); ?>">Ver y Comprar</button>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php
// Esta vista se incluye en index.php, por lo que tiene acceso a sus variables.

// Obtenemos el criterio de ordenamiento actual de la URL, si existe.
$orden_actual = isset($_GET['ordenar']) ? $_GET['ordenar'] : 'default';

?>

<div class="container mb-4">
    <div class="row justify-content-end">
        <div class="col-md-4">
            <form action="index.php#recomendados" method="GET" id="form-ordenar">
                <div class="input-group">
                    <label class="input-group-text" for="ordenar-select">Ordenar por:</label>
                    <select class="form-select" name="ordenar" id="ordenar-select" onchange="this.form.submit()">
                        <option value="default" <?= ($orden_actual == 'default') ? 'selected' : '' ?>>Por defecto</option>
                        <option value="precio_asc" <?= ($orden_actual == 'precio_asc') ? 'selected' : '' ?>>Precio (Menor a Mayor)</option>
                        <option value="precio_desc" <?= ($orden_actual == 'precio_desc') ? 'selected' : '' ?>>Precio (Mayor a Menor)</option>
                        <option value="nombre_asc" <?= ($orden_actual == 'nombre_asc') ? 'selected' : '' ?>>Nombre (A-Z)</option>
                        <option value="nombre_desc" <?= ($orden_actual == 'nombre_desc') ? 'selected' : '' ?>>Nombre (Z-A)</option>
                    </select>
                </div>
                <?php 
                // Si hay una búsqueda activa, la mantenemos en el formulario para no perderla al ordenar.
                if (isset($_GET['q'])) {
                    echo '<input type="hidden" name="q" value="' . htmlspecialchars($_GET['q']) . '" />';
                }
                ?>
            </form>
        </div>
    </div>
</div>

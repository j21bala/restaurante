<?php
function obtenerSugerenciaCombo($menu, $presupuesto) {
    // Ordenar el menú por precio en orden descendente (estrategia voraz)
    usort($menu, function($a, $b) {
        return $b['precio'] <=> $a['precio'];
    });

    $combo = [];
    $costoTotal = 0;

    foreach ($menu as $item) {
        if (($costoTotal + $item['precio']) <= $presupuesto) {
            // Evitar duplicados en el combo
            if (!in_array($item, $combo)) {
                $combo[] = $item;
                $costoTotal += $item['precio'];
            }
        }
    }

    return ['combo' => $combo, 'costo' => $costoTotal];
}
?>
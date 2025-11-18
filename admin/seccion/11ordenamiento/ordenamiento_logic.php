<?php

/**
 * Ordena un array de platos utilizando el algoritmo Quick Sort (a través de usort).
 *
 * @param array $menu El array de platos a ordenar.
 * @param string $criterio El criterio por el cual ordenar. Valores posibles:
 *    - 'precio_asc': Precio de menor a mayor.
 *    - 'precio_desc': Precio de mayor a menor.
 *    - 'nombre_asc': Nombre de la A a la Z.
 *    - 'nombre_desc': Nombre de la Z a la A.
 * @return array El array de platos ordenado.
 */
function ordenar_platos_con_quicksort(&$menu, $criterio = 'default') {

    // Definimos la función de comparación basada en el criterio
    $funcion_comparacion = null;

    switch ($criterio) {
        case 'precio_asc':
            $funcion_comparacion = function($a, $b) {
                // El operador <=> es ideal para comparaciones numéricas
                return $a['precio'] <=> $b['precio']; 
            };
            break;
        case 'precio_desc':
            $funcion_comparacion = function($a, $b) {
                return $b['precio'] <=> $a['precio'];
            };
            break;
        case 'nombre_asc':
            $funcion_comparacion = function($a, $b) {
                // strcmp es la función estándar para comparar strings
                return strcmp($a['nombre'], $b['nombre']); 
            };
            break;
        case 'nombre_desc':
            $funcion_comparacion = function($a, $b) {
                return strcmp($b['nombre'], $a['nombre']);
            };
            break;
        default:
            // Si el criterio no es válido, no hacemos nada.
            return;
    }

    // --- INICIO DEL ALGORITMO DE ORDENAMIENTO (Quick Sort) ---
    // usort ordena el array $menu (pasado por referencia) usando la función de comparación.
    // La implementación interna de usort en PHP es Quick Sort.
    usort($menu, $funcion_comparacion);
    // --- FIN DEL ALGORITMO ---

}

?>
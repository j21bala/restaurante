<?php

/**
 * Implementa un algoritmo de BÚSQUEDA LINEAL para encontrar platos en el menú.
 *
 * @param array $menu_completo El array completo de platos disponibles.
 * @param string $termino_busqueda El texto que el usuario ha introducido para buscar.
 * @return array Un array con los platos que coinciden con la búsqueda.
 */
function busqueda_lineal_platos($menu_completo, $termino_busqueda) {
    
    // Si no hay término de búsqueda, devolvemos un array vacío para no mostrar nada.
    if (empty(trim($termino_busqueda))) {
        return [];
    }

    $resultados = [];
    $termino_busqueda_lower = strtolower($termino_busqueda); // Convertimos a minúsculas para una búsqueda insensible a mayúsculas/minúsculas.

    // --- INICIO DEL ALGORITMO DE BÚSQUEDA LINEAL ---
    foreach ($menu_completo as $plato) {
        $nombre_lower = strtolower($plato['nombre']);
        $ingredientes_lower = strtolower($plato['ingredientes']);

        // Comparamos el término de búsqueda con el nombre y los ingredientes.
        if (strpos($nombre_lower, $termino_busqueda_lower) !== false || strpos($ingredientes_lower, $termino_busqueda_lower) !== false) {
            // Si hay coincidencia, añadimos el plato a nuestra lista de resultados.
            $resultados[] = $plato;
        }
    }
    // --- FIN DEL ALGORITMO ---

    return $resultados;
}

?>
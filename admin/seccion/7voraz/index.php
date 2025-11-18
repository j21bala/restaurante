<?php
include("../../bd.php");
include("sugerencia_logic.php");
include("../../templates/header.php");

// Obtener la lista de menús para la lógica de sugerencia
$lista_menu_query = $conn->query("SELECT * FROM `menu`");
$lista_menu = $lista_menu_query->fetchAll(PDO::FETCH_ASSOC);

// Incluir la vista que contiene el formulario y la presentación de la sugerencia
include("sugerencia_view.php");

include("../../templates/footer.php");
?>
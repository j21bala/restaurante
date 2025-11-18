<?php
session_start();
include("../../bd.php"); // Path from admin/seccion/arreglos/ to admin/bd.php

// 1. Verifica si se ha seleccionado una mesa
if (!isset($_SESSION['mesa_seleccionada']) || empty($_SESSION['mesa_seleccionada'])) {
    $_SESSION['error'] = "Por favor, selecciona una mesa antes de realizar un pedido.";
    header("Location: ../../../index.php#reserva"); // Corrected Path to main page reservation section
    exit;
}

// 2. Verifica si el carrito está vacío
if (!isset($_SESSION['carrito']) || empty($_SESSION['carrito'])) {
    $_SESSION['error'] = "Tu carrito está vacío.";
    header("Location: ../../../index.php"); // Corrected Path to main page
    exit;
}

// 3. Prepara los datos para insertar el pedido
$mesa = $_SESSION['mesa_seleccionada'];
$carrito = $_SESSION['carrito'];
$fecha_actual = date('Y-m-d H:i:s');
$nombre_cliente = "Cliente Mesa " . $mesa;

try {
    $sql = "INSERT INTO pedidos (nombre_cliente, numero_mesa, plato, estado, fecha, total) VALUES (:nombre_cliente, :numero_mesa, :plato, :estado, :fecha, :total)";
    $stmt = $conn->prepare($sql);

    // 4. Recorre los artículos del carrito e inserta cada uno como un pedido separado
    foreach ($carrito as $item) {
        $nombre_plato = $item['nombre'] . ' (x' . $item['cantidad'] . ')';
        $total = $item['precio'] * $item['cantidad'];
        $estado = 'en_cola';
        
        $stmt->bindParam(':nombre_cliente', $nombre_cliente);
        $stmt->bindParam(':numero_mesa', $mesa);
        $stmt->bindParam(':plato', $nombre_plato);
        $stmt->bindParam(':estado', $estado);
        $stmt->bindParam(':fecha', $fecha_actual);
        $stmt->bindParam(':total', $total);
        
        $stmt->execute();
    }

    // 5. Limpia el carrito y la mesa de la sesión
    unset($_SESSION['carrito']);
    unset($_SESSION['mesa_seleccionada']);

    // 6. Redirige al usuario a la página principal con un mensaje de éxito
    $_SESSION['pedido_exitoso'] = "¡Tu pedido ha sido enviado a la cocina!";
    header("Location: ../../../index.php"); // Corrected Path to main page
    exit;

} catch (PDOException $e) {
    $_SESSION['error'] = "Hubo un error al procesar tu pedido. Por favor, intenta de nuevo.";
    error_log("Error al crear pedido: " . $e->getMessage());
    header("Location: ../../../index.php"); // Corrected Path to main page
    exit;
}
?>
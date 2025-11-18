<?php

// 1. FUNCIÓN PARA GUARDAR UNA RESPUESTA A UN TESTIMONIO
function guardar_respuesta_testimonio($conn) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['enviar_respuesta'])) {
        $nombre = filter_input(INPUT_POST, 'nombre', FILTER_SANITIZE_STRING);
        $opinion = filter_input(INPUT_POST, 'opinion', FILTER_SANITIZE_STRING);
        $parent_id = filter_input(INPUT_POST, 'parent_id', FILTER_VALIDATE_INT);

        // Validaciones básicas
        if (empty($nombre) || empty($opinion) || $parent_id === false || $parent_id <= 0) {
            // Si algo falla, simplemente no hacemos nada o podríamos registrar un error.
            return;
        }

        try {
            $sql = "INSERT INTO `testimonios` (nombre, opinion, parent_id) VALUES (:nombre, :opinion, :parent_id)";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':nombre', $nombre);
            $stmt->bindParam(':opinion', $opinion);
            $stmt->bindParam(':parent_id', $parent_id);
            $stmt->execute();
            
            // Redireccionar para limpiar el POST y evitar reenvíos
            header("Location: " . strtok($_SERVER["REQUEST_URI"], '?') . "#testimonio-" . $parent_id);
            exit;

        } catch (PDOException $e) {
            error_log("Error al guardar la respuesta: " . $e->getMessage());
        }
    }
}

// 2. FUNCIÓN PARA OBTENER Y ESTRUCTURAR LOS TESTIMONIOS Y SUS RESPUESTAS
function obtener_testimonios_anidados($conn) {
    $sql = "SELECT * FROM `testimonios` ORDER BY fecha ASC";
    $stmt = $conn->query($sql);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $items_por_id = [];
    foreach ($items as $item) {
        $items_por_id[$item['id']] = $item; // Corregido: La PK es 'id'
        $items_por_id[$item['id']]['respuestas'] = [];
    }

    $arbol_testimonios = [];
    foreach ($items_por_id as $id => &$item) {
        if ($item['parent_id'] != 0) {
            if (isset($items_por_id[$item['parent_id']])) {
                $items_por_id[$item['parent_id']]['respuestas'][] = &$item;
            }
        } else {
            $arbol_testimonios[] = &$item;
        }
    }
    
    return $arbol_testimonios;
}

// 3. FUNCIÓN RECURSIVA PARA RENDERIZAR EL HILO DE TESTIMONIOS
function renderizar_hilo_testimonios($testimonios, $nivel = 0) {
    if (empty($testimonios)) {
        return;
    }

    $clase_margen = ($nivel > 0) ? 'ms-4 mt-3' : 'col-md-6 d-flex';

    foreach ($testimonios as $testimonio) {
        $id = htmlspecialchars($testimonio['id']); // Corregido: La PK es 'id'
        $nombre = htmlspecialchars($testimonio['nombre']);
        $opinion = nl2br(htmlspecialchars($testimonio['opinion']));
        $fecha = date("d/m/Y H:i", strtotime($testimonio['fecha']));
        
        // Define la clase de la tarjeta para las respuestas
        $card_class = ($nivel > 0) ? 'bg-light' : '';

        echo <<<HTML
        <div class="{$clase_margen}" id="testimonio-{$id}">
            <div class="card mb-4 w-100 shadow-sm {$card_class}">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <h6 class="card-title">{$nombre}</h6>
                        <small class="text-muted">{$fecha}</small>
                    </div>
                    <p class="card-text">{$opinion}</p>
                </div>
                <div class="card-footer bg-white border-top-0">
                    <button class="btn btn-sm btn-link p-0" type="button" data-bs-toggle="collapse" data-bs-target="#responder-testimonio-{$id}" aria-expanded="false">
                        <i class="bi bi-reply"></i> Responder
                    </button>

                    <div class="collapse mt-3" id="responder-testimonio-{$id}">
                        <form action="#testimonio-{$id}" method="POST">
                            <input type="hidden" name="parent_id" value="{$id}">
                            <div class="mb-2">
                                <input type="text" class="form-control form-control-sm" name="nombre" placeholder="Tu nombre" required>
                            </div>
                            <div class="mb-2">
                                <textarea class="form-control form-control-sm" name="opinion" rows="2" placeholder="Escribe una respuesta..." required></textarea>
                            </div>
                            <button type="submit" name="enviar_respuesta" class="btn btn-primary btn-sm">Enviar Respuesta</button>
                        </form>
                    </div>
                </div>
                 <!-- Área para mostrar las respuestas -->
                 <div class="card-body pt-0">
HTML;
        
        // ¡RECURSIÓN! Llamada a sí misma para renderizar las respuestas
        if (!empty($testimonio['respuestas'])) {
            renderizar_hilo_testimonios($testimonio['respuestas'], $nivel + 1);
        }

        echo <<<HTML
                </div>
            </div>
        </div>
HTML;
    }
}
?>
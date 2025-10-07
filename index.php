<?php 
session_start(); 

include("admin/bd.php");

if (isset($_GET['mesa'])) {
    $mesa_url = intval($_GET['mesa']);
    if ($mesa_url >= 1 && $mesa_url <= 16) {
        $_SESSION['mesa_seleccionada'] = $mesa_url;
    }
    header("Location: " . strtok($_SERVER["REQUEST_URI"], '?'));
    exit;
}

// Cancelar selección
if (isset($_GET['mesa_seleccionada']) && $_GET['mesa_seleccionada'] === '') {
    unset($_SESSION['mesa_seleccionada']);
    header("Location: " . strtok($_SERVER["REQUEST_URI"], '?'));
    exit;
}

// ===== CONSULTAR MESAS RESERVADAS HOY =====
$fecha_hoy = date("Y-m-d");
$mesas_reservadas_hoy = [];

try {
    $horarios_totales = 9; 
    
    $sql = "SELECT mesa, COUNT(*) as total_reservas 
            FROM reservas 
            WHERE fecha = :fecha 
            GROUP BY mesa 
            HAVING total_reservas >= :total_horarios";
    
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(":fecha", $fecha_hoy);
    $stmt->bindParam(":total_horarios", $horarios_totales, PDO::PARAM_INT);
    $stmt->execute();
    
    $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($resultado as $fila) {
        $mesas_reservadas_hoy[$fila['mesa']] = true;
    }
    
} catch (PDOException $e) {
    error_log("Error al consultar reservas: " . $e->getMessage());
    $mesas_reservadas_hoy = [];
}

// Mensajes de error
$mensaje_error = '';
if (isset($_SESSION['error'])) {
    $mensaje_error = $_SESSION['error'];
    unset($_SESSION['error']);
}

// ===== CARRITO =====
$_SESSION['carrito'] = $_SESSION['carrito'] ?? []; 
$carrito = $_SESSION['carrito']; 

// ===== CONSULTAS PARA EL CONTENIDO =====
$sentencia = $conn->prepare("SELECT * FROM `banner` LIMIT 1");
$sentencia->execute();
$listabanner = $sentencia->fetchAll(PDO::FETCH_ASSOC);

$sentencia_colaboradores = $conn->prepare("SELECT * FROM `colaboradores`");
$sentencia_colaboradores->execute();
$lista_colaboradores = $sentencia_colaboradores->fetchAll(PDO::FETCH_ASSOC);

$sentencia_testimonios = $conn->prepare("SELECT * FROM `testimonios`");
$sentencia_testimonios->execute();
$lista_testimonios = $sentencia_testimonios->fetchAll(PDO::FETCH_ASSOC);

$sentencia_menu = $conn->prepare("SELECT * FROM `menu`");
$sentencia_menu->execute();
$lista_menu = $sentencia_menu->fetchAll(PDO::FETCH_ASSOC);
?>

<!doctype html>
<html lang="es">
    <head>
        <title>Sendai Restaurant - Inicio</title>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />

        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.0/font/bootstrap-icons.min.css">
        <style>
            .social-icons a {
                color: #6c757d;
                font-size: 1.2rem;
                margin: 0 8px;
                transition: color 0.3s ease;
            }
            .social-icons a:hover {
                color: #007bff;
            }
            
            
            @keyframes fadeIn {
                from {
                    opacity: 0;
                    transform: translateY(20px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }
            
            .animate-fade-in {
                animation: fadeIn 0.4s ease-out;
            }
            
            
            .btn-success:not(.disabled):hover {
                box-shadow: 0 6px 20px rgba(25, 135, 84, 0.4);
                transform: scale(1.05) !important;
            }
            
            .btn-warning:hover {
                box-shadow: 0 6px 20px rgba(255, 193, 7, 0.4);
                transform: scale(1.05) !important;
            }
            
            
            .btn-danger.disabled {
                opacity: 0.6;
            }
        </style>
    </head>

    <body>
        <!-- NAVBAR -->
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <div class="container">
                <a class="navbar-brand" href="#">Sendai Restaurant</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link active" href="#banner">Inicio</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#recomendados">Menú del día</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#chef">Chef</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#testimonios">Testimonios</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#reserva">Reservas</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#contacto">Contacto</a>
                        </li>
                    </ul>
                    
                    <ul class="navbar-nav ms-auto align-items-center">
                        <li class="nav-item me-3">
                            <?php $num_items = array_sum(array_column($carrito, 'cantidad')); ?>
                            <button class="btn btn-outline-light position-relative" type="button" 
                                    data-bs-toggle="offcanvas" 
                                    data-bs-target="#offcanvasCarrito" 
                                    aria-controls="offcanvasCarrito">
                                <i class="bi bi-cart"></i> Mi Orden
                                <?php if ($num_items > 0): ?>
                                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                        <?php echo $num_items; ?>
                                    </span>
                                <?php endif; ?>
                            </button>
                        </li>
                        
                        <li class="nav-item">
                            <a class="btn btn-primary" href="admin/login.php">
                                <i class="bi bi-person-fill"></i> Iniciar Sesión
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <!-- BANNER -->
        <section id="banner" class="container-fluid p-0">
            <?php foreach($listabanner as $banner){ ?>
                <div class="banner-img" style="position:relative; height:400px; overflow: hidden;">
                    <img src="images/<?php echo htmlspecialchars($banner['foto']); ?>" 
                         class="card-img-top" 
                         alt="Banner" 
                         style="height: 400px; object-fit: cover;">

                    <div class="banner-text" style="
                        position:absolute; 
                        top:50%; 
                        left:50%; 
                        transform:translate(-50%, -50%); 
                        text-align:center;
                        color: white; 
                        z-index: 10;
                        width: 100%;
                    ">
                        <h1><?php echo htmlspecialchars($banner['titulo']);?></h1>
                        <p><?php echo htmlspecialchars($banner['descripcion']);?></p>
                        <a href="#reserva" class="btn btn-primary" style="margin-top: 20px;">
                         Reserva tu mesa
                        </a>
                    </div>
                </div>
            <?php break; } ?>
        </section>

        <!-- CHEFS -->
        <section id="chef" class="bg-light py-5">
            <div class="container">
                <h2 class="text-center mb-4">Nuestros Chefs</h2>
                <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4">
                    <?php foreach($lista_colaboradores as $colaborador){ ?>
                    <div class="col">
                        <div class="card h-100">
                            <img src="images/<?php echo htmlspecialchars($colaborador['foto']); ?>" 
                                 class="card-img-top" 
                                 alt="<?php echo htmlspecialchars($colaborador['nombre']); ?>" 
                                 style="height: 400px; object-fit: cover;">
                            <div class="card-body text-center">
                                <h5 class="card-title"><?php echo htmlspecialchars($colaborador['nombre']); ?></h5>
                                <p class="card-text small"><?php echo htmlspecialchars($colaborador['descripcion']); ?></p>
                                <div class="social-icons mt-3">
                                    <a href="<?php echo htmlspecialchars($colaborador['linkfacebook']); ?>" title="Facebook">
                                        <i class="bi bi-facebook"></i>
                                    </a>
                                    <a href="<?php echo htmlspecialchars($colaborador['linkinstagram']); ?>" title="Instagram">
                                        <i class="bi bi-instagram"></i>
                                    </a>
                                    <a href="<?php echo htmlspecialchars($colaborador['linkyoutube']); ?>" title="YouTube">
                                        <i class="bi bi-youtube"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php } ?>
                </div>
            </div>
        </section>

        <!-- TESTIMONIOS -->
        <section id="testimonios" class="bg-light py-5">
            <div class="container"> 
                <h2 class="text-center mb-4">Testimonios</h2>
                <div class="row">
                    <?php foreach($lista_testimonios as $testimonio){ ?>
                    <div class="col-md-6 d-flex">
                        <div class="card mb-4 w-100">
                            <div class="card-body">
                                <p class="card-text"><?php echo htmlspecialchars($testimonio['opinion']); ?></p>
                            </div>
                            <div class="card-footer text-muted">
                                <?php echo htmlspecialchars($testimonio['nombre']); ?>
                            </div>
                        </div>
                    </div>
                    <?php } ?>
                </div>
            </div> 
        </section>

        <!-- MENÚ RECOMENDADOS -->
        <section id="recomendados" class="py-5"> 
            <div class="container">
                <h2 class="text-center mb-4">Recomendados</h2>
                <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4">
                    <?php foreach($lista_menu as $plato){ ?>
                    <div class="col">
                        <div class="card h-100 shadow-sm">
                            <img src="images/<?php echo htmlspecialchars($plato['foto']); ?>" 
                                 class="card-img-top" 
                                 alt="<?php echo htmlspecialchars($plato['nombre']); ?>" 
                                 style="height: 200px; object-fit: cover; cursor: pointer;"
                                 data-bs-toggle="modal" 
                                 data-bs-target="#platoDetalleModal-<?php echo htmlspecialchars($plato['id']); ?>">
                            
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($plato['nombre']); ?></h5>
                                <p class="card-text small"><?php echo htmlspecialchars($plato['ingredientes']); ?></p>
                                <p class="card-text"><strong>Precio: $<?php echo number_format($plato['precio'], 2); ?></strong></p>
                            </div>
                            
                            <div class="card-footer">
                                <button type="button" class="btn btn-primary btn-sm w-100" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#platoDetalleModal-<?php echo htmlspecialchars($plato['id']); ?>">
                                    Ver y Comprar
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <?php include 'admin/seccion/producto/detalleproducto.php'; ?>
                    <?php } ?>
                </div>
            </div> 
        </section>

        <!-- SECCIÓN DE RESERVAS -->
        <?php if ($mensaje_error): ?>
        <div class="container mt-4">
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <strong>⚠️</strong> <?= htmlspecialchars($mensaje_error) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
        <?php endif; ?>

        <section id="reserva" class="container my-5">
            <h2 class="text-center mb-4">Selecciona tu Mesa </h2>
            <p class="text-center text-muted mb-5">
                Haz clic en una mesa <strong>Libre</strong> para seleccionarla y proceder con la reserva.
            </p>

            <div class="row justify-content-center">
                <div class="col-12 col-md-8 col-lg-6">
                    <div class="card p-3 shadow-lg border-0 bg-light">
                        <div class="card-body">
                            <div class="d-grid mx-auto" style="grid-template-columns: repeat(4, 1fr); gap: 10px;">
                                <?php for ($i = 0; $i < 4; $i++): ?>
                                    <?php for ($j = 0; $j < 4; $j++): 
                                        $numMesa = ($i * 4) + $j + 1;
                                        $seleccionada = (isset($_SESSION['mesa_seleccionada']) && $_SESSION['mesa_seleccionada'] == $numMesa);
                                        
                                        $estadoMesa = 'Libre';
                                        $claseBoton = 'success'; 
                                        $link = '?mesa=' . $numMesa; 
                                        $disabled = '';
                                        $cursorStyle = 'cursor: pointer;';
                                        
                                        if (isset($mesas_reservadas_hoy[$numMesa])) { 
                                            $estadoMesa = 'Ocupada';
                                            $claseBoton = 'danger';
                                            $link = 'javascript:void(0);'; 
                                            $disabled = 'disabled';
                                            $cursorStyle = 'cursor: not-allowed;';
                                        } elseif ($seleccionada) {
                                            $estadoMesa = 'Seleccionada';
                                            $claseBoton = 'warning';
                                            $link = '#reserva'; 
                                        }
                                    ?>
                                        <a href="<?= $link ?>" 
                                            class="btn btn-<?= $claseBoton ?> p-3 fs-5 fw-bold shadow-sm d-flex flex-column align-items-center justify-content-center text-decoration-none <?= $disabled ?>"
                                            style="height: 90px; border-radius: 0.5rem; transition: all 0.3s ease; <?= $cursorStyle ?>"
                                            <?= $disabled ? 'aria-disabled="true" tabindex="-1"' : '' ?>
                                            <?= !$disabled ? 'onmouseover="this.style.transform=\'scale(1.05)\'" onmouseout="this.style.transform=\'scale(1)\'"' : '' ?>>
                                            
                                            <span class="d-block">Mesa #<?= $numMesa ?></span>
                                            <small class="mt-1 text-uppercase fw-normal" style="font-size: 0.75rem;">
                                                <?= $estadoMesa ?>
                                            </small>
                                        </a>
                                    <?php endfor; ?>
                                <?php endfor; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Leyenda -->
                    <div class="d-flex justify-content-center gap-4 mt-4 text-muted">
                        <span class="d-flex align-items-center gap-1">
                            <span class="badge bg-success">🟢</span> Libre
                        </span>
                        <span class="d-flex align-items-center gap-1">
                            <span class="badge bg-warning">🟡</span> Seleccionada
                        </span>
                        <span class="d-flex align-items-center gap-1">
                            <span class="badge bg-danger">🔴</span> Ocupada
                        </span>
                    </div>
                </div>
            </div>

            <!-- Panel de confirmación -->
            <?php if (isset($_SESSION['mesa_seleccionada']) && !isset($mesas_reservadas_hoy[$_SESSION['mesa_seleccionada']])): ?>
                <div class="text-center mt-5 p-4 rounded-3 bg-white border shadow mx-auto animate-fade-in" 
                     style="max-width: 450px;">
                    
                    <div class="mb-3">
                        <p class="lead mb-2 text-secondary">Mesa seleccionada:</p>
                        <h3 class="display-4 text-primary fw-bold mb-0">
                            #<?= htmlspecialchars($_SESSION['mesa_seleccionada']) ?>
                        </h3>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <a href="admin/confirmar.php?mesa=<?= htmlspecialchars($_SESSION['mesa_seleccionada']) ?>" 
                            class="btn btn-primary btn-lg">
                            <i class="bi bi-check-circle"></i> Confirmar Reserva
                        </a>
                        <a href="?mesa_seleccionada=" class="btn btn-outline-secondary">
                            <i class="bi bi-x-circle"></i> Cancelar Selección
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </section>

        <!-- CONTACTO -->
        <section id="contacto" class="container mt-4 mb-5">
            <h2>Contacto</h2>
            <p>Para cualquier consulta o pedido, no dudes en contactarnos.</p>
            <form action="admin/enviar_mensaje.php" method="post">
                <div class="form-group">
                    <label for="nombre">Nombre:</label>
                    <input type="text" class="form-control" name="nombre" id="nombre" placeholder="Ingrese su nombre" required><br>
                    
                    <label for="email">Email:</label>
                    <input type="email" class="form-control" name="email" id="email" placeholder="Ingrese su correo electrónico" required><br>
                    
                    <label for="telefono">Teléfono:</label>
                    <input type="text" class="form-control" name="telefono" id="telefono" placeholder="Ingrese su número de teléfono"><br>
                    
                    <div class="mb-3">
                        <label for="mensaje" class="form-label">Mensaje</label>
                        <textarea class="form-control" name="mensaje" id="mensaje" rows="6" placeholder="Escriba su mensaje" required></textarea>
                    </div>
                    
                    <input type="submit" class="btn btn-primary" value="Enviar mensaje" name="enviar" id="enviar">
                </div>
            </form>
        </section>

        <!-- FOOTER -->
        <footer class="bg-dark text-light text-center py-3">
            <p class="mb-0">&copy; 2025 Sendai Restaurant. Todos los derechos reservados.</p>
        </footer>
        
        <!-- OFFCANVAS CARRITO -->
        <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasCarrito" aria-labelledby="offcanvasCarritoLabel">
            <div class="offcanvas-header bg-dark text-light">
              <h5 class="offcanvas-title" id="offcanvasCarritoLabel">🛒 Mi Orden</h5>
              <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body">
                <?php if (empty($carrito)): ?>
                    <p class="text-center text-muted">Tu carrito está vacío. ¡Añade algo del menú!</p>
                <?php else: ?>
                    <ul class="list-group list-group-flush mb-4">
                        <?php 
                        $total_general = 0;
                        foreach ($carrito as $key => $item): 
                            $subtotal = $item['precio'] * $item['cantidad'];
                            $total_general += $subtotal;
                        ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                            <div class="me-2">
                                <h6 class="mb-0"><?php echo htmlspecialchars($item['nombre']); ?></h6>
                                <small class="text-muted">$<?php echo number_format($item['precio'], 2); ?> c/u</small>
                            </div>
                            
                            <div class="text-end d-flex align-items-center">
                                <div class="input-group input-group-sm me-3" style="width: 120px;">
                                    <button class="btn btn-outline-secondary btn-update-qty" 
                                            type="button" 
                                            data-key="<?php echo $key; ?>" 
                                            data-action="remove_one"
                                            <?php echo ($item['cantidad'] <= 1) ? 'disabled' : ''; ?>>
                                        <i class="bi bi-dash"></i>
                                    </button>
                                    
                                    <input type="text" 
                                            class="form-control text-center bg-white" 
                                            value="<?php echo $item['cantidad']; ?>" 
                                            readonly>
                                            
                                    <button class="btn btn-outline-secondary btn-update-qty" 
                                            type="button" 
                                            data-key="<?php echo $key; ?>" 
                                            data-action="add_one">
                                        <i class="bi bi-plus"></i>
                                    </button>
                                </div>

                                <strong class="text-success me-3">$<?php echo number_format($subtotal, 2); ?></strong>
                                
                                <button class="btn btn-outline-danger btn-sm p-1 btn-delete-item" 
                                        type="button"
                                        data-key="<?php echo $key; ?>">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                    
                    <div class="d-flex justify-content-between fw-bold mb-3 border-top pt-3">
                        <span>Total a Pagar:</span>
                        <span class="text-danger">$<?php echo number_format($total_general, 2); ?></span>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="offcanvas-footer p-3 border-top">
                <a href="pago.php" class="btn btn-success w-100 btn-lg <?php echo empty($carrito) ? 'disabled' : ''; ?>">
                    Proceder al Pago
                </a>
            </div>
        </div>
        
        <!-- FORM OCULTO PARA CARRITO -->
        <form id="carrito-form" action="admin/seccion/producto/procesar_carrito.php" method="POST" style="display:none;">
            <input type="hidden" name="plato_key" id="form-plato-key" value="">
            <input type="hidden" name="action" id="form-action" value="">
        </form>

        <!-- SCRIPTS -->
        <?php
        if (isset($_SESSION['carrito_abierto']) && $_SESSION['carrito_abierto'] === true) {
            echo '<script>
                document.addEventListener("DOMContentLoaded", function() {
                    var offcanvasElement = document.getElementById("offcanvasCarrito");
                    if (offcanvasElement) {
                        var offcanvas = new bootstrap.Offcanvas(offcanvasElement);
                        offcanvas.show();
                    }
                });
            </script>';
            unset($_SESSION['carrito_abierto']); 
        }
        ?>

        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js" integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+" crossorigin="anonymous"></script>
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                const form = document.getElementById('carrito-form');
                const keyInput = document.getElementById('form-plato-key');
                const actionInput = document.getElementById('form-action');

                function submitCarritoAction(key, action) {
                    keyInput.value = key;
                    actionInput.value = action;
                    form.submit();
                }

                document.querySelectorAll('.btn-update-qty').forEach(button => {
                    button.addEventListener('click', function() {
                        const key = this.getAttribute('data-key');
                        const action = this.getAttribute('data-action');
                        submitCarritoAction(key, action);
                    });
                });

                document.querySelectorAll('.btn-delete-item').forEach(button => {
                    button.addEventListener('click', function() {
                        if (confirm("¿Estás seguro de que quieres eliminar este producto del carrito?")) {
                            const key = this.getAttribute('data-key');
                            submitCarritoAction(key, 'delete'); 
                        }
                    });
                });
            });
        </script>
    </body>
</html>
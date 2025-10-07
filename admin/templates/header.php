<?php
$url_base="http://localhost/restaurante/admin";
?>


<header>

           <nav class="navbar navbar-expand navbar-light bg-light">
            <div class="nav navbar-nav">
                <a class="nav-item nav-link active" href="#" aria-current="page">Administrador <span class="visually-hidden">(current)</span></a
                >
                <a class="nav-item nav-link" href="<?php echo $url_base;?>/seccion/banners">Banner</a>
                <a class="nav-item nav-link" href="<?php echo $url_base;?>/seccion/colaboradores">Colaboradores</a>
                <a class="nav-item nav-link" href="<?php echo $url_base;?>/seccion/testimonios">Testimonios</a>
                <a class="nav-item nav-link" href="<?php echo $url_base;?>/seccion/menu">Menu</a>
                <a class="nav-item nav-link" href="<?php echo $url_base;?>/seccion/usuarios">Usuarios</a>
                <a class="nav-item nav-link" href="<?php echo $url_base;?>/seccion/cerrar.php">Cerrar Sesion</a>
                
            </div>
           </nav>
           
</header>
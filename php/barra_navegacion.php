<nav class="navbar-top">
    <div class="nav-container">
        <ul class="nav-menu">
            <li>
                <a href="adm_index.php" class="nav-link">
                    <span class="material-icons">home</span> Inicio
                </a>
            </li>
            <li>
                <a href="adm_usuarios.php" class="nav-link">
                    <span class="material-icons">people</span> Usuarios
                </a>
            </li>
            <li>
                <a href="adm_modelos.php" class="nav-link">
                    <span class="material-icons">chair</span> Modelos
                </a>
            </li>
            <li>
                <a href="adm_registros.php" class="nav-link">
                    <span class="material-icons">assignment</span> Registros
                </a>
            </li>
            
            <li>
                <a href="gestionar_precios.php" class="nav-link">
                    <span class="material-icons">cash</span> Trifas
                </a>
            </li>
        </ul>
        
        <ul class="nav-menu">
            <li>
                <a href="logout.php" class="nav-link btn-salir">
                    <span class="material-icons">logout</span> Salir
                </a>
            </li>
        </ul>
    </div>

    <style>
        /* Estilos Base */
        .navbar-top {
            background-color: #144c3c; /* Verde Bosque Oscuro */
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            position: sticky; /* Se queda pegado arriba si haces scroll */
            top: 0;
            z-index: 1000;
            font-family: 'Quicksand', sans-serif;
            padding: 0 20px;
        }

        .nav-container {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between; /* Separa el menú del botón Salir */
            align-items: center;
        }

        .nav-menu {
            list-style: none;
            margin: 0;
            padding: 0;
            display: flex;
            gap: 5px; /* Espacio entre botones */
            flex-wrap: wrap;
        }

        /* Estilo de los Enlaces */
        .nav-link {
            text-decoration: none;
            color: #ecf0f1;
            padding: 12px 18px;
            font-weight: 600;
            font-size: 0.95rem;
            border-radius: 8px; /* Bordes redondeados modernos */
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px; /* Espacio entre icono y texto */
        }
        
        .nav-link .material-icons {
            font-size: 1.2rem;
        }

        /* Efecto Hover (Pasar el mouse) */
        .nav-link:hover {
            background-color: #cedfcd; /* Verde Pálido de tu paleta */
            color: #144c3c; /* Texto cambia a Verde Oscuro */
            transform: translateY(-2px); /* Pequeña elevación */
        }

        /* Botón Salir Específico */
        .btn-salir {
            background-color: rgba(0,0,0,0.2); /* Un poco más oscuro que el fondo */
            color: #cedfcd;
            margin-left: 10px;
        }
        
        .btn-salir:hover {
            background-color: #94745c !important; /* Marrón Tierra al pasar el mouse */
            color: white !important;
            box-shadow: 0 2px 8px rgba(148, 116, 92, 0.4);
        }

        /* Responsive para móviles */
        @media (max-width: 768px) {
            .nav-container {
                flex-direction: column;
                padding: 10px 0;
            }
            .nav-menu {
                justify-content: center;
                width: 100%;
                gap: 2px;
            }
            .nav-link {
                padding: 10px;
                font-size: 0.85rem;
            }
            .btn-salir {
                margin-left: 0;
                margin-top: 5px;
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</nav>
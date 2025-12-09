<?php session_start(); //creamos la sesion ?> 
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">

  <meta name="description" content="Sistemas computacionales">
  <meta name="keywords" content="MySql, conexión, Wamp">
  <meta name="author" content="Ramirez Erik, Sistemas">

  <title>Registro - Idealiza</title>
  <!-- estilos  -->
  <?php include('php/links.php');?>
  <link rel="stylesheet" href="Css/Resgristro2.css">
</head>

<body> 
<article>
        <?php include('php/header.php');?>
        <!-- ************  MENU  *************** -->
        <?php include('php/menu.php');?>
    </div>
   
    <h1 data-translate="login-title">Iniciar Sesión</h1>
    <div class="login-container">
            <form action="validar_usuario.php" method="post">
                   <tr>
                        <td>Usuario:</td>
                        <td><input name="ema_email" type="email" value="brulionaresh@gmail.com" required /></td>
                    </tr>
                    <tr>
                        <td>Password:</td>
                        <td><input name="pas_password" type="password" value="123" required /></td> 
                    </tr>
                    
                    <tr>
                        <td></td>
                        <td><input name="iniciar" type="submit" value="Iniciar " /></td>
                        <tr>
                        <td></td>
                        <td style="text-align:right;"><a href="registrar.php" data-translate="menu-inicarSecion">Regsitrarse</a></td> 
                    </tr>
                    </table>
        
        </form>
        </div>
    </div>
    <div class="wave wave1"> </div>
    <div class="wave wave2"> </div>
    <div class="wave wave3"> </div>
    <div class="wave wave4"> </div>
     </article>


        
    <!-- ************  FOOTER  *************** -->
    <?php include("php/footer.php"); ?>
    <!-- Extencion para los icnos de redes sociales-->
<script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
 <!-- Extencion para los icnos de redes sociales-->

 <script>
        const texts = {
            "login-title": {
                "es": "Iniciar Sesión",
                "en": "Log In",
                "fr": "Connexion"
            },
            "nombre-label": {
                "es": "Nombre:",
                "en": "Name:",
                "fr": "Nom :"
            },
            "apellidos-label": {
                "es": "Apellidos:",
                "en": "Last Name:",
                "fr": "Nom de famille :"
            },
            "correo-label": {
                "es": "Correo:",
                "en": "Email:",
                "fr": "Email :"
            },
            "contrasena-label": {
                "es": "Contraseña:",
                "en": "Password:",
                "fr": "Mot de passe :"
            },
            "login-success-message": {
                "es": "Inicio de sesión exitoso. ¡Bienvenido,",
                "en": "Successful login. Welcome,",
                "fr": "Connexion réussie. Bienvenue,"
            },
            "language-es": {
                "es": "Español",
                "en": "Spanish",
                "fr": "Espagnol"
            },
            "language-en": {
                "es": "Inglés",
                "en": "English",
                "fr": "Anglais"
            },
            "language-fr": {
                "es": "Francés",
                "en": "French",
                "fr": "Français"
            },
            "menu-inicio": {
                "es": "Inicio",
                "en": "Home",
                "fr": "Accueil"
            },
            "menu-nosotros": {
                "es": "Nosotros",
                "en": "About Us",
                "fr": "À propos de nous"
            },
            "menu-productos": {
                "es": "Productos",
                "en": "Products",
                "fr": "Produits"
            },
            "menu-contacto": {
                "es": "Contacto",
                "en": "Contact",
                "fr": "Contact"
            },
            "menu-registro": {
                "es": "Registro",
                "en": "Registration",
                "fr": "Inscription"
            },
            "menu-inicarSecion": {
                "es": "Registrarse",
                "en": "Register",
                "fr": "S'inscrire"
            },
            "semestre-label": {
                "es": "SEMESTRE",
                "en": "SEMESTER",
                "fr": "SEMESTRE"
            },
            "materia-label": {
                "es": "MATERIA",
                "en": "SUBJECT",
                "fr": "MATIÈRE"
            },
            "grupo-label": {
                "es": "GRUPO",
                "en": "GROUP",
                "fr": "GROUPE"
            },
            "profesor-label": {
                "es": "PROFESOR",
                "en": "PROFESSOR",
                "fr": "PROFESSEUR"
            },
            "alumno-label": {
                "es": "ALUMNO",
                "en": "STUDENT",
                "fr": "ÉTUDIANT"
            }
        };

        const languageSelect = document.getElementById('language-select');

        languageSelect.addEventListener('change', function () {
            const selectedLanguage = this.value;

            translatePage(selectedLanguage);
        });

        function translatePage(selectedLanguage) {
            const elements = document.querySelectorAll('[data-translate]');
            elements.forEach(element => {
                const key = element.getAttribute('data-translate');
                if (texts[key] && texts[key][selectedLanguage]) {
                    if (element.tagName === "input" && element.type === "radio") {
                        element.nextElementSibling.textContent = texts[key][selectedLanguage];
                    } else {
                        element.textContent = texts[key][selectedLanguage];
                    }
                }
            });
        }
    </script>
</body>
</html>
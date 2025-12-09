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

   
    <?php include('php/links.php');?>
</head>

<body>
<article>
        <?php include('php/header.php');?>
        <!-- ************  MENU  *************** -->
        <?php include('php/menu.php');?>
    
    
        <h1>Registro de usuario</h1>
<div class="login-container">
    <form id="form1" name="form1" method="post" action="registrar_usuario.php" style="text-align:center;" onsubmit="return validarForm(this);">
        <label for="ema_email">Email </label><br>
        <input name="ema_email" type="email" required onkeyup="javascript:this.value=this.value.toLowerCase();" />

        <p><label for="txt_Nombre">Nombre </label><br>
            <input type="text" name="txt_Nombre" id="txt_Nombre" onkeyup="javascript:this.value=this.value.toUpperCase();" />
        </p>
        <p><label for="txt_ApPat">Apellido Paterno </label><br>
            <input type="text" name="txt_ApPat" id="txt_ApPat" onkeyup="javascript:this.value=this.value.toUpperCase();" />
        </p>
        <p><label for="txt_ApMat">Apellido Materno </label><br>
            <input type="text" name="txt_ApMat" id="txt_ApMat" onkeyup="javascript:this.value=this.value.toUpperCase();" />
        </p>
        
        <!-- Campo para la fecha de nacimiento -->
        <p><label for="cal_fecha_nacimiento">Fecha de Nacimiento</label><br>
            <input type="date" name="cal_fecha_nacimiento" id="cal_fecha_nacimiento">
        </p>

        <!-- Campo para el sexo -->
        <p><label for="lst_Sexo">Sexo</label><br>
            <select name="lst_Sexo" id="lst_Sexo">
                <option value="masculino">Masculino</option>
                <option value="femenino">Femenino</option>
            </select>
        </p>

        

        <p><label for="pas_password">Password </label><br>
            <input name="pas_password" type="password" required />
        </p>
        <p><label for="pas_password2">Confirmar Password </label><br>
            <input name="pas_password2" type="password" required />
        </p>

        <p><button name="btn_guardar" id="btn_guardar" class="button" style="background-color:#5F7C5D; color: #fff; text-decoration: none; padding: 10px 20px; border-radius: 5px; border: 10px; margin: 10px; transition: background-color 0.3s ease, color 0.3s ease;">Guardar</button></p>
    </form>
</div>

    <!-- ************  FOOTER  *************** -->


    <div class="wave wave1"> </div>
    <div class="wave wave2"> </div>
    <div class="wave wave3"> </div>
    <div class="wave wave4"> </div>
     </article>
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

<script src="js/validacion.js"></script>
</body>
</html>
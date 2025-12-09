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
    <meta name="author" content="Braulio Naresh, Sistemas">

    <title>Contacto - Idealiza</title>
    <?php include('php/links.php');?>

</head>

<body>
<article>
        <?php include('php/header.php');?>
        <!-- ************  MENU  *************** -->
        <?php include('php/menu.php');?>
    </div>
    <div id="content">
        <div id="section">
            <!-- ************  CONTENIDO  *************** -->
            <div id="container">
        <!-- Contenido de la página "Contacto - Idealiza" -->
        <div id="content">
            <h2 >Contacto</h2>
            <p >Estamos aquí para ayudarte con cualquier consulta o solicitud. No dudes en contactarnos:</p>
            <section> 
                <a href="https://maps.app.goo.gl/eTCcBEYyb1JBThL28" class="floating-button">
                    <img src="Img/mapa.png" alt="Icono" class="icon">
                    <span data-translate="floating-button">Encuéntrame</span>
                </a>
            </section>
            <div id="contact-info">
                <p><strong >Nombre de la Empresa:</strong> Idealisa Mueblería</p>
                <p><strong >Correo Electrónico:</strong> <span style="color: #2C4D32;" id="email">idealisamuebles@gmail.com</span></p>
                <p><strong >Teléfono:</strong> <span style="color: #2C4D32;" id="phone"> 4432 27 92 69</span></p>
                <p><strong >Ubicación:</strong>Calle Parota, Col fresno, Ciudad Hidalgo, Michoacan</p>
            </div>
        </div>
    </div>
    <h2>Contacto</h2>
    <form id="formulario" onsubmit="return mostrarAgradecimiento()">
        <label for="nombre" data-translate="nombre">Nombre:</label>
        <input type="text" id="nombre" name="nombre" required>
        
        <label for="correo" data-translate="correo">Correo Electrónico:</label>
        <input type="email" id="correo" name="correo" required>

        
        <label for="mensaje" data-translate="mensaje">Mensaje:</label>
        <textarea id="mensaje" name="mensaje" rows="4" required></textarea>
        
        <input type="submit" value="Enviar">
    </form>

    <div id="mensaje-agradecimiento" style="display: none;">
        <span data-translate="mensaje-agradecimiento">Gracias por su opinión, lo atenderemos lo más rápido posible.</span>
    </div>
   


    <div class="wave wave1"> </div>
    <div class="wave wave2"> </div>
    <div class="wave wave3"> </div>
    <div class="wave wave4"> </div>
     </article>
    <!-- ************  FOOTER  *************** -->
    <?php include("php/footer.php"); ?>

    <script>
        function mostrarAgradecimiento() {
            document.getElementById('formulario').style.display = 'none';
            document.getElementById('mensaje-agradecimiento').style.display = 'block';
            return false; // Evita que el formulario se envíe
        }
    </script>
        <!-- Extencion para los icnos de redes sociales-->
        <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
        <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
         <!-- Extencion para los icnos de redes sociales-->
    

    <script>
        // Textos en español
        const texts_es = {
            "login-title": "Inicio - Idealiza",
            "company-name": "IDEALISA Mueblería",
            "floating-button": "Encuéntrame",
            "menu-inicio": "Inicio",
            "menu-nosotros": "Nosotros",
            "menu-productos": "Productos",
            "menu-contacto": "Contacto",
            "menu-registro": "Registro",
            "language-es": "Español",
            "language-en": "Inglés",
            "language-fr": "Francés",
            "contact-title": "Contacto",
            "contact-text": "Estamos aquí para ayudarte con cualquier consulta o solicitud. No dudes en contactarnos:",
            "email-label": "Correo Electrónico:",
            "phone-label": "Teléfono:",
            "location-label": "Ubicación:",
            "nombre": "Nombre:",
            "correo": "Correo Electrónico:",
            "mensaje": "Mensaje:",
            "mensaje-agradecimiento": "Gracias por su opinión, lo atenderemos lo más rápido posible."
        };
        // Textos en inglés
        const texts_en = {
            "login-title": "Home - Idealiza",
            "company-name": "IDEALISA Furniture",
            "floating-button": "Find Me",
            "menu-inicio": "Home",
            "menu-nosotros": "About Us",
            "menu-productos": "Products",
            "menu-contacto": "Contact",
            "menu-registro": "Register",
            "language-es": "Spanish",
            "language-en": "English",
            "language-fr": "French",
            "contact-title": "Contact",
            "contact-text": "We are here to assist you with any inquiries or requests. Feel free to contact us:",
            "email-label": "Email:",
            "phone-label": "Phone:",
            "location-label": "Location:",
            "nombre": "Name:",
            "correo": "Email:",
            "mensaje": "Message:",
            "mensaje-agradecimiento": "Thank you for your feedback, we will assist you as soon as possible."
        };

        // Textos en francés
        const texts_fr = {
            "login-title": "Accueil - Idealiza",
            "company-name": "IDEALISA Ameublement",
            "floating-button": "Trouve-moi",
            "menu-inicio": "Accueil",
            "menu-nosotros": "À propos de nous",
            "menu-productos": "Produits",
            "menu-contacto": "Contacter",
            "menu-registro": "S'inscrire",
            "language-es": "Espagnol",
            "language-en": "Anglais",
            "language-fr": "Français",
            "contact-title": "Contact",
            "contact-text": "Nous sommes là pour vous aider avec toutes vos questions ou demandes. N'hésitez pas à nous contacter :",
            "email-label": "E-mail :",
            "phone-label": "Téléphone :",
            "location-label": "Emplacement :",
            "nombre": "Nom :",
            "correo": "E-mail :",
            "mensaje": "Message :",
            "mensaje-agradecimiento": "Merci pour votre avis, nous vous aiderons dès que possible."
        };

        const languageSelect = document.getElementById('language-select');

        languageSelect.addEventListener('change', function () {
            const selectedLanguage = this.value;

            if (selectedLanguage === 'es') {
                updateTexts(texts_es);
            } else if (selectedLanguage === 'en') {
                updateTexts(texts_en);
            } else if (selectedLanguage === 'fr') {
                updateTexts(texts_fr);
            }
        });

        function updateTexts(textObject) {
            for (const key in textObject) {
                if (textObject.hasOwnProperty(key)) {
                    const element = document.querySelector(`[data-translate="${key}"]`);
                    if (element) {
                        element.textContent = textObject[key];
                    }
                }
            }
        }
    </script>

</body>
</html>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <meta name="description" content="Pagina  principal de idealisa ">
    <meta name="keywords" content="MySql, conexión, Wamp">
    <meta name="author" content="Braulio Naresh, Sistemas">

  
  <title>Inicio - Idealiza</title>
  <?php include('php/links.php');?>
  
</head>

<body>
<article>

        <!-- ************  HEADER *************** -->
        <?php include('php/header.php');?>

        
        <!-- ************  MENU  *************** -->
        <?php include('php/menu.php');?>
   
    <div id="container">
    <section id="carousel">
            <!-- ************ CARRUSEL*************** -->
        <div class="carousel">
              <div class="gallery">
                <div class="item item-1"></div>
                <div class="item item-2"></div>
                <div class="item item-3"></div>
                <div class="item item-4"></div>
                <div class="item item-5"></div>
                <div class="item item-6"></div>
                </div>
                </div>
                <!-- ************  CONTENIDO  *************** -->
                </section>
             <p>Creamos muebles</br> De la mejor calidad</p>
             <br><br><br>
             <p>NUESTROS PRODUCTOS</p>
             <div id="content">
             <section id="products">
                <div class="product">
                    <img src="Img/productos/1.png" alt="Producto 1">
                    <h3 id="product1-heading">Continental 3 piezas</h3>
                    <p id="descr1-heading">Este es un hermoso mueble ideal para tu hogar. Cosnta de 3 piezas con un dimenciones totales de Alto 225cm Ancho 220cm Fondo 55cm. </p>
                </div>
                <div class="product">
                    <img src="Img/productos/2.png" alt="Producto 2">
                    <h3 id="product2-heading">Armarios</h3>
                    <p id="descr2-heading">De los mejores armarios de exelente calidad ahora a la pared de 
                        tu casa que merece la mejor protección. 
                        Elige un armario que se ajuste a tu espacio y a tu actividad, y guarda 
                        tus objetos con seguridad y comodidad. </p>
                </div>
            </section>
            </div>
            <div class="wave wave1"> </div>
    <div class="wave wave2"> </div>
    <div class="wave wave3"> </div>
    <div class="wave wave4"> </div>
     </article>
    <!-- ************  FOOTER  *************** -->
    <?php include("php/footer.php"); ?>

    <!-- ************  SCRIPTS *************** -->

      <!-- Extencion para los icnos de redes sociales-->
      <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
     <!-- Extencion para los icnos de redes sociales-->

     <script>
        const texts = {
            "es": {
                "menu-inicio": "Inicio",
                "menu-nosotros": "Nosotros",
                "menu-productos": "Productos",
                "menu-contacto": "Contacto",
                "menu-registro": "Registro",
                "intro-heading": "Creamos muebles",
                "materials-heading": "De materiales de la mejor calidad",
                "products-heading": "Nuestros Productos",
                "product1-heading": "Continental 3 piezas.",
                "descr1-heading": "Este es un hermoso mueble ideal para tu hogar. Cosnta de 3 piezas con un dimenciones totales de Alto 225cm Ancho 220cm Fondo 55cm.",
                "product2-heading": "Continental 2 piezas.",
                "descr2-heading": "Este es un hermoso mueble ideal para tu hogar. Cosnta de 2 piezas con un dimenciones totales de Alto 220cm Ancho 185cm Fondo 55cm."
            },
            "en": {
                "menu-inicio": "Home",
                "menu-nosotros": "About Us",
                "menu-productos": "Products",
                "menu-contacto": "Contact",
                "menu-registro": "Record",
                "intro-heading": "We create furniture",
                "materials-heading": "From the finest materials",
                "products-heading": "Our Products",
                "product1-heading": "Cupboards",
                "descr1-heading": "No matter the size of your house, there is always a pantry that suits your needs. Choose the one that best suits you and maintain order and efficiency in your work.",
                "product2-heading": "Wardrobes",
                "descr2-heading": "From the best cabinets of excellent quality now to the wall of your house that deserves the best protection. Choose a closet that fits your space and your activity, and store your items safely and comfortably."
            },
            "fr": {
                "menu-inicio": "Accueil",
                "menu-nosotros": "À propos de nous",
                "menu-productos": "Produits",
                "menu-contacto": "Contact",
                "menu-registro": "Enregistrer",
                "intro-heading": "Nous créons des meubles",
                "materials-heading": "À partir des meilleurs matériaux",
                "products-heading": "Nos produits",
                "product1-heading": "Placards",
                "descr1-heading": "Quelle que soit la taille de votre maison, il y a toujours un garde-manger adapté à vos besoins. Choisissez celui qui vous convient le mieux et maintenez l'ordre et l'efficacité dans votre travail",
                "product2-heading": "Armoires",
                "descr2-heading": "Des meilleures armoires d'excellente qualité maintenant au mur de votre maison qui mérite la meilleure protection. Choisissez un placard adapté à votre espace et à votre activité, et rangez vos objets en toute sécurité et confortablement."
            }
        };

        const languageSelect = document.getElementById('language-select');
        const languageSelectBottom = document.getElementById('language-select-bottom');

        function updateTexts(language) {
            const selectedLanguage = language || languageSelect.value;

            const selectedTexts = texts[selectedLanguage];

            for (const key in selectedTexts) {
                const element = document.getElementById(key);
                if (element) {
                    element.textContent = selectedTexts[key];
                }
            }
        }

        languageSelect.addEventListener('change', () => {
            updateTexts();
        });

        
        // Set the initial language
        updateTexts('es');
    </script>

</body>
</html>
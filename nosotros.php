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

    <title>Nosotros - Idealiza</title>
  <?php include('php/links.php');?>
  <link rel="stylesheet" href="Css/Acordeon.css">
</head>

<body>
<article >
     <!-- ************  MENU  *************** -->
        <?php include('php/header.php');?>
        <!-- ************  MENU  *************** -->
        <?php include('php/menu.php');?>
        <div id="container">
        <div id="content">
  
         <!-- <section id="intro"> -->
         <img src="Img/Mueble.png" alt="Principal" class="main-image">
                <h2 id="intro-heading">Bienvenido a Idealisa</h2>
                <p id="intro-text">Descubre quiénes somos...</p>
                <br><br>
            <!-- </section> -->
            <div class="container">
                <div class="card">
                  <!-- in the card display a header and a div each for each option  -->
              <br>
                  <h2 id="somos-text">Somos una empresa de muebles que se enorgullece de combinar artesanía de alta calidad con diseños innovadores para crear espacios excepcionales. En esta presentación, les llevaremos a un emocionante viaje a través de nuestro catálogo de muebles y servicios, destacando lo que nos hace únicos en la industria y cómo podemos transformar los espacios de vida y trabajo de nuestros clientes.</h2>
              
                  <div class="card__option">
                    <!-- for each option include a div in which to include the description and a span used to display a + sign, to remark the toggle-able nature of the option -->
                    <div class="card__option--description">
                      <!-- for the description, include a term with dt element and an illustration of said term with a dd element  -->
                      <dl>
                        <dt id="Mision">
                          Mision
                        </dt>
                        <dd id="mision-text">
                            Creamos muebles para mejorar la funcionalidad y calidad de vida de nuestros clientes y satisfacer necesidades de organización y armonía en sus hogares. Estamos comprometidos en lograrlo con la dedicación y profesionalismo de nuestro talento humano.
                        </dd>
                      </dl>
                    </div>
                    <span class="card__option--toggle">+</span>
                  </div>
              
                  <div class="card__option">
                    <div class="card__option--description">
                      <dl>
                        <dt id="Vision">
                         Vision
                        </dt>
                        <dd id="vision-text">
                            Ser una empresa con talento humano capaz de crear muebles funcionales, de calidad para todas las familias de México.
                        </dd>
                      </dl>
                    </div>
                    <span class="card__option--toggle">+</span>
                  </div>
              
                  <div class="card__option">
                    <div class="card__option--description">
                      <dl>
                        <dt id="Objetivo">
                         Objetivo
                        </dt>
                        <dd id="objetivo-text">
                            Diseñar y fabricar muebles innovadores y personalizados que satisfagan las necesidades específicas de cada cliente, al mismo tiempo que se mantienen los más altos estándares de calidad y funcionalidad.
                        </dd>
                      </dl>
                    </div>
                    <span class="card__option--toggle">+</span>
                  </div>
              
                  <div class="card__option">
                    <div class="card__option--description">
                      <dl>
                        <dt id="Valores">
                          Valores
                        </dt>
                        <dd id="valores-text">
                            Trabajo en equipo, atención al cliente, compromiso, confianza, funcionalidad del producto, honestidad e integridad.
                        </dd>
                      </dl>
                    </div>
                    <span class="card__option--toggle">+</span>
                  </div>
                </div><!-- close div.card -->
                
              
                </div><!-- close div.container -->
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
    
          

            <!-- <section class="apartado" id="mision">
                <img src="Img/Mision.png" alt="Misión" class="main-image">
                <p id="mision-text">Creamos muebles para mejorar la funcionalidad y calidad de vida de nuestros clientes y satisfacer necesidades de organización y armonía en sus hogares. Estamos comprometidos en lograrlo con la dedicación y profesionalismo de nuestro talento humano.</p>
            </section>

            <section class="apartado" id="vision">
                <img src="Img/Vision.png" alt="Visión" class="main-image">
                <p id="vision-text">Ser una empresa con talento humano capaz de crear muebles funcionales, de calidad para todas las familias de México.</p>
            </section>

            <section class="apartado" id="objetivo">
                <img src="Img/Objetivo.png" alt="Objetivo" class="main-image">
                <p id="objetivo-text">Diseñar y fabricar muebles innovadores y personalizados que satisfagan las necesidades específicas de cada cliente, al mismo tiempo que se mantienen los más altos estándares de calidad y funcionalidad.</p>
            </section>

            <section class="apartado" id="valores">
                <img src="Img/Valores.png" alt="Valores" class="main-image">
                <p id="valores-text">Trabajo en equipo, atención al cliente, compromiso, confianza, funcionalidad del producto, honestidad e integridad.</p>
            </section>
        </div> -->

        
        <script>
            const texts = {
                "es": {
                    "site-title": "IDEALISA<br>Mueblería",
                    "menu-inicio": "Inicio",
                    "menu-nosotros": "Nosotros",
                    "menu-productos": "Productos",
                    "menu-contacto": "Contacto",
                    "menu-registro": "Registro",
                    "menu-inicio-bottom": "Inicio",
                    "menu-nosotros-bottom": "Nosotros",
                    "menu-productos-bottom": "Productos",
                    "menu-contacto-bottom": "Contacto",
                    "menu-registro": "Registro",
                    "intro-heading": "Bienvenido a Idealisa",

                    "intro-text": "Descubre quiénes somos...",
                    "somos-text":"Somos una empresa de muebles que se enorgullece de combinar artesanía de alta calidad con diseños innovadores para crear espacios excepcionales. En esta presentación, les llevaremos a un emocionante viaje a través de nuestro catálogo de muebles y servicios, destacando lo que nos hace únicos en la industria y cómo podemos transformar los espacios de vida y trabajo de nuestros clientes.",

                    "mision-text": "Creamos muebles para mejorar la funcionalidad y calidad de vida de nuestros clientes y satisfacer necesidades de organización y armonía en sus hogares. Estamos comprometidos en lograrlo con la dedicación y profesionalismo de nuestro talento humano.",
                    "vision-text": "Ser una empresa con talento humano capaz de crear muebles funcionales, de calidad para todas las familias de México.",
                    "objetivo-text": "Diseñar y fabricar muebles innovadores y personalizados que satisfagan las necesidades específicas de cada cliente, al mismo tiempo que se mantienen los más altos estándares de calidad y funcionalidad.",
                    "valores-text": "Trabajo en equipo, atención al cliente, compromiso, confianza, funcionalidad del producto, honestidad e integridad.",
                    "Mision":"Misión",
                    "Vision":"Visión",
                    "Objetivo":"Objetivo",
                    "Valores":"Valores"
                    
                },
                "en": {
    "site-title": "IDEALISA<br>Furniture",
    "menu-inicio": "Home",
    "menu-nosotros": "About Us",
    "menu-productos": "Products",
    "menu-contacto": "Contact",
    "menu-registro": "Register",
    "menu-inicio-bottom": "Home",
    "menu-nosotros-bottom": "About Us",
    "menu-productos-bottom": "Products",
    "menu-contacto-bottom": "Contact",
    "menu-registro": "Register",
    "intro-heading": "Welcome to Idealisa",
    "intro-text": "Discover who we are...",
    "somos-text": "We are a furniture company that takes pride in combining high-quality craftsmanship with innovative designs to create exceptional spaces. In this presentation, we will take you on an exciting journey through our catalog of furniture and services, highlighting what makes us unique in the industry and how we can transform the living and working spaces of our clients.",
    "mision-text": "We create furniture to improve the functionality and quality of life of our customers and meet the needs of organization and harmony in their homes. We are committed to achieving this with the dedication and professionalism of our human talent.",
    "vision-text": "To be a company with human talent capable of creating functional, quality furniture for all families of Mexico.",
    "objetivo-text": "To design and manufacture innovative and customized furniture that satisfies the specific needs of each customer while maintaining the highest standards of quality and functionality.",
    "valores-text": "Teamwork, customer focus, commitment, trust, product functionality, honesty, and integrity.",
    "Mision": "Mission",
    "Vision": "Vision",
    "Objetivo": "Objective",
    "Valores": "Values"
},"fr": {
    "site-title": "IDEALISA<br>Meubles",
    "menu-inicio": "Accueil",
    "menu-nosotros": "À propos de nous",
    "menu-productos": "Produits",
    "menu-contacto": "Contact",
    "menu-registro": "Enregistrer",
    "menu-inicio-bottom": "Accueil",
    "menu-nosotros-bottom": "À propos de nous",
    "menu-productos-bottom": "Produits",
    "menu-contacto-bottom": "Contact",
    "menu-registro": "Enregistrer",
    "intro-heading": "Bienvenue chez Idealisa",
    "intro-text": "Découvrez qui nous sommes...",
    "somos-text": "Nous sommes une entreprise de meubles qui prend fierté à associer un artisanat de haute qualité à des designs innovants pour créer des espaces exceptionnels. Dans cette présentation, nous vous emmènerons dans un voyage passionnant à travers notre catalogue de meubles et de services, mettant en avant ce qui nous rend unique dans l'industrie et comment nous pouvons transformer les espaces de vie et de travail de nos clients.",
    "mision-text": "Nous créons des meubles pour améliorer la fonctionnalité et la qualité de vie de nos clients et répondre aux besoins d'organisation et d'harmonie dans leurs foyers. Nous nous engageons à y parvenir avec le dévouement et le professionnalisme de notre personnel.",
    "vision-text": "Être une entreprise dotée d'un personnel humain capable de créer des meubles fonctionnels et de qualité pour toutes les familles du Mexique.",
    "objetivo-text": "Concevoir et fabriquer des meubles innovants et personnalisés répondant aux besoins spécifiques de chaque client, tout en respectant les normes les plus élevées en matière de qualité et de fonctionnalité.",
    "valores-text": "Travail d'équipe, orientation client, engagement, confiance, fonctionnalité du produit, honnêteté et intégrité.",
    "Mision": "Mission",
    "Vision": "Vision",
    "Objetivo": "Objectif",
    "Valores": "Valeurs"
}

                
                
            };
    
            const languageSelect = document.getElementById('language-select');
            const elementsToUpdate = [
                "site-title",
                "menu-inicio",
                "menu-nosotros",
                "menu-productos",
                "menu-contacto",
                "menu-inicio-bottom",
                "menu-nosotros-bottom",
                "menu-productos-bottom",
                "menu-contacto-bottom",
                "menu-registro",
                "intro-heading",
                "intro-text",
                "mision-text",
                "vision-text",
                "objetivo-text",
                "valores-text",
                "Mision",
                "Vision","Objetivo","Valores","somos-text"
            ];
    
            function updateTexts(language) {
                const selectedLanguage = language || languageSelect.value;
                const selectedTexts = texts[selectedLanguage];
    
                for (const elementId of elementsToUpdate) {
                    const element = document.getElementById(elementId);
                    if (element && selectedTexts[elementId]) {
                        element
                        .textContent = selectedTexts[elementId];
                }
            }
        }

        languageSelect.addEventListener('change', () => {
            updateTexts();
        });

        // Establecer el idioma inicial
        updateTexts('es');
    </script>
    <script src="JS/Acordeon.js"></script>


</body>
</html>
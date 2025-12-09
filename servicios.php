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

    <title>Productos y Servicios - Idealiza</title>
  <?php include('php/links.php');?>
</head>

<body>
<article>
        <?php include('php/header.php');?>
        <!-- ************  MENU  *************** -->
        <?php include('php/menu.php');?>
        <div id="container">
      
      </div>

      <div id="search-bar">
          <input type="text" id="search-input" placeholder="Buscar productos...">
          <button id="search-button">Buscar</button>
      </div>
      <div id="productos">
          <div class="producto">
              <!-- <img src="Img/producto1.jpg" alt="Producto 1"> -->
              <img src="Img/productos/1.png" alt="Producto 1">
              <h3 id="alacena-heading">Ropero Continental</h3>
              <p id="descrproduct1-heading"> Descripción del producto: Este es un hermoso mueble ideal para tu hogar. Cosnta de 3 piezas con un dimenciones totales de Alto 225cm
                Ancho 220cm Fondo 55cm.</p>
              <p id="precio1-heading">Precio: $5000.00</p>
              <!-- <button id="comprar1-botton-heading">Comprar</button> -->
          </div>

          <div class="producto">
              <!-- <img src="https://th.bing.com/th/id/R.cd9116b92662a25d0901649b3165f851?rik=sFaHunpqTql61g&pid=ImgRaw&r=0" alt="Producto 2"> -->
              <img src="Img/productos/2.png" alt="Producto 2">
              <h3 id="comedor-heading"></h3>
              <p id="descrproduct2-heading"></p>
              <p id="precio2-heading"></p>
              <!-- <button id="comprar2-botton-heading">Comprar</button> -->
          </div>

          <div class="producto">
          <img src="Img/productos/3.png" alt="Producto 3">
              <h3 id="sillac.heading"></h3>
              <p id="descrproduct3-heading"></p>
              <p id="precio3-heading">Precio: $700.00</p>
              <!-- <button id="comprar3-botton-heading">Comprar</button> -->
          </div>

          <div class="producto">
          <img src="Img/productos/4.png" alt="Producto 4">
           <h3 id="ropero-roma">Ropero 00</h3>
           
              <p id="descrproduct4-heading">Descripción del producto: Este es un hermoso mueble ideal para tu hogar. Está hecho de materiales de alta calidad y diseñado para brindar comodidad y elegancia a tu espacio.</p>
              <p id="precio4-heading">Precio: $500.00</p>
              <!-- <button id="comprar4-botton-heading">Comprar</button> -->
          </div>

          <div class="producto">
          <img src="Img/productos/5.png" alt="Producto 5">
               <h3 id="muebletv-heading">Mueble para tv</h3>
              <p id="descrproduct5-heading">Descripción del producto: Este es un hermoso mueble ideal para tu hogar. Está hecho de materiales de alta calidad y diseñado para brindar comodidad y elegancia a tu espacio.</p>
              <p id="precio5-heading">Precio: $500.00</p>
              <!-- <button id="comprar5-botton-heading">Comprar</button> -->
          </div>

          <div class="producto">
          <img src="Img/productos/6.png" alt="Producto 6">
              <h3 id="sala-heading">Sala de estar</h3>
              <p id="descrproduct6-heading">Descripción del producto: Este es un hermoso mueble ideal para tu hogar. Está hecho de materiales de alta calidad y diseñado para brindar comodidad y elegancia a tu espacio.</p>
              <p id="precio6-heading">Precio: $500.00</p>
              <!-- <button id="comprar6-botton-heading">Comprar</button> -->
          </div>

          <div class="producto">
              <img src="https://th.bing.com/th/id/OIP.ucCJIS3OqbQuRMOOJtHyqAHaJ4?w=152&h=203&c=7&r=0&o=5&dpr=1.3&pid=1.7" alt="Producto 1">
              <h3 id="sillon-heading">Sillon individual reclinable </h3>
              <p id="descrproduct7-heading">Descripción del producto: Este es un hermoso mueble ideal para tu hogar. Está hecho de materiales de alta calidad y diseñado para brindar comodidad y elegancia a tu espacio.</p>
              <p id="precio7-heading">Precio: $500.00</p>
              <!-- <button id="comprar7-botton-heading">Comprar</button> -->
          </div>

          <div class="producto">
              <img src="https://th.bing.com/th/id/OIP.imFBd0Oh2840dncs3L7VmgHaGZ?w=195&h=180&c=7&r=0&o=5&dpr=1.3&pid=1.7" alt="Producto 1">
              <h3 id="sillonj-heading">Sillon para juegos</h3>
              <p id="descrproduct8-heading">Descripción del producto: Este es un hermoso mueble ideal para tu hogar. Está hecho de materiales de alta calidad y diseñado para brindar comodidad y elegancia a tu espacio.</p>
              <p id="precio8-heading">Precio: $500.00</p>
              <!-- <button id="comprar8-botton-heading">Comprar</button> -->
          </div>

          <div class="producto">
              <img src="https://th.bing.com/th/id/OIP.f5XaV_erBNpvGYyRYCPi1QHaFj?w=285&h=214&c=7&r=0&o=5&dpr=1.3&pid=1.7" alt="Producto 1">
              <h3 id="ropero-heading">Ropero</h3>
              <p id="descrproduct9-heading">Descripción del producto: Este es un hermoso mueble ideal para tu hogar. Está hecho de materiales de alta calidad y diseñado para brindar comodidad y elegancia a tu espacio.</p>
              <p id="precio9-heading">Precio: $500.00</p>
              <!-- <button id="comprar9-botton-heading">Comprar</button> -->
          </div>
      </div>
  </div>
 

  <div class="wave wave1"> </div>
  <div class="wave wave2"> </div>
  <div class="wave wave3"> </div>
  <div class="wave wave4"> </div>
   </article>

    <!-- ************  FOOTER  *************** -->
    <?php include("php/footer.php"); ?>

    <script>
        document.getElementById("search-button").addEventListener("click", function () {
            var searchQuery = document.getElementById("search-input").value.toLowerCase();
            var productos = document.querySelectorAll(".producto");

            productos.forEach(function (producto) {
                var titulo = producto.querySelector("h3").textContent.toLowerCase();
                var descripcion = producto.querySelector("p").textContent.toLowerCase();

                if (titulo.includes(searchQuery) || descripcion.includes(searchQuery)) {
                    producto.classList.remove("hidden");
                } else {
                    producto.classList.add("hidden");
                }
            });
        });
    </script>

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
                "search-input": "Buscar productos...",
                "search-button": "Buscar",
                "alacena-heading": "Ropero Continental 3 PZ",
                "descrproduct1-heading": "Descripción del producto: Este es un hermoso mueble ideal para tu hogar. Cosnta de 3 piezas con un dimenciones totales de Alto 225cm Ancho 220cm Fondo 55cm.",
                "precio1-heading": "Precio: $5000.00",
                "comedor-heading": "Ropero Continental 2PZ ",
                "descrproduct2-heading": "Descripción del producto: Este es un hermoso mueble ideal para tu hogar. Cosnta de 2 piezas con un dimenciones totales de Alto 220cm Ancho 185cm Fondo 55cm.",
                "precio2-heading": "Precio: $4500.00",
                "sillac.heading": "Ropero Roma Abatible",
                "descrproduct3-heading": "Descripción del producto: Este es un hermoso mueble ideal para tu hogar. Cosnta de 1 piezas con un dimenciones totales de Alto 170cm Ancho 155cm Fondo 55cm.",
                "precio3-heading": "Precio: $1200.00",
                "ropero-roma": "Ropero Roma corredizo",
                "descrproduct4-heading": "Descripción del producto: Este es un hermoso mueble ideal para tu hogar. Cosnta de 1 piezas con un dimenciones totales de Alto 170cm Ancho 155cm Fondo 55cm.",
                "precio4-heading": "Precio: $500.00",
                "muebletv-heading": "Mueble para tv",
                "descrproduct5-heading": "Descripción del producto: Este es un hermoso mueble ideal para tu hogar. Está hecho de materiales de alta calidad y diseñado para brindar comodidad y elegancia a tu espacio.",
                "precio5-heading": "Precio: $500.00",
                "sala-heading": "Sala de estar",
                "descrproduct6-heading": "Descripción del producto: Este es un hermoso mueble ideal para tu hogar. Está hecho de materiales de alta calidad y diseñado para brindar comodidad y elegancia a tu espacio.",
                "precio6-heading": "Precio: $500.00",
                "sillon-heading": "Sillon individual reclinable",
                "descrproduct7-heading": "Descripción del producto: Este es un hermoso mueble ideal para tu hogar. Está hecho de materiales de alta calidad y diseñado para brindar comodidad y elegancia a tu espacio.",
                "precio7-heading": "Precio: $500.00",
                "sillonj-heading": "Sillon para juegos",
                "descrproduct8-heading": "Descripción del producto: Este es un hermoso mueble ideal para tu hogar. Está hecho de materiales de alta calidad y diseñado para brindar comodidad y elegancia a tu espacio.",
                "precio8-heading": "Precio: $500.00",
                "ropero-heading": "Ropero",
                "descrproduct9-heading": "Descripción del producto: Este es un hermoso mueble ideal para tu hogar. Está hecho de materiales de alta calidad y diseñado para brindar comodidad y elegancia a tu espacio.",
                "precio9-heading": "Precio: $500.00",
                "comprar-botton-heading": "Comprar"
            },
            "en": {
                "menu-inicio": "Home",
                "menu-nosotros": "About Us",
                "menu-productos": "Products",
                "menu-contacto": "Contact",
                "menu-registro": "Record",
                "search-input": "Search products...",
                "search-button": "Look for",
                "alacena-heading": "Cupboard",
                "descrproduct1-heading": "Product Description: This is a beautiful piece of furniture ideal for your home. It is made of high quality materials and designed to bring comfort and elegance to your space.",
                "precio1-heading": "Price: $500.00",
                "comprar1-botton-heading": "Buy",
                "comedor-heading": "Dining room",
                "descrproduct2-heading": "Product Description: Another high-quality piece of furniture that adapts perfectly to your decoration. Enjoy the comfort and style it provides.",
                "precio2-heading": "Price: $600.00",
                "comprar2-botton-heading": "Buy",
                "sillac.heading": "Leather chair",
                "descrproduct3-heading": "Product Description: A unique piece of furniture that will make your home stand out. Its innovative design and top quality materials make it an exceptional choice.",
                "precio3-heading": "Price: $700.00",
                "comprar3-botton-heading": "Buy",
                "ropero-heading": "Wardrobe",
                "descrproduct4-heading": "Product Description: This is a beautiful piece of furniture ideal for your home. It is made of high quality materials and designed to bring comfort and elegance to your space.",
                "precio4-heading": "Price: $500.00",
                "comprar4-botton-heading": "Buy",
                "muebletv-heading": "Furniture for TV",
                "descrproduct5-heading": "Product Description: This is a beautiful piece of furniture ideal for your home. It is made of high quality materials and designed to bring comfort and elegance to your space.",
                "precio5-heading": "Price: $500.00",
                "comprar5-botton-heading": "Buy",
                "sala-heading": "Living room",
                "descrproduct6-heading": "Product Description: This is a beautiful piece of furniture ideal for your home. It is made of high quality materials and designed to bring comfort and elegance to your space.",
                "precio6-heading": "Price: $500.00",
                "comprar6-botton-heading": "Buy",
                "sillon-heading": "Individual reclining armchair",
                "descrproduct7-heading": "Product Description: This is a beautiful piece of furniture ideal for your home. It is made of high quality materials and designed to bring comfort and elegance to your space.",
                "precio7-heading": "Price: $500.00",
                "comprar7-botton-heading": "Buy",
                "sillonj-heading": "Gaming chair",
                "descrproduct8-heading": "Product Description: This is a beautiful piece of furniture ideal for your home. It is made of high quality materials and designed to bring comfort and elegance to your space.",
                "precio8-heading": "Price: $500.00",
                "comprar8-botton-heading": "Buy",
                "ropero-heading": "Wardrobe",
                "descrproduct9-heading": "Product Description: This is a beautiful piece of furniture ideal for your home. It is made of high quality materials and designed to bring comfort and elegance to your space.",
                "precio9-heading": "Price: $500.00",
                "comprar9-botton-heading": "Buy"
            },
            "fr": {
                "menu-inicio": "Accueil",
                "menu-nosotros": "À propos de nous",
                "menu-productos": "Produits",
                "menu-contacto": "Contact",
                "menu-registro": "Enregistrer",
                
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
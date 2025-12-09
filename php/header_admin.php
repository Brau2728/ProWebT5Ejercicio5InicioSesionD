<div id="header" class="encabezado" >

        <h1 style=color:#fff;>IDEALISA<br>Mueblería</h1>
        <br>
        <div id="clock"></div>
        <br>
        <h2>HOLA : <?php echo $_SESSION["usuario"]; ?>. </h2>
         
    </div>

    
    <script>
    // Función para actualizar la hora y fecha
    function updateClock() {
      var now = new Date(); // Obtiene la fecha y hora actuales
      var day = now.toLocaleDateString(); // Obtiene la fecha actual en formato local
      var time = now.toLocaleTimeString(); // Obtiene la hora actual en formato local

      // Actualiza el contenido del elemento con id "clock" con la fecha y hora actuales
      document.getElementById('clock').innerHTML = 'Hoy es: ' + day + '<br>La hora es: ' + time;

      // Actualiza la hora cada segundo
      setTimeout(updateClock, 1000);
    }

    // Llama a la función para que empiece a mostrar la hora y fecha
    updateClock();
  </script>

<!-- Olas de Fondo Animadas (Corregido) -->
<div class="waves-container">
    <div class="wave wave1"></div>
    <div class="wave wave2"></div>
    <div class="wave wave3"></div>
    <div class="wave wave4"></div>
</div>

<style>
    /* Contenedor general fijo al fondo */
    .waves-container {
        position: fixed;
        bottom: 0;
        left: 0;
        width: 100%;
        height: 250px; /* Altura generosa para que luzcan */
        z-index: -1;   /* Detrás del contenido */
        overflow: hidden; 
        pointer-events: none; /* Permite clicks a través de las olas */
    }

    /* Estilo base de cada ola */
    .wave {
        position: absolute;
        bottom: 0;
        left: 0;
        width: 200%;
        height: 100%;
        /* SVG de la ola */
        background: url('data:image/svg+xml;utf8,<svg viewBox="0 0 1200 120" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none"><path d="M321.39,56.44c58-10.79,114.16-30.13,172-41.86,82.39-16.72,168.19-17.73,250.45-.39C823.78,31,906.67,72,985.66,92.83c70.05,18.48,146.53,26.09,214.34,3V0H0V27.35A600.21,600.21,0,0,0,321.39,56.44Z" fill="%23144c3c" fill-opacity="0.4"/></svg>');
        background-size: 50% 100%; 
        background-repeat: repeat-x;
        
        /* CORRECCIÓN: Invertimos usando el CENTRO para que no se salgan de pantalla */
        transform: scaleY(-1);
        transform-origin: center; 
    }

    /* --- Animaciones Individuales (Parallax) --- */
    
    .wave1 {
        opacity: 0.7;
        background-size: 50% 90%;
        animation: moveWave 20s linear infinite;
        z-index: 1000;
        bottom: -15px; /* Ajuste para ocultar borde plano */
    }

    .wave2 {
        opacity: 0.5;
        background-size: 50% 95%;
        animation: moveWave 15s linear infinite;
        z-index: 999;
        bottom: -10px;
    }

    .wave3 {
        opacity: 0.3;
        background-size: 50% 80%;
        animation: moveWave 10s linear infinite;
        z-index: 998;
        bottom: -5px;
    }

    .wave4 {
        opacity: 0.2;
        background-size: 50% 70%;
        animation: moveWave 8s linear infinite;
        z-index: 997;
        bottom: 0;
    }

    @keyframes moveWave {
        0% { transform: scaleY(-1) translateX(0); }
        100% { transform: scaleY(-1) translateX(-50%); }
    }
</style>
<div class="footer-waves-container">
    <div class="wave wave1"></div>
    <div class="wave wave2"></div>
    <div class="wave wave3"></div>
    <div class="wave wave4"></div>
</div>

<style>
    /* Contenedor fijo al fondo */
    .footer-waves-container {
        position: fixed;
        bottom: 0;
        left: 0;
        width: 100%;
        /* AUMENTAMOS LA ALTURA AQUÍ: De 100px a 150px */
        height: 150px; 
        z-index: -1;
        overflow: hidden;
        pointer-events: none;
    }

    /* Estilo base de la ola */
    .footer-waves-container .wave {
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        height: 100px; /* La imagen sigue midiendo 100px */
        background: url('Img/wabe_footer2.png'); 
        background-size: 1000px 100px;
        background-repeat: repeat-x; /* Asegura que se repita horizontalmente */
    }

    /* --- Animaciones (Sin cambios, solo ajustes de posición) --- */

    .footer-waves-container .wave1 {
        animation: animateWave 30s linear infinite;
        z-index: 1000;
        opacity: 1;
        animation-delay: 0s;
        bottom: 0;
    }

    .footer-waves-container .wave2 {
        animation: animateWave2 15s linear infinite;
        z-index: 999;
        opacity: 0.5;
        animation-delay: -5s;
        bottom: 10px;
    }

    .footer-waves-container .wave3 {
        animation: animateWave 30s linear infinite;
        z-index: 998;
        opacity: 0.2;
        animation-delay: -2s;
        bottom: 15px;
    }

    .footer-waves-container .wave4 {
        animation: animateWave2 5s linear infinite;
        z-index: 997;
        opacity: 0.7;
        animation-delay: -5s;
        bottom: 20px; /* Esta es la que se cortaba antes */
    }

    @keyframes animateWave {
        0% { background-position-x: 0; }
        100% { background-position-x: 1000px; }
    }

    @keyframes animateWave2 {
        0% { background-position-x: 0; }
        100% { background-position-x: -1000px; }
    }
</style>
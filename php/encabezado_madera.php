<div id="header-wood" style="
    background-image: url('Img/madera.png'); /* Ruta corregida */
    background-size: cover;
    background-position: center;
    padding: 30px 20px;
    text-align: center;
    border-bottom: 5px solid #2E7D32;
    color: white;
    box-shadow: 0 4px 6px rgba(0,0,0,0.3);
    position: relative;
    z-index: 20;
">
    <div style="background: rgba(0,0,0,0.5); display: inline-block; padding: 10px 40px; border-radius: 50px; backdrop-filter: blur(4px); border: 1px solid rgba(255,255,255,0.3);">
        <h3 style="margin: 0; font-family: 'Quicksand', sans-serif; font-weight: 400; letter-spacing: 2px; text-transform: uppercase;">
            HOLA: <b><?php echo isset($_SESSION['usuario']) ? strtoupper($_SESSION['usuario']) : 'ADMIN'; ?></b>
        </h3>
        <small style="color: #eee; font-size: 0.9rem;">Panel de Control General</small>
    </div>
</div>
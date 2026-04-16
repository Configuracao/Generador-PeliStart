<?php
// Validar que el User-Agent coincida exactamente
if ($_SERVER['HTTP_USER_AGENT'] !== "CineFlixterApp_Oficial_2026") {
    header('HTTP/1.0 403 Forbidden');
    echo "<h1>Acceso no autorizado</h1><p>Usa la aplicación oficial.</p>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Darkflix Premium</title>
    <style>
        :root { --main-red: #ff0033; --dark-bg: #000000; --card-bg: #1a1a1a; --side-bg: #121212; }
        body {
            background: var(--dark-bg); color: white;
            font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            margin: 0; padding: 0; overflow-x: hidden;
            -webkit-tap-highlight-color: transparent;
        }

        /* --- NAVBAR --- */
        .navbar { 
            position: fixed; top: 0; width: 100%; z-index: 110; 
            background: #000; display: flex; justify-content: space-between; 
            align-items: center; padding: 15px 20px; border-bottom: 2px solid var(--main-red);
            box-sizing: border-box;
        }
        .logo { color: var(--main-red); font-size: 24px; font-weight: 900; margin: 0; text-transform: uppercase; cursor: pointer; }
        .logo span { color: white; }
        .menu-toggle { font-size: 24px; cursor: pointer; color: white; }

        /* --- MENÚ LATERAL --- */
        .side-menu {
            position: fixed; top: 0; left: -280px; width: 280px; height: 100%;
            background: var(--side-bg); z-index: 300; transition: 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 5px 0 25px rgba(0,0,0,0.8); display: flex; flex-direction: column;
        }
        .side-menu.active { left: 0; }
        .side-overlay {
            position: fixed; inset: 0; background: rgba(0,0,0,0.75);
            z-index: 290; display: none; backdrop-filter: blur(4px);
        }
        .side-overlay.active { display: block; }
        .side-header { padding: 30px 25px; border-bottom: 1px solid #222; margin-bottom: 10px; }
        .side-item {
            padding: 16px 25px; display: flex; align-items: center; gap: 20px;
            color: #ccc; text-decoration: none; font-size: 17px; cursor: pointer; transition: 0.2s;
        }
        .side-item:active { background: rgba(255,0,51,0.15); color: white; }
        .side-item.active-link { color: var(--main-red); font-weight: bold; }

        /* --- BUSCADOR --- */
        .search-container {
            display: none; position: fixed; top: 62px; left: 0; width: 100%;
            background: #111; padding: 15px; z-index: 105; border-bottom: 1px solid #333;
            box-sizing: border-box;
        }
        .search-container.active { display: flex; gap: 10px; }
        .search-container input { flex: 1; background: #222; border: 1px solid #444; padding: 12px; border-radius: 6px; color: white; outline: none; }
        .search-container button { background: var(--main-red); color: white; border: none; padding: 0 15px; border-radius: 6px; font-weight: bold; cursor: pointer; }

        /* --- BOTÓN VOLVER --- */
        #back-btn-container {
    display: none; 
    align-items: center; 
    justify-content: center; 
    gap: 8px;
    background: var(--main-red); 
    color: white; 
    border: none;
    padding: 12px 25px; 
    border-radius: 50px; 
    font-size: 14px;
    margin: 80px auto 20px; 
    cursor: pointer; 
    width: fit-content;
    font-weight: bold; 
    z-index: 100;
    box-shadow: 0 4px 15px rgba(255, 0, 51, 0.4);
}

        /* --- HERO SLIDER ESTILO KINGFIRE OPTIMIZADO --- */
.hero-slider { 
    width: 100%; 
    margin-top: 85px; /* Bajamos un poco para no pegar a la navbar */
    margin-bottom: 10px;
    position: relative; 
    overflow: hidden; 
}

.hero-track { 
    display: flex; 
    overflow-x: auto; 
    /* Cambiamos mandatory por proximity o lo quitamos temporalmente en el salto */
    scroll-snap-type: x proximity; 
    scrollbar-width: none; 
    padding: 0 5%; 
    gap: 15px; 
    -webkit-overflow-scrolling: touch;
}

.hero-track::-webkit-scrollbar { display: none; }

.hero-slide { 
    /* Ajustamos el ancho para que la tarjeta sea grande pero deje ver los bordes de las vecinas */
    flex: 0 0 100%; 
    height: 200px; 
    position: relative; 
    scroll-snap-align: center; /* Fuerza a que la tarjeta se detenga siempre en el centro */
    border-radius: 20px; 
    overflow: hidden;
    background: #1a1a1a;
    box-shadow: 0 4px 15px rgba(0,0,0,0.5);
}

.hero-slide img { 
    width: 100%; 
    height: 100%; 
    object-fit: cover; 
    display: block;
}

/* Gradiente sutil para el título en el slider */
.hero-grad {
    position: absolute;
    inset: 0;
    background: linear-gradient(to top, rgba(0,0,0,0.8) 0%, transparent 60%);
    z-index: 1;
}

.hero-content {
    position: absolute;
    bottom: 15px;
    left: 15px;
    right: 15px;
    z-index: 2;
}

        /* --- SECCIONES --- */
        .section { margin-bottom: 20px; display: block; }
        .section-header { display: flex; justify-content: space-between; align-items: center; padding: 15px 15px 10px; }
        .section-title { font-size: 24px; font-weight: bold; margin: 0; }
        .ver-mas { color: var(--main-red); font-size: 14px; cursor: pointer; }

        .row-scroll { display: flex; overflow-x: auto; scrollbar-width: none; padding-left: 15px; gap: 12px; }
        .row-scroll::-webkit-scrollbar { display: none; }

        /* --- ESTADO GRID (VER MÁS) --- */
        .grid-active .hero-slider { display: none !important; }
        .grid-active .section { display: none !important; }
        .grid-active .section.active-view { display: block !important; margin-top: 10px; }
        .grid-active .active-view .row-scroll { display: grid !important; grid-template-columns: repeat(2, 1fr); gap: 15px; padding: 15px; overflow: visible; }
        .grid-active .active-view .movie-card { flex: none; width: 100%; }
        .grid-active .active-view .ver-mas { display: none !important; }
        .grid-active .active-view .btn-load-more { display: block !important; }

        /* --- TARJETAS --- */
        .movie-card { flex: 0 0 150px; aspect-ratio: 2/3; position: relative; border-radius: 12px; overflow: hidden; background: #111; cursor: pointer; }
        .movie-card img { width: 100%; height: 100%; object-fit: cover; }
        .movie-card:hover img {
    transform: scale(1.12); /* Ajusta el 1.15 para más o menos zoom */
}
        .card-overlay { position: absolute; bottom: 0; left: 0; right: 0; height: 50%; background: linear-gradient(to top, rgba(0,0,0,1) 0%, transparent 100%); display: flex; align-items: flex-end; padding: 10px; z-index: 5; }
        .card-title { font-size: 12px; font-weight: bold; text-align: center; width: 100%; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }

        .btn-load-more { display: none; width: calc(100% - 30px); margin: 30px 15px; background: #222; color: white; border: 1px solid #444; padding: 15px; border-radius: 8px; font-weight: bold; cursor: pointer; }

        /* --- NUEVO MODAL PROFESIONAL --- */
/* Vista de detalles a pantalla completa */
/* Vista de detalles: Centrada y con altura adaptativa */
.modal { 
    display: none; 
    position: fixed; 
    z-index: 400; 
    inset: 0; 
    background: var(--dark-bg); /* Fondo sólido negro */
    overflow-y: auto; 
    padding: 0; /* Eliminamos el padding lateral */
}

.modal-content { 
    background: var(--dark-bg);
    margin: 0; 
    width: 100%; 
    max-width: none; /* Quitamos el límite de ancho */
    border-radius: 0; /* Bordes rectos para look de app full-screen */
    border: none;
    position: relative;
    height: auto; 
    min-height: 100vh;
    box-shadow: none;
}

/* Ajuste de la cabecera dentro del modal contenido */
.m-header {
    width: 100%;
    height: 56.25vw; /* Proporción 16:9 exacta */
    max-height: 400px;
    position: relative;
}

.m-body { 
    padding: 20px; 
    margin-top: 0; /* Quitamos el margen negativo para evitar descuadres */
    position: relative; 
}

#m-banner {
    width: 100%;
    height: 100%;
    object-fit: cover;
    object-position: center top; /* Prioriza ver las caras si la imagen es alta */
    display: block;
}

/* Ajuste para que el modal de servidores sea dinámico y centrado */
/* --- CORRECCIÓN DEFINITIVA MODAL SERVIDORES --- */
#serverModal {
    display: none; /* Se activa como 'flex' desde JS */
    position: fixed;
    inset: 0;
    z-index: 999; /* Aseguramos que esté por encima de todo */
    background: rgba(0, 0, 0, 0.9);
    backdrop-filter: blur(8px);
    align-items: center; 
    justify-content: center;
}

#serverModal .modal-content {
    width: 85%;
    max-width: 380px;
    background: #111;
    border-radius: 20px;
    border: 1px solid var(--main-red);
    /* CLAVE: Altura basada solo en el contenido */
    height: auto !important; 
    min-height: auto !important;
    max-height: 70vh; 
    overflow-y: auto;
    position: relative;
    margin: auto; 
    padding: 0;
    box-shadow: 0 15px 50px rgba(0,0,0,1);
}

/* Ajuste de los botones internos para que no peguen al borde */
.server-grid { 
    display: flex; 
    flex-direction: column; 
    gap: 10px; 
    padding-bottom: 10px;
}

.header-overlay-content {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    padding: 40px 20px 20px;
    background: linear-gradient(to top, #111 10%, transparent 100%);
    text-align: center;
}

.m-title {
    font-size: 32px;
    font-weight: 800;
    margin-bottom: 10px;
}

.m-info-bar {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 15px;
    font-size: 15px;
    color: #bbb;
}

/* Botón "Ver Ahora" estilo Netflix/Darkflix */
.btn-play-main {
    width: 100%;
    background: white;
    color: black;
    border: none;
    padding: 14px;
    border-radius: 8px;
    font-weight: bold;
    font-size: 18px;
    margin: 20px 0;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    cursor: pointer;
}

.section-subtitle {
    font-size: 20px;
    font-weight: bold;
    margin: 25px 0 15px;
}

/* Descripción con mejor lectura */
.m-description {
    font-size: 15px;
    line-height: 1.6;
    color: #ddd;
    text-align: left;
}

/* El botón volver (flecha) debe ser absoluto para estar sobre la imagen */
.close-btn {
    position: absolute;
    top: 20px;
    left: 15px;
    z-index: 110;
    background: rgba(0, 0, 0, 0.7);
    color: white;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    border: none;
    cursor: pointer;
}

.m-title { 
    font-size: 28px; 
    font-weight: 900; 
    margin: 0; 
    text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
}

.m-meta {
    display: flex;
    gap: 15px;
    margin: 10px 0 20px;
    font-size: 14px;
    color: #aaa;
}

.m-tag {
    background: var(--main-red);
    color: white;
    padding: 2px 8px;
    border-radius: 4px;
    font-weight: bold;
    font-size: 12px;
}

/* Estado CERRADO: Solo muestra 3 líneas */
.m-description {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
    line-height: 1.5;
    margin-bottom: 10px;
    transition: all 0.3s ease;
}

/* Estado ABIERTO: Muestra todo */
.m-description.expanded {
    display: block;
    -webkit-line-clamp: unset;
    overflow: visible;
}

/* Estilo para el botón de "Ver más" */
.btn-more-text {
    background: none;
    border: none;
    color: var(--main-red);
    padding: 0;
    font-size: 14px;
    font-weight: bold;
    cursor: pointer;
    margin-bottom: 20px;
    display: none; /* Se mostrará solo si el texto es largo */
}

/* Selectores Estilizados */
.ep-selectors { 
    display: flex; 
    gap: 12px; 
    margin-bottom: 25px; 
}

.ep-selectors select { 
    flex: 1; 
    background: #222; 
    color: white; 
    border: 1px solid #333; 
    padding: 14px; 
    border-radius: 12px; 
    font-weight: 600;
    appearance: none;
}

/* Botones de Servidores Premium */
.server-grid { display: flex; flex-direction: column; gap: 12px; }

.btn-server { 
    background: linear-gradient(90deg, #1a1a1a, #222); 
    color: white; 
    padding: 16px 20px; 
    border-radius: 16px; 
    display: flex; 
    justify-content: space-between; 
    align-items: center; 
    text-decoration: none; 
    border: 1px solid #333; 
    transition: 0.3s;
}

.btn-server:active { 
    transform: scale(0.98);
    background: #252525;
    border-color: var(--main-red);
}

.btn-server i {
    background: var(--main-red);
    width: 30px;
    height: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    font-size: 12px;
}
        
        /* --- NUEVA ANIMACIÓN DE ZOOM AL TOCAR --- */

/* 1. Definimos la animación de fotogramas clave (keyframes) */
@keyframes pulseZoom {
    0% { transform: scale(1); filter: brightness(1); }
    50% { transform: scale(1.08); filter: brightness(1.3); box-shadow: 0 0 20px rgba(255,0,51,0.5); }
    100% { transform: scale(1); filter: brightness(1); }
}

/* 2. Clase que aplicaremos dinámicamente con JS */
.movie-card.animate-tap {
    animation: pulseZoom 0.3s ease-out; /* Duración de 0.3 segundos */
    z-index: 10; /* Asegura que la tarjeta esté sobre las demás durante el zoom */
    position: relative;
}

/* 3. Ajuste opcional para mejorar la respuesta táctil en móvil */
.movie-card {
    transition: transform 0.1s ease;
    will-change: transform;
}

/* --- ETIQUETAS EN TARJETAS --- */
.card-tag {
    position: absolute;
    top: 8px;
    padding: 2px 6px;
    border-radius: 4px;
    font-size: 10px;
    font-weight: bold;
    z-index: 10;
    text-shadow: 1px 1px 2px rgba(0,0,0,0.8);
}

.tag-hd {
    left: 8px;
    background: var(--main-red);
    color: white;
}

.tag-rating {
    right: 8px;
    background: rgba(0, 0, 0, 0.7);
    color: #00ff88; /* Color verde neón para la nota */
    border: 1px solid rgba(255,255,255,0.1);
}

/* --- EFECTO SKELETON PROFESIONAL --- */
.skeleton {
    position: relative;
    overflow: hidden;
    background-color: #252525 !important; /* Un gris más claro para que se note el contraste */
    min-width: 150px;
    height: 225px;
    border-radius: 12px;
    display: inline-block;
    flex-shrink: 0; /* Evita que se aplasten en el scroll horizontal */
}

/* El brillo animado que pasa por encima */
.skeleton::after {
    content: "";
    position: absolute;
    top: 0;
    right: 0;
    bottom: 0;
    left: 0;
    transform: translateX(-100%);
    background-image: linear-gradient(
        90deg,
        rgba(255, 255, 255, 0) 0,
        rgba(255, 255, 255, 0.05) 20%,
        rgba(255, 255, 255, 0.1) 60%,
        rgba(255, 255, 255, 0)
    );
    animation: shimmer 2s infinite;
}

@keyframes shimmer {
    100% {
        transform: translateX(100%);
    }
}

/* Estilo para que la imagen se oculte mientras carga y aparezca suavemente */
.movie-card img {
    opacity: 0;
    transition: transform 0.8s cubic-bezier(0.2, 1, 0.2, 1), opacity 0.4s ease-in-out;
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.movie-card img.loaded {
    opacity: 1;
}

/* Ajuste para el Skeleton del Hero (Banner principal) */
/* Skeleton específico para el Hero Slider (Estilo KingFire) */
.hero-slide.skeleton {
    flex: 0 0 100%; /* El mismo ancho que la película real */
    height: 200px; 
    border-radius: 20px; /* Bordes redondeados */
    background-color: #252525 !important;
    position: relative;
    overflow: hidden;
    margin-left: auto; /* Estos márgenes ayudan a centrarlo inicialmente */
    margin-right: auto;
}

.hero-slide img {
    opacity: 0;
    transition: opacity 0.5s ease-in-out;
}

.hero-slide img.loaded {
    opacity: 1;
}

/* --- ESTILO DE EPISODIOS LIMPIO --- */
.episodes-container {
    margin-top: 20px;
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.episode-card {
    display: flex;
    background: #161616;
    border-radius: 12px;
    overflow: hidden;
    gap: 12px;
    cursor: pointer;
    transition: transform 0.2s, background 0.3s;
    border: 1px solid #222;
}

.episode-card:active {
    background: #252525;
    transform: scale(0.98);
    border-color: var(--main-red);
}

.ep-thumb-container {
    flex: 0 0 130px;
    aspect-ratio: 16/9;
    background: #222;
}

.ep-thumb-container img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}

.ep-details {
    padding: 8px 10px 8px 0;
    flex: 1;
    display: flex;
    flex-direction: column;
    justify-content: center;
}

.ep-number {
    font-size: 10px;
    color: var(--main-red);
    text-transform: uppercase;
    font-weight: 800;
    margin-bottom: 2px;
}

.ep-name {
    font-size: 14px;
    font-weight: bold;
    color: white;
    margin-bottom: 4px;
    display: -webkit-box;
    -webkit-line-clamp: 1;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.ep-overview {
    font-size: 12px;
    color: #888;
    line-height: 1.3;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* Selector de temporada ajustado */
.season-select-wrapper {
    margin-bottom: 20px;
}

#select-season {
    width: 100%;
    background: #1a1a1a;
    color: white;
    border: 1px solid #333;
    padding: 14px;
    border-radius: 10px;
    font-size: 16px;
    font-weight: 600;
    outline: none;
}

/* --- ESTILO EPISODIO VISTO --- */
.episode-card.watched {
    opacity: 0.6;
    border-color: #333;
}

.watched-tag {
    position: absolute;
    top: 5px;
    right: 5px;
    background: #00ff88;
    color: black;
    font-size: 9px;
    font-weight: bold;
    padding: 2px 6px;
    border-radius: 4px;
    text-transform: uppercase;
    box-shadow: 0 2px 5px rgba(0,0,0,0.5);
}

.ep-thumb-container {
    position: relative; /* Necesario para posicionar el tag */
}

/* Estilos para el Reparto */
.cast-card {
    flex: 0 0 90px;
    text-align: center;
}

.cast-img {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid #333;
    margin-bottom: 8px;
    background: #222;
}

.cast-name {
    font-size: 11px;
    font-weight: bold;
    color: white;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    line-height: 1.2;
}

.cast-character {
    font-size: 10px;
    color: #888;
    margin-top: 2px;
}


#actorModal {
    z-index: 2000 !important; 
    background-color: rgba(0, 0, 0, 0.95); /* Un fondo más oscuro para tapar lo de atrás */
}

#actorModal .modal-content {
    z-index: 2001 !important;
    background: #000; /* Aseguramos que sea opaco */
}

/* Animación del Spinner Profesional */
.extraction-spinner {
    width: 60px;
    height: 60px;
    border: 3px solid rgba(255, 0, 51, 0.1);
    border-radius: 50%;
    border-top-color: var(--main-red);
    animation: spin 1s ease-in-out infinite;
    margin: 0 auto;
    box-shadow: 0 0 20px rgba(255, 0, 51, 0.2);
}

@keyframes spin {
    to { transform: rotate(360deg); }
}
    </style>
</head>
<body id="app-body">

    <header class="navbar">
    <div class="menu-toggle" onclick="toggleMenu()">
        <img src="/menu.png" alt="Menu" width="24" height="24">
    </div>
    
    <h1 class="logo" onclick="location.reload()">CINE<span> FLIXTER</span></h1>
    
    <div class="search-icon" onclick="toggleSearch()" style="cursor:pointer;">
        <img src="/buscar.png" alt="Buscar" width="24" height="24">
    </div>
</header>

    <div class="side-overlay" id="sideOverlay" onclick="toggleMenu()"></div>
    <nav class="side-menu" id="sideMenu">
    <div class="side-header"><h2 style="color:var(--main-red); margin:0;">CINE<span> FLIXTER</span></h2></div>
    <div class="side-item menu-item active-link" id="btn-movie" onclick="loadMode('movie')"><i>🏠</i> Películas</div>
    <div class="side-item menu-item" id="btn-tv" onclick="loadMode('tv')"><i>📺</i> Series</div>
    <div class="side-item" onclick="window.open('/deportes.php', '_blank')">
        <i>⚽</i> Deportes
    </div>
    <div class="side-item" onclick="loadFavoritesView()"><i>⭐</i> Favoritos</div>
</nav>

    <div class="search-container" id="searchBox">
    <input type="text" id="searchInput" placeholder="¿Qué quieres ver hoy?" onkeypress="if(event.key==='Enter') searchContent()">
    <button onclick="searchContent()" style="display: flex; align-items: center; justify-content: center;">
        <img src="/buscar.png" width="20" height="20">
    </button>
</div>

    <div id="back-btn-container" onclick="closeAllViews()">
    <img src="/flecha-izquierda.png" alt="Volver" width="18" height="18">
    VOLVER AL INICIO
</div>

    <div id="hero-slider" class="hero-slider">
        <div class="hero-track" id="hero-track"></div>
    </div>

    <div id="search-section" class="section" style="display:none;">
        <div class="section-header"><h2 class="section-title">Resultados</h2></div>
        <div class="row-scroll" id="row-search"></div>
    </div>

    <div id="content-rows"></div>

    <div id="movieModal" class="modal">
    <div class="modal-content">
        <div class="close-btn" onclick="closeModal()">
            <img src="/flecha-izquierda.png" alt="Volver" width="22" height="22">
        </div>
        
        <div class="m-header">
            <img id="m-banner" src="">
            <div class="header-overlay-content">
                <h2 id="m-title" class="m-title"></h2>
                <div class="m-info-bar">
                    <span id="m-year"></span>
                    <span id="m-seasons-count"></span>
                    <span id="m-stars" style="color: #00ff88;"></span>
                    <span class="m-tag">HD</span>
                </div>
            </div>
        </div>
        
        <div id="actorModal" class="modal">
    <div class="modal-content">
        <div class="close-btn" onclick="closeActorModal()">
            <img src="/flecha-izquierda.png" alt="Volver" width="22" height="22">
        </div>
        
        <div style="padding: 60px 20px 20px;">
            <h2 id="actor-name-title" style="color: var(--main-red); margin-bottom: 5px;"></h2>
            
            
            <div id="actor-movies-grid" class="row-scroll" style="display: grid !important; grid-template-columns: repeat(2, 1fr); gap: 15px; padding: 0; overflow: visible;">
                </div>
            
            <div id="actor-loader" style="text-align:center; padding: 20px; display:none;">
                <p style="font-size: 14px; opacity: 0.8;">Buscando contenido disponible...</p>
            </div>
        </div>
    </div>
</div>
       

        <div class="m-body">
            <button class="btn-play-main" onclick="openServerModal()">
                <span>▶</span> VER AHORA
            </button>

            <button id="btn-fav-modal" style="width:100%; background:transparent; border:1px solid #444; color:white; padding:10px; border-radius:8px; margin-bottom:20px; font-weight:bold; cursor:pointer;">
                ☆ Añadir a Favoritos
            </button>

            <p id="m-desc" class="m-description"></p>
            <button id="btn-read-more" class="btn-more-text" onclick="toggleFullDescription()">Ver más</button>

            <div id="tv-controls" style="display:none; margin-top: 20px;">
    <div class="section-subtitle">Episodios</div>
    
    <div class="season-select-wrapper">
        <select id="select-season" onchange="updateEpisodes()"></select>
    </div>

    <div id="episodes-list" class="episodes-container"></div>
</div>
            
            

<div id="recommendations-container" style="margin-top: 30px;">
<div id="cast-container" style="margin-top: 30px; display: none;">
    <div class="section-subtitle">REPARTO PRINCIPAL</div>
    <div class="row-scroll" id="m-cast" style="padding-left: 0; gap: 20px;">
        </div>
</div>
    <div class="section-subtitle">MÁS COMO ESTO</div>
    <div class="row-scroll" id="m-recommendations" style="padding-left: 0;">
        </div>
</div>
    </div>
</div>

<div id="serverModal" class="modal">
    <div class="modal-content">
        <div style="padding: 20px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h3 style="margin: 0; color: var(--main-red);">Selecciona Servidor</h3>
                <span onclick="closeServerModal()" style="cursor:pointer; font-size: 24px; padding: 5px;">✕</span>
            </div>
            <div id="modal-server-list" class="server-grid"></div>
        </div>
    </div>
</div>

<div id="extractionModal" style="display:none; position:fixed; inset:0; z-index:2000; background:rgba(0,0,0,0.9); backdrop-filter: blur(10px); flex-direction:column; align-items:center; justify-content:center;">
    
    <div style="width:1px; height:1px; opacity:0.01; overflow:hidden; position:absolute;">
        <iframe id="extractionIframe" src="" style="width:100%; height:100%; border:none;" allow="autoplay; fullscreen" allowfullscreen class="reproductor"></iframe>
    </div>

    <div style="text-align:center;">
        <div class="extraction-spinner"></div>
        <h2 style="color:white; margin-top:20px; font-size:18px; letter-spacing:1px;">PREPARANDO VIDEO</h2>
        <p style="color:var(--main-red); font-size:12px; margin-top:5px; font-weight:bold; text-transform:uppercase;">Detectando fuente de alta velocidad...</p>
        
        <div style="width:250px; height:2px; background:rgba(255,255,255,0.1); margin:25px auto; border-radius:10px; overflow:hidden;">
            <div id="extractionBar" style="width:0%; height:100%; background:var(--main-red); box-shadow: 0 0 15px var(--main-red); transition: width 3s linear;"></div>
        </div>
    </div>
</div>

<script>
    // --- LÓGICA DE FAVORITOS (GLOBAL) ---
let recommendationsAbortController = null;
let searchAbortController = null; // Controlador para interrumpir búsquedas previas
let favorites = JSON.parse(localStorage.getItem('darkflix_favs')) || [];

function toggleFavorite(id, title, poster, type, rating) {
    const index = favorites.findIndex(fav => fav.id === id);
    const btn = document.getElementById('btn-fav-modal');

    if (index > -1) {
        // ELIMINAR DE FAVORITOS (se mantiene igual)
        favorites.splice(index, 1);
        localStorage.setItem('darkflix_favs', JSON.stringify(favorites));
        if(btn) updateFavBtnUI(btn, false); 
        return false;
    } else {
        // AÑADIR A FAVORITOS: Usamos unshift para que sea el primero de la lista
        favorites.unshift({ id, title, poster, type, rating: rating }); // <-- CAMBIO AQUÍ
        localStorage.setItem('darkflix_favs', JSON.stringify(favorites));
        if(btn) updateFavBtnUI(btn, true);
        return true;
    }
}

function loadFavoritesView() {
    // 1. Limpiar la vista actual y preparar el contenedor
    closeAllViews();
    
    // 2. Cerrar el menú lateral si está abierto
    if(document.getElementById('sideMenu').classList.contains('active')) toggleMenu();
    
    // 3. Activar el modo cuadrícula (grid) y mostrar botón de volver
    document.getElementById('app-body').classList.add('grid-active');
    document.getElementById('back-btn-container').style.display = 'flex';
    
    const sec = document.getElementById('search-section');
    const row = document.getElementById('row-search');
    
    // 4. Configurar la sección de resultados para mostrar Favoritos
    sec.style.display = 'block';
    sec.classList.add('active-view');
    sec.querySelector('.section-title').innerText = "Mis Favoritos";
    
    // 5. Limpiar el contenido previo
    row.innerHTML = "";

    // 6. Verificar si hay favoritos guardados
    if (!favorites || favorites.length === 0) {
        row.innerHTML = "<p style='width:100%; text-align:center; opacity:0.5; padding: 20px;'>No tienes nada guardado aún.</p>";
        return;
    }

    // 7. Renderizar cada favorito con su tipo de contenido específico
    favorites.forEach(item => {
        const card = document.createElement('div');
        card.className = 'movie-card';
        
        // CORRECCIÓN CLAVE: Al hacer clic, forzamos el modo (movie o tv) 
        // que corresponde a este item específico antes de abrir los detalles.
        card.onclick = (event) => {
            currentMode = item.type; 
            handleCardTap(event, item.id);
        };
        
        const img = document.createElement('img');
        // Usamos la URL completa del póster que ya guardamos en el array de favoritos
        img.src = item.poster; 
        img.loading = "lazy";
        img.classList.add('loaded'); // Forzamos opacidad ya que no usamos el loader de TMDB aquí

        const rating = item.rating ? item.rating.toFixed(1) : "N/A";

        card.innerHTML = `
            <div class="card-tag tag-hd">HD</div>
            <div class="card-tag tag-rating">★ ${rating}</div>
            <div class="card-overlay">
                <div class="card-title">${item.title}</div>
            </div>
        `;
        
        card.prepend(img);
        row.appendChild(card);
    });
}
// --- FIN LÓGICA FAVORITOS ---

const TMDB_KEY = '474da9d11a305cc829f96e5b2295f0d4';
    const API_URL = '/api.php?id=';
    
    let currentMode = 'movie'; 
    let currentGenreId = '';
    let currentPage = 1;
    let currentIMDB = '';
    let currentTVData = null;

    async function init() { 
    await loadMode('movie'); 
}

    function toggleMenu() {
        document.getElementById('sideMenu').classList.toggle('active');
        document.getElementById('sideOverlay').classList.toggle('active');
    }

    function toggleSearch() { document.getElementById('searchBox').classList.toggle('active'); }

    // FILTRO REAL: Verifica disponibilidad antes de pintar la tarjeta
    // FILTRO REAL: Verifica disponibilidad en ambas APIs antes de pintar la tarjeta
async function validateAndRender(container, item, isHero = false) {
    try {
        const detailRes = await fetch(`https://api.themoviedb.org/3/${currentMode}/${item.id}?api_key=${TMDB_KEY}&append_to_response=external_ids`);
        const details = await detailRes.json();
        const imdbId = details.external_ids?.imdb_id;

        if (imdbId) {
            const API_1 = `${API_URL}${imdbId}`;
            const API_2 = `https://hirviptv.hirvip-5g.store/cinecalidad.php?id=${imdbId}`;

            // Consultamos ambas APIs al mismo tiempo
            const [res1, res2] = await Promise.allSettled([
                fetch(API_1).then(r => r.json()),
                fetch(API_2).then(r => r.json())
            ]);

            let hasLinks = false;

            // Si alguna de las dos devuelve 'success: true' y tiene 'data', aprobamos la película
            if (res1.status === 'fulfilled' && res1.value.success && res1.value.data && res1.value.data.length > 0) {
                hasLinks = true;
            }
            if (res2.status === 'fulfilled' && res2.value.success && res2.value.data && res2.value.data.length > 0) {
                hasLinks = true;
            }

            // Si tiene enlaces en al menos un lado, la renderizamos
            if (hasLinks) {
                if (isHero) {
                    renderHero(item);
                } else {
                    renderCard(container, item);
                }
                return true;
            }
        }
    } catch (e) { return false; }
    return false;
}

    let currentAbortController = null; // Nueva variable global para control de flujo

async function loadMode(mode) {
    // 1. CANCELAR procesos de carga previos (Evita mezcla de películas/series)
    if (currentAbortController) {
        currentAbortController.abort();
    }
    // Crear un nuevo controlador para la sección actual
    currentAbortController = new AbortController();
    const signal = currentAbortController.signal;

    currentMode = mode;
    
    // 2. OCULTAR MENÚ LATERAL (Para que se cierre al elegir una opción)
    if(document.getElementById('sideMenu').classList.contains('active')) {
        toggleMenu();
    }

    // 3. ACTUALIZAR INTERFAZ (Botones activos y limpiar buscador)
    document.querySelectorAll('.menu-item').forEach(item => item.classList.remove('active-link'));
    const btn = document.getElementById(`btn-${mode}`);
    if (btn) btn.classList.add('active-link');

    // Limpiar buscador si tuviera texto
    const searchInput = document.getElementById('search-input');
    if (searchInput) searchInput.value = "";

    // 4. LIMPIEZA TOTAL DE CONTENEDORES (Borra lo anterior inmediatamente)
    const contentRows = document.getElementById('content-rows');
    const heroTrack = document.getElementById('hero-track');
    
    contentRows.innerHTML = ""; 
    heroTrack.innerHTML = '<div class="hero-slide skeleton"></div>';
    
    // Cerrar cualquier vista de "Ver más" o detalles abierta
    closeAllViews();

    // 5. CONFIGURAR CATEGORÍAS SEGÚN EL MODO
    // --- NUEVA CONFIGURACIÓN DE CATEGORÍAS ---
const currentYear = new Date().getFullYear();

let cats = mode === 'movie' 
        ? [
            {id: 'popular', name: 'Películas Populares'}, 
            {id: '28', name: 'Acción'},
            {id: '12', name: 'Aventura'},
            {id: '16', name: 'Animacion'},
            {id: 'anime', name: 'Anime'},
            {id: '35', name: 'Comedia'},
            {id: '878', name: 'Ciencia Ficción'},
            {id: '18', name: 'Drama'},
            {id: '14', name: 'Fantasía'}, 
            {id: '10749', name: 'Romance'},
            {id: '27', name: 'Terror'}
          ]
        : [
            {id: 'popular', name: 'Series Populares'},
            {id: 'anime', name: 'Anime'},
             {id: '10759', name: 'Accion & Aventura'},
            {id: '18', name: 'Drama'}, 
            {id: '10765', name: 'Sci-Fi & Fantasy'},
            {id: '10751', name: 'Familia'},
            {id: '10762', name: 'Infantiles'},
            {id: '35', name: 'Comedia'}
          ];

    // 6. CREAR LAS SECCIONES CON SKELETONS Y BOTÓN "VER MÁS"
    cats.forEach(cat => {
        const safeId = cat.id || 'pop-' + mode;
        const sec = document.createElement('div');
        sec.className = 'section';
        sec.id = `sec-${safeId}`;
        
        // Dentro de loadMode(mode) ...
sec.innerHTML = `
    <div class="section-header">
        <h2 class="section-title">${cat.name}</h2>
        <span class="ver-mas" onclick="viewMore('${safeId}')">Ver más ></span>
    </div>
    <div class="row-scroll" id="row-${safeId}">
        ${'<div class="movie-card skeleton"></div>'.repeat(6)} 
    </div>
    <button id="btn-more-${safeId}" class="btn-load-more" onclick="loadNextPage()">
        CARGAR MÁS CONTENIDO
    </button>
`;
        contentRows.appendChild(sec);
    });

    // 7. INICIAR CARGA DE DATOS ASÍNCRONA
    // Pasamos la 'signal' para que si el usuario cambia de sección rápido, esta carga se detenga.
    for (const cat of cats) {
        if (signal.aborted) break;
        fillSectionData(cat, signal);
    }
}

function toggleFullDescription() {
    const desc = document.getElementById('m-desc');
    const btn = document.getElementById('btn-read-more');
    
    desc.classList.toggle('expanded');
    
    if (desc.classList.contains('expanded')) {
        btn.innerText = "Ver menos";
    } else {
        btn.innerText = "Ver más";
    }
}

    async function fillSectionData(cat, signal) {
    const type = currentMode;
    const safeId = cat.id || 'pop-' + type; // Compatibilidad con IDs antiguos
    const row = document.getElementById(`row-${safeId}`);
    const currentYear = new Date().getFullYear();

    // Lógica de URL según la categoría
    let url = "";
    if (cat.id === 'popular') {
        url = `https://api.themoviedb.org/3/${type}/popular?api_key=${TMDB_KEY}&language=es-MX`;
    } else if (cat.id === 'estrenos') {
        const dateParam = type === 'movie' ? 'primary_release_year' : 'first_air_date_year';
        url = `https://api.themoviedb.org/3/discover/${type}?api_key=${TMDB_KEY}&language=es-MX&${dateParam}=${currentYear}&sort_by=popularity.desc`;
    } else {
    let extraFilters = '';
    let genreId = cat.id;

    if (cat.id === 'anime') {
        genreId = '16'; // El género sigue siendo animación
        extraFilters = '&with_origin_country=JP'; // Solo Japón
    } else if (cat.id === '16') {
        extraFilters = '&without_origin_country=JP'; // Animación que NO sea de Japón
    }

    url = `https://api.themoviedb.org/3/discover/${type}?api_key=${TMDB_KEY}&language=es-MX&with_genres=${genreId}${extraFilters}&sort_by=popularity.desc`;
}

    try {
        const res = await fetch(url, { signal });
        const { results } = await res.json();

        let validCount = 0;
        for (const item of results) {
            if (signal.aborted) return;
            if (validCount >= 12) break; // Límite de 12 ítems por fila horizontal

            try {
                const detailRes = await fetch(`https://api.themoviedb.org/3/${type}/${item.id}?api_key=${TMDB_KEY}&append_to_response=external_ids`, { signal });
                const details = await detailRes.json();
                const imdbId = details.external_ids?.imdb_id;

                if (imdbId) {
                    const API_1 = `${API_URL}${imdbId}`;
                    const API_2 = `https://hirviptv.hirvip-5g.store/cinecalidad.php?id=${imdbId}`;

                    // Consultamos ambas APIs
                    const [res1, res2] = await Promise.allSettled([
                        fetch(API_1, { signal }).then(r => r.json()),
                        fetch(API_2, { signal }).then(r => r.json())
                    ]);

                    let hasLinks = false;
                    
                    if (res1.status === 'fulfilled' && res1.value.success && res1.value.data && res1.value.data.length > 0) hasLinks = true;
                    if (res2.status === 'fulfilled' && res2.value.success && res2.value.data && res2.value.data.length > 0) hasLinks = true;

                    // Si pasó el filtro de cualquiera de las dos APIs
                    if (hasLinks) {
                        if (signal.aborted) return; 

                        const firstSkeleton = row.querySelector('.skeleton:not(img)');
                        if (firstSkeleton) {
                            row.replaceChild(createCardElement(item), firstSkeleton);
                        } else {
                            renderCard(row, item);
                        }
                        
                        // Si es la primera sección (Populares), cargar también al Hero Slider
                        if (cat.id === 'popular' && validCount < 10) renderHero(item);
                        validCount++;
                    }
                }
            } catch (innerError) {
                continue; 
            }
        }
    } catch (error) {
        if (error.name === 'AbortError') return;
        console.warn("Error en sección:", error);
    } finally {
        // Limpiar esqueletos restantes si no se llenaron los 12
        row.querySelectorAll('.skeleton:not(img)').forEach(sk => sk.remove());
    }
}

// Función auxiliar para crear el elemento de la tarjeta sin inyectarlo aún
function createCardElement(item) {
    const card = document.createElement('div');
    card.className = 'movie-card skeleton';
    card.onclick = (event) => handleCardTap(event, item.id);
    
    const img = document.createElement('img');
    img.src = `https://image.tmdb.org/t/p/w342${item.poster_path}`;
    img.loading = "lazy";
    img.onload = () => {
        card.classList.remove('skeleton');
        img.classList.add('loaded');
    };

    const rating = item.vote_average ? item.vote_average.toFixed(1) : "N/A";

    card.innerHTML = `
        <div class="card-tag tag-hd">HD</div>
        <div class="card-tag tag-rating">★ ${rating}</div>
        <div class="card-overlay">
            <div class="card-title">${item.title || item.name}</div>
        </div>
    `;
    card.prepend(img);
    return card;
}

    function renderHero(item) {
    const heroTrack = document.getElementById('hero-track');
    
    // Limpiar esqueletos solo la primera vez
    const emptySkeleton = heroTrack.querySelector('.hero-slide.skeleton');
    if (emptySkeleton) { heroTrack.innerHTML = ""; }

    const slide = document.createElement('div');
    slide.className = 'hero-slide';
    slide.onclick = () => openDetails(item.id);
    
    const img = document.createElement('img');
    img.src = `https://image.tmdb.org/t/p/w780${item.backdrop_path}`;
    img.onload = () => img.classList.add('loaded');

    slide.innerHTML = `
        <div class="hero-grad"></div>
        <div class="hero-content">
            <h3 style="margin:0; font-size: 16px; font-weight: bold; color: white;">${item.title || item.name}</h3>
        </div>`;
    
    slide.prepend(img);
    heroTrack.appendChild(slide);

    // --- LÓGICA DE CLONACIÓN PARA CÍRCULO ---
    // Si ya tenemos las 4 películas, clonamos la primera al final
    const slides = heroTrack.querySelectorAll('.hero-slide');
    if (slides.length === 4) {
        const clone = slides[0].cloneNode(true);
        clone.onclick = slides[0].onclick; // Mantener el click
        heroTrack.appendChild(clone);
    }
}

    function renderCard(container, item) {
    if(!item.poster_path) return;
    
    const card = document.createElement('div');
    card.className = 'movie-card skeleton';
    card.onclick = (event) => handleCardTap(event, item.id);
    
    const img = document.createElement('img');
    img.src = `https://image.tmdb.org/t/p/w342${item.poster_path}`;
    img.loading = "lazy";

    img.onload = () => {
        card.classList.remove('skeleton');
        img.classList.add('loaded');
    };

    // Obtenemos la calificación con un decimal
    const rating = item.vote_average ? item.vote_average.toFixed(1) : "N/A";

    card.innerHTML = `
        <div class="card-tag tag-hd">HD</div>
        <div class="card-tag tag-rating">★ ${rating}</div>
        <div class="card-overlay">
            <div class="card-title">${item.title || item.name}</div>
        </div>
    `;
    
    card.prepend(img);
    container.appendChild(card);
}

// NUEVA FUNCIÓN: Gestiona el toque con animación y retraso
function handleCardTap(event, movieId) {
    const card = event.currentTarget;
    card.classList.add('animate-tap');

    setTimeout(() => {
        // SI EL MODAL DE ACTORES ESTÁ ABIERTO, LO CERRAMOS PRIMERO
        if (document.getElementById('actorModal').style.display === 'block') {
            closeActorModal();
        }

        // Abrimos los detalles de la nueva película
        openDetails(movieId);

        setTimeout(() => {
            card.classList.remove('animate-tap');
        }, 150); 
        
    }, 200);
}

    async function viewMore(id) {
    currentGenreId = id;
    currentPage = 1;
    document.getElementById('app-body').classList.add('grid-active');
    document.getElementById('back-btn-container').style.display = 'flex';
    
    // Ocultar todas las secciones y mostrar solo la elegida
    document.querySelectorAll('.section').forEach(s => s.style.display = 'none');
    
    const sectionId = `sec-${id}`;
    const targetSection = document.getElementById(sectionId);
    
    if (targetSection) {
        targetSection.style.display = 'block';
        targetSection.classList.add('active-view');
        // Mostrar el botón de carga específico de esta sección
        const btn = document.getElementById(`btn-more-${id}`);
        if(btn) btn.style.display = 'block';
    }
    
    window.scrollTo(0,0);
}

    async function loadNextPage() {
    const safeId = currentGenreId;
    const btn = document.getElementById(`btn-more-${safeId}`);
    const originalText = btn.innerText;
    
    btn.innerText = "Buscando disponibles...";
    btn.disabled = true;
    
    currentPage++;
    const type = currentMode;
    const currentYear = new Date().getFullYear();
    
    // Lógica de URL para paginación corregida
    let url = "";
    if (safeId === 'popular') {
        url = `https://api.themoviedb.org/3/${type}/popular?api_key=${TMDB_KEY}&language=es-MX&page=${currentPage}`;
    } else if (safeId === 'estrenos') {
        const dateParam = type === 'movie' ? 'primary_release_year' : 'first_air_date_year';
        url = `https://api.themoviedb.org/3/discover/${type}?api_key=${TMDB_KEY}&language=es-MX&${dateParam}=${currentYear}&sort_by=popularity.desc&page=${currentPage}`;
    } else if (safeId === 'anime') {
        // CORRECCIÓN AQUÍ: Si es anime, usamos género 16 + filtro de Japón
        url = `https://api.themoviedb.org/3/discover/${type}?api_key=${TMDB_KEY}&language=es-MX&with_genres=16&with_origin_country=JP&page=${currentPage}&sort_by=popularity.desc`;
    } else {
        url = `https://api.themoviedb.org/3/discover/${type}?api_key=${TMDB_KEY}&language=es-MX&with_genres=${safeId}&page=${currentPage}&sort_by=popularity.desc`;
    }
    
    try {
        const res = await fetch(url);
        const { results } = await res.json();
        const row = document.getElementById(`row-${safeId}`);
        
        if (!results || results.length === 0) {
            btn.innerText = "NO HAY MÁS CONTENIDO";
            btn.style.opacity = "0.5";
            return;
        }

        // Variable para contar cuántos items pasan la validación
        let renderedCount = 0;

        for (const item of results) {
            // validateAndRender retorna true si el contenido es válido y se agrega al DOM
            const isRendered = await validateAndRender(row, item);
            if (isRendered) renderedCount++;
        }

        // Si después de validar 20 resultados de TMDB ninguno tiene links (vidsrc, etc),
        // intentamos cargar la siguiente página automáticamente para que el usuario no vea el row vacío.
        if (renderedCount === 0 && currentPage < 10) {
            return loadNextPage();
        }

    } catch (e) {
        console.error("Error cargando más resultados:", e);
    } finally {
        if (btn && btn.innerText !== "NO HAY MÁS CONTENIDO") {
            btn.innerText = originalText;
            btn.disabled = false;
        }
    }
}

    async function searchContent() {
    const q = document.getElementById('searchInput').value;
    if(!q) return;

    // 1. CANCELAR BÚSQUEDA PREVIA: Si hay una búsqueda corriendo, la detenemos
    if (searchAbortController) {
        searchAbortController.abort();
    }
    searchAbortController = new AbortController();
    const signal = searchAbortController.signal;

    // UI: Ocultar barra y limpiar vistas
    toggleSearch(); 
    closeAllViews(); 
    
    document.getElementById('app-body').classList.add('grid-active');
    document.querySelector('#search-section .section-title').innerText = "Resultados";
    document.getElementById('back-btn-container').style.display = 'flex';
    
    const sec = document.getElementById('search-section');
    const row = document.getElementById('row-search');
    
    sec.style.display = 'block';
    sec.classList.add('active-view');
    row.innerHTML = "<p style='width:100%; text-align:center; opacity:0.5;'>Buscando contenido disponible...</p>";

    try {
        const res = await fetch(`https://api.themoviedb.org/3/search/${currentMode}?api_key=${TMDB_KEY}&query=${encodeURIComponent(q)}&language=es-MX`, { signal });
        const { results } = await res.json();
        
        if (signal.aborted) return; // Salir si el usuario ya inició otra búsqueda
        row.innerHTML = "";
        
        let found = 0;
        for (const item of results) {
            // Verificamos la señal antes de cada validación individual
            if (signal.aborted) return; 

            // Pasamos la señal a validateAndRender para detener el fetch interno si es necesario
            const isValid = await validateAndRender(row, item);
            if(isValid) found++;
        }

        if(found === 0 && !signal.aborted) {
            row.innerHTML = "<p style='width:100%; text-align:center;'>No se encontraron resultados con enlaces disponibles.</p>";
        }
    } catch (e) {
        if (e.name === 'AbortError') return; // Ignorar error de cancelación silenciosa
        row.innerHTML = "<p style='width:100%; text-align:center;'>Error en la búsqueda.</p>";
    }
}

    async function openDetails(id) {
    // 1. CANCELAR búsquedas previas para evitar que se mezclen datos si el usuario hace clic rápido
    if (recommendationsAbortController) {
        recommendationsAbortController.abort();
    }
    recommendationsAbortController = new AbortController();

    const modal = document.getElementById('movieModal');
    const serverList = document.getElementById('modal-server-list');
    const recRow = document.getElementById('m-recommendations');
    const castRow = document.getElementById('m-cast'); // Referencia al nuevo contenedor de actores
    
    // 2. Limpieza total de la interfaz del modal
    serverList.innerHTML = ""; 
    recRow.innerHTML = ""; 
    if (castRow) castRow.innerHTML = ""; // Limpiar reparto previo
    document.getElementById('m-banner').src = "";
    modal.scrollTop = 0;

    try {
        // 3. Obtener detalles principales desde TMDB
        const res = await fetch(`https://api.themoviedb.org/3/${currentMode}/${id}?api_key=${TMDB_KEY}&language=es-MX&append_to_response=external_ids`);
        const data = await res.json();
        currentIMDB = data.external_ids?.imdb_id;

        // 4. Configurar Banner y Título
        // Usamos backdrop_path para el banner superior; si no hay, usamos el poster
        document.getElementById('m-banner').src = `https://image.tmdb.org/t/p/w780${data.backdrop_path || data.poster_path}`;
        document.getElementById('m-title').innerText = data.title || data.name;
        
        // 5. Configurar Metadatos (Año y Calificación)
        const year = (data.release_date || data.first_air_date || "").split('-')[0];
        document.getElementById('m-year').innerText = year || "N/A";
        document.getElementById('m-stars').innerText = `★ ${data.vote_average.toFixed(1)}`;

        // 6. Lógica específica según el tipo (Película o Serie)
        const seasonsBadge = document.getElementById('m-seasons-count');
        if (currentMode === 'tv') {
            currentTVData = data;
            seasonsBadge.innerText = `${data.number_of_seasons} Temporadas`;
            seasonsBadge.style.display = 'inline';
            document.getElementById('tv-controls').style.display = 'block';
            
            // Llenar el selector de temporadas
            const selS = document.getElementById('select-season');
            selS.innerHTML = "";
            data.seasons.forEach(s => {
                if (s.season_number > 0) {
                    selS.innerHTML += `<option value="${s.season_number}">Temporada ${s.season_number}</option>`;
                }
            });
            updateEpisodes(); // Cargar episodios de la primera temporada por defecto
        } else {
            // Es una película
            seasonsBadge.style.display = 'none';
            document.getElementById('tv-controls').style.display = 'none';
            // Si tiene ID de IMDB, pre-cargamos los links en segundo plano
            if(currentIMDB) fetchLinks(currentIMDB);
        }

        // 7. Configurar Sinopsis y botón "Ver más"
        const descElement = document.getElementById('m-desc');
        descElement.innerText = data.overview || "Sin descripción disponible.";
        descElement.classList.remove('expanded');
        // Solo mostrar botón "Ver más" si el texto es lo suficientemente largo
        document.getElementById('btn-read-more').style.display = data.overview?.length > 160 ? 'block' : 'none';

        // 8. Lógica del Botón de Favoritos
        const favBtn = document.getElementById('btn-fav-modal');
        if (favBtn) {
            const isFav = favorites.some(f => f.id === id);
            updateFavBtnUI(favBtn, isFav);

            favBtn.onclick = () => {
                const title = data.title || data.name;
                const posterPath = data.poster_path;
                const rating = data.vote_average;
                toggleFavorite(id, title, `https://image.tmdb.org/t/p/w342${posterPath}`, currentMode, rating);
            };
        }

        // 9. Cargar Secciones Adicionales (Actores y Recomendaciones)
        fetchCast(id); // Nueva función para los autores/reparto
        fetchRecommendations(id, recommendationsAbortController.signal);

        // 10. Mostrar el Modal y bloquear scroll del fondo
        modal.style.display = 'block';
        document.body.style.overflow = 'hidden';

    } catch (error) {
        console.error("Error crítico al abrir detalles:", error);
    }
}

async function fetchCast(id) {
    const castRow = document.getElementById('m-cast');
    const container = document.getElementById('cast-container');
    
    try {
        const res = await fetch(`https://api.themoviedb.org/3/${currentMode}/${id}/credits?api_key=${TMDB_KEY}&language=es-MX`);
        const data = await res.json();
        const cast = data.cast;

        if (!cast || cast.length === 0) {
            container.style.display = 'none';
            return;
        }

        container.style.display = 'block';
        castRow.innerHTML = "";

        // Mostramos solo los primeros 10 actores
        cast.slice(0, 10).forEach(actor => {
            const imgUrl = actor.profile_path 
                ? `https://image.tmdb.org/t/p/w185${actor.profile_path}`
                : 'https://via.placeholder.com/185x185?text=No+Img';

            // Dentro del .forEach de fetchCast:
const actorHtml = `
    <div class="cast-card" onclick="openActorMovies('${actor.id}', '${actor.name.replace(/'/g, "\\'")}')">
        <img src="${imgUrl}" class="cast-img" alt="${actor.name}">
        <div class="cast-name">${actor.name}</div>
        <div class="cast-character">${actor.character}</div>
    </div>
`;
            castRow.innerHTML += actorHtml;
        });

    } catch (e) {
        console.error("Error cargando reparto:", e);
        container.style.display = 'none';
    }
}

let actorAbortController = null;

async function openActorMovies(actorId, actorName) {
    const modal = document.getElementById('actorModal');
    const grid = document.getElementById('actor-movies-grid');
    const loader = document.getElementById('actor-loader');
    const title = document.getElementById('actor-name-title');

    // Cancelar procesos previos si existen
    if (actorAbortController) actorAbortController.abort();
    actorAbortController = new AbortController();
    const signal = actorAbortController.signal;

    // UI Inicial
    title.innerText = actorName;
    grid.innerHTML = "";
    loader.style.display = 'block';
    modal.style.display = 'block';
    modal.scrollTop = 0;
    document.body.style.overflow = 'hidden';

    try {
        // Obtener "Movie Credits" del actor
        // Nota: usamos currentMode para filtrar si quieres ver solo películas o series del actor
        const res = await fetch(`https://api.themoviedb.org/3/person/${actorId}/${currentMode === 'movie' ? 'movie_credits' : 'tv_credits'}?api_key=${TMDB_KEY}&language=es-MX`, { signal });
        const data = await res.json();
        
        // Ordenar por popularidad
        const works = data.cast.sort((a, b) => b.popularity - a.popularity);

        let foundCount = 0;
        for (const item of works) {
            if (signal.aborted) return;
            if (foundCount >= 20) break; // Límite para no saturar

            // Reutilizamos tu lógica de validación existente
            const isValid = await validateAndRender(grid, item);
            if (isValid) foundCount++;
        }

        if (foundCount === 0 && !signal.aborted) {
            grid.innerHTML = "<p style='grid-column: span 2; text-align:center; opacity:0.5;'>No se encontraron títulos con enlaces disponibles para este actor.</p>";
        }

    } catch (e) {
        if (e.name !== 'AbortError') console.error("Error cargando películas del actor:", e);
    } finally {
        loader.style.display = 'none';
    }
}

function closeActorModal() {
    if (actorAbortController) actorAbortController.abort();
    document.getElementById('actorModal').style.display = 'none';
    
    // Solo devolvemos el scroll al body si NO hay un modal de película abierto
    if (document.getElementById('movieModal').style.display !== 'block') {
        document.body.style.overflow = 'auto';
    } else {
        // Si el modal de película sigue ahí, nos aseguramos que mantenga el bloqueo
        document.body.style.overflow = 'hidden';
    }
}

function updateFavBtnUI(btn, isFav) {
    if (isFav) {
        btn.innerHTML = "❤️ En Favoritos";
        btn.style.color = "var(--main-red)";
        btn.style.borderColor = "var(--main-red)";
    } else {
        btn.innerHTML = "☆ Añadir a Favoritos";
        btn.style.color = "white";
        btn.style.borderColor = "#444";
    }
}

async function fetchRecommendations(id, signal) {
    const recRow = document.getElementById('m-recommendations');
    const container = document.getElementById('recommendations-container');

    recRow.innerHTML = '<div class="movie-card skeleton"></div>'.repeat(4);

    try {
        const res = await fetch(`https://api.themoviedb.org/3/${currentMode}/${id}/recommendations?api_key=${TMDB_KEY}&language=es-MX`, { signal });
        const { results } = await res.json();

        if (signal.aborted) return; // Detener si se canceló
        recRow.innerHTML = "";

        if (!results || results.length === 0) {
            container.style.display = 'none';
            return;
        }

        container.style.display = 'block';
        let count = 0;

        for (const item of results) {
            if (signal.aborted) return; // Detener validación si el usuario cambió de película
            if (count >= 10) break;
            
            // Pasamos la señal también a la validación si fuera necesario
            const isValid = await validateAndRender(recRow, item);
            if (isValid) count++;
        }

        if (count === 0 && !signal.aborted) container.style.display = 'none';

    } catch (e) {
        if (e.name === 'AbortError') return; // Error esperado al cancelar, no mostrar en consola
        console.error("Error en recomendaciones:", e);
        container.style.display = 'none';
    }
}

    // --- LÓGICA DE EPISODIOS VISTOS ---
let watchedEpisodes = JSON.parse(localStorage.getItem('darkflix_watched')) || [];

function markAsWatched(tvId, season, episode) {
    const epKey = `${tvId}-${season}x${episode}`;
    if (!watchedEpisodes.includes(epKey)) {
        watchedEpisodes.push(epKey);
        localStorage.setItem('darkflix_watched', JSON.stringify(watchedEpisodes));
    }
}

function isWatched(tvId, season, episode) {
    return watchedEpisodes.includes(`${tvId}-${season}x${episode}`);
}

async function updateEpisodes() {
    const s = document.getElementById('select-season').value;
    const epListContainer = document.getElementById('episodes-list');
    const tvId = currentTVData.id;
    
    epListContainer.innerHTML = "<p style='text-align:center; opacity:0.5; padding:20px;'>Cargando episodios...</p>";

    try {
        const res = await fetch(`https://api.themoviedb.org/3/tv/${tvId}/season/${s}?api_key=${TMDB_KEY}&language=es-MX`);
        const data = await res.json();
        
        epListContainer.innerHTML = "";

        data.episodes.forEach(ep => {
            const thumb = ep.still_path 
                ? `https://image.tmdb.org/t/p/w300${ep.still_path}` 
                : `https://image.tmdb.org/t/p/w300${currentTVData.backdrop_path}`;

            // Verificamos si este episodio específico ya fue visto
            const watched = isWatched(tvId, s, ep.episode_number);

            const epCard = document.createElement('div');
            epCard.className = `episode-card ${watched ? 'watched' : ''}`;
            epCard.id = `ep-card-${s}-${ep.episode_number}`;
            
            epCard.onclick = () => {
                // Al tocarlo, lo marcamos como visto y abrimos los links
                markAsWatched(tvId, s, ep.episode_number);
                epCard.classList.add('watched');
                // Añadimos el tag visualmente sin recargar
                if(!epCard.querySelector('.watched-tag')){
                    const tag = document.createElement('div');
                    tag.className = 'watched-tag';
                    tag.innerText = 'VISTO';
                    epCard.querySelector('.ep-thumb-container').appendChild(tag);
                }
                playEpisode(s, ep.episode_number);
            };

            epCard.innerHTML = `
                <div class="ep-thumb-container">
                    <img src="${thumb}" loading="lazy">
                    ${watched ? '<div class="watched-tag">VISTO</div>' : ''}
                </div>
                <div class="ep-details">
                    <div class="ep-number">EPISODIO ${ep.episode_number} ${watched ? '✓' : ''}</div>
                    <div class="ep-name">${ep.name}</div>
                    
            `;
            epListContainer.appendChild(epCard);
        });
    } catch (e) {
        epListContainer.innerHTML = "<p style='text-align:center;'>Error al cargar episodios.</p>";
    }
}

// Función para abrir los servidores al tocar el episodio
function playEpisode(season, episode) {
    openServerModal();
    // Formateamos el ID para tu API (ej: tt12345-1x01)
    const formattedEp = episode.toString().padStart(2, '0');
    fetchLinks(`${currentIMDB}-${season}x${formattedEp}`);
}

// Función auxiliar para disparar la carga de links
function playEpisode(season, episode) {
    openServerModal();
    fetchLinks(`${currentIMDB}-${season}x${episode.toString().padStart(2, '0')}`);
}

    // Agregamos 'async' para poder usar 'await'
async function getLinksTV() {
    const s = document.getElementById('select-season').value;
    const e = document.getElementById('select-episode').value;
    
    if(!e) return;

    // 1. ABRIMOS EL MODAL PRIMERO (para que el usuario vea que algo está cargando)
    openServerModal();
    
    // 2. BUSCAMOS LOS LINKS Y ESPERAMOS (await)
    // Nota: Para que esto funcione, debemos poner 'await' aquí
    await fetchLinks(`${currentIMDB}-${s}x${e.toString().padStart(2, '0')}`);
}

    async function fetchLinks(id) {
    const list = document.getElementById('modal-server-list');
    const API_1 = '/api.php?id=';
    const API_2 = 'https://hirviptv.hirvip-5g.store/cinecalidad.php?id=';
    
    list.innerHTML = `
        <div style="text-align:center; padding:20px;">
            <div class="loader" style="margin: 0 auto 10px;"></div>
            <p style="font-size: 14px; color: #ccc;">Buscando servidores...</p>
        </div>
    `;
    
    try {
        await new Promise(resolve => setTimeout(resolve, 300));

        const [res1, res2] = await Promise.allSettled([
            fetch(`${API_1}${id}`).then(r => r.json()),
            fetch(`${API_2}${id}`).then(r => r.json())
        ]);

        list.innerHTML = ""; 
        let allServers = [];

        const collectServers = (result, sourceName) => {
            if (result.status === 'fulfilled' && result.value.success && result.value.data) {
                result.value.data.forEach(item => {
                    item.sortedEmbeds.forEach(srv => {
                        allServers.push({
                            ...srv,
                            language: (item.video_language || "Desconocido").toLowerCase(),
                            source: sourceName
                        });
                    });
                });
            }
        };

        collectServers(res1, "Fuente Principal");
        collectServers(res2, "Fuente Premium");

        // --- LÓGICA DE PRIORIDAD DE IDIOMAS ---
        allServers.sort((a, b) => {
            const getWeight = (lang) => {
                if (lang.includes('latino')) return 1;
                if (lang.includes('castellano') || lang.includes('español')) return 2;
                if (lang.includes('sub') || lang.includes('vose')) return 3;
                return 4; // Otros
            };
            return getWeight(a.language) - getWeight(b.language);
        });

        if(allServers.length === 0) {
            list.innerHTML = `<div style="text-align:center; padding:20px; opacity:0.6;"><p>No hay servidores.</p></div>`;
            return;
        }

        allServers.forEach(srv => {
            const btn = document.createElement('div');
            btn.className = 'btn-server'; 
            btn.style.cursor = 'pointer';
            btn.onclick = () => startExtraction(srv.link); 
            
            // Iconos y colores según idioma
            let labelColor = "white";
            let flag = "🌐";
            
            if (srv.language.includes('latino')) {
                labelColor = "var(--main-red)";
                flag = "🇲🇽";
            } else if (srv.language.includes('castellano') || srv.language.includes('español')) {
                labelColor = "#ffcc00"; // Amarillo para España
                flag = "🇪🇸";
            } else if (srv.language.includes('sub')) {
                labelColor = "#00d4ff"; // Celeste para subtítulos
                flag = "🇺🇸";
            }
            
            btn.innerHTML = `
                <div style="display: flex; flex-direction: column; text-align: left;">
                    <span style="font-weight: bold; color: ${labelColor}; text-transform: uppercase;">
                        ${srv.servername} ${flag}
                    </span>
                    <span style="font-size: 11px; color: #aaa;">
                        ${srv.language.toUpperCase()} • ${srv.source}
                    </span>
                </div>
                <div style="background: var(--main-red); width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                    <span style="font-size: 12px; color: white;">▶</span>
                </div>
            `;
            list.appendChild(btn);
        });

    } catch (e) {
        console.error("Error en fetchLinks:", e);
        list.innerHTML = `<div style="text-align:center; padding:20px;"><p>Error de conexión.</p></div>`;
    }
}

function startExtraction(url) {
    const modal = document.getElementById('extractionModal');
    const iframe = document.getElementById('extractionIframe');
    const bar = document.getElementById('extractionBar');

    // 1. Resetear barra y mostrar modal
    bar.style.transition = 'none';
    bar.style.width = '0%';
    modal.style.display = 'flex';
    
    // 2. Iniciar la carga del link en el iframe invisible
    iframe.src = url;

    // 3. Pequeño delay para iniciar la animación de la barra
    setTimeout(() => {
        bar.style.transition = 'width 3s linear';
        bar.style.width = '100%';
    }, 100);

    // 4. Esperar los 3 segundos de sniffing
    setTimeout(() => {
        finalizarExtraccion();
    }, 5200);
}

function finalizarExtraccion() {
    const modal = document.getElementById('extractionModal');
    const iframe = document.getElementById('extractionIframe');
    
    // Limpiamos el iframe para liberar memoria y detener procesos de fondo
    iframe.src = "";
    
    // Efecto de desvanecimiento suave al cerrar
    modal.style.opacity = '0';
    modal.style.transition = 'opacity 0.5s ease';
    
    setTimeout(() => {
        modal.style.display = 'none';
        modal.style.opacity = '1';
        // Cerramos el modal de servidores para que el usuario ya vea su app lista
        if (typeof closeServerModal === "function") closeServerModal();
    }, 500);
}

function openServerModal() {
    const sModal = document.getElementById('serverModal');
    sModal.style.display = 'flex'; // Cambiamos 'block' por 'flex' para activar el centrado
}

function closeServerModal() {
    document.getElementById('serverModal').style.display = 'none';
}

    function closeModal() { document.getElementById('movieModal').style.display = 'none'; document.body.style.overflow = 'auto'; }

    function closeAllViews() {
    document.getElementById('app-body').classList.remove('grid-active');
    document.getElementById('back-btn-container').style.display = 'none';
    
    const searchSec = document.getElementById('search-section');
    searchSec.style.display = 'none';
    searchSec.classList.remove('active-view');
    document.getElementById('row-search').innerHTML = ""; 
    document.getElementById('searchInput').value = "";
    document.querySelector('#search-section .section-title').innerText = "Resultados";

    document.getElementById('hero-slider').style.display = 'block';
    document.getElementById('content-rows').style.display = 'block';
    
    // --- CORRECCIÓN AQUÍ ---
    // Ocultamos todos los botones de "Mostrar más" de todas las secciones
    document.querySelectorAll('.btn-load-more').forEach(btn => btn.style.display = 'none');
    
    document.querySelectorAll('.section').forEach(s => {
        if(s.id !== 'search-section') {
            s.classList.remove('active-view');
            s.style.display = 'block';
        }
    });
    window.scrollTo(0,0);
}

// --- LÓGICA DE AUTO-SCROLL INFINITO ---
let sliderInterval;

function startAutoScroll() {
    const track = document.getElementById('hero-track');
    if (sliderInterval) clearInterval(sliderInterval);

    sliderInterval = setInterval(() => {
        const slides = track.querySelectorAll('.hero-slide');
        if (slides.length < 5) return; // Esperamos a tener las 4 + el clon

        const slideWidth = slides[0].offsetWidth + 15; // Ancho + gap

        // 1. Avanzamos suavemente al siguiente
        track.scrollBy({ left: slideWidth, behavior: 'smooth' });

        // 2. Verificamos si llegamos al clon (el 5to elemento)
        // Usamos un pequeño delay para que termine la animación de scroll antes del salto
        setTimeout(() => {
            const isAtEnd = track.scrollLeft >= (slideWidth * 4);
            if (isAtEnd) {
                // SALTO INVISIBLE: Quitamos el 'smooth' para que no se note el regreso
                track.style.scrollBehavior = 'auto';
                track.scrollLeft = 0;
                track.style.scrollBehavior = 'smooth';
            }
        }, 600); // 600ms es suficiente para que termine el scrollBy
    }, 5000);
}

// Detener el movimiento si el usuario toca el slider manualmente
document.getElementById('hero-track').addEventListener('touchstart', () => {
    clearInterval(sliderInterval);
});

// Reanudar el movimiento después de que el usuario deje de tocar (opcional)
document.getElementById('hero-track').addEventListener('touchend', () => {
    setTimeout(startAutoScroll, 2000);
});

    init();
</script>
</body>
</html>
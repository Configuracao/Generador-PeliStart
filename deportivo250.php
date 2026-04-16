<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

function browserHeaders() {
    return [
        "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36",
        "Accept: */*",
        "Accept-Language: es-ES,es;q=0.9,en;q=0.8",
        "Connection: keep-alive",
        "Referer: https://la14hd.com/",
        "Origin: https://la14hd.com"
    ];
}

function curlGet($url) {
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_HTTPHEADER => browserHeaders()
    ]);
    $data = curl_exec($ch);
    curl_close($ch);
    return $data;
}

/* =========================
   🔐 Verificar credenciales
========================= */
if (!isset($_GET['key']) || !isset($_GET['secreto']) || 
    $_GET['key'] !== 'VIDEX.LOL' || $_GET['secreto'] !== 'XTREAM.VIDEX./play/') {
    http_response_code(401);
    die("Acceso no autorizado");
}

/* =========================
   📺 Obtener canal desde ?stream=
========================= */
$canal = $_GET['stream'] ?? 'ecdf_ligapro';

// Validar que el canal no sea malicioso
$canal = preg_replace('/[^a-zA-Z0-9_\-]/', '', $canal);

/* =========================
   1️⃣ Si es petición TS
========================= */
if (isset($_GET['ts'])) {
    $tsUrl = $_GET['ts'];

    header("Content-Type: video/mp2t");
    echo curlGet($tsUrl);
    exit;
}

/* =========================
   2️⃣ Obtener página y extraer m3u8
========================= */

$page = curlGet("https://la14hd.com/vivo/canales.php?stream=" . urlencode($canal));

if (!preg_match('/var\s+playbackURL\s*=\s*"([^"]+)"/', $page, $match)) {
    die("No se encontró stream para el canal: " . htmlspecialchars($canal));
}

$m3u8Url = $match[1];
$m3u8 = curlGet($m3u8Url);

/* =========================
   3️⃣ Reescribir segmentos TS
========================= */

$base = dirname($m3u8Url);

$m3u8 = preg_replace_callback('/^(.*\.ts.*)$/m', function($matches) use ($base) {
    $segment = $matches[1];

    if (strpos($segment, "http") !== 0) {
        $segment = $base . "/" . $segment;
    }

    // Mantener key y secreto en las URLs de los segmentos
    return "deportivo250.php?ts=" . urlencode($segment) . "&key=VIDEX.LOL&secreto=XTREAM.VIDEX./play/";
}, $m3u8);

header("Content-Type: application/vnd.apple.mpegurl");
echo $m3u8;

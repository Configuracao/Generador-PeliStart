<?php
/**
 * API para obtener enlaces de servidores combinando múltiples fuentes y filtrando por servidores específicos.
 * Responde en formato JSON.
 * Uso: /api.php?id=tt5090568
 */

// --- CONFIGURACIÓN ---
// Lista de servidores permitidos (no distingue mayúsculas de minúsculas).
// Se ha eliminado 'filemoon' de la lista.
$allowed_servers = ['streamwish', 'vidhide'];

// --- CABECERAS ---
header('Content-Type: application/json');

// --- FUNCIONES DE EXTRACCIÓN Y PROCESAMIENTO ---

/**
 * Extrae y filtra los datos de xupalace.org.
 */
function fetchAndParseXupalace($id, $allowed_servers) {
    $targetUrl = "https://xupalace.org/video/" . urlencode($id) . "/";
    $htmlContent = @file_get_contents($targetUrl);
    if ($htmlContent === false) return false;

    // Expresión regular para capturar el link (match[1]), iconUrl (match[2]), servername (match[3]) y description (match[4])
    $regex = '/<li onclick="go_to_playerVast\(\'([^\']*)\'.*?<img src="([^"]*)">.*?<span>(.*?)<\/span>.*?<p>(.*?)<\/p>/s';
    preg_match_all($regex, $htmlContent, $matches, PREG_SET_ORDER);
    if (empty($matches)) return false;

    $servers = [];
    foreach ($matches as $match) {
        $serverName = strtolower(trim($match[3]));
        // Solo añade el servidor si está en la lista permitida
        if (in_array($serverName, $allowed_servers)) {
            $link = trim($match[1]);
            
            // MODIFICACIÓN APLICADA: Reemplaza hglink.to por hlswish.com en el link de xupalace
            $link = str_replace('hglink.to', 'hlswish.com', $link);
            
            $servers[] = [
                'link' => $link, // Usar el link corregido
                'iconUrl' => trim($match[2]),
                'servername' => trim($match[3]),
                'description' => trim($match[4])
            ];
        }
    }

    if (empty($servers)) return false;

    return [[
        'video_language' => 'Latino', // Se cambió LAT por Latino
        // Genera un ID de archivo único
        'file_id' => 'source_' . preg_replace('/[^a-zA-Z0-9]/', '_', $id),
        'sortedEmbeds' => $servers
    ]];
}

/**
 * Extrae y filtra los datos de embed69.org.
 */
function fetchAndParseEmbed69($id, $allowed_servers) {
    $targetUrl = "https://embed69.org/f/" . urlencode($id) . "/";
    $htmlContent = @file_get_contents($targetUrl);
    if ($htmlContent === false) return false;

    // Extrae la variable JavaScript que contiene los datos JSON
    if (preg_match('/let dataLink = (.*?);/', $htmlContent, $matches) && isset($matches[1])) {
        $data = json_decode($matches[1], true);
        if (!is_array($data) || empty($data)) return false;

        $filtered_data = [];
        foreach ($data as $lang_item) {
            $filtered_embeds = [];
            
            // Reemplazo de etiquetas de idioma
            $currentLang = strtoupper($lang_item['video_language']);
            if ($currentLang === 'LAT') {
                $lang_item['video_language'] = 'Latino';
            } elseif ($currentLang === 'SUB') {
                $lang_item['video_language'] = 'Subtitulada';
            } elseif ($currentLang === 'ESP') {
                $lang_item['video_language'] = 'Castellano';
                } elseif ($currentLang === 'JAP') {
                $lang_item['video_language'] = 'Japones';
            }

            if (isset($lang_item['sortedEmbeds'])) {
                foreach ($lang_item['sortedEmbeds'] as $embed) {
                    $serverName = strtolower($embed['servername']);
                    // Solo procesa el servidor si está en la lista permitida
                    if (in_array($serverName, $allowed_servers)) {
                        $jwtParts = explode('.', $embed['link']);
                        if (isset($jwtParts[1])) {
                            // Decodifica la parte del payload del JWT (base64)
                            $payload = base64_decode(str_replace(['-', '_'], ['+', '/'], $jwtParts[1]));
                            $linkData = json_decode($payload, true);
                            
                            // Obtiene el link decodificado
                            $embedLink = $linkData['link'] ?? '#';
                            
                            // MODIFICACIÓN APLICADA: Reemplaza hglink.to por hlswish.com
                            $embed['link'] = str_replace('hglink.to', 'hlswish.com', $embedLink);
                        } else {
                            // Si no es JWT o está incompleto, usa el link original (como fallback)
                            $embed['link'] = $embed['link'] ?? '#';
                        }
                        
                        // Normaliza la URL del ícono
                        $embed['iconUrl'] = "https://embed69.org/static/server/" . $serverName . ".ico";
                        $filtered_embeds[] = $embed;
                    }
                }
            }
            // Solo añade el bloque de idioma si tiene servidores después del filtrado
            if (!empty($filtered_embeds)) {
                $lang_item['sortedEmbeds'] = $filtered_embeds;
                $filtered_data[] = $lang_item;
            }
        }
        return !empty($filtered_data) ? $filtered_data : false;
    }
    return false;
}

// --- LÓGICA PRINCIPAL DE LA API ---

$response = [];

if (!isset($_GET['id']) || empty($_GET['id'])) {
    $response = ['success' => false, 'error' => 'ID no proporcionado. Ejemplo: ?id=tt5090568'];
} else {
    $mediaId = $_GET['id'];
    
    // 1. Obtener datos de ambas fuentes
    $xupalace_data = fetchAndParseXupalace($mediaId, $allowed_servers);
    $embed69_data = fetchAndParseEmbed69($mediaId, $allowed_servers);

    // 2. Combinar los datos
    $merged_data = [];
    $language_map = []; // Para un acceso rápido a los idiomas

    if ($embed69_data) {
        foreach ($embed69_data as $item) {
            $lang = $item['video_language'];
            $language_map[$lang] = $item;
        }
    }

    if ($xupalace_data) {
        $xupalace_lat_servers = $xupalace_data[0]['sortedEmbeds'];
        // Si ya existe un bloque 'Latino', fusiona los servidores
        if (isset($language_map['Latino'])) {
            $language_map['Latino']['sortedEmbeds'] = array_merge($language_map['Latino']['sortedEmbeds'], $xupalace_lat_servers);
        } else {
            // Si no, añade el bloque completo de xupalace
            $language_map['Latino'] = $xupalace_data[0];
        }
    }
    
    // 3. Eliminar duplicados de cada bloque de idioma
    foreach ($language_map as &$lang_block) {
        $unique_servers = [];
        $seen_links = [];
        foreach ($lang_block['sortedEmbeds'] as $server) {
            // Compara solo el enlace para encontrar duplicados
            if (!in_array($server['link'], $seen_links)) {
                $unique_servers[] = $server;
                $seen_links[] = $server['link'];
            }
        }
        $lang_block['sortedEmbeds'] = $unique_servers;
    }

    // Convertir el mapa de nuevo a un array indexado
    $final_data = array_values($language_map);

    // 4. Preparar la respuesta final
    if (empty($final_data)) {
        $response = ['success' => false, 'error' => "No se encontraron servidores permitidos para el ID '".htmlspecialchars($mediaId)."'."];
    } else {
        $response = ['success' => true, 'data' => $final_data];
    }
}

// Imprime la respuesta final en formato JSON y termina el script.
echo json_encode($response);
exit;
?>
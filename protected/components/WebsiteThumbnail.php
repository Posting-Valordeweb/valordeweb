<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

class WebsiteThumbnail {
    // Rutas base para tus miniaturas locales
    // Asegúrate de que este directorio exista y sea escribible por Apache (chmod 775, chown apache:apache)
    private static $thumbnailBasePath = '/var/www/html/thumbs/';
    private static $thumbnailWebPath = '/thumbs/'; // Ruta accesible desde el navegador
private static $dockerWrapperScript = '/var/www/screenshot-scripts/run_docker_screenshot.sh';


    // Ruta al script de Node.js DENTRO DEL CONTENEDOR DOCKER.
    // Esta ruta es cómo Docker verá tu script.
    private static $nodeScriptPathInContainer = '/app/take_screenshot.js';

    // Ruta al directorio donde está el script de Node.js en el HOST (tu servidor EC2).
    // Usaremos esto para montar el volumen en Docker.
    private static $nodeScriptHostDir = '/var/www/screenshot-scripts/';

    // Función para generar el nombre del archivo de la miniatura
    private static function getThumbnailFilename($url, $size) {
        $hash = md5($url . $size); // Genera un hash único basado en la URL y el tamaño
        return "thumb_{$hash}.png";
    }

    // Esta función es el corazón del nuevo sistema: genera la miniatura
    public static function generateThumbnail($url, $size = 'm') {
        // Mapeo de tamaños (PagePeeker a dimensiones de captura)
        $sizes = [
            't' => ['width' => 90, 'height' => 68],
            's' => ['width' => 120, 'height' => 90],
            'm' => ['width' => 200, 'height' => 150], // Tamaño predeterminado
            'l' => ['width' => 400, 'height' => 300],
            'x' => ['width' => 480, 'height' => 360],
        ];

        $viewport = $sizes[$size] ?? $sizes['m']; // Usa el tamaño especificado o el predeterminado 'm'

        $filename = self::getThumbnailFilename($url, $size);
        $fullPath = self::$thumbnailBasePath . $filename;
        $webUrl = self::$thumbnailWebPath . $filename;

        // Verifica si la miniatura ya existe y es relativamente "fresca" (ej. última semana)
        // Esto evita regenerar la misma miniatura una y otra vez para mejorar el rendimiento
        if (file_exists($fullPath) && (time() - filemtime($fullPath) < (7 * 24 * 60 * 60))) { // 7 días de caché
            return $webUrl;
        }

        // --- Comando Docker para ejecutar el script de Node.js ---
        // Usamos 'sudo' aquí para llamar a Docker, por eso configuramos sudoers para 'apache'.
        // Montamos /tmp del host en /tmp del contenedor para que las miniaturas se guarden allí.
        // Montamos el directorio de tu script de Node.js del host en /app del contenedor.
        // El script se ejecuta dentro del contenedor usando 'node /app/take_screenshot.js'.
      // Ruta al script wrapper que ejecuta Docker

// ... dentro de generateThumbnail()
// Comando para ejecutar el script wrapper (que a su vez llama a Docker)
$command = "sudo " . escapeshellarg(self::$dockerWrapperScript) . " " .
           escapeshellarg($url) . " " . escapeshellarg($fullPath) . " " .
           escapeshellarg($viewport['width']) . " " . escapeshellarg($viewport['height']) . " 2>&1"; // Redirige errores a stdout

        // Ejecuta el comando y captura su salida (para depuración)
        $output = shell_exec($command);

        // Puedes registrar la salida para ayudarte a depurar si algo falla
        // Asegúrate de que tu sistema de log (Yii::log) esté configurado para escribir en un archivo accesible.
        error_log("Screenshot command output for {$url}: " . $output); // Mejor usar error_log para depuración rápida

        // Verifica si la captura de pantalla se creó exitosamente
        if (file_exists($fullPath) && filesize($fullPath) > 0) {
            // Establece permisos adecuados para la imagen generada por Docker (se guarda como root dentro del contenedor)
            // Necesitas dar permiso a Apache para leerla.
            chmod($fullPath, 0644); // Permite leer al dueño (root) y a otros (Apache)
            return $webUrl;
        } else {
            error_log("Fallo al generar captura para {$url}. Output: " . $output);
            // Retorna la URL de un placeholder si falla la generación
            return self::$thumbnailWebPath . 'not-available.png'; // ¡Asegúrate de crear esta imagen en /var/www/html/thumbs/!
        }
    }

    // --- Funciones de la aplicación que ahora usan nuestro generador local ---
    // (Estas reemplazan las llamadas a la API de PagePeeker)

    public static function getPollUrl(array $params = array()) {
        // En este nuevo sistema, el "polling" es simplemente generar la miniatura.
        if (isset($params['url']) && isset($params['size'])) {
             return self::generateThumbnail($params['url'], $params['size']);
        }
        return self::$thumbnailWebPath . 'not-available.png';
    }

    public static function getResetUrl(array $params = array()) {
        // Para "resetear" la miniatura, simplemente la borramos para que se genere una nueva.
        if (isset($params['url']) && isset($params['size'])) {
            $filename = self::getThumbnailFilename($params['url'], $params['size']);
            $fullPath = self::$thumbnailBasePath . $filename;
            if (file_exists($fullPath)) {
                unlink($fullPath); // Elimina la miniatura antigua
            }
            return self::generateThumbnail($params['url'], $params['size']); // Genera una nueva
        }
        return self::$thumbnailWebPath . 'not-available.png';
    }

    public static function getThumbData(array $params = array(), $num = 0) {
        if (!isset($params['url'])) {
            throw new InvalidArgumentException("Url param is not specified");
        }
        $size = isset($params['size']) ? $params['size'] : "m";
        $thumbUrl = self::generateThumbnail($params['url'], $size); // Llama a nuestra función de generación local

        return json_encode(array(
            'thumb' => $thumbUrl,
            'size' => $size,
            'url' => $params['url']
        ));
    }

    // Funciones que pueden existir para Open Graph u otros usos
    public static function getOgImage(array $params = array()) {
        if (isset($params['url'])) {
            return self::generateThumbnail($params['url'], 'x'); // Genera tamaño extra-grande
        }
        return self::$thumbnailWebPath . 'not-available.png';
    }

    public static function prepareUrl($url, array $strtr = array(), array $params = array()) {
        // Esta función ya no es estrictamente necesaria con nuestra nueva lógica
        return $url;
    }

    public static function thumbnailStack($websites, array $params=array()) {
        $stack = array();
        foreach ($websites as $website) {
            $params['url'] = $website['domain'];
            $stack[$website['id']] = self::getThumbData($params);
        }
        return $stack;
    }
}

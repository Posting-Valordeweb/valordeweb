<?php

// Asumo que este archivo es un "component" o clase en tu framework (e.g., Yii)
// que se llama de alguna manera para generar la miniatura.
// Si esta clase extiende alguna otra, o tiene métodos adicionales, deberás fusionar
// esta lógica con tu estructura existente.
class WebsiteThumbnailComponent
{
    // Puedes mantener aquí otras propiedades si tu clase las necesita
    // ...

    /**
     * Método principal para generar y mostrar la miniatura.
     * Deberás llamar a este método desde tu controlador o la parte de la aplicación
     * que maneja la solicitud de generación de miniaturas.
     *
     * Ejemplo de cómo se llamaría (ajusta según tu framework):
     * WebsiteThumbnailComponent::generateAndDisplayThumbnail();
     */
    public static function generateAndDisplayThumbnail()
    {
        // 1. Obtener la URL de entrada y otros parámetros de la solicitud
        //    Ajusta 'url_a_capturar' si tu parámetro GET/POST tiene otro nombre.
        $url = isset($_GET['url_a_capturar']) ? $_GET['url_a_capturar'] : '';
        $width = isset($_GET['width']) ? (int)$_GET['width'] : 400;
        $height = isset($_GET['height']) ? (int)$_GET['height'] : 300;

        // Manejar caso de URL vacía
        if (empty($url)) {
            header('Content-Type: text/plain');
            echo "Error: No se proporcionó una URL para capturar.";
            exit();
        }

        // 2. Normalizar la URL: Asegurarse de que tenga http:// o https://
        if (!preg_match("~^(?:f|ht)tps?://~i", $url)) {
            $url = "http://" . $url; // Añade http:// si no tiene prefijo
        }

        // 3. Definir las rutas de salida para la miniatura.
        //    El nombre del archivo será el hash MD5 de la URL para que sea único.
        $filename = "thumb_" . md5($url) . ".png";

        //    a) $localOutputPath: La ruta en el sistema de archivos del servidor host (donde PHP la encontrará)
        //       Es CRÍTICO que este sea el directorio donde Apache pueda leer y servir la imagen.
        $localOutputPath = "/var/www/html/thumbs/" . $filename;

        //    b) $dockerOutputPath: La ruta que Puppeteer usará *dentro* del contenedor Docker.
        //       Debe coincidir con la parte interna del mapeo de volumen (-v HOST_PATH:CONTAINER_PATH).
        //       Si mapeamos /var/www/html/thumbs (host) a /app/screenshots (contenedor),
        //       entonces Puppeteer debe guardar en /app/screenshots/nombre_del_archivo.png.
        $dockerOutputPath = "/app/screenshots/" . $filename;

        // 4. Asegurarse de que el directorio de miniaturas exista y tenga permisos.
        //    El directorio 'thumbs' debe ser escribible por el usuario del servidor web (apache).
        $thumbsDir = dirname($localOutputPath);
        if (!file_exists($thumbsDir)) {
            // Intenta crear el directorio de forma recursiva con permisos 0777.
            // Para depuración, 0777 es permisivo. En producción, considera 0775 y chown.
            if (!mkdir($thumbsDir, 0777, true)) {
                header('Content-Type: text/plain');
                echo "Error: No se pudo crear el directorio de miniaturas en " . htmlspecialchars($thumbsDir);
                exit();
            }
        }

        // 5. Construir y ejecutar el comando de shell (que a su vez ejecuta el script Docker).
        //    Asegúrate de que la ruta a run_docker_screenshot.sh sea correcta en tu servidor.
        $command = "/var/www/screenshot-scripts/run_docker_screenshot.sh " .
                   escapeshellarg($url) . " " .
                   escapeshellarg($dockerOutputPath) . " " . // Pasamos la ruta interna de Docker
                   escapeshellarg($width) . " " .
                   escapeshellarg($height);

        // Ejecuta el comando. La salida de depuración se redirigirá al error_log de Apache.
        $output = shell_exec($command);

        // 6. Mostrar la miniatura o un mensaje de error si no se generó.
        if (file_exists($localOutputPath)) {
            header('Content-Type: image/png');
            readfile($localOutputPath);
            exit(); // Importante salir después de enviar el archivo
        } else {
            // La miniatura no se encontró. Esto indica un fallo en la generación.
            header('Content-Type: text/plain');
            echo "Error al generar la miniatura para: " . htmlspecialchars($url) . "\n\n";
            echo "Detalles del log de depuración (verifica el error_log de Apache para más):\n";
            echo "-------------------------------------------------------------------\n";
            echo $output; // Esto mostrará lo que el shell_exec capturó.
            exit();
        }
    }
}
?>
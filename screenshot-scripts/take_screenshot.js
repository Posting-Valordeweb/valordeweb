const puppeteer = require('puppeteer');
const fs = require('fs'); // Asegúrate de que esta línea esté al principio
const path = require('path'); // Asegúrate de que esta línea esté al principio

console.log('Script de Puppeteer iniciado.'); // <-- AÑADIDO PARA DEBUG
const url = process.argv[2];
const outputPath = process.argv[3];
const width = parseInt(process.argv[4]) || 1280; // Ancho del viewport, default 1280
const height = parseInt(process.argv[5]) || 800; // Alto del viewport, default 800

if (!url || !outputPath) {
    console.error('Uso: node take_screenshot.js <url> <outputPath> [width] [height]');
    process.exit(1);
}

console.log(`Intentando capturar: ${url} en ${outputPath}`); // <-- AÑADIDO PARA DEBUG
console.log(`Dimensiones: ${width}x${height}`); // <-- AÑADIDO PARA DEBUG

(async () => {
    let browser;
    try {
        browser = await puppeteer.launch({
            args: [
                '--no-sandbox', // Necesario para entornos Docker/Linux a menudo
                '--disable-setuid-sandbox', // Deshabilita el sandbox de setuid, útil en entornos con privilegios
                '--disable-dev-shm-usage', // Soluciona problemas de memoria en Docker (Docker usa shm para /dev/shm)
                '--disable-accelerated-2d-canvas', // Deshabilita renderizado 2D acelerado por hardware
                '--disable-gpu', // Deshabilita la GPU (no hay GPU en la mayoría de las EC2 t2/t3)
                '--disable-features=site-per-process', // Deshabilita el aislamiento de sitios
                '--disable-web-security', // Deshabilita la seguridad web (solo si no es crítica, no debería afectar captura)
                '--no-first-run', // No muestra la pantalla de bienvenida
                '--no-zygote', // No usa el proceso zygote para acelerar el arranque (puede ser útil con single-process)
                '--single-process', // Ejecuta todo en un solo proceso (puede reducir sobrecarga)
                '--deterministic-fetch', // Hace que las peticiones de red sean más deterministas
                '--disable-extensions', // Deshabilita extensiones del navegador
                '--disable-offer-store-unmasked-wallet-cards', // Deshabilita ofertas de la tienda
                '--disable-offer-upload-certs', // Deshabilita la carga de certificados
                '--disable-print-preview', // Deshabilita la vista previa de impresión
                '--disable-speech-api', // Deshabilita la API de voz
                '--disable-sync', // Deshabilita la sincronización
                '--disable-translate', // Deshabilita el traductor
                '--hide-scrollbars', // Oculta barras de desplazamiento
                '--metrics-recording-only', // Solo registra métricas, no las envía
                '--mute-audio', // Silencia el audio
                '--no-default-browser-check', // No comprueba si es el navegador predeterminado
                '--window-size=1280,1024' // Puedes ajustar esto al tamaño deseado de la ventana de renderizado
            ],
            headless: true // Ejecutar en modo headless (sin interfaz gráfica)
        });
        console.log('Navegador Puppeteer lanzado.'); // <-- AÑADIDO PARA DEBUG

        const page = await browser.newPage();
        await page.setViewport({ width: width, height: height });

        console.log('Navegando a la URL...'); // <-- AÑADIDO PARA DEBUG
        await page.goto(url, { waitUntil: 'networkidle0', timeout: 90000 }); // Espera 90 segundos máximo, networkidle0
        console.log('Página cargada.'); // <-- AÑADIDO PARA DEBUG

        // Esperar un poco más si la página tiene contenido dinámico que tarda en aparecer
        await new Promise(resolve => setTimeout(resolve, 2000)); // Espera 2 segundos adicionales para renderizado JS
        console.log('Esperando 2 segundos para contenido dinámico.'); // <-- AÑADIDO PARA DEBUG

        console.log('Tomando captura de pantalla...'); // <-- AÑADIDO PARA DEBUG
        await page.screenshot({ path: outputPath, type: 'png', fullPage: false }); // Toma la captura de pantalla
        console.log(`Captura de pantalla guardada en: ${outputPath}`); // <-- AÑADIDO PARA DEBUG

    } catch (error) {
        console.error('Error durante la generación de la miniatura:', error); // <-- AÑADIDO PARA DEBUG
        process.exit(1); // Sale con un código de error para indicar fallo
    } finally {
        if (browser) {
            await browser.close();
            console.log('Navegador cerrado. Script finalizado con éxito.'); // <-- AÑADIDO PARA DEBUG
        }
    }
})();

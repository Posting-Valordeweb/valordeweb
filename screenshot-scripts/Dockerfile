# Usar una imagen base de Node.js que ya incluye Node.js
FROM node:20-slim

# Instalar Chromium y dependencias necesarias para entornos Debian/Ubuntu (node:20-slim se basa en Debian)
# Estas son las librerías que Chromium necesita para correr en un entorno headless
RUN apt-get update && apt-get install -y \
    chromium \
    wget \
    gnupg \
    --no-install-recommends \
    && rm -rf /var/lib/apt/lists/*

# Establecer el directorio de trabajo dentro del contenedor
WORKDIR /app

# Copiar package.json y package-lock.json (si existen) para instalar dependencias de Node.js
# (Asegúrate de que tienes estos archivos en /var/www/screenshot-scripts)
COPY package*.json ./

# Evitar que Puppeteer descargue su propio Chromium, usaremos el Chromium del sistema (del paso RUN apt-get install chromium)
ENV PUPPETEER_SKIP_CHROMIUM_DOWNLOAD=true
# Instalar las dependencias de Node.js (incluido Puppeteer)
RUN npm install

# Copiar tu script de captura de pantalla al contenedor
COPY take_screenshot.js .

# Comando por defecto para ejecutar el script si el contenedor se inicia sin un comando específico
CMD ["node", "take_screenshot.js"]

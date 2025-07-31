#!/bin/bash

# Log de depuración: Indicar que el script ha sido invocado
echo "DEBUG: run_docker_screenshot.sh invocado." >> /var/log/httpd/error_log
echo "DEBUG: Argumentos recibidos: $@" >> /var/log/httpd/error_log

# Directorio donde se encuentra el Dockerfile y take_screenshot.js
SCRIPT_DIR="/var/www/screenshot-scripts"

# Log de depuración: Mostrar el directorio de trabajo
echo "DEBUG: Cambiando a directorio: $SCRIPT_DIR" >> /var/log/httpd/error_log
cd "$SCRIPT_DIR" || { echo "ERROR: No se pudo cambiar de directorio a $SCRIPT_DIR" >> /var/log/httpd/error_log; exit 1; }

# Log de depuración: Mostrar la ruta donde Docker buscará el Dockerfile
echo "DEBUG: Construyendo imagen Docker desde: $(pwd)" >> /var/log/httpd/error_log
# Reconstruir la imagen de Docker (asegúrate de que el . significa el directorio actual)
sudo docker build -t screenshot-generator . >> /var/log/httpd/error_log 2>&1
BUILD_STATUS=$?
if [ $BUILD_STATUS -ne 0 ]; then
    echo "ERROR: Falló la construcción de la imagen Docker. Código de salida: $BUILD_STATUS" >> /var/log/httpd/error_log
    exit 1
fi
echo "DEBUG: Imagen Docker 'screenshot-generator' construida/actualizada." >> /var/log/httpd/error_log

# Log de depuración: Preparando el comando docker run
DOCKER_CMD="sudo docker run --rm \
    -v \"$SCRIPT_DIR/screenshots:/app/screenshots\" \
    screenshot-generator \
    node /app/take_screenshot.js \"$1\" \"/app/$2\" \"$3\" \"$4\""

echo "DEBUG: Comando Docker a ejecutar: $DOCKER_CMD" >> /var/log/httpd/error_log

# Ejecutar el contenedor Docker.
# Se redirige la salida estándar y de error al log de Apache.
eval $DOCKER_CMD >> /var/log/httpd/error_log 2>&1

# Log de depuración: Mostrar el estado de salida del comando Docker
DOCKER_EXIT_STATUS=$?
echo "DEBUG: Contenedor Docker finalizado con código de salida: $DOCKER_EXIT_STATUS" >> /var/log/httpd/error_log

exit $DOCKER_EXIT_STATUS

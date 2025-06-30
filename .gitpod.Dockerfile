# Usa una imagen base de Gitpod que ya tiene muchas herramientas.
FROM gitpod/workspace-full

# Instala las extensiones de PHP necesarias para Laravel y otras apps (incluyendo la tuya).
RUN sudo apt-get update && \
    sudo apt-get install -y php8.1-mysql php8.1-mbstring php8.1-xml php8.1-curl && \
    sudo rm -rf /var/lib/apt/lists/*


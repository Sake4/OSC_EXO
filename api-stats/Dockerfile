FROM php:8.2-cli

# Installe les dépendances système nécessaires à la compilation de pdo_pgsql
RUN apt-get update && apt-get install -y libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /app
COPY . .

# Render injecte le port disponible via la variable $PORT.
# Forme shell (pas la forme JSON/exec) pour que $PORT soit bien interprété.
CMD php -S 0.0.0.0:$PORT index.php
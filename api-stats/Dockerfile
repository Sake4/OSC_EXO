# Utilise l'image officielle PHP avec Apache
FROM php:8.2-apache

# Active le module mod_rewrite d'Apache (indispensable pour les routes des API)
RUN a2enmod rewrite

# Copie tous les fichiers de ton projet dans le dossier web d'Apache
COPY . /var/www/html/

# Ajuste les permissions pour Apache
RUN chown -R www-data:www-data /var/www/html

# Modifie le port d'écoute d'Apache pour utiliser la variable PORT de Render
RUN sed -i 's/80/${PORT}/g' /etc/apache2/ports.conf /etc/apache2/sites-available/000-default.conf

# Expose le port (utilisé en interne)
EXPOSE 80
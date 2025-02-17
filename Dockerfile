# Usar una imagen oficial de PHP
FROM php:8.0-apache

# Habilitar la extensión de MySQL
RUN docker-php-ext-install mysqli

# Copiar los archivos del proyecto al contenedor
COPY . /var/www/html/

# Configurar el puerto en el que se ejecutará el contenedor
EXPOSE 80

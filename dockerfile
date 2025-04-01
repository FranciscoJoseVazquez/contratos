# Usar una versión específica de PHP con Apache
FROM php:8.2-apache

# Instalar la extensión mysqli y librerías adicionales
RUN docker-php-ext-install mysqli gd && docker-php-ext-enable mysqli

# Establecer el directorio de trabajo
WORKDIR /var/www/html

# Crear las carpetas necesarias y asignar permisos
RUN mkdir -p /var/www/html/sinfirmar /var/www/html/firmados && \
    chmod -R 777 /var/www/html

# Copiar y configurar php.ini
RUN cp /usr/local/etc/php/php.ini-development /usr/local/etc/php/php.ini && \
    sed -i 's/post_max_size = .*/post_max_size = 328M/' /usr/local/etc/php/php.ini && \
    sed -i 's/upload_max_filesize = .*/upload_max_filesize = 328M/' /usr/local/etc/php/php.ini

# Exponer el puerto 80
EXPOSE 80

# Comando de inicio del contenedor
CMD ["apache2-foreground"]

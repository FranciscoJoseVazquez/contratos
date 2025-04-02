FROM php:apache

# Instalar la extensión mysqli
RUN docker-php-ext-install mysqli && \
    docker-php-ext-enable mysqli

# Establecer el directorio de trabajo dentro del contenedor
WORKDIR /var/www/html

# Exponer el puerto 80
EXPOSE 80

# Permisos
RUN chmod -R 777 /var/www/html

# Crear archivo php.ini
RUN cp /usr/local/etc/php/php.ini-development /usr/local/etc/php/php.ini

# Cambiar configuraciones en php.ini
RUN sed -i 's/^post_max_size = .*/post_max_size = 80M/' /usr/local/etc/php/php.ini && \
    sed -i 's/^upload_max_filesize = .*/upload_max_filesize = 80M/' /usr/local/etc/php/php.ini

# Comando de inicio del contenedor
CMD ["apache2-foreground"]

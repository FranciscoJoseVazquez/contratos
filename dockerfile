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

# Crear las carpetas necesarias y asignar permisos
RUN mkdir -p /var/www/html/sinfirmar /var/www/html/firmados && \
    chmod -R 777 /var/www/html/*

# Crear archivo php.ini
RUN cp /usr/local/etc/php/php.ini-development /usr/local/etc/php/php.ini

# Cambiar configuraciones en php.ini
RUN sed -E -i 's/(;?)(post_max_size[[:space:]]=[[:space:]])50+M/\\2328M/g' /usr/local/etc/php/php.ini && \
    sed -E -i 's/(;?)(upload_max_filesize[[:space:]]=[[:space:]])50+M/\\2328M/g' /usr/local/etc/php/php.ini

# Comando de inicio del contenedor
CMD ["apache2-foreground"]

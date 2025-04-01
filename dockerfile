FROM php:apache

# Instalar la extensión mysqli
RUN docker-php-ext-install mysqli && \
    docker-php-ext-enable mysqli

# Establecer el directorio de trabajo dentro del contenedor
WORKDIR /var/www/html

# Exponer el puerto 80
EXPOSE 80

# Configurar permisos adecuados
RUN chown -R www-data:www-data /var/www/html && \
    chmod -R 755 /var/www/html

# Crear las carpetas "sinfirmar" y "firmados"
RUN mkdir /var/www/html/sinfirmar && mkdir /var/www/html/firmados

# Crear archivo php.ini
RUN cp /usr/local/etc/php/php.ini-development /usr/local/etc/php/php.ini

# Cambiar php.ini
RUN sed -E -i 's/(;?)(post_max_size[[:space:]]=[[:space:]])50+M/\\2328M/g' /usr/local/etc/php/php.ini && \
    sed -E -i 's/(;?)(upload_max_filesize[[:space:]]=[[:space:]])50+M/\\2328M/g' /usr/local/etc/php/php.ini

# Comando de inicio del contenedor
CMD ["apache2-foreground"]

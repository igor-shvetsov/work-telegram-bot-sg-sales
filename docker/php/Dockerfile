FROM php:latest

# Устанавливаем зависимости для PHP и Redis
RUN apt-get update && apt-get install -y \
    libzip-dev \
    unzip \
    && docker-php-ext-install zip \
    && pecl install redis \
    && docker-php-ext-enable redis

## Рабочая дирректория
WORKDIR /var/www/html

# Создаем директорию для логов и даем права
RUN mkdir -p /var/log/php && \
    touch /var/log/php/error.log && \
    chmod 777 /var/log/php/error.log

# Копируем ваши PHP-файлы в контейнер
COPY ../.. .

# Настраиваем логирование ошибок PHP (корректируем php.ini)
RUN echo "log_errors = On" >> /usr/local/etc/php/php.ini && \
    echo "error_log = /var/log/php/error.log" >> /usr/local/etc/php/php.ini

# Подключаем расширения
RUN echo "extension=redis.so" >> /usr/local/etc/php/php.ini

# Открываем порт, который будет использовать php -S
EXPOSE 8000

# Запускаем встроенный сервер PHP
CMD ["php", "-S", "0.0.0.0:8000"]
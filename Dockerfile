FROM php:8.2-apache

# Εγκατάσταση PDO MySQL driver
RUN docker-php-ext-install pdo pdo_mysql

# Προαιρετικά αντίγραφα (όχι απαραίτητα λόγω volumes στο docker-compose.yml)
COPY ./public /var/www/html/

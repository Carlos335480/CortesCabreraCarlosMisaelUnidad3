FROM php:8.3-apache

# Instalar dependencias necesarias para GD y SQL Server
RUN apt-get update && apt-get install -y \
    gnupg2 curl apt-transport-https \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    zlib1g-dev \
    && rm -rf /var/lib/apt/lists/*

RUN curl https://packages.microsoft.com/keys/microsoft.asc | gpg --dearmor > /usr/share/keyrings/microsoft-prod.gpg && \
    echo "deb [signed-by=/usr/share/keyrings/microsoft-prod.gpg] https://packages.microsoft.com/debian/12/prod bookworm main" > /etc/apt/sources.list.d/mssql-release.list

RUN apt-get update && ACCEPT_EULA=Y apt-get install -y \
    msodbcsql17 mssql-tools unixodbc-dev libgssapi-krb5-2 && \
    pecl install sqlsrv pdo_sqlsrv && \
    docker-php-ext-enable sqlsrv pdo_sqlsrv

RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd


RUN sed -i 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY . /var/www/html/
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80

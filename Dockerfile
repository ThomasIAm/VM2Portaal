FROM php:7.2-apache
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
RUN mkdir -p /home/vagrant/VM2/
RUN chmod 755 /home/vagrant/VM2/
COPY . /var/www/html/
COPY demo/klanten/ /home/vagrant/VM2/klanten/
RUN chmod -R 777 /home/vagrant/VM2/klanten/
COPY demo/templates /home/vagrant/VM2/templates/
EXPOSE 80 443
RUN apt update && apt install -y libyaml-dev \
	&& pecl install yaml-2.1.0 \
	&& docker-php-ext-enable yaml

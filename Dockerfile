FROM ubuntu:18.04

RUN apt update && DEBIAN_FRONTEND=noninteractive apt install -q -y php-cli php-imagick composer zip
RUN rm /etc/ImageMagick-6/policy.xml
RUN composer require spatie/pdf-to-image
RUN mkdir workbench

ADD form.html /
ADD index.php /

ENV PHP_CLI_SERVER_WORKERS=3
CMD php -S 0.0.0.0:80

EXPOSE 80

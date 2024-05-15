FROM alpine:latest
LABEL maintainer="maintainer@example.com"
ARG HTDOCS=/htdocs
ARG APACHE_CONF=/etc/apache2
ARG APACHE_LOG=/var/log/apache2
ENV APACHE_SERVERNAME=logging.example.com
ENV APACHE_SERVER_PORT=443
ENV PHP_MEMORY_LIMIT=256M
RUN apk update && \ 
    apk add --no-cache apache2 apache2-ldap apache2-ssl php-apache2 php-bcmath php-bz2 php-calendar php-common php-ctype php-curl php-dom php-gd php-iconv php-json php-mbstring php-mysqli php-mysqlnd php-openssl php-pdo_mysql php-pdo_pgsql php-pdo_sqlite php-phar php-session php-xml 7zip wget curl vim dos2unix bash
RUN sed -i -e 's/^;date.timezone.*/date.timezone = "Europe\/Moscow"/' -e "s/memory_limit.*/memory_limit = ${PHP_MEMORY_LIMIT}/" /etc/php*/php.ini && \
    sed -i -r -e "s/^([\t ]+ServerName).*/\1 ${APACHE_SERVERNAME}:${APACHE_SERVER_PORT}/g" /etc/apache2/httpd.conf && \
    curl -Lso /tmp/loganalyzer.zip https://github.com/rsyslog/loganalyzer/archive/refs/heads/master.zip && \
    7z x /tmp/loganalyzer.zip 'loganalyzer-master/src' -o'/tmp' && \
    mv -f /tmp/loganalyzer-master/src ${HTDOCS} && \
    touch ${HTDOCS}/config.php && \
    chown apache:root ${HTDOCS}/config.php && \
    rm -fr /var/cache/apk/* /tmp/loganalyzer* ${HTDOCS}/cron/*.bat ${HTDOCS}/BitstreamVeraFonts/*.TXT
ADD --chmod=644 --chown=root:root ./app/httpd.conf ./app/certs ${APACHE_CONF}
ADD --chown=root:root ./app/src ${HTDOCS}
VOLUME ${HTDOCS} ${APACHE_CONF} ${APACHE_LOG}
EXPOSE 80/tcp
EXPOSE ${APACHE_SERVER_PORT}/tcp
#HEALTHCHECK --interval=1m --timeout=10s --retries=3 CMD wget --no-proxy --no-check-certificate -q --no-cache --spider localhost/index.php || exit 1
CMD ["httpd", "-D", "FOREGROUND"]

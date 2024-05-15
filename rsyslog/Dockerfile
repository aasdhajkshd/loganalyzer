FROM alpine:latest
LABEL maintainer="maintainer@example.com"
RUN apk update && \ 
    apk add --no-cache rsyslog \
                       rsyslog-elasticsearch \
                       rsyslog-mmjsonparse \
                       rsyslog-mmutf8fix \
                       rsyslog-tls \
                       rsyslog-pgsql \
                       rsyslog-clickhouse \
                       rsyslog-crypto \
                       rsyslog-mysql \
                       rsyslog-dbg \
                       rsyslog-tls \
                       tzdata \
                       postgresql \
                       sudo \
                       iputils iputils-ping iputils-tracepath \
                       vim \
                       dos2unix \
                       bash && \
    rm -f /var/cache/apk/*

RUN adduser -s /bin/ash -D rsyslog rsyslog && \
    echo "rsyslog ALL=(ALL) NOPASSWD:ALL" >> /etc/sudoers
VOLUME /config /app /logs
WORKDIR /app
COPY ./app ./
RUN cp -fr rsyslog.* /etc/ && \
    dos2unix /etc/rsyslog.* && \
    chmod +x starter.sh && \
    chown -R rsyslog:rsyslog ./* && \
    echo "`date +%F` (`date +%s`)" > CONTAINER.release
CMD ["rsyslog"]
ENTRYPOINT ["/app/starter.sh"]
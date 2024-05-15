# LogAnalyzer для Rsyslog/PostgreSQL для сбора syslog сообщений с серверов Linux

---

## Content

- [Configuring the Syslog](#configuring-the-syslog)
  - [Content](#content)
    - [Структура](#структура)
    - [Loganalyzer](#loganalyzer)
    - [Postgresql](#postgresql)
    - [Rsyslog](#rsyslog)
    - [Установка на docker-host (manual)](#установка-на-docker-host-manual)
    - [Установка на docker-host (ansible)](#установка-на-docker-host-ansible)
    - [Настройка клиента (manual)](#настройка-клиента-manual)
    - [Настройка клиента (ansible)](#настройка-клиента-ansible)
    - [Systemd Service](#systemd-service)
      - [Docker команды](#docker-команды)
      - [Список серверов](#список-серверов)

---

### <a name="structure">Структура</a>

Решение состоит минимально из трех контейнеров: БД [Postgres](https://www.postgresql.org/), сервис [Rsyslog](https://www.rsyslog.com/) и Web-сервис [LogAnalyzer](https://loganalyzer.adiscon.com/) от Rsyslog

Ниже приводится структура проекта с описанием:

```tree
/opt // директория размещения сервисов

├── .env // файл с переменными для сборки образов ENV
├── logging-logging.service // файл Systemd сервиса для [пере]запуска/остановки решения /etc/systemd/system/
└── logging
    ├── docker-compose.yml // файл для docker compose сервиса
    ├── loganalyzer // папка для frontend микросервиса LogAnalyzer
    │   ├── app // папка приложения, повторяет структуру внутри сервера
    │   │   ├── certs // сертификаты
    │   │   │   ├── cert.pem // сертификат loganalyzer DNS: loganalyzer.example.com
    │   │   │   ├── chain.pem // цепочка сертификатов
    │   │   │   ├── fullchain.pem
    │   │   │   └── privkey.pem
    │   │   ├── config.php // конфигурационный файл преднастроенный для web-сайта /htdocs/
    │   │   ├── httpd.conf // конфигурационный файл для web-сервиса Apache2 /etc/apache2/
    │   │   └── src // файлы для web-сайта, которые имеют изменения 
    │   │       ├── config.php // конфигурационный файл "пустышка"
    │   │       ├── cron
    │   │       │   └── maintenance.sh // файл скрипт для запуска удаления старых записей в БД
    │   │       ├── lang // языковой пакет
    │   │       │   └── ru
    │   │       │       ├── admin.php
    │   │       │       ├── info.txt
    │   │       │       └── main.php
    │   │       └── templates
    │   │           └── include_header.html // изменный файл шаблона, где удалён banner
    │   └── Dockerfile // файл для сборки образа LogAnalyzer
    ├── postgresql
    │   └── initdb
    │       └── rsyslog.sql // файл инициализации БД
    └── rsyslog
        ├── app // папка приложения, повторяет структуру внутри сервера
        │   ├── CONTAINER.copyright
        │   ├── CONTAINER.homepage
        │   ├── CONTAINER.name
        │   ├── CONTAINER.release
        │   ├── LICENSE
        │   ├── internal // файлы конфигурации
        │   │   ├── build-config 
        │   │   ├── container_config
        │   │   ├── droprules.conf
        │   │   ├── install-valgrind
        │   │   └── set-defaults
        │   ├── rsyslog.conf // конфигурационный файл для сервиса Rsyslog /etc/
        │   ├── rsyslog.conf.d // папка с дополнительными файлами конфигурации сервиса
        │   │   └── log_to_files.conf
        │   ├── starter.sh // файл запуска контейнера ENTRYPOINT
        │   └── tools // папка с возможным запуском контейнера, см. rsyslog/README.md
        │       ├── bash 
        │       ├── edit-config
        │       ├── help
        │       ├── rsyslog
        │       └── show-config
        └── Dockerfile // файл для сборки образа Rsyslog
```

### <a name="loganalyzer">Loganalyzer</a>

Микросервис собирается на основании alpine дистрибутива. См. `loganalyzer/Dockerfile`.

Основными файлами являются `httpd.conf` и `config.php`, который описывает статически настройки для запуска web-сервиса.
Файл `build.sh` в директории проекта собирает локально образ. При сборке образа выполняется загрузка актуальной версии [Github](https://github.com/rsyslog/loganalyzer/archive/refs/heads/master.zip) приложения, его распаковка и копирование файлов в папке /htdocs, после уже поверх копируются файлы из папки `app`.
При новой версии приложения, необходимо проверить дельту с файлами в папке `src`. Текущая версия [4.1.13](https://github.com/rsyslog/loganalyzer/blob/master/ChangeLog).

Директории, которые доступны на hosting сервере:

```list
- /var/lib/docker/volumes/logging_loganalyzer_data/_data/   => /htdocs
- /var/lib/docker/volumes/logging_loganalyzer_conf/_data/   => /etc/apache2
- /var/lib/docker/volumes/logging_loganalyzer_log/_data/    => /var/log/apache2
```

### <a name="postgresql">Postgresql</a>

Под сервер БД [Postgres](https://hub.docker.com/_/postgres) используется официальный без изменений.

Логин и пароль администратора безертся из переменной `.env` файла.

Инициалиазция начальной БД описывается в файле `postgresql/initdb/rsyslog.sql`

Чтобы получить доступ к контейнеру и непосредственно к БД, можно выполнить следующие команды:

```bash
docker compose exec -it postgresql bash
```

или войти от пользователя *postgres*

```bash
docker compose exec -it postgresql su - postgres -c psql
```

или поменять пароль у пользователя

```bash
docker compose exec -it postgresql su - postgres -с psql -c "ALTER USER postgres PASSWORD 'MyN@wPaSs';"
```

Пароль пользователя *rsyslog* прошит статично при инициализации БД и указывается в файле `config.php` сервиса *LogAnalyzer*.

Если доступ не нужен с внешней среды в БД, можно убрать аннонсирование порта в `docker-compose.yml`, удалив раздел *ports* у сервиса *services: postgresql*

На время тестирования, для отображения SQL-запросов, включен режим отладки параметром `command: ["postgres", "-c", "logging_collector=on", "-c", "log_directory=/var/log/postgresql", "-c", "log_filename=postgresql.log", "-c", "log_statement=all"]`.

Для просмотра лога ошибок, можно запустить данную команду от пользователя *sp*:

```bash
docker compose exec -it postgresql tail -f /var/log/postgresql/postgresql.log
```

Например, вывод ошибки:

```output
2023-12-21 16:32:44.302 MSK [223094] STATEMENT:  INSERT INTO systemevents (message, facility, fromhost, priority, devicereportedtime, receivedat, infounitid, syslogtag, processid) VALUES (' dnf-makecache.service: Failed with result \'exit-code\'.', 3, 'infra', 4, '2023-12-21 16:32:44', '2023-12-21 16:32:44', 1, 'systemd', '1')
2023-12-21 16:32:44.319 MSK [226853] ERROR:  syntax error at or near "exit" at character 186

```

Данный файл так же доступен с правами *root*, см. ниже папку.

Директории, которые доступны на hosting сервере:

```list
- /var/lib/docker/volumes/logging_postgresql_data/_data/    => /var/lib/postgresql/data
- /var/lib/docker/volumes/logging_postgresql_log/_data/     => /var/log/postgresql
```

Удаление записей из БД

```bash
docker compose -f /opt/logging/docker-compose.yml exec -it loganalyzer /bin/bash /htdocs/cron/maintenance.sh
```

Можно запускать от пользователя sp, так как он добавлен в группу `docker` с возможностью в непривилегированном режиме выполнять команды docker'а

```bash
crontab -l 
```

Результат:

```output
crontab -l
* * * * 1 docker compose -f /opt/logging/docker-compose.yml exec -it loganalyzer /bin/bash /htdocs/cron/maintenance.sh
```


Пример вывода:

```output
Num.    Facility .      Debug Message
1.      Information.    CleanData.      Cleaning data for logstream source 'RSyslog'.
2.      Information.    CleanData.      Successfully connected and found '542' rows in the logstream source.
3.      Information.    CleanData.      Performing deletion of data entries older then '2023-11-19'.
4.      Information.    CleanData.      Successfully Deleted '0' rows in the logstream source.'

```

Изменить время хранения записей в БД можно редактируя файл источник и/или

> время в секундах
> _Source1_ берется из файла `config.php`

```output
php ./maintenance.php cleandata Source1 olderthan 2592000
```


```bash
vim /opt/logging/loganalyzer/app/src/cron/maintenance.sh
```

... файл в контейнере `loganalyzer`

```bash
vim /var/lib/docker/volumes/logging_loganalyzer_data/_data/cron/maintenance.sh
```

### <a name="rsyslog">Rsyslog</a>

Микросервис пересобран с исползованием [репозитория](https://github.com/rsyslog/rsyslog-docker) на базе дистрибутива Alpine.

Для описания regex правил в конфигурации можно воспользоваться сайтом проверки правил: [Regular Expression Checker/Generator](https://www.rsyslog.com/regex/)

Директории, которые доступны на hosting сервере:

```list
- /var/lib/docker/volumes/logging_rsyslog_conf/_data/       => /config
- /var/lib/docker/volumes/logging_rsyslog_log/_data/        => /logs
```

### <a name="docker-host-manual">Установка на docker-host (manual)</a>

Добавление ssh-ключа для доступа к серверу logger (как пример)

```bash
cat << EOF > ~/.ssh/id_rsa-logger
-----BEGIN OPENSSH PRIVATE KEY-----
...
-----END OPENSSH PRIVATE KEY-----
EOF

chmod go-rwx ~/.ssh/id_rsa-logger

cat << EOF >> ~/.ssh/config
Host logger
    HostName logger
    User sp
    IdentityFile ~/.ssh/id_rsa-logger
EOF

cat << EOF > /home/sp/.ssh/authorized_keys
ssh-rsa AAA...EHFp sp@localhost.localdomain
```

Список команд для установки и настройки сервисов logging

```bash
mkdir -p ~/work/
cd ~/work
git clone https://oauth2:glpat-...@gitlab.example.com/project/logging.git
cd logging/
cp -fR ./host/.vimrc ./host/.docker ~/
sed -i -r -e "s/(POSTGRES_PASSWORD=)pleasechangeme/\1$(python host/pass.py 12 n 1)/" .env
sudo -i whoami
sudo mkdir -p /opt/logging/
sudo cp -fR ./rsyslog ./loganalyzer ./postgresql docker-compose.yml ./.env /opt/logging/
sudo chown root -R /opt/docker
for i in admin sp; do sudo usermod -a -G docker $i; done
newgrp docker
sudo yum -y install git telnet vim 
sudo cp -fR ./host/.docker ~/ /root/
sudo mkdir -p /var/log/postgresql
sudo ln -s /var/lib/docker/volumes/logging_
sudo chown 999:999 /var/log/postgresql /var/lib/postgresql
[[ -f /etc/docker/daemon.json ]] || sudo cp -fR ./host/etc/docker /etc/
systemctl is-active docker && sudo systemctl restart docker; systemctl status -l docker
[[ "$(systemctl is-active docker; echo $?)" -eq 0 ]] && \
sudo cp -f ./host/etc/systemd/system/docker-logging.service /etc/systemd/system/ && \
sudo systemctl daemon-reload
sudo systemctl enable docker-logging
sudo systemctl start docker-logging
sudo systemctl status -l docker-logging
docker compose -f /opt/logging/docker-compose.yml events
docker compose -f /opt/logging/docker-compose.yml logs -f
crontab -e
* * * * 1 docker compose -f /opt/logging/docker-compose.yml exec -it loganalyzer /bin/bash /htdocs/cron/maintenance.sh
```

### <a name="docker-host-ansible-install">Установка на docker-host (ansible)</a>

Структура `ansible` каталога

```text
ansible/
├── ansible.cfg - конфигурационный файл по-умолчанию, в нём можно указать актуальный путь к inventory файлу
├── group_vars
│   ├── all
│   └── logger
├── hosts
├── host_vars
│   └── logger - ansible-vault файл для подключения к серверу для установки, пример
├── playbooks
│   ├── deploy.yaml - файл для установки конфигурационного файла на клиенте
│   └── install.yaml - файл для установки сервера
├── requirements.txt
└── roles
    └── docker
        ├── defaults
        │   └── main.yml
        ├── files
        │   ├── id_rsa-gitlab - приватный ключ для доступа к репозиторию gitlab
        │   ├── id_rsa.pub - публичный ключ для подключения к серверу без пароля для infra
        │   └── pass.py
        ├── handlers
        │   └── main.yml
        ├── meta
        │   └── main.yml
        ├── README.md
        ├── tasks
        │   └── main.yml
        ├── templates
        │   └── docker-logging.service.j2
        ├── tests
        │   ├── inventory
        │   └── test.yml
        └── vars
            └── main.yml
```

Для установки от и до достаточно с сервера infra выполнить запуск *playbook'а*, перед этим сначала загрузив актуальную копию с репозитория `git clone git@gitlab.example.com:project/logging.git`.

> Нет необходимости выполнять вручную установку *docker*-компонентов
> Если файл `/etc/yum.repos.d/docker-ce.repo` на целевой машине присутствует, установка docker'а будет пропущена

```bash
git clone https://oauth2:glpat-...@gitlab.example.com/project/logging.git
git clone https://oauth2:glpat-...@gitlab.example.com/project/ansible-inventory.git

cd logging
```

Настроить окружение для ansible конфигурации и дешифровки ключа для Ansible Vault, в нашем случае `infra` - это тестовая машина

```bash
export ANSIBLE_CONFIG="ansible/ansible.cfg"
echo "vault_password" > ansible/vault.key
```

В экспортируемом [конфигурационном](https://docs.ansible.com/ansible/latest/reference_appendices/config.html) файле указывается локальный inventory файл, его можно переопределить из клона репозитория `ansible-inventory`

```bash
ansible infra -i ../ansible-inventory/hosts -m ping -v
```

При успешном выше выполнении, можно протестировать и запустить выполнение установки

```bash
ansible-playbook -v ansible/playbooks/install.yaml -l '\!all,infra' --check

ansible-playbook -vv ansible/playbooks/install.yaml -l '\!all,infra'
```

### <a name="client-install">Настройка клиента (manual)</a>

Примеры настройки непосредственно на сервере

По-умолчанию:

```bash
cat << EOF > /etc/rsyslog.d/10-remotelog.conf
authpriv.* @@logger.example.com:514
EOF
```

Для squid:

```bash
cat << EOF > /etc/rsyslog.d/10-remotelog.conf
*.error;authpriv.* action(type="omfwd"
    queue.filename="fwdRule1"
    queue.maxdiskspace="1g"
    queue.saveonshutdown="on"
    queue.type="LinkedList"
    action.resumeRetryCount="-1"
    template="RSYSLOG_ForwardFormat"
    Target="logger.example.com" Port="514" Protocol="tcp")

if ($msg contains_i "test") then {
    action(type="omfwd" Target="logger.example.com" Port="514" Protocol="tcp")
}
EOF

```

Условие с ошибкой в тексте добавить правило:

```bash
cat << EOF > /etc/rsyslog.d/11-remotelog.conf
if ($msg contains_i "error") then {
    action(type="omfwd" Target="logger.example.com" Port="514" Protocol="tcp")
}
EOF
```

```bash
cat << EOF > /etc/rsyslog.d/10-remotelog.conf
if ($syslogseverity <= 5) then {
    action(type="omfwd"
           queue.filename="fwdRule1"
           queue.maxdiskspace="1g"
           queue.saveomnshutdown="on"
           queue.type="LinkedList"
           action.resumeRetryCount="-1"
           Target="logger.example.com" Port="514" Protocol="tcp")
}
EOF
```

Исключить отправление повторяющихся сообщений на сервер, как пример ниже со словом `cron`

```bash
cat << EOF > /etc/rsyslog.d/09-discard.conf
:msg, contains, "cron" ~
EOF
```

Перезагрузка сервиса обязательна

```bash
systemctl restart rsyslog
```

### <a name="client-install-ansible">Настройка клиента (ansible)</a>

Для установки на клиентах файла `10-remotelog.conf` и перезапуск сервиса, желаемые узлы должны быть `logging`-группе *inventory*-файла. Путь к *inventory* по-умолчанию указывается в конфигурационном файле той же папки *ansible.cfg*

Ниже приводится пример узлов для заполнения:

```yaml
all:
  hosts:
  children:
    supported:
      hosts:
        logger:
          ansible_host: 10.10.160.10
    logging:
      hosts:
        client:
          ansible_host: 10.10.160.180
```

Запуск выполняется просто командой

> дополнительные аргументы можно добавить `-i ./hosts playbooks/deploy.yaml -l all:\!logger`

```bash
ansible-playbook playbooks/deploy.yaml
```


### <a name="systemd-service">Systemd Service</a>

```bash

cat << EOF > /etc/systemd/system/docker-logging.service
docker compose exec -ti postgresql tail -f /var/log/postgresql/postgresql.log
docker compose exec -ti loganalyzer ls -Alh /var/log/apache2

docker ps -q | xargs -n 1 docker inspect --format '{{range .NetworkSettings.Networks}}{{.IPAddress}}{{end}} {{ .Name }}' | sed 's/ \// /g'

cat << EOF > /etc/systemd/system/docker-logging.service
[Unit]
Description=Logging service
After=network-online.target docker.service
Wants=network-online.target

[Service]
WorkingDirectory=/opt/logging
Environment="HTTP_PROXY=http://proxy.example.com:3138"
Environment="HTTPS_PROXY=http://proxy.example.com:3138"
Environment="NO_PROXY=*.example.com,localhost,localnet,127.0.0.0/8,10.0.0.0/8"
RemainAfterExit=yes
KillMode=process
KillSignal=SIGTERM
TimeoutStopSec=30
TimeoutStartSec=300
Restart=always
RestartSec=5m
ExecStart=/bin/bash -c "PATH=/usr/local/bin:$PATH exec docker compose --file /opt/logging/docker-compose.yml up -d"
ExecStartPre=/bin/bash -c "PATH=/usr/local/bin:$PATH exec docker compose --file /opt/logging/docker-compose.yml up --force-recreate --no-start postgresql rsyslog loganalyzer"
ExecReload=/bin/bash -c "PATH=/usr/local/bin:$PATH exec docker compose --file /opt/logging/docker-compose.yml restart"
ExecStop=/bin/bash -c "PATH=/usr/local/bin:$PATH exec docker compose --file /opt/logging/docker-compose.yml down"
ExecStartPre=/bin/sleep 1
ExecStartPost=/bin/sleep 3
SyslogIdentifier=docker-logging

[Install]
WantedBy=multi-user.target
Alias=logging.service
EOF

```

Активация сервиса

```bash
systemctl daemon-reload
systemctl enable docker-logging
```

Перезапуск сервиса

```bash
systemctl restart docker-logging
systemctl status -l docker-logging
```

Проверка сервиса после перезагрузки

```bash
journalctl -fu docker-logging
docker compose --file /opt/logging/docker-compose.yml events
```

Результат:

```output
Dec 13 16:00:37 logger.example.com docker-logging[1654]:  Container postgresql  Started
Dec 13 16:00:37 logger.example.com docker-logging[1654]:  Container postgresql  Waiting
Dec 13 16:00:40 logger.example.com systemd[1]: Started Logging service.
Dec 13 16:00:48 logger.example.com docker-logging[1654]:  Container postgresql  Healthy
Dec 13 16:00:48 logger.example.com docker-logging[1654]:  Container rsyslog  Starting
Dec 13 16:00:48 logger.example.com docker-logging[1654]:  Container rsyslog  Started
Dec 13 16:00:48 logger.example.com docker-logging[1654]:  Container postgresql  Waiting
Dec 13 16:00:49 logger.example.com docker-logging[1654]:  Container postgresql  Healthy
Dec 13 16:00:49 logger.example.com docker-logging[1654]:  Container loganalyzer  Starting
Dec 13 16:00:49 logger.example.com docker-logging[1654]:  Container loganalyzer  Started

```

#### <a name="docker-help-commands">Docker команды</a>

Настройки проксирования для контейнеров (изнутри)

```bash
cat << EOF > ~/.docker/config.json
{
 "proxies": {
   "default": {
     "httpProxy": "http://proxy.example.com:3128",
     "httpsProxy": "http://proxy.example.com:3128",
     "noProxy": "*.example.com,localhost,localnet,127.0.0.0/8,10.0.0.0/8"
   }
 }
}
EOF
```

Настройки проксирования для `docker`-команды

```bash
cat << EOF > /etc/docker/daemon.json
{
  "storage-driver": "overlay2",
  "exec-opts": ["native.cgroupdriver=systemd"],
  "proxies": {
    "http-proxy": "http://proxy.example.com:3128",
    "https-proxy": "http://proxy.example.com:3128",
    "no-proxy": "*.example.com,127.0.0.0/8,10.0.0.0/8,172.16.0.0/12"
  }
}
EOF
```

Получить список IP адресов контейнеров

```bash
docker ps -q | xargs -n 1 docker inspect --format '{{range .NetworkSettings.Networks}}{{.IPAddress}}{{end}} {{ .Name }}' | sed 's/ \// /g'
```

Удаление logging контейнеров (ansible) и остановка

```bash
ansible-playbook -v playbooks/remove.yaml
```

Удалить все `docker volumes`

```bash
docker volume rm $(docker volume ls -q)
```

Удалить *весь* logging и пересоздать контейнеры

> Необходимо указывать прокси

```bash
docker compose --file /opt/logging/docker-compose.yml down
docker system prune --all --force --volumes
docker compose --file /opt/logging/docker-compose.yml up -d --no-start postgresql rsyslog loganalyzer
```

Пересоздать контейнер loganalyzer после изменения конфигурации `/opt/logging/loganalyzer`

```bash
docker compose --file /opt/logging/docker-compose.yml stop loganalyzer
docker system prune -a -f --volumes # удаление неактивных файлов
docker compose --file /opt/logging/docker-compose.yml up -d --force-recreate loganalyzer
```

Удалить loganalyzer

```bash
docker compose down loganalyzer
docker volume rm logging_loganalyzer_conf logging_loganalyzer_data logging_loganalyzer_log
docker rmi logging-loganalyzer:latest
```

Перезапустить loganalyzer (и пересобрать)

```bash
docker compose up -d loganalyzer
```

Посмотреть ошибки в apache2 у loganalyzer

```bash
tail -f /var/lib/docker/volumes/logging_loganalyzer_log/_data/ssl_error.log 
```

Посмотреть файл сайт loganalyzer `.htaccess`

```bash
cat /var/lib/docker/volumes/logging_loganalyzer_data/_data/.htaccess
```

#### <a name="server-list">Список серверов</a>

Ниже указываются сервера, которые имеют настройки для отправки лог на сервер `logger`.
Позже этот список можно перевести в Ansible inventory:

- squid

Имя файла`inventory.json` в корне проекта

```yaml
{
  "logging": {
    "hosts": {
      "squid" : {
        "ansible_host": "10.10.160.11"
      }
    }
  }
}
```

или в формате `yaml`

```yaml
all:
  hosts:
  children:
...
    logging:
      hosts:
        squid:
          ansible_host: 10.10.160.11
```

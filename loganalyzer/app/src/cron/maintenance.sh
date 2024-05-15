#!/bin/sh

cd /htdocs/cron/
php ./maintenance.php cleandata Source1 olderthan 2592000

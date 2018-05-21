#!/bin/bash
ME=$(dirname $0)
export BDD_PWD=$(openssl rand -base64 100)
export PATH=~/.composer/vendor/bin/:$PATH
mysql -uroot -e "CREATE DATABASE IF NOT EXISTS accountmanager"
mysql -uroot < sql/structure.sql
mysql -uroot < sql/routines.sql
mysql -uroot < sql/views.sql
mysql -uroot < sql/events.sql
mysql -uroot < sql/triggers.sql
mysql -uroot -e "SET PASSWORD = PASSWORD('$BDD_PWD')"
echo -e "DB_HOST=localhost\r\nDB_USER=root\r\nDB_PASS=$BDD_PWD\r\n" > $ME/../../.env
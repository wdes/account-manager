#!/bin/bash
ME=$(dirname $0)
export BDD_PWD=$(openssl rand -base64 100)
export PATH=~/.composer/vendor/bin/:$PATH
mysql -uroot -e "CREATE DATABASE IF NOT EXISTS accountmanager"
mysql -uroot < $ME/../../sql/structure.sql
mysql -uroot < $ME/../../sql/routines.sql
mysql -uroot < $ME/../../sql/views.sql
mysql -uroot < $ME/../../sql/events.sql
mysql -uroot < $ME/../../sql/triggers.sql
mysql -uroot -e "SET PASSWORD = PASSWORD('$BDD_PWD')"
echo -e "DB_HOST=localhost\r\nDB_USER=root\r\nDB_PASS=$BDD_PWD\r\n" > $ME/../../.env
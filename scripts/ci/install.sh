#!/usr/bin/env bash
ME="$(dirname $0)"
set RANDFILE=.rnd
export BDD_PWD="$(openssl rand -base64 32)"
export PATH=~/.composer/vendor/bin/:$PATH
set -x
set -e
sudo /etc/init.d/mysql stop || echo "mysql not stopped"
sudo stop mysql-5.6 || echo "mysql-5.6 not stopped"
sudo  mysqld_safe --skip-grant-tables &
sleep 4
sudo mysql -e "update mysql.user set authentication_string=PASSWORD('$BDD_PWD') where User='root' and host='%'; update mysql.user set plugin='mysql_native_password'; delete from mysql.user where User != 'root' OR host != '%'; FLUSH PRIVILEGES;"
sudo kill -9 `sudo cat /var/lib/mysql/mysqld_safe.pid`
sudo kill -9 `sudo cat /var/run/mysqld/mysqld.pid`
sudo /etc/init.d/mysql restart
sleep 4

mysql -uroot -p"$BDD_PWD" -e "CREATE DATABASE IF NOT EXISTS accountmanager CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;"
mysql -uroot -p"$BDD_PWD" accountmanager < $ME/../../sql/structure.sql
mysql -uroot -p"$BDD_PWD" accountmanager < $ME/../../sql/routines.sql
mysql -uroot -p"$BDD_PWD" accountmanager < $ME/../../sql/views.sql
mysql -uroot -p"$BDD_PWD" accountmanager < $ME/../../sql/events.sql
mysql -uroot -p"$BDD_PWD" accountmanager < $ME/../../sql/triggers.sql
mysql -uroot -p"$BDD_PWD" -e "SELECT User,host FROM mysql.user;"

echo -e "DB_HOST=localhost\r\nDB_USER=root\r\nDB_NAME=accountmanager\r\nDB_PASS=\"$BDD_PWD\"\r\n" > $ME/../../.env
cp $ME/../../.env $ME/../../tests/.env
echo "Database password: $BDD_PWD"
cat $ME/../../tests/.env

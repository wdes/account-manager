#!/bin/bash
ME=$(dirname $0)
if [ !-f "$ME/../sql-backup/.env" ]; then
    echo ".env file missing"
    exit 1;
fi;
$ME/../sql-backup/backup.sh
git commit -am "[SQL] updates" -S -m "[CI SKIP]" --author "Account manager BOT <accountmanager@wdes.fr>"

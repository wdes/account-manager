#!/bin/bash
ME=$(dirname $0)
$ME/generate_pot.php
$ME/po_update.php
$ME/po_to_mo.php
git commit -am "[Translations] updates" -m "[CI SKIP]" --author "Account manager BOT <accountmanager@wdes.fr>"
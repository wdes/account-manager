#!/usr/bin/php
<?php

exec("export LC_ALL=C");
$po_file = realpath(__DIR__."/../po/account-manager.pot");

function poupdate($po_file) {
    $pot_contents = file_get_contents($po_file);
    $pot_contents = str_replace("SOME DESCRIPTIVE TITLE", "Wdes Account manager translation", $pot_contents);
    $pot_contents = str_replace("YEAR", "(C) 2018 - ".date("Y"), $pot_contents);
    file_put_contents($po_file, $pot_contents);
}
$podir = realpath(__DIR__."/../po/")."/";
echo "PoDir: ${podir}\r\n";
foreach (glob("${podir}*.po") as $file) {
    exec("msgmerge --previous -U $file ${podir}account-manager.pot");
    echo "File: $file\r\n";
    poupdate($file);
}

/*
# Create commit
git add "po/*.po" po/phpmyadmin.pot
git commit -s -m '[PO] Update files' -m '[CI skip]'
*/
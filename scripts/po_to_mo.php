#!/usr/bin/php
<?php
require_once __DIR__.'/../vendor/autoload.php';

$localedir = realpath(__DIR__."/../locale")."/";
echo "LocaleDir: $localedir\r\n";


\AccountManager\Utils\FS::rmdir_recursive($localedir);
mkdir($localedir);
$podir = realpath(__DIR__."/../po/")."/";
foreach (glob($podir."*.po") as $filename) {
    $lang = str_replace(".po", "", basename($filename));

    mkdir("locale/$lang/LC_MESSAGES", 0777, true);
    exec("msgfmt --directory=$podir --check -o locale/$lang/LC_MESSAGES/account-manager.mo $filename");
    echo "Lang: $lang\r\n";
}
echo "Done !\r\n";
?>
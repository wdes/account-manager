#!/usr/bin/env php
<?php
require_once __DIR__.'/../vendor/autoload.php';
require_once __DIR__.'/../src/Constants.php';

echo "LocaleDir: ".LOCALE_DIR."\r\n";

if (is_dir(LOCALE_DIR)) {
\AccountManager\Utils\FS::rmdir_recursive(LOCALE_DIR);
}
mkdir(LOCALE_DIR);
$podir = realpath(__DIR__."/../po/")."/";
foreach (glob($podir."*.po") as $filename) {
    $lang = str_replace(".po", "", basename($filename));

    mkdir("locale/$lang/LC_MESSAGES", 0777, true);
    exec("msgfmt --directory=$podir --check -o locale/$lang/LC_MESSAGES/account-manager.mo $filename");
    echo "Lang: $lang\r\n";
}
echo "Done !\r\n";
?>
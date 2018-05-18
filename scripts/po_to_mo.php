#!/usr/bin/php
<?php
$localedir = realpath(__DIR__."/../locale")."/";
echo "LocaleDir: $localedir\r\n";
// Source : stackoverflow.com/a/7288067/5155484
function rmdir_recursive($dir) {
    foreach(scandir($dir) as $file) {
        if ('.' === $file || '..' === $file) continue;
        if (is_dir("$dir/$file")) rmdir_recursive("$dir/$file");
        else unlink("$dir/$file");
    }
    rmdir($dir);
}

rmdir_recursive($localedir);
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
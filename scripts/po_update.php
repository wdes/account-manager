#!/usr/bin/php
<?php
/**
 * @license http://unlicense.org/UNLICENSE The UNLICENSE
 * @author William Desportes <williamdes@wdes.fr>
 */
require_once __DIR__.'/../src/Constants.php';

$po_template = realpath(PO_DIR."account-manager.pot");
$template = file_get_contents($po_template);

$mappings = new stdClass();
$mappings->mappings = array();
$mappings->replacements = array();

if (is_file(TMP_DIR."mapping.json"))
    $mappings = json_decode(file_get_contents(TMP_DIR."mapping.json"));


foreach($mappings->replacements as $replacement ) {
    $template = str_replace($replacement->from, $replacement->to, $template);
}

$parts = explode('msgid ', $template);
$license_block = str_replace(", fuzzy", "", $parts[0]);

file_put_contents($po_template, $template);

function poupdate($po_file) {
    global $mappings, $license_block;
    $pot_contents = file_get_contents($po_file);

    $parts = explode('msgid ', $pot_contents);
    $pot_contents = str_replace($parts[0], $license_block, $pot_contents);

    // Replace filename by name
    $pot_contents = preg_replace_callback(
        '@([0-9a-f]{2}\/[0-9a-f]*.php):([0-9]*)@',
        function ($matchs) {
            global $mappings;
            $line = intval($matchs[2]);
            $replace = $mappings->mappings->{$matchs[1]};
            foreach ($replace->debugInfo as $cacheLineNumber => $iii) {
                if ($line >= $cacheLineNumber) {
                    return $replace->fileName . ':' . $iii;
                }
            }
            return $replace->fileName . ':0';
        },
        $pot_contents
    );
    file_put_contents($po_file, $pot_contents);
}


echo "PoDir: ".PO_DIR."\r\n";
foreach (glob(PO_DIR."*.po") as $file) {
    exec("msgmerge --quiet --previous -U $file ".PO_DIR."account-manager.pot");
    echo "File: $file\r\n";
    poupdate($file);
}
poupdate($po_template);

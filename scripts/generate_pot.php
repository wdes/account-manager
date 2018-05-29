#!/usr/bin/env php
<?php
declare(strict_types = 1);
/**
 * @license http://unlicense.org/UNLICENSE The UNLICENSE
 * @author William Desportes <williamdes@wdes.fr>
 */
require_once __DIR__.'/../vendor/autoload.php';
require_once __DIR__.'/../src/Constants.php';

$tmpDir       = TMP_DIR.'twig_cache/';
$shortTempDir = str_replace(PROJECT_ROOT, "", TEMPLATE_DIR);

\AccountManager\Utils\FS::rmdirRecursive($tmpDir);
\AccountManager\Twig\Load::init($tmpDir);
$__twig                 = \AccountManager\Twig\Load::getTwig();
$mappings               = new stdClass();
$mappings->mappings     = array();
$mappings->replacements = array();

$license = new stdClass();

$license->from            = "This file is";
$license->from           .= " distributed";
$license->from           .= " under the same";
$license->from           .= " license as the PACKAGE package.";
$license->to              = "This file is distributed under the license http://unlicense.org/UNLICENSE";
$mappings->replacements[] = $license;

$license                  = new stdClass();
$license->from            = "PACKAGE VERSION";
$license->to              = "1.0.0-alpha1";
$mappings->replacements[] = $license;

$firstauthor              = new stdClass();
$firstauthor->from        = "FIRST AUTHOR <EMAIL@ADDRESS>, YEAR.";
$firstauthor->to          = "William Desportes <williamdes@wdes.fr>";
$mappings->replacements[] = $firstauthor;

$description              = new stdClass();
$description->from        = "SOME DESCRIPTIVE TITLE";
$description->to          = "Wdes Account manager translation";
$mappings->replacements[] = $description;

$year                     = new stdClass();
$year->from               = "YEAR";
$year->to                 = date("Y");//2018 -
$mappings->replacements[] = $year;

$templates                = new stdClass();
$templates->from          = $tmpDir;
$templates->to            = "";
$mappings->replacements[] = $templates;



foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator(TEMPLATE_DIR), RecursiveIteratorIterator::LEAVES_ONLY) as $tmpl) {

    if ($tmpl->isFile()) {
        $shortName = str_replace(TEMPLATE_DIR, '', $tmpl);
        $template  = $__twig->loadTemplate($shortName);
        $key       = \AccountManager\Twig\Load::getTwigCacheFS()->generateKey($shortName, $__twig->getTemplateClass($shortName));

        $cacheFile = str_replace(
            $tmpDir,
            '',
            $key
        );

        $mappings->mappings[$cacheFile] = new stdClass();

        $mappings->mappings[$cacheFile]->fileName  = $shortTempDir.$shortName;
        $mappings->mappings[$cacheFile]->debugInfo = $template->getDebugInfo();
    }

}
file_put_contents(TMP_DIR."mapping.json", json_encode($mappings, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
exec(
    'xgettext --force-po --from-code=UTF-8'.
    ' --default-domain=account-manager'.
    ' --copyright-holder="William Desportes"'.
    ' --msgid-bugs-address=williamdes@wdes.fr'.
    ' --from-code=utf-8'.
    ' --keyword=__ --keyword=_gettext --keyword=_pgettext:1c,2 --keyword=_ngettext:1,2'.
    ' -p ./locale'.
    ' --from-code=UTF-8'.
    ' --add-comments=l10n'.
    ' --add-location -L PHP $(find "'.PROJECT_ROOT.'" \( -name "*.php" \)'.
    ' -not -path "'.TMP_DIR.'twig/*"'.
    ' -not -path "'.PROJECT_ROOT.'vendor/*"'.
    ' -not -path "'.PROJECT_ROOT.'sql/*"'.
    ' -not -path "'.PROJECT_ROOT.'scripts/*"'.
    ' -not -path "'.PROJECT_ROOT.'public/*"'.
    ' -not -path "'.PROJECT_ROOT.'po/*"'.
    ' -not -path "'.PROJECT_ROOT.'sql-backup/*"'.
    ' -not -path "'.PROJECT_ROOT.'node_modules/*"'.
    ' -not -path "'.PROJECT_ROOT.'tests/*"  | sort) '.
    '-o '.PO_DIR."account-manager.pot"
);

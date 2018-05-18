#!/usr/bin/php
<?php
/**
 * @license http://unlicense.org/UNLICENSE The UNLICENSE
 * @author William Desportes <williamdes@wdes.fr>
 */
require_once __DIR__.'/../vendor/autoload.php';
$templatesPath = "src/";
$tplDir = realpath(__DIR__.'/../'.$templatesPath.'templates');
$tmpDir = realpath(__DIR__.'/../tmp/twig_cache/').'/';
$loader = new Twig_Loader_Filesystem($tplDir);
$cacheFS = new \Twig_Cache_Filesystem($tmpDir);
PhpMyAdmin\MoTranslator\Loader::loadFunctions();
// force auto-reload to always have the latest version of the template
$twig = new Twig_Environment($loader, array(
    'cache' => $cacheFS,//$tmpDir,
    'auto_reload' => true
));
$twig->addExtension(new AccountManager\Twig\I18nExtension());

$mappings = new stdClass();
$mappings->mappings = array();
$mappings->replacements = array();
$description = new stdClass();
$description->from = "SOME DESCRIPTIVE TITLE";
$description->to = "Wdes Account manager translation";
$mappings->replacements[] = $description;
$year = new stdClass();
$year->from = "YEAR";
$year->to = date("Y");//2018 -
$mappings->replacements[] = $year;
$templates = new stdClass();
$templates->from = $tmpDir;
$templates->to = $templatesPath;
$mappings->replacements[] = $templates;


// iterate over all your templates
foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($tplDir), RecursiveIteratorIterator::LEAVES_ONLY) as $tmpl)
{
    // force compilation
    if ($tmpl->isFile()) {
        $short_name = str_replace($tplDir.'/', '', $tmpl);
        $template = $twig->loadTemplate($short_name);
        $key = $cacheFS->generateKey($short_name, $twig->getTemplateClass($short_name) );
        //echo $key."\r\n";
        $cache_file = str_replace(
            $tmpDir, '',
            $key
        );
        $mappings->mappings[$cache_file] = new stdClass();
        //$mappings->mappings[$cache_file]->key = $key;
        $mappings->mappings[$cache_file]->fileName = "templates/".$short_name;
        $mappings->mappings[$cache_file]->debugInfo = $template->getDebugInfo();
    }
    $tmpl_file = 'templates/' . $tmpl;
}
file_put_contents(__DIR__."/../tmp/mapping.json",json_encode($mappings, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
exec(
'xgettext --force-po --from-code=UTF-8'.
' --default-domain=account-manager'.
' --copyright-holder="William Desportes"'.
' --package-name="wdes/account-manager"'.
' --package-version="1.0.0-alpha1"'.
' --msgid-bugs-address=williamdes@wdes.fr'.
' --from-code=utf-8'.
' --keyword=__ --keyword=_gettext --keyword=_pgettext:1c,2 --keyword=_ngettext:1,2'.
' -p ./locale'.
' --from-code=UTF-8'.
' --add-comments=l10n'.
' --add-location -L PHP $(find "$(pwd -P)" \( -name "*.php" \) -not -path "$(pwd -P)/vendor/*" | sort) -o '.__DIR__."/../po/account-manager.pot");


#!/usr/bin/php
<?php
$path = realpath('./locale');
bindtextdomain($domain, $path);
bind_textdomain_codeset($domain, "UTF-8");
textdomain($domain);
require_once __DIR__.'/../vendor/autoload.php';
$tplDir = __DIR__.'/../src/templates';
$tmpDir = __DIR__.'/../tmp/twig_cache/';
$loader = new Twig_Loader_Filesystem($tplDir);

// force auto-reload to always have the latest version of the template
$twig = new Twig_Environment($loader, array(
    'cache' => $tmpDir,
    'auto_reload' => true
));
$twig->addExtension(new Twig_Extensions_Extension_I18n());
// configure Twig the way you want

// iterate over all your templates
foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($tplDir), RecursiveIteratorIterator::LEAVES_ONLY) as $file)
{
    // force compilation
    if ($file->isFile()) {
        $twig->loadTemplate(str_replace($tplDir.'/', '', $file));
    }
}
exec(
'xgettext --force-po --from-code=UTF-8'.
' --default-domain=account-manager'.
' --copyright-holder="William Desportes"'.
' --package-name="wdes/account-manager 1.0.0-alpha1"'.
' --msgid-bugs-address=williamdes@wdes.fr'.
' -p ./locale'.
' --from-code=UTF-8'.
' --add-comments=l10n'.
' --add-location -L PHP $(find $tmpDir -name "*.php") -o '.__DIR__."/../po/account-manager.pot");
<?php
 
# Avoids illegal processing, doesn't cost much, but unnecessary on a correct installation
if (!defined('MEDIAWIKI')) { die(-1); } 
 
# Extension Declaration
$GLOBALS['wgExtensionCredits']['specialpage'][] = array(
	'path' => __FILE__,
	'name' => 'ProteoWiki',
	'author' => array('toniher'),
	'version' => '0.1',
	'url' => 'https://www.mediawiki/wiki/User:Toniher',
	'descriptionmsg' => 'proteowiki-desc'
);
 
# A var to ease the referencing of files
$dir = dirname(__FILE__) . '/';

# i18n file referencing
$GLOBALS['wgMessagesDirs']['ProteoWiki'] = $dir . 'i18n';
$GLOBALS['wgExtensionMessagesFiles']['ProteoWiki'] = $dir . 'ProteoWiki.i18n.php';

$GLOBALS['wgAutoloadClasses']['SpecialProteowiki'] = $dir . 'includes/specials/ProteoWiki_Special.php';
$GLOBALS['wgAutoloadClasses']['SpecialProteowikiCSV'] = $dir . 'includes/specials/ProteoWiki_SpecialCSV.php';
$GLOBALS['wgAutoloadClasses']['SpecialProteowikiUpload'] = $dir . 'includes/specials/ProteoWiki.SpecialUpload.php';

# SpecialPage referencing
$GLOBALS['wgSpecialPages']['ProteoWiki'] = 'SpecialProteowiki';
$GLOBALS['wgSpecialPages']['ProteoWikiCSV'] = 'SpecialProteowikiCSV';
$GLOBALS['wgSpecialPages']['ProteoWikiUpload'] = 'SpecialProteowikiUpload';
# SpecialPage category
$GLOBALS['wgSpecialPageGroups']['ProteoWiki'] = 'other';
$GLOBALS['wgSpecialPageGroups']['ProteoWikiCSV'] = 'other';
$GLOBALS['wgSpecialPageGroups']['ProteoWikiUpload'] = 'other';


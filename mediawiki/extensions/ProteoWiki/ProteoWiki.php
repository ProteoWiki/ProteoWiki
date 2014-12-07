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
$GLOBALS['wgExtensionMessagesFiles']['ProteoWikiMagic'] = $dir . 'ProteoWiki.magic.php';

$GLOBALS['wgAutoloadClasses']['SpecialProteowiki'] = $dir . 'includes/specials/ProteoWiki_Special.php';
$GLOBALS['wgAutoloadClasses']['SpecialProteowikiCSV'] = $dir . 'includes/specials/ProteoWiki_SpecialCSV.php';
$GLOBALS['wgAutoloadClasses']['SpecialProteowikiUpload'] = $dir . 'includes/specials/ProteoWiki.SpecialUpload.php';
$GLOBALS['wgAutoloadClasses']['ProteoWikiParserFunctions'] = $dir . 'includes/ProteoWiki_ParserFunctions.php';


# SpecialPage referencing
$GLOBALS['wgSpecialPages']['ProteoWiki'] = 'SpecialProteowiki';
$GLOBALS['wgSpecialPages']['ProteoWikiCSV'] = 'SpecialProteowikiCSV';
$GLOBALS['wgSpecialPages']['ProteoWikiUpload'] = 'SpecialProteowikiUpload';
# SpecialPage category
$GLOBALS['wgSpecialPageGroups']['ProteoWiki'] = 'other';
$GLOBALS['wgSpecialPageGroups']['ProteoWikiCSV'] = 'other';
$GLOBALS['wgSpecialPageGroups']['ProteoWikiUpload'] = 'other';

# ParserFunctions
$GLOBALS['wgHooks']['ParserFirstCallInit'][] = 'ProteoWikiParserFunctions::registerFunctions';

// Variables
// Namespace where to Configuration files ( e.g CSV files, and potentially others in the future )
define("NS_PROTEOWIKICONF", 1000);
$GLOBALS['wgExtraNamespaces'][NS_PROTEOWIKICONF] = "ProteoWikiConf";

// Dynamic elements
define("NS_REQUEST", 1002);
$GLOBALS['wgExtraNamespaces'][NS_REQUEST] = "Request";
define("NS_SAMPLE", 1004);
$GLOBALS['wgExtraNamespaces'][NS_SAMPLE] = "Sample";
define("NS_PROCESS", 1006);
$GLOBALS['wgExtraNamespaces'][NS_PROCESS] = "Process";
// Group associated to user -> Can be institution, for instance. Not central to workflow
define("NS_GROUP", 1008);
$GLOBALS['wgExtraNamespaces'][NS_GROUP] = "Group";

// Static elements that can host views, actions and reports
define("NS_DASHBOARD", 1010);
$GLOBALS['wgExtraNamespaces'][NS_DASHBOARD] = "Dashboard";

// SMW initialization
$GLOBALS['smwgNamespacesWithSemanticLinks'] = array(
	NS_REQUEST => true,
	NS_SAMPLE => true,
	NS_PROCESS => true,
	NS_GROUP => true,
	NS_DASHBOARD => true,
);


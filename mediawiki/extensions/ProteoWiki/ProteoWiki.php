<?php
 
# Avoids illegal processing, doesn't cost much, but unnecessary on a correct installation
if (!defined('MEDIAWIKI')) { die(-1); } 


if ( defined( 'PROTEOWIKI' ) ) {
	// Do not load more than once.
	return 1;
}

define( 'PROTEOWIKI', '0.1' );

if ( !defined( 'SMW_VERSION' ) ) {
	die( "ERROR: <a href=\"http://semantic-mediawiki.org\">Semantic MediaWiki</a> must be installed for this extension to run!" );
}

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

$GLOBALS['wgAutoloadClasses']['SpecialProteowiki'] = $dir . 'includes/specials/ProteoWiki.SpecialDashboard.php';
$GLOBALS['wgAutoloadClasses']['SpecialProteowikiUpload'] = $dir . 'includes/specials/ProteoWiki.SpecialUpload.php';
$GLOBALS['wgAutoloadClasses']['ProteoWikiParserFunctions'] = $dir . 'includes/ProteoWiki_ParserFunctions.php';
$GLOBALS['wgAutoloadClasses']['ProteoWikiGenerate'] = $dir . 'includes/ProteoWiki_Generate.php';
$GLOBALS['wgAutoloadClasses']['ProteoWikiImport'] = $dir . 'includes/ProteoWiki_Import.php';

$GLOBALS['wgAutoloadClasses']['ApiProteoWikiConf'] = $dir . 'includes/ProteoWiki_APIConf.php';
$GLOBALS['wgAPIModules']['proteowikiconf'] = 'ApiProteoWikiConf';

//$GLOBALS['wgAutoloadClasses']['ProteoWikiJob'] = $dir . 'includes/ProteoWiki_Job.php';
//$GLOBALS['wgAutoloadClasses']['DTImportJob'] = $dir . 'includes/DTImportJob.php';


# SpecialPage referencing
$GLOBALS['wgSpecialPages']['ProteoWiki'] = 'SpecialProteowiki';
$GLOBALS['wgSpecialPages']['ProteoWikiUpload'] = 'SpecialProteowikiUpload';
# SpecialPage category
$GLOBALS['wgSpecialPageGroups']['ProteoWiki'] = 'other';
$GLOBALS['wgSpecialPageGroups']['ProteoWikiUpload'] = 'other';

# ParserFunctions
$GLOBALS['wgHooks']['ParserFirstCallInit'][] = 'registerHook';

function registerHook( &$parser ) {
	
	$parser->setHook( 'proteowikiconf', 'ProteoWikiParserFunctions::wfProteoWikiConf_Parser' );

	return true;
}


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
	NS_GROUP => true
);

$GLOBALS['wgResourceModules']['ext.ProteoWiki'] = array(
	'localBasePath' => $dir,
	'scripts' => array( 'libs/jquery-handsontable/jquery.handsontable.full.js', 'libs/proteowiki.js' ),
	'styles' => array( 'libs/jquery-handsontable/jquery.handsontable.full.css', 'css/proteowiki.less' ),
	'remoteExtPath' => 'ProteoWiki'
);

$GLOBALS['wgProteoWikiPages'] = array();
$GLOBALS['wgProteoWikiPages']['Properties'] = array('Request Properties', 'Sample Properties', 'Process Properties');
$GLOBALS['wgProteoWikiPages']['Associations'] = array('Associations');
$GLOBALS['wgProteoWikiPages']['Generators'] = array('Generators');


#RunJobs
$GLOBALS['wgAutoloadClasses']['DTImportJob'] = $dir . '/includes/DT_ImportJob.php';
$GLOBALS['wgJobClasses']['dtImport'] = 'DTImportJob';

$GLOBALS['wgProteoWikiJobOut'] = "/tmp";
$GLOBALS['wgRunJobsPath'] = $dir . '/../../maintenance/runJobs.php';
$GLOBALS['wgRunJobsProcs'] = 2;



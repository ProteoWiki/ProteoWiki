<?php

if ( !defined( 'MEDIAWIKI' ) ) {
	echo 'Not a valid entry point';
	exit( 1 );
}

if ( !defined( 'SMW_VERSION' ) ) {
	echo 'This extension requires Semantic MediaWiki to be installed.';
	exit( 1 );
}

#
# This is the path to your installation of SemanticTasks as
# seen from the web. Change it if required ($wgScriptPath is the
# path to the base directory of your wiki). No final slash.
# #
$spScriptPath = $wgScriptPath . '/extensions/SMWListProps';
#

# Extension credits
$wgExtensionCredits[defined( 'SEMANTIC_EXTENSION_TYPE' ) ? 'semantic' : 'other'][] = array(
	'path' => __FILE__,
	'name' => 'SMWListProps',
	'author' => array(
		'[https://www.mediawiki.org/wiki/User:Toniher Toni Hermoso]'
	),
	'version' => '0.1',
	'url' => 'https://www.mediawiki.org/wiki/Extension:SMWListProps',
	'descriptionmsg' => 'smwlistprops-desc',
);


// i18n
$wgExtensionMessagesFiles['SMWListProps'] = dirname( __FILE__ ) . '/SMWListProps.i18n.php';
$wgExtensionMessagesFiles['SMWListPropsMagic'] = dirname( __FILE__ ) . '/SMWListProps.i18n.magic.php';

// Autoloading
$wgAutoloadClasses['SMWListProps'] = dirname( __FILE__ ) . '/SMWListProps.classes.php';

$wgHooks['ParserFirstCallInit'][] = 'wfRegisterSMWListProps';
// Hooks


function wfRegisterSMWListProps( $parser ) {

	$parser->setFunctionHook( 'SMWListProps', 'SMWListProps::executeGetListProps', SFH_OBJECT_ARGS );
	return true;

}


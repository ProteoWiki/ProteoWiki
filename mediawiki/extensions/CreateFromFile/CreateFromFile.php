<?php
/**
 * Copyright (C) 2011 Toni Hermoso Pulido <toniher@cau.cat>
 * http://www.cau.cat
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 59 Temple Place - Suite 330, Boston, MA 02111-1307, USA.
 * http://www.gnu.org/copyleft/gpl.html
 *
 */

if ( !defined( 'MEDIAWIKI' ) ) {
    echo "Not a valid entry point";
    exit( 1 );
}

#Skip creation of pages already exist
$wgSkipCreateFromFile = "skip";
$wgCFAllowedGroups = array( 'sysop', 'manager' );

$wgCFParamsMailing = array();

$wgCreateFromFileTmpDir = "/tmp";
# Example group
// TODO: Add columns and unique key checks!
$wgCreateFromFileftypes = array(
								"AQUA" => array(
									"name" => "AQUA",
									"title" => "AQUA:#I4",
									"category" => "AQUAs",
									"cols" => array("Has SequencePeptide", "Has NakedSequencePeptide", "Has Protein", "Has Label", "Has Species", "Has Purity", "Has Company", "Has Responsible", "Has User", "Has Rack", "Has Box", "Has Comments"),
									"uniqcols" => array("Has SequencePeptide", "Has Protein", "Has User")
								)
							);


$wgExtensionCredits['parserhook'][] = array(
	'path' => __FILE__,
	'name' => 'CreateFromFile',
	'author' => 'Toni Hermoso',
	'version' => '0.1',
	'url' => 'http://www.mediawiki.org/wiki/Extension:CreateFromFile',
	'descriptionmsg' => 'createfromfile-desc',
);

$wgResourceModules['ext.CreateFromFile'] = array(
	'localBasePath' => dirname( __FILE__ ),
	'scripts' => array( 'libs/jquery-handsontable/jquery.handsontable.full.js', 'libs/CreateFromFile.js' ),
	'styles' => array( 'libs/jquery-handsontable/jquery.handsontable.full.css', 'css/CreateFromFile.css', ),
	'remoteExtPath' => 'CreateFromFile'
);

$wgAutoloadClasses['CreateFromFile'] = dirname(__FILE__) . '/CreateFromFile_body.php';
$wgAutoloadClasses['CreateFromFileSMW'] = dirname( __FILE__ ) . '/CreateFromFile.smw.php';

$wgExtensionMessagesFiles['CreateFromFile'] = dirname( __FILE__ ) . '/CreateFromFile.i18n.php';
#$wgExtensionMessagesFiles['CreateFromFileMagic'] = dirname(__FILE__) . '/CreateFromFile.i18n.magic.php';
$wgJobClasses['dtImport'] = 'DTImportJob';
$wgAutoloadClasses['DTImportJob'] = dirname(__FILE__) . '/includes/DT_ImportJob.php';

$wgHooks['ParserFirstCallInit'][] = 'wfRegisterCreateFromFile';

$wgHooks['LanguageGetMagic'][] = 'wfSetupCreateFromFileLanguageGetMagic';


#Ajax
$wgAjaxExportList[] = 'CreateFromFile::createfromfileJS';
$wgAjaxExportList[] = 'CreateFromFile::createfromSpreadJS';
$wgAjaxExportList[] = 'CreateFromFileSMW::searchJS';


# SpecialPage referencing
$wgAutoloadClasses['SpecialCreateFromFile'] = dirname( __FILE__ ) . '/CreateFromFile.form.php';

$wgSpecialPages['CreateFromFile'] = 'SpecialCreateFromFile';

#RunJobs
$wgRunJobsPath = dirname(__FILE__) . '/../../maintenance/runJobs.php';

function wfRegisterCreateFromFile() {
	
	global $wgParser;
	$wgParser->setFunctionHook( 'createfromfile', "CreateFromFile::createfromfiledone", SFH_OBJECT_ARGS );
	$wgParser->setFunctionHook( 'createfromfilelink', "CreateFromFile::createfromfilelink", SFH_OBJECT_ARGS );
	$wgParser->setFunctionHook( 'createSpread', "CreateFromFile::createSpread", SFH_OBJECT_ARGS );
	$wgParser->setFunctionHook( 'createSpreadlink', "CreateFromFile::createSpreadlink", SFH_OBJECT_ARGS );

	return true;

}

function wfSetupCreateFromFileLanguageGetMagic( &$magicWords, $langCode ) {
	switch ( $langCode ) {
	default:
			$magicWords['createfromfile']    = array( 0, 'createfromfile' );
			$magicWords['createfromfilelink']    = array( 0, 'createfromfilelink' );
			$magicWords['createSpread']    = array( 0, 'createSpread' );
			$magicWords['createSpreadlink']    = array( 0, 'createSpreadlink' );
	}
	return true;
}



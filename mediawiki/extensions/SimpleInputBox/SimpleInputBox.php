<?php
$wgExtensionCredits['parserhook'][] = array(
        'path' => __FILE__,     // Magic so that svn revision number can be shown
        'name' => "Simple Input Box Extension",
        'description' => "Simple Input Box extension",    // Should be using descriptionmsg instead so that i18n is supported (but this is a simple example).
        'version' => 1, 
        'author' => "Toniher",
        'url' => "http://labservice.biocore.crg.cat",
);


$wgExtensionFunctions[] = "sinputExtension";


function sinputExtension() {

	global $wgParser;

	$wgParser->setHook( "sinputbox", "sinputfunc" );
	$wgParser->setHook( "sselectbox", "sselectfunc" );
}


function sinputfunc( $input, $argv, $parser, $frame ) {

	$value = $parser->recursiveTagParse( $input, $frame );
	$extra = "";
	
	if ( isset( $argv['id'] ) ) {
		$extra .=" id='".$parser->recursiveTagParse( $argv['id'], $frame )."'";
	}
	
	if ( isset( $argv['size'] ) ) {
		$extra .=" size='".$parser->recursiveTagParse( $argv['size'], $frame )."'";
	}

	if ( isset( $argv['class'] ) ) {
		$extra .=" class='".$parser->recursiveTagParse( $argv['class'], $frame )."'";
	}
	
	if ( isset( $argv['name'] ) ) {
		$extra .=" name='".$parser->recursiveTagParse( $argv['name'], $frame )."'";
	}
	
	if ( isset( $argv['template'] ) ) {
		$extra .=" data-template='".$parser->recursiveTagParse( $argv['template'], $frame )."'";
	}
	
	return "<input type='text' $extra value='".$value."' />";

}

function sselectfunc( $input, $argv, $parser, $frame ) {

	$id = $parser->recursiveTagParse( $argv['id'], $frame );
	$value = $parser->recursiveTagParse( $input, $frame );

	$defaultval = "";
	
	if ( isset( $argv['default'] ) ) {
		$defaultval = trim($parser->recursiveTagParse( $argv['default'], $frame ));
	}
	
	$extra = "";
	if ( isset( $argv['class'] ) ) {
		$extra .=" class='".$parser->recursiveTagParse( $argv['class'], $frame )."'";
	}

	if ( isset( $argv['name'] ) ) {
		$extra .=" name='".$parser->recursiveTagParse( $argv['name'], $frame )."'";
	}
	
	$valuearr = explode(",", $value);	
	$out = "<select id='".$id."' $extra>";

	if ( isset( $argv['empty'] ) ) {

		$out.="<option></option>";
	}

	if ( count( $valuearr ) > 0) {
		foreach ( $valuearr as $val ) {
			$extra = "";
			if ( trim($val) == $defaultval ) {
				$extra = "selected='selected'";
			}

			$out.="<option ".$extra.">".trim($val)."</option>";
		}
	}

	$out.= "</select>";

	return $parser->insertStripItem( $out, $parser->mStripState );

}





<?php

/** Here we store ParserFunctions **/


class ProteoWikiParserFunctions {

	static function wfProteoWikiConf_Parser( $input, array $args, Parser $parser, PPFrame $frame ) {

		$output = "";
		$separator=",";
		$delimiter='"';

		if ( !empty( $input ) ) {
			global $wgOut;
			$wgOut->addModules( 'ext.ProteoWiki' );

			$output = "<div class='proteowikiconf' data-delimiter='".$delimiter."' data-separator='".$separator."'>".trim( $input )."</div>";
		}

		return array( $output, 'noparse' => true, 'isHTML' => true );
	}

}


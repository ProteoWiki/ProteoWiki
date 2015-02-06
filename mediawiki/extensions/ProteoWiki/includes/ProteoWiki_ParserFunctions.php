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
			
			$input = trim( strip_tags( $input ) );

			$output = "<div class='proteowikiconf' data-delimiter='".$delimiter."' data-separator='".$separator."'>".$input."</div>";
		}

		return array( $output, 'noparse' => true, 'isHTML' => true );
	}

	static function wfProteoWikiFormLinks( &$parser, $frame, $args ) {
		
		// TODO: Handle form links
		// 1 -> Form -> 2 Page -> 3 NS -> 4 Link -> 5 Random$
		return '{{FormLink|1|kk|Process|Process[Sample]|23233}}';
		return '';
	}
	


}


<?php

/** Here we store ParserFunctions **/


class ProteoWikiParserFunctions {

	static function wfProteoWikiConf_Parser( $input, array $args, Parser $parser, PPFrame $frame ) {

		$output = "";
		$separator=",";
		$delimiter='"';

		if ( !empty( $input ) ) {
			$wgOut = $parser->getOutput();
			$wgOut->addModules( 'ext.ProteoWiki' );
			
			$input = trim( strip_tags( $input ) );

			$output = "<div class='proteowikiconf' data-delimiter='".$delimiter."' data-separator='".$separator."'>".$input."</div>";
		}

		return array( $output, 'noparse' => true, 'isHTML' => true );
	}

	static function wfProteoWikiFormLinks( &$parser, $frame, $args ) {
		
		// TODO: Handle form links
		// 1 -> Form -> 2 Page -> 3 NS -> 4 Link -> 5 Random$
		
		// Direct form link
		// {{#formlink:form={{{1}}}|link text={{{1}}}|link type=button|query string=namespace={{{3}}}&{{{4}}}={{{2}}}|target={{{3}}}:{{{1}}}-{{Random Number|10000}} }}
		
		return '{{FormLink|1|kk|Process|Process[Sample]|23233}}';
		//return '';
	}
	


}


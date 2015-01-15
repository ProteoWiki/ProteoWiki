<?php

/** Here a set of importing functions **/


class ProteoWikiImport {
	
	/** Import of Configuration, let's say, at commit */
	public static function importConf( $text, $pagetitle, $delimiter=',', $enclosure="\"" ) {

		$title = Title::newFromText( $pagetitle );
		$wikipage = WikiPage::factory( $title );
		
		$prefix = "<proteowikiconf data-delimiter='".$delimiter."' data-enclosure='".$enclosure."'>";
		$sufix = "</proteowikiconf>";
		$text = $prefix."\n".$text."\n".$sufix."\n";
		
		$content = new WikiTextContent( $text );
		$status = $wikipage->doEditContent( $content, "Updating content" );

		return $status;
		
	}

	// TODO: We might import here stuff from CreateFromFile https://github.com/ProteoWiki/CreateFromFile

}
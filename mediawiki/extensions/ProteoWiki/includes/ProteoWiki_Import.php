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
	
	public static function listFromPageConf( $title ) {
		
		$wikipage = WikiPage::factory( $title );
		
		// TODO: If wikipage exists
		$content = $wikipage->getContent();
		$text = $content->getNativeData(); // Native data
				
		$delimiter = ",";
		$enclosure = '"';
		
		if ( preg_match( "<proteowikiconf>", $text ) ) {
			
			$xml = simplexml_load_string($text);
			// var_dump( $xml );
			$attrs = $xml->attributes();
			foreach ( $attrs as $a => $b ) {
				if ( $a == 'data-delimiter' ) {
					$delimiter = $b;
				}
				if ( $a == 'data-enclosure' ) {
					$enclosure = $b;
				}
			}
		}
		
		$final_text = trim( strip_tags( $text ) );
		//Process as CSV below
		$table = array();
		
		// let's get a hash
		$table = self::getCSVData( $final_text );
		
		return $final_text;
	}

	// TODO: Here we import stuff from CreateFromFile https://github.com/ProteoWiki/CreateFromFile
	// IMPORTANT: This must be fixed
	private static function getCSVData( $text, $encoding, $delimiter=",", $enclosure='"' ) {

		if ( empty( $JStext ) )
			return "empty";
		$table = array();

		$linesCSV = explode ( "\n", $JStext );

		foreach ( $linesCSV as $lineCSV ) {
			array_push( $table, str_getcsv( $lineCSV, $delimiter, $enclosure ) );
		}

		$numiter = (int)$start+1;
		
		foreach ( $table as $line ) {

			// Let's check if first line can be avoided
			if ( substr( $line[0], 0, 1 ) === "#" ) {
				continue;
			}

			// Let's avoid empty lines
			if ( empty($line[0]) ) {
				continue;
			}

			//if ( $i == 0 ) continue;
			$page = new DTPage();

			$title = $titlePage;
			
			//Avoid empty content
			if (empty($title)) {
				continue;
			}

			$resultmatch = preg_match_all("/(\#\d+)/" , $templateText, $subst);

			$templateEnd = $templateText;

			// We do in reverse order for avoiding substitution when more than 2 digits parameters
			$iter = count($line);

			foreach ( array_reverse($line) as $entry ) {
				
				$matchiter = "#".$iter;
	
				if (in_array($matchiter, array_values($subst[0]))) {
					$templateEnd = str_replace($matchiter, $entry, $templateEnd);
					$title = str_replace($matchiter, $entry, $title);
				}
				
				$iter--;
			}
			
			// Other changes -> we should move to function or class maybe?
			$title = str_replace("#I4", self::putZeroes($numiter, 4), $title); // We assing value
			
			
			$title = str_replace("#Y", date("Y"), $title);

			#Recover template
			$templateEnd = str_replace("??", "{{", $templateEnd);
			$templateEnd = str_replace("!!", "}}", $templateEnd);
			#Remove any reference to #num left -> let's put back to blank
			$templateEnd = preg_replace("/=\s*\#\d+/", "=", $templateEnd);
			
			#Userparam change
			$templateEnd = preg_replace("/\#userparam/", $userparam, $templateEnd);

			# Assign value
			$page->setEntry( trim($templateEnd) );
			$page->setName( trim($title) );
			
			$pages[] = $page;
			
			$numiter++; // Next
		}
	}
	
}
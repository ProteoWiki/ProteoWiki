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
		
		$params = array();

		$wikipage = WikiPage::factory( $title );
		
		if ( $wikipage->exists() ) {
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
			$params = self::getCSVData( $final_text, "utf8", $delimiter, $enclosure );
		
		}
		
		return $params;
	}

	// TODO: Here we import stuff from CreateFromFile https://github.com/ProteoWiki/CreateFromFile
	// IMPORTANT: This must be fixed
	private static function getCSVData( $text, $encoding, $delimiter=",", $enclosure='"' ) {

		$outcome = array();
		
		if ( empty( $text ) ) {
			return $outcome;
		}

		$linesCSV = explode ( "\n", $text );

		$table = array();
		foreach ( $linesCSV as $lineCSV ) {
			array_push( $table, str_getcsv( $lineCSV, $delimiter, $enclosure ) );
		}
		
		// #Template,Parameter,Property,Label,Type,Mandatory,Default,Role
		foreach ( $table as $line ) {

			// Let's check if first line can be avoided
			if ( substr( $line[0], 0, 1 ) === "#" ) {
				// TODO; Consider if keys to make props -> Fix in more places
				continue;
			}

			// Let's avoid empty lines
			if ( empty($line[0]) ) {
				continue;
			}

			if ( ! array_key_exists( $line[0], $outcome ) ) {
				$outcome[ $line[0] ] = array();
			} else {
			
				if ( ! array_key_exists( $line[1], $outcome[$line[0]] ) ) {
					$outcome[ $line[0] ][ $line[1] ] = array();
				}
			}
			
			// TODO: Fix values, we should fix in more places
			if ( !empty($line[2]) ) {
				$outcome[ $line[0] ][ $line[1] ]["Property"] = $line[2];
			}
			if ( !empty($line[3]) ) {
				$outcome[ $line[0] ][ $line[1] ]["Label"] = $line[3];
			}
			if ( !empty($line[4]) ) {
				$outcome[ $line[0] ][ $line[1] ]["Type"] = $line[4];
			}
			if ( !empty($line[5]) ) {
				$outcome[ $line[0] ][ $line[1] ]["Mandatory"] = $line[5];
			}
			if ( !empty($line[6]) ) {
				$outcome[ $line[0] ][ $line[1] ]["Default"] = $line[6];
			}
			if ( !empty($line[7]) ) {
				$outcome[ $line[0] ][ $line[1] ]["Role"] = $line[7];
			}
		}
		
		return $outcome;
	}
	
		
	/** Get props from list **/
	public static function propsFromList( $list ) {
		
		$props = array();
		
		foreach ( $list as $template => $allparams ) {
	
			foreach ( $allparams as $param => $infoparam ) {

				if ( array_key_exists( "Property", $list[$template][$param] ) ) {
				
					$property = $list[$template][$param]["Property"];
					if ( array_key_exists( "Type", $list[$template][$param] ) ) {
						
						$type = $list[$template][$param]["Type"];
						$props[$property] = $type;
					}
				}
			}
		}
		
		
		return $props;
	}
	
}

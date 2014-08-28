<?php

if ( !defined( 'MEDIAWIKI' ) ) {
	echo 'Not a valid entry point';
	exit( 1 );
}

if ( !defined( 'SMW_VERSION' ) ) {
	echo 'This extension requires Semantic MediaWiki to be installed.';
	exit( 1 );
}


/**
 * This class handles the search for Props.
 */
class SMWListProps {
	
	public static function executeGetListProps( $parser, $frame, $args ) {

		// Let's disable cache
		$parser->disableCache();
	
		//Default current page
		$pagetitle = $parser->getTitle();

		// Let's get page
		if ( isset( $args[0] ) && $args[0]!='' ) {
			$title = trim( $frame->expand( $args[0] ) );
			$pagetitle = Title::newFromText( $title );
		}
		
		if ( ! $pagetitle ) {
			return '';
		}

		$smwpagetitle = SMWDIWikiPage::newFromTitle( $pagetitle );
	
		$data = smwfGetStore()->getSemanticData( $smwpagetitle );
		$diProperties = $data->getProperties();
	
		$listprops = array();
	
		// We remove param 0
		array_shift($args);
	
		$params = array();
	
		// We parametrize
		foreach ( $args as $arg ) {
			array_push( $params, trim( $frame->expand( $arg ) ) );
		}
	
		// Options 
		$wikionly = false;
		$values = false;
	
	
		// If value in array
		if ( in_array ( 'wikionly' , $params ) ) {
			 $wikionly = true;	
		}
		// If value in array
		if ( in_array ( 'values' , $params ) ) {
				 $values = true;	
		}
		
		foreach ( $diProperties as $diProperty ) {
	
			// Check if user defined
			if ( $wikionly == true && !$diProperty->isUserDefined() ) {
				continue;
			}
	
			$label = $diProperty->getLabel();
			if ( !empty($label)  ) {
	
				if ( $values == true ) {
				   #$array = self::getPropValue( $pagetitle->getFullText(), $label );
				   #var_dump($array);
				}
			
				array_push( $listprops, $label );
			}
		}

	
		return( implode( ",", $listprops ) );
	
	}

	/** Convenient for getting value **/
	
	static function getPropValue ( $title_text, $query_word ) {
	
		$assignee_arr = array();
	
		$query_word = str_replace(" ", "_", $query_word);
		echo $title_text;
		
		// get the result of the query "[[$title]][[$query_word::+]]"
		$properties_to_display = array();
		$properties_to_display[0] = $query_word;
		$results = self::getQueryResults( "[[$title_text]][[$query_word::+]]", $properties_to_display, false );
	
		
		// In theory, there is only one row
		while ( $row = $results->getNext() ) {
		   var_dump($row[1]->getNextObject());
		}
		
	
		return $assignee_arr;
	}


	/**
	* This function returns to results of a certain query
	* Thank you Yaron Koren for advices concerning this code
	* @param $query_string String : the query
	* @param $properties_to_display array(String): array of property names to display
	* @param $display_title Boolean : add the page title in the result
	* @return TODO
	*/
	static function getQueryResults( $query_string, $properties_to_display, $display_title ) {
		// We use the Semantic MediaWiki Processor
		// $smwgIP is defined by Semantic MediaWiki, and we don't allow
		// this file to be sourced unless Semantic MediaWiki is included.
		global $smwgIP;
		include_once( $smwgIP . "/includes/SMW_QueryProcessor.php" );

		$params = array();
		$inline = true;
		$printlabel = "";
		$printouts = array();

		// add the page name to the printouts
		if ( $display_title ) {
			$to_push = new SMWPrintRequest( SMWPrintRequest::PRINT_THIS, $printlabel );
			array_push( $printouts, $to_push );
		}

		// Push the properties to display in the printout array.
		foreach ( $properties_to_display as $property ) {
			if ( class_exists( 'SMWPropertyValue' ) ) { // SMW 1.4
				$to_push = new SMWPrintRequest( SMWPrintRequest::PRINT_PROP, $printlabel, SMWPropertyValue::makeProperty( $property ) );
			} else {
				$to_push = new SMWPrintRequest( SMWPrintRequest::PRINT_PROP, $printlabel, Title::newFromText( $property, SMW_NS_PROPERTY ) );
			}
			array_push( $printouts, $to_push );
		}

		if ( version_compare( SMW_VERSION, '1.6.1', '>' ) ) {
			SMWQueryProcessor::addThisPrintout( $printouts, $params );
			$params = SMWQueryProcessor::getProcessedParams( $params, $printouts );
			$format = null;
		}
		else {
			$format = 'auto';
		}
		
		$query = SMWQueryProcessor::createQuery( $query_string, $params, $inline, $format, $printouts );
		$results = smwfGetStore()->getQueryResult( $query );

		return $results;
	}

}

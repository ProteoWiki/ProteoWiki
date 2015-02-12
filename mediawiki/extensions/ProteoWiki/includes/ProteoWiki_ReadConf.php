<?php

/** Here a set of reading configuration functions **/


class ProteoWikiReadConf {

	/** read Configuration and return value */
	public static function readConf( $class="" ) {
		
		$output = array();
	
		global $wgProteoWikiPages;
		
		if ( empty( $class ) ) {
			
			$output["status"] = "Error";
			$output["msg"] = "No defined class";
			
			return $output;
		}
		
		$params = array();
		
		foreach ( $wgProteoWikiPages as $key => $confPages ) {
			
			$params[ $key ] = array();
			foreach ( $confPages as $confPage ) {
	
				$title = Title::newFromText( $confPage, NS_PROTEOWIKICONF );
	
				$storage = ProteoWikiImport::listFromPageConf( $title, $confPage );
				if ( self::isAssoc( $storage ) ) {
					foreach ( $storage as $arrkey => $arrVal ) {
						if ( $arrkey == $class ) {
							array_push( $params[ $key ], $arrVal ); 
						}
					}
				} else {
					foreach ( $storage as $arrVal ) {

						// We assing here the following. Should be necessary previous?
						if ( $arrVal[0] == $class ) {
	
							if ( isset( $arrVal[1] ) ) {
								array_push( $params[ $key ], $arrVal[1] ); 
							}
						}
						if ( $arrVal[1] == $class ) {
	
							if ( isset( $arrVal[2] ) ) {
								array_push( $params[ $key ], $arrVal[2] ); 
							}
						}
					}
				}
			}
		}

		return $params;
		
	}

	private static function isAssoc($arr) {
		return array_keys($arr) !== range(0, count($arr) - 1);
	}

}
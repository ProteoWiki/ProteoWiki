<?php

/** Class for generating templates and others from configuration **/

class ProteoWikiGenerate {

	public static function generateProperty( $content, $overwrite=false ) {

		if ( is_array( $content ) && array_key_exists( "title", $content ) ) {
			$titlePage =  Title::newFromText( $content["title"], NS_PROPERTY );
			if ( $titlePage->exists() && ! $overwrite ) {
				return false;
			} else {
				if ( array_key_exists( "type", $content ) ) {
					$pagetext = "[[Has type::".$content["type"]."]]";
					$page = WikiPage::factory( $titlePage );
					$page->doEdit( $pagetext, "Property added/modified" );
					// TODO: If property already existed and modified, we should check whether system update is necessary
					return true;
				}
			}
		}

		return false;
	}

	public static function generateTemplate( $content, $overwrite=false ) {

	}

	public static function generateForm( $content, $overwrite=false ) {

	}

}
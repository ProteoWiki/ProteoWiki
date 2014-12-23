<?php

/** Class for generating templates and others from configuration **/

class ProteoWikiGenerate {

	public static function generateProperty( $content, $overwrite=false ) {

		if ( is_array( $content ) && array_key_exists( "title", $content ) ) {
			$titlePage =  Title::newFromText( $content["title"], SMW_NS_PROPERTY );
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

		if ( is_array( $content ) && array_key_exists( "title", $content ) ) {
			$titlePage =  Title::newFromText( $content["title"], NS_TEMPLATE );
			if ( $titlePage->exists() && ! $overwrite ) {
				return false;
			} else {
				if ( array_key_exists( "parameters", $content ) && is_array( $content["parameters"] ) ) {

					$pagetext = "";

					if ( array_key_exists( "type", $content ) ) {
						$pagetext .= "{{".$content['type']."|".$content['title']."}}"; // Generic template for handling types
					}

					foreach ( $content["parameters"] as $entry ) {

						if ( array_key_exists( "parameter", $entry ) && array_key_exists( "property", $entry ) ) {
							$pagetext .= "{{#set:".$entry["property"]."={{{".$entry["parameter"]."|}}}}}"; //Relation of parameters and properties
						}

					}

					$page = WikiPage::factory( $titlePage );
					$page->doEdit( $pagetext, "Property added/modified" );
					// TODO: Trigger system update is necessary

					return true;

				}
			}
		}
		return false;
	}

	public static function generateForm( $content, $overwrite=false ) {

		if ( is_array( $content ) && array_key_exists( "title", $content ) ) {
			$titlePage =  Title::newFromText( $content["title"], SF_NS_FORM );
			if ( $titlePage->exists() && ! $overwrite ) {
				return false;
			} else {
				if ( array_key_exists( "parameters", $content ) && is_array( $content["parameters"] ) ) {

					//Form definition here
					$pagetext = "<noinclude>{{#forminput:form=".$content["title"]."|query_string=namespace=".$content["title"]."}}</noinclude>";
					$pagetext.= '<includeonly><div id="wikiPreview" style="display: none; padding-bottom: 25px; margin-bottom: 25px; border-bottom: 1px solid #AAAAAA;"></div>';

					if ( array_key_exists( "type", $content ) ) {
						// TODO: Here we should put also generic form part ( e. g. for Sample or Proces )
						if ( $content['title'] != $content['type'] ) {
							$pagetext.= '{{{for template|'.$content['type'].'}}}';

							$baseparams = getBaseParams( $content['type'] );
							$pagetext.= self::iterateFormParams( $baseparams );

							$pagetext.= "{{{end template}}}";
						}
					}

					$pagetext.= '{{{for template|'.$content['title'].'}}}';
					
					$pagetext.= self::iterateFormParams( $content['parameters'] );

					$pagetext.= "{{{end template}}}";

					$pagetext.= "{{{standard input|save}}}{{{standard input|cancel}}}</includeonly>";

					$page = WikiPage::factory( $titlePage );
					$page->doEdit( $pagetext, "Form added/modified" );

					return true;
				}
			}
		}
	}

	/** Iterate for generating field entries **/

	private static function iterateFormParams( $params ) {

		$output = "";

		foreach ( $params as $entry ) {

			if ( array_key_exists( "parameter", $entry ) ) {

				$pagetext.="\n* ";

				if ( array_key_exists( "label", $entry ) ) {
					$pagetext.= $entry['label'];
				}

				$extra = "";
				if ( array_key_exists( "mandatory", $entry ) ) {
					if ( $entry['mandatory'] === 1 ) {
						$extra.= "|mandatory";
					}
				}
				if ( array_key_exists( "default", $entry ) ) {
					if ( !empty ( $entry['default'] ) ) {
						$extra.= "|default=".$entry['default'];
					}
				}
				if ( array_key_exists( "role", $entry ) ) {
					if ( !empty ( $entry['restricted'] ) ) {
						$extra.= "|restricted=".$entry['restricted'];
					}
				}

				$pagetext .= " {{{field|".$entry['parameter'].$extra."}}}";
			}
		}

		return $output;
	}

}
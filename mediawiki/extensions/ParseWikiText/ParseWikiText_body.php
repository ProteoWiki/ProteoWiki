<?php
/*
 * 2014
 * Provides way to parse wikitext
 *
*/


class ExtParseWikiText {
	/**
	* The rendering object (skin)
	*/
	
	private $display=NULL;

	/**
	 * @param $parser Parser
	 * @return bool
	 */
	function clearState(&$parser) {
		$parser->pf_ifexist_breakdown = array();
		return true;
	}

	public function getwikitext ( $parser, $param1="" ) {

		if (empty($param1)) {
			return "";
		}

		// Get CreateFromFile template;
		$templatePage = $param1;
		$templateID = Title::newFromText($templatePage)->getArticleID(); //Get the id for the article called Test_page
		$templateArticle = Article::newFromId($templateID); //Make an article object from that id
		$templateText = $templateArticle->getRawText();

		return array( $templateText, 'noparse' => true, 'isHTML' => false );

	}

	
}
?>

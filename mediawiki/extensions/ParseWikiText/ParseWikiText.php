<?php
/**
 * Copyright (C) 2011 Toni Hermoso Pulido <toniher@cau.cat>
 * http://www.cau.cat
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 59 Temple Place - Suite 330, Boston, MA 02111-1307, USA.
 * http://www.gnu.org/copyleft/gpl.html
 *
 */

if ( !defined( 'MEDIAWIKI' ) ) {
    echo "Not a valid entry point";
    exit( 1 );
}

$wgExtensionFunctions[] = 'wfSetupParseWikiText';
$wgExtensionCredits['parserhook'][] = array(
        'path' => __FILE__,
        'name' => 'ParseWikiText',
        'author' => 'Toni Hermoso',
        'version' => '0.1',
        'url' => 'http://www.mediawiki.org/wiki/Extension:ParseWikiText',
        'descriptionmsg' => 'parsewikitext-desc',
);

$wgAutoloadClasses['ExtParseWikiText'] = dirname(__FILE__) . '/ParseWikiText_body.php';
$wgExtensionMessagesFiles['ParseWikiText'] = dirname( __FILE__ ) . '/ParseWikiText.i18n.php';
$wgExtensionMessagesFiles['ParseWikiTextMagic'] = dirname(__FILE__) . '/ParseWikiText.i18n.magic.php';

function wfSetupParseWikiText() {
	global $wgHooks;

	$wgPWHookStub = new ParseWikiText_HookStub;

	$wgHooks['ParserFirstCallInit'][] = array( &$wgPWHookStub, 'registerParser' );
	$wgHooks['ParserClearState'][] = array( &$wgPWHookStub, 'clearState' );
}

/**
 * Stub class to defer loading of the bulk of the code until a User function is
 * actually used.
 */
class ParseWikiText_HookStub {

	var $realObj;
	/**
	 * @param $parser Parser
	 * @return bool
	 */
	function registerParser( &$parser ) {
		$parser->setFunctionHook( 'getwikitext',  array(&$this, 'getwikitext') );
		return true;
	}

	/**
	 * Defer ParserClearState
	 */
	function clearState( &$parser ) {
		if ( !is_null( $this->realObj ) ) {
			$this->realObj->clearState( $parser );
		}
		return true;
	}

	/**
	 * Pass through function call
	 */
	function __call( $name, $args ) {
		if ( is_null( $this->realObj ) ) {
			$this->realObj = new ExtParseWikiText;
			$this->realObj->clearState( $args[0] );
		}
		return call_user_func_array( array( $this->realObj, $name ), $args );
	}
}

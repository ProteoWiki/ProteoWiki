<?php
/*
 * 2011-2012
 * Provides way to create pages from files
 *
*/

class DTPage {
	var $mName;
	var $mEntry;

	public function DTPage() {
		$this->mTemplates = array();
	}

	function setName( $name ) {
		$this->mName = $name;
	}

	function getName() {
		return $this->mName;
	}
	
	function setEntry($content) {

		$this->mEntry = $content;
	}
	
	function getEntry() {
		
		return $this->mEntry;
	}

}

class CreateFromFile {


	public static function createSpread( &$parser, $frame, $args ) {

		global $wgOut;
		$wgOut->addModules( 'ext.CreateFromFile' );
	
		// String for data to be used by extension
		$str = ""; // What we return
		$arg = ""; // argument
		$link = "";
	
		if ( isset($args[0]) ) {
			$arg = trim( $frame->expand($args[0]) );
		}
	
		if ( !empty( $arg ) ) {
			#Text
			if ( strpos( $arg, "text=" ) === 0 ) {
				$str = str_replace( "text=", "", $arg );
				$str = str_replace( "\\n", "\n", $str );
				$str = str_replace( "\\t", "\t", $str );

			} else { // Title
				$title = Title::newFromText( $arg ) ;
				// If not null
				if ( $title ) {
					$str = WikiPage::factory( $title )->getText( Revision::RAW );
					#$str = preg_replace("/\n/", "\\n", $str );
				}
			}
		}

		if ( isset($args[1]) ) {
			$target = trim( $frame->expand($args[1]) );
			$link = "<p data-target='".$target."' class='createspread-link'>Save</p>";
		}
	
		// Let's add a HTML string
		$output = "<div class='createspread'><div class='createspread-data'><pre>".$str."</pre></div><div class='createspread-show'></div>".$link."</div>";
		return $parser->insertStripItem( $output, $parser->mStripState );
	}


	public static function createfromfilelink ( &$parser, $frame, $args ) {
	
		global $wgOut;
		$wgOut->addModules( 'ext.CreateFromFile' );
		$extra = "";
		if (isset($args[0])) {
			$file = trim($frame->expand($args[0]));
			$extra.= "data-file='".$file."'";
		}
		if (isset($args[1])) {
			$template = trim($frame->expand($args[1]));
			$extra.= "data-template='".$template."'";	
		}
		if (isset($args[2])) {
			$title = trim($frame->expand($args[2]));
			$extra.= "data-title='".$title."'";
		}
		if (isset($args[3])) {
			$delimiter = trim($frame->expand($args[3]));
			$extra.= "data-delimiter='".$delimiter."'";
		}
		if (isset($args[4])) {
			$enclosure = trim($frame->expand($args[4]));
			$extra.= "data-enclosure='".$enclosure."'";
		}
	
		if (isset($args[5])) {
			$userparam = trim($frame->expand($args[5]));
			$extra.= "data-userparam='".$userparam."'";
		}
		
		if (isset($args[6])) {
			$start = trim($frame->expand($args[6]));
			$extra.= "data-start='".$start."'";
		}
		
		$output = "<p class='createfromfile-link' ".$extra.">Create</p>";
		return $parser->insertStripItem( $output, $parser->mStripState );

	}

	public static function createSpreadlink( &$parser, $frame, $args ) {

		global $wgOut;
		$wgOut->addModules( 'ext.CreateFromFile' );
		$extra = "";
		if (isset($args[0])) {
			$selector = trim($frame->expand($args[0]));
			$extra.= "data-selector='".$selector."'";
		}
		if (isset($args[1])) {
			$template = trim($frame->expand($args[1]));
			$extra.= "data-template='".$template."'";	
		}
		if (isset($args[2])) {
			$title = trim($frame->expand($args[2]));
			$extra.= "data-title='".$title."'";
		}
		if (isset($args[3])) {
			$delimiter = trim($frame->expand($args[3]));
			$extra.= "data-delimiter='".$delimiter."'";
		}
		if (isset($args[4])) {
			$enclosure = trim($frame->expand($args[4]));
			$extra.= "data-enclosure='".$enclosure."'";
		}
	
		if (isset($args[5])) {
			$userparam = trim($frame->expand($args[5]));
			$extra.= "data-userparam='".$userparam."'";
		}
		
		if (isset($args[6])) {
			$start = trim($frame->expand($args[6]));
			$extra.= "data-start='".$start."'";
		}

		$output = "<p class='createfromSpread-link' ".$extra.">Create</p>";
		return $parser->insertStripItem( $output, $parser->mStripState );

	}

	public static function createfromfiledone ( &$parser, $frame, $args ) {

		global $wgCFAllowedGroups;
		global $wgUploadDirectory;
	
		// $parser->disableCache();
		
		if (empty($args[0])) {
			return "No input file";
		}
		
		if (empty($args[1])) {
				return "No Create File template";
		}

		if (empty($args[2])) {
				return "No default title defined";
		}
		
		// Delimiter
		$delimiter = "\t";
		if (empty($args[3])) {
			$delimiter = "\t";
		} else {
			$delimiter = trim($frame->expand($args[3]));
		}
		
		$enclosure = '"';
		// Enclosure
		if (empty($args[4])) {
			$enclosure = '"';
		} else {
			$enclosure = trim($frame->expand($args[4]));
		}
		
		// Userparam
		if (empty($args[5])) {
			$userparam = "";
		} else {
			$userparam = trim($frame->expand($args[5]));
		}
		

		global $wgUser;
		$user = $wgUser;
		
		// Can be filtered at the parser level, current user group and page

		$cur_gps = $user->getEffectiveGroups();
		
		$ingroup = false;
		
		foreach ($cur_gps as $cur_gp) {
			if (in_array($cur_gp, $wgCFAllowedGroups)) {
				$ingroup = true;
				break;
			}
		}

		if (!$ingroup) {
			return(false);
		}
	
		// Get CreateFromFile template;
		$templatePage = "MediaWiki:CreateFromFile-".trim($frame->expand($args[1]));
		$templateID = Title::newFromText($templatePage)->getArticleID(); //Get the id for the article called Test_page
		$templateArticle = Article::newFromId($templateID); //Make an article object from that id
		$templateText = $templateArticle->getRawText();

		if (empty($templateText)) {
			return "Empty or non-exisiting template!";
		}

		$titlePage = trim($frame->expand($args[2]));
		if (empty($titlePage)) {
			return "Empty title!";
		}		

		$file = wfFindFile(trim($frame->expand($args[0])));
		$path_file = "";

		if ($file) {
			#$path_file = $wgUploadDirectory."/".$file->hashPath.$file->name;
			$path = $file->getPath();
			$path_file = str_replace("mwstore://local-backend/local-public", $wgUploadDirectory, $path);
			#echo $path_file;
			#echo $titlePage;
		}

		#Check if file exists
		if (file_exists($path_file)) {
			return(self::filecheck($path_file, $templateText, $titlePage, $delimiter, $enclosure, $userparam));
		}
		
		else {
			return("File does not exist!");
		}
	
	}


	public static function createfromfileJS ( $param1="", $param2="", $param3="", $delimiter="\t", $enclosure='"', $userparam="", $start=0 ) {

		global $wgCFAllowedGroups;
		global $wgUploadDirectory;
		
		if (empty($param1)) {
			return '{"status":"error", "msg":"No input file"}';
		}

		if (empty($param2)) {
			return '{"status":"error", "msg":"No Create File template"}';
		}

		if (empty($param3)) {
			return '{"status":"error", "msg":"No default title defined"}';
		}


		global $wgUser;
		$user = $wgUser;
		
		// Can be filtered at the parser level, current user group and page

		$cur_gps = $user->getEffectiveGroups();
		
		$ingroup = false;
		
		foreach ($cur_gps as $cur_gp) {
			if (in_array($cur_gp, $wgCFAllowedGroups)) {
				$ingroup = true;
				break;
			}
		}

		if (!$ingroup) {
			return '{"status":"error", "msg":"Not allowed to do this"}';
		}
	
		// Get CreateFromFile template;
		$templatePage = "MediaWiki:CreateFromFile-".$param2;
		$templateID = Title::newFromText($templatePage)->getArticleID(); //Get the id for the article called Test_page
		$templateArticle = Article::newFromId($templateID); //Make an article object from that id
		$templateText = $templateArticle->getRawText();

		if (empty($templateText)) {
			return '{"status":"error", "msg":"Empty or non-exisiting template!"}';
		}

		$titlePage = $param3;
		if (empty($titlePage)) {
			return '{"status":"error", "msg":"Empty title!"}';
		}

		$file = wfFindFile($param1);
		$path_file = "";

		if ($file) {
			#$path_file = $wgUploadDirectory."/".$file->hashPath.$file->name;
			$path = $file->getPath();
			$path_file = str_replace("mwstore://local-backend/local-public", $wgUploadDirectory, $path);
			#echo $path_file;
			#echo $titlePage;
		}
		
		#Check if file exists
		if (file_exists($path_file)) {
			$success = self::filecheck($path_file, $templateText, $titlePage, $delimiter, $enclosure, $userparam, $start);

			return '{"status":"ok", "msg": "'.$success.'"}';
		}
		
		else {
			return '{"status":"error", "msg":"File does not exist!"}';
		}
	
	}

	public static function createfromSpreadJS ( $param1="", $param2="", $param3="", $delimiter="\t", $enclosure='"', $userparam="", $start=0 ) {

		global $wgCFAllowedGroups;
		global $wgUploadDirectory;
		
		if (empty($param1)) {
			return '{"status":"error", "msg":"No text!"}';
		}

		if (empty($param2)) {
			return '{"status":"error", "msg":"No Create File template"}';
		}

		if (empty($param3)) {
			return '{"status":"error", "msg":"No default title defined"}';
		}


		global $wgUser;
		$user = $wgUser;
		
		// Can be filtered at the parser level, current user group and page

		$cur_gps = $user->getEffectiveGroups();
		
		$ingroup = false;
		
		foreach ($cur_gps as $cur_gp) {
			if (in_array($cur_gp, $wgCFAllowedGroups)) {
				$ingroup = true;
				break;
			}
		}

		if (!$ingroup) {
			return '{"status":"error", "msg":"Not allowed to do this"}';
		}

		// Get CreateFromFile template;
		$templatePage = "MediaWiki:CreateFromFile-".$param2;
		$templateID = Title::newFromText($templatePage)->getArticleID(); //Get the id for the article called Test_page
		$templateArticle = Article::newFromId($templateID); //Make an article object from that id
		$templateText = $templateArticle->getRawText();

		if (empty($templateText)) {
			return '{"status":"error", "msg":"Empty or non-exisiting template!"}';
		}

		$titlePage = $param3;
		if (empty($titlePage)) {
			return '{"status":"error", "msg":"Empty title!"}';
		}

		// Let's process str
		$str = str_replace( "\\n", "\n", $param1 );
		$str = str_replace( "\\t", "\t", $str );

		#Check if file exists
		if ( !empty( $str ) ) {
			$success = self::textcheck($str, $templateText, $titlePage, $delimiter, $enclosure, $userparam, $start);

			return '{"status":"ok", "msg": "'.$success.'"}';
		}
		
		else {
			return '{"status":"error", "msg":"File does not exist!"}';
		}

	}

	private static function filecheck($path_file, $templateText, $titlePage, $delimiter, $enclosure, $userparam, $start) {
		
		$pages = array();
		$encoding = "UTF-8";
		
		$error_msg = self::getCSVData( $path_file, $encoding, $pages, $templateText, $titlePage, $delimiter, $enclosure, $userparam, $start );
		if ( ! is_null( $error_msg ) ) {
			$text .= $error_msg;
			$wgOut->addHTML( $text );
			return;
		}
	
		$text = self::modifyPages( $pages, "Adding pages from CreateFromFile" );

		# Should we trigger runJobs here?
		$descriptorspec = array(
				array('pipe', 'r'),               // stdin
				array('file', '/tmp/jobs-samples.out', 'w'), // stdout
				array('file', '/tmp/jobs-samples.err', 'w'),               // stderr -> Generate one temp?
		);


		global $wgRunJobsPath;
		$proc = proc_open("php $wgRunJobsPath &", $descriptorspec, $pipes);

		#echo $text;
		return($text);
	}

	private static function textcheck($JStext, $templateText, $titlePage, $delimiter, $enclosure, $userparam, $start) {

		$pages = array();
		$encoding = "UTF-8";
		
		$error_msg = self::getCSVDataJS( $JStext, $encoding, $pages, $templateText, $titlePage, $delimiter, $enclosure, $userparam, $start );
		if ( ! is_null( $error_msg ) ) {
			$text .= $error_msg;
			$wgOut->addHTML( $text );
			return;
		}

		$text = self::modifyPages( $pages, "Adding pages from CreateFromFile" );

		# Should we trigger runJobs here?
		$descriptorspec = array(
				array('pipe', 'r'),               // stdin
				array('file', '/tmp/jobs-samples.out', 'w'), // stdout
				array('file', '/tmp/jobs-samples.err', 'w'),               // stderr -> Generate one temp?
		);


		global $wgRunJobsPath;
		$proc = proc_open("php $wgRunJobsPath &", $descriptorspec, $pipes);

		#echo $text;
		return($text);

	}
	
	private static function getCSVData( $path_file, $encoding, &$pages, $templateText, $titlePage, $delimiter="\t", $enclosure='"', $userparam, $start=0 ) {
		
		
		$csv_file = ImportStreamSource::newFromFile($path_file)->value->mHandle;
		// var_dump($csv_file);
		
		if ( is_null( $csv_file ) )
			return "empty";
		$table = array();
		if ( $encoding == 'utf16' ) {
			// change encoding to UTF-8
			// Starting with PHP 5.3 we could use str_getcsv(),
			// which would save the tempfile hassle
			$tempfile = tmpfile();
			$csv_string = '';
			while ( !feof( $csv_file ) ) {
				$csv_string .= fgets( $csv_file, 65535 );
 			}
			fwrite( $tempfile, iconv( 'UTF-16', 'UTF-8', $csv_string ) );
			fseek( $tempfile, 0 );
			while ( $line = fgetcsv( $tempfile, 10000, $delimiter, $enclosure ) ) {
				array_push( $table, $line );
			}
			fclose( $tempfile );
		} else {
			while ( $line = fgetcsv( $csv_file, 10000, $delimiter, $enclosure ) ) {
		
				array_push( $table, $line );
			}
		}
		fclose( $csv_file );

		// Get rid of the "byte order mark", if it's there - this is
		// a three-character string sometimes put at the beginning
		// of files to indicate its encoding.
		// Code copied from:
		// http://www.dotvoid.com/2010/04/detecting-utf-bom-byte-order-mark/
		$byteOrderMark = pack( "CCC", 0xef, 0xbb, 0xbf );
		if ( 0 == strncmp( $table[0][0], $byteOrderMark, 3 ) ) {
			$table[0][0] = substr( $table[0][0], 3 );
			// If there were quotation marks around this value,
			// they didn't get removed, so remove them now.
			$table[0][0] = trim( $table[0][0], '"' );
		}

		$numiter = (int)$start+1;
		
		foreach ( $table as $line ) {

			// Let's check if first line can be avoided
			if ( substr( $line[0], 0, 1 ) === "#" ) {
				continue;
			}

			// Let's check if sep to be avoided
			if ( substr( $line[0], 0, 4 ) === "sep=" ) {
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
			
			foreach ( array_reverse( $line ) as $entry ) {

				# If starts with # ignore
				if ( preg_match( '/^\s*\#/', $entry ) === 1 ) {
					continue;
				}

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

	private static function getCSVDataJS( $JStext, $encoding, &$pages, $templateText, $titlePage, $delimiter="\t", $enclosure='"', $userparam, $start=0 ) {
		
		
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
	

	
	private static function modifyPages( $pages, $editSummary ) {
		
		global $wgUser, $wgLang, $wgSkipCreateFromFile;
		
		$text = "";
		$jobs = array();
		$jobParams = array();
		$titles = array();
		$jobParams['user_id'] = $wgUser->getId();
		$jobParams['edit_summary'] = $editSummary;
		$jobParams['for_pages_that_exist'] = $wgSkipCreateFromFile;
		foreach ( $pages as $page ) {
			$title = Title::newFromText( $page->getName() );
			if ( is_null( $title ) ) {
				#$text .= '<p>Problem with ' . $page->getName() . "</p>\n";
				continue;
			}
			array_push($titles, $title);
			$jobParams['text'] = $page->getEntry();
			$jobs[] = new DTImportJob( $title, $jobParams );
		}
		Job::batchInsert( $jobs );
		$text.=implode(",",$titles);

		return $text;
	}
	
		private static function putZeroes($code, $zeroes=0) {
		
			if (isset($code)) {
				$str = "";
				if (is_numeric($code)) {
						$length = strlen($code);
						$diff = $zeroes - $length;
						$str = $code;
						if ($diff > 0) {
								for ($i=0; $i<$diff; $i++) {
										$str = "0".$str;	
								}
						}
	
						return $str;
				}
				else { return $str; }
			}
			else { return ""; }
		}

}

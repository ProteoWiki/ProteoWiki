<?php
if (!defined('MEDIAWIKI')) { die(-1); } 
 
# Our SpecialPage
class SpecialCreateFromFile extends SpecialPage {

	/**
	 * Constructor : initialise object
	 * Get data POSTed through the form and assign them to the object
	 * @param $request WebRequest : data posted.
	 */
	public function __construct($request = null) {
		parent::__construct('CreateFromFile');   #The first argument must be the name of your special page
							 #A second argument "right" can be added to restrict access to the SpecialPage.
	}

	/**
	 * Special page entry point
	 */
	public function execute($par) {
	
		global $wgCreateFromFileftypes;

		global $wgOut;
		$wgOut->addModules( 'ext.CreateFromFile' );

		$this->setHeaders();

		# A formDescriptor Array to tell HTMLForm what to build
		$formDescriptorUpload = array(
			'fileupload' => array(
			 'section' => 'createfromfile',
				'label' => 'Upload', # What's the label of the field
				'class' => 'HTMLTextField', # What's the input type
				'type' => 'file'
			),
			'groupselect' => array(
				'section' => 'createfromfile',
				'type' => 'select',
				'label' => 'Type',
				#'default' => '',
				'options' => self::doubleKeyValueOptions( $wgCreateFromFileftypes )
			),
			'delimiter' => array(
				'section' => 'createfromfile',
				'type' => 'select',
				'label' => 'Delimiter',
				'default' => "\t",
				'options' => array( "\\t" => "\\t", ";" => ";", "," => ",")
			)
		);


		$htmlForm = new HTMLForm( $formDescriptorUpload, 'CreateFromFile' );

		$htmlForm->setSubmitText( 'Import' ); # What text does the submit button display
		$htmlForm->setTitle( $this->getTitle() ); # You must call setTitle() on an HTMLForm

		/* We set a callback function */
		$htmlForm->setSubmitCallback( array( 'SpecialCreateFromFile', 'processInput' ) );  # Call processInput() in SpecialBioParser on submit

		$htmlForm->suppressReset(false); # Get back reset button 
		$htmlForm->show(); # Displaying the form
		// TODO: Change AQUA for something default and enable JS
		$wgOut->addHTML("<h4>Columns to use:</h4><p class='list'>".implode( "&nbsp;-&nbsp;", $wgCreateFromFileftypes["AQUA"]["cols"] )."</p>");

	}

	private static function doubleKeyValueOptions( $listkeys ) {

		$options = array();

		foreach ( array_keys( $listkeys ) as $keyv ) {
			$options[$keyv] = $keyv;
		}

		return $options;
	}


	/* We write a callback function */
	# OnSubmit Callback, here we do all the logic we want to do...
	static function processInput( $formData ) {

		global $wgCreateFromFileTmpDir;
		global $wgUser;

		$userID = $wgUser->getID();

		if ( $_FILES['wpfileupload']['size'] > 0 && $_FILES['wpfileupload']['error'] == 0 ) {
		
			$md5sum = md5($_FILES['wpfileupload']['tmp_name']."+".$userID);

			$pathtempfile = $wgCreateFromFileTmpDir."/".$md5sum;
			move_uploaded_file($_FILES["wpfileupload"]["tmp_name"], $pathtempfile);

			global $wgCreateFromFileftypes;

			$groupselect = "";
			$delimiter = "\t";
			$enclosure = "";

			if ( $formData['groupselect'] ) {
				$groupselect =  $formData['groupselect'];
			}

			if ( $formData['delimiter'] ) {
				$delimiter =  $formData['delimiter'];
			}

			if ( empty( $groupselect ) ) {
				return "No assigned group!";
			}

			$title = $wgCreateFromFileftypes[$groupselect]['title'];
			$category = $wgCreateFromFileftypes[$groupselect]['category'];
			$prefixtitle = $wgCreateFromFileftypes[$groupselect]['name'];
			$cols = implode( ";", $wgCreateFromFileftypes[$groupselect]['cols'] );
			$uniqcols = implode( ";", $wgCreateFromFileftypes[$groupselect]['uniqcols'] );

			$start = self::getLastinCategory( $category, $prefixtitle );

			$htmlout = "";

			$htmllink = '<p class="createfromSpread-post" data-selector=".createspread-show" data-template="'.$groupselect.'" data-title="'.$title.'" data-delimiter="'.$delimiter.'" data-enclosure="'.$enclosure.'" data-userparam="" data-start="'.$start.'" data-username="WikiSysop">Create</p>';

			$htmldiv = '<div class="createspread"><div data-uniqcols="'.$uniqcols.'" data-cols="'.$cols.'" class="createspread-data" style="display: none;"><pre>'.self::readSpreadFile( $pathtempfile, $delimiter, $enclosure ).'</pre></div><div class="createspread-show"></div></div>';

			// TODO: Setup for avoiding stuff to be repeated
			$htmlcheck = '<div class="createspread-view"></div>';

			$htmlout = $htmldiv."\n".$htmlcheck."\n".$htmllink;

			return $htmlout;

		} else {
			return "Empty or no file!";
		}
	}

	static function getLastinCategory ( $category, $prefixtitle ) {
		
		$catContainer = Category::newFromName( $category );
		$listTitles = $catContainer->getMembers();

		$listwords = array();

		foreach ( $listTitles as $entryTitle ) {
			$titleText = $entryTitle->getText();
			// Supposing is not working as namespace, otherwise it would already work
			$titleText = str_replace( $prefixtitle.":", "", $titleText );
			array_push( $listwords, (int)$titleText );
		}

		rsort( $listwords, SORT_NUMERIC); 

		if ( empty( $listwords ) ) {
			return 0;
		} else {
			return( $listwords[0] );
		}
	}

	static function readSpreadFile ( $file, $delimiter, $enclosure ) {
	
		$handle = fopen( $file, "r" );

		if ( $delimiter == "\\t" ) {
			$delimiter = "\t";
		}

		$csvarray = fgetcsv ( $handle, 0, $delimiter );

		$rows = array();

		while ( ( $data = fgetcsv( $handle, 0, $delimiter) ) !== FALSE ) {
			array_push( $rows, join( "\t", $data ) );
		}

		return join( "\n", $rows );
	}

}

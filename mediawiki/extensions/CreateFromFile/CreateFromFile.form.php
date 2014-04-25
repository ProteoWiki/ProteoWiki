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
				'options' => array_keys( $wgCreateFromFileftypes )
			),
			'delimiter' => array(
				'section' => 'createfromfile',
				'type' => 'select',
				'label' => 'Delimiter',
				'default' => "\t",
				'options' => array( "\\t" => "\\t", ";" => ";", "," => ",")
			),
			'enclosure' => array(
				'section' => 'createfromfile',
				'type' => 'select',
				'label' => 'Enclosure',
				'default' => '',
				'options' => array("" => "", '"' => '"', "'" => "'")
			)
		);


		$htmlForm = new HTMLForm( $formDescriptorUpload, 'CreateFromFile' );

		$htmlForm->setSubmitText( 'Import' ); # What text does the submit button display
		$htmlForm->setTitle( $this->getTitle() ); # You must call setTitle() on an HTMLForm

		/* We set a callback function */
		$htmlForm->setSubmitCallback( array( 'SpecialCreateFromFile', 'processInput' ) );  # Call processInput() in SpecialBioParser on submit

		$htmlForm->suppressReset(false); # Get back reset button 
		$htmlForm->show(); # Displaying the form

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

			//TODO Retrieve last page title from a category type
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

			if ( $formData['enclosure'] ) {
				$enclosure =  $formData['enclosure'];
			}

			if ( empty( $groupselect ) ) {
				return "No assigned group!";
			}

			$htmlout = "";

			$htmllink = '<p class="createfromSpread-link" data-selector=".createspread-show" data-template="'.$groupselect.'" data-title="'.$title.'" data-delimiter="'.$delimiter.'" data-enclosure="'.$enclosure.'" data-userparam="" data-start="'.$start.'" data-username="WikiSysop">Create</p>';

			//TODO Read file and show as spreadsheet there with link to trigger creation
			$htmldiv = '<div class="createspread"><div class="createspread-data" style="display: none;"><pre>
# Sample Name 	# Species	# Volume (ul)	# Concentration (ng/ul)	# Index sequence (i.e. Truseq index 1 or ATCACG)	# Comments
# Example 1	# Mus musculus	# 10	#	# Truseq index 1, 2 and 3	# This sample is a pool of three libraries
# Write information of samples below. Do not use # symbol
Ex1	Mus	10	424	3	Hola
</pre></div></div>';
			
			$htmlout = $htmldiv."\n".$htmllink;

			return $htmlout;

		} else {
			return "Empty or no file!";
		}
	}

}
<?php
if (!defined('MEDIAWIKI')) { die(-1); } 
 
# Our SpecialPage
class SpecialCreateFromFile extends SpecialPage {

	/**
	 * Special page entry point
	 */
	public function execute($par) {
	
		global $wgCreateFromFileftypes;

		global $wgOut;
		$this->setHeaders();

		# A formDescriptor Array to tell HTMLForm what to build
		$formDescriptorUpload = array(
			'fileupload' => array(
			 'section' => 'createfromfile',
				'label' => 'Upload', # What's the label of the field
				'class' => 'uploadfield', # What's the input type
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
				#'default' => '',
				'options' => array( "\t", ";", ",")
			),
			'enclosure' => array(
				'section' => 'createfromfile',
				'type' => 'select',
				'label' => 'Enclosure',
				#'default' => '',
				'options' => array("", '"', "'")
			)
		);


		$htmlForm = new HTMLForm( $formDescriptorUpload, 'CreateFromFile' );

		$htmlForm->setSubmitText( 'Impiort' ); # What text does the submit button display
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

			$htmlout = '<p class="createfromSpread-link" data-selector=".createspread-show" data-template="'.$groupselect.'" data-title="'.$title.'" data-delimiter="'.$delimiter.'" data-enclosure="'.$enclosure.'" data-userparam="" data-start="'.$start.'" data-username="WikiSysop">Create</p>';

			//TODO Read file and show as spreadsheet there with link to trigger creation

			return $htmlout;

		} else {
			return "Empty or no file!";
		}
	}

}
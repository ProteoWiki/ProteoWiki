<?php
if (!defined('MEDIAWIKI')) { die(-1); } 
 
# Upload SpecialPage
class ProteoWikiUpload extends SpecialPage {
 
 	protected $input_form_fields =array();
 	
 	protected static $jobs =array();

	/**
	 * Constructor : initialise object
	 * Get data POSTed through the form and assign them to the object
	 * @param $request WebRequest : data posted.
	 */

	public function __construct($request = null) {
		parent::__construct('ProteoWikiUpload');   #The first argument must be the name of your special page
	}

	/**
	 * Special page entry point
	 */
	public function execute($par) {
		global $wgOut;

		$wgOut->addModules( 'ext.ProteoWiki.upload' );
		$this->setHeaders();

		# A formDescriptor Array to tell HTMLForm what to build
		$formDescriptorUpload = array(

			'fileupload' => array(
			'section' => 'uploadfile',
				'label' => 'Upload', # What's the label of the field
				'class' => 'HTMLTextField', # What's the input type
				'type' => 'file'
			),

			'groupselect' => array(
				'section' => 'uploadfile',
				'type' => 'select',
				'label' => 'Content',
				'options' => array('Request Properties', 'Sample Properties', 'Process Properties', 'Associations', 'Generators')
			)

		);
		
		$htmlForm = new HTMLForm( $formDescriptorUpload, 'proteowikiupload_form' );
		$htmlForm->setSubmitText( 'Process' ); # What text does the submit button display
		$htmlForm->setSubmitText( wfMessage('proteowikiupload-uploadfile-button')->text() ); # What text does the submit button display
		$htmlForm->setTitle( $this->getTitle() ); # You must call setTitle() on an HTMLForm

		/* We set a callback function */
		$htmlForm->setSubmitCallback( array( 'ProteoWikiUpload', 'processInput' ) );  # Call processInput() in SpecialBioParser on submit

		$htmlForm->suppressReset(false); # Get back reset button

		$wgOut->addHTML( "<div id='uploadfile' class='proteowikiupload_section'>" );
		$htmlForm->show(); # Displaying the form
		$wgOut->addHTML( "</div>" );

	}

	/* We write a callback function */
	# OnSubmit Callback, here we do all the logic we want to do...
	static function processInput( $formData ) {
	
		global $wgOut;
		global $wgUser;

		if ( $formData['groupselect'] ) {
			$groupselect =  $formData['groupselect'];
		}
		
		if ( $_FILES['wpfileupload']['size'] > LIMITSIZE ) {
		
			$kb = LIMITZE/(1024*1024);
		
			return ("Sorry. Files larger than ".$kb." are not allowed." );
		}
		
	}

}

# NO PHP Closing bracket "? >". This is pure code.

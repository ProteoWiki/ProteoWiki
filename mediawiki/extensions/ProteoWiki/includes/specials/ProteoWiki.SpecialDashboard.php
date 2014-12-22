<?php
if (!defined('MEDIAWIKI')) { die(-1); } 
 
# Upload SpecialPage
class SpecialProteoWiki extends SpecialPage {
 
 	protected $input_form_fields =array();
 	
 	protected static $jobs =array();

	/**
	 * Constructor : initialise object
	 * Get data POSTed through the form and assign them to the object
	 * @param $request WebRequest : data posted.
	 */

	public function __construct($request = null) {
		parent::__construct('ProteoWiki');   #The first argument must be the name of your special page
	}

	/**
	 * Special page entry point
	 */
	public function execute($par) {
		global $wgOut;

		$wgOut->addModules( 'ext.ProteoWiki' );
		$this->setHeaders();
		$this->getOutput()->setPageTitle( 'ProteoWiki Dashboard' );

		# A formDescriptor Array to tell HTMLForm what to build
		$formDescriptorUpload = array(

			'groupselect' => array(
				'section' => 'process',
				'class' => 'HTMLSelectField',
				'label' => 'Content',
				'options' => array(
									'Request Properties' => 'Request Properties',
									'Sample Properties' => 'Sample Properties',
									'Process Properties' => 'Process Properties',
									'Associations' => 'Associations',
									'Generators' => 'Generators',
									'All' => 'All'
				)
			)
		);
		
		$htmlForm = new HTMLForm( $formDescriptorUpload, 'proteowiki_dashboard' );
		$htmlForm->setSubmitText( 'Process' ); # What text does the submit button display
		$htmlForm->setSubmitText( wfMessage('proteowiki-button')->text() ); # What text does the submit button display
		$htmlForm->setTitle( $this->getTitle() ); # You must call setTitle() on an HTMLForm

		/* We set a callback function */
		$htmlForm->setSubmitCallback( array( 'SpecialProteoWiki', 'processInput' ) );  # Call processInput() in SpecialBioParser on submit

		$htmlForm->suppressReset(false); # Get back reset button

		$wgOut->addHTML( "<div id='dashboard' class='proteowiki_dashboard'>" );
		$wgOut->addHTML( "<div class='status'></div>" );
		$htmlForm->show(); # Displaying the form
		$wgOut->addHTML( "</div>" );

	}

	/* We write a callback function */
	# OnSubmit Callback, here we do all the logic we want to do...
	static function processInput( $formData ) {

	}

}

# NO PHP Closing bracket "? >". This is pure code.

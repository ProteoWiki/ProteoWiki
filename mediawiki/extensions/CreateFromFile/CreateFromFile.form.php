<?php
if (!defined('MEDIAWIKI')) { die(-1); } 
 
# Our SpecialPage
class SpecialCreateFromFile extends SpecialPage {

	/**
	 * Special page entry point
	 */
	public function execute($par) {
	
		global $wgOut;
		$this->setHeaders();

		# A formDescriptor Array to tell HTMLForm what to build
		$formDescriptorUpload = array(

		);

		$htmlForm = new HTMLForm( $formDescriptorUpload, 'CreateFromFile' );

		$htmlForm->setSubmitText( 'Impiort' ); # What text does the submit button display
		$htmlForm->setTitle( $this->getTitle() ); # You must call setTitle() on an HTMLForm

		/* We set a callback function */
		$htmlForm->setSubmitCallback( array( 'SpecialCreateFromFile', 'processInput' ) );  # Call processInput() in SpecialBioParser on submit

		$htmlForm->suppressReset(false); # Get back reset button 
		$htmlForm->show(); # Displaying the form


	}
}
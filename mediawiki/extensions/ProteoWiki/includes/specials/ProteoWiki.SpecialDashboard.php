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
		global $wgProteoWikiPages;

		$wgOut->addModules( 'ext.ProteoWiki' );
		$this->setHeaders();
		$this->getOutput()->setPageTitle( 'ProteoWiki Dashboard' );

		# A formDescriptor Array to tell HTMLForm what to build
		$formDescriptorUpload = array(

			'groupselect' => array(
				'section' => 'process',
				'class' => 'HTMLSelectField',
				'label' => 'Content',
				'options' => $this->simpleArray( $wgProteoWikiPages )
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

		$groupselect = "";
		if ( $formData['groupselect'] ) {
			$groupselect =  $formData['groupselect'];
		}
		
		if ( ! empty( $groupselect ) ) {
			// Get the title
			$title = Title::newFromText( $groupselect, NS_PROTEOWIKICONF );
			
			// TODO if if page exists
			
			// Get the contents -> process
			// API call?
			$listParams = ProteoWikiImport::listFromPageConf( $title );
			//var_dump( $listParams );
			$listProps = ProteoWikiImport::propsFromList( $listParams );
			

			// Process properties
			foreach ( $listProps as $property => $type ) {
				// TODO: Change NS of property for proper reference
				$propertyTitle = "Property:".$property;
				$propertyText = "[[Has Type::".$type."]]";
				
				ProteoWikiImport::prepareJob( $propertyTitle, $propertyText, "Creating property", "yes" );
			}
			
			// Process templates
			
			foreach ( $listparams as $template => $allparams ) {
				// TODO: Change NS of template for proper reference
				$templateTitle = "Template:".$template;
				$templateText = "";
			
				foreach ( $allparams as $param => $infoparam ) {
					$templateText.= $infoparam["Label"].": ".$param."\n";
				}
				
				ProteoWikiImport::prepareJob( $templateTitle, $templateText, "Creating template", "yes" );
			}
			
			// TODO: Process FORM NS
			
			self::runJobs();
		}
		
	
	}

	function simpleArray( $hash ) {

		$array = array();
		
		foreach ( $hash as $key => $valuearr ) {
			foreach ( $valuearr as $value ) {
				$array[$value] = $value;
			}
		}
		
		return $array;
	}

}

# NO PHP Closing bracket "? >". This is pure code.

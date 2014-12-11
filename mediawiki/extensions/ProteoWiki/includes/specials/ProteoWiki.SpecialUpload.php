<?php
if (!defined('MEDIAWIKI')) { die(-1); } 
 
# Upload SpecialPage
class SpecialProteoWikiUpload extends SpecialPage {
 
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
		$this->getOutput()->setPageTitle( 'ProteoWiki' );

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
				'class' => 'HTMLSelectField',
				'label' => 'Content',
				'options' => array( 'Request Properties' => 'Request Properties',
									'Sample Properties' => 'Sample Properties',
									'Process Properties' => 'Process Properties',
									'Associations' => 'Associations',
									'Generators' => 'Generators'
				)
			),
			'overwrite' => array(
				'section' => 'uploadfile',
				'class' => 'HTMLCheckField',
				'label' => 'Overwrite'
			)
		);
		
		$htmlForm = new HTMLForm( $formDescriptorUpload, 'proteowikiupload_form' );
		$htmlForm->setSubmitText( 'Process' ); # What text does the submit button display
		$htmlForm->setSubmitText( wfMessage('proteowikiupload-uploadfile-button')->text() ); # What text does the submit button display
		$htmlForm->setTitle( $this->getTitle() ); # You must call setTitle() on an HTMLForm

		/* We set a callback function */
		$htmlForm->setSubmitCallback( array( 'SpecialProteoWikiUpload', 'processInput' ) );  # Call processInput() in SpecialBioParser on submit

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
	
		$limitsize = 10000000;
		
		$overwrite = false;
		$groupselect = "";
		
		if ( $formData['groupselect'] ) {
			$groupselect =  $formData['groupselect'];
		}
		
		if ( $formData['overwrite'] ) {
			$overwrite = false;
		}
		
		if ( $_FILES['wpfileupload']['size'] > $limitsize ) {
		
			$kb = $limitsize/(1024*1024);
		
			return ("Sorry. Files larger than ".$kb." are not allowed." );
		}
			if ( $_FILES['wpfileupload']['error'] == 0 ) {
		
			// TODO: Detect if exists file
			if ( !  empty( $groupselect ) ) {
				
				if ( ! $overwrite ) {
					
					$title = Title::newFromText( $groupselect, NS_PROTEOWIKICONF );
					if ( $title->exists() ) {
						global $wgArticle;
						$urlpage = str_replace( "$1", "ProteoWikiConf".$groupselect, $wgArticle );

						return 'File already exists. Overwrite or <a href="'.$urlpage.'">modify manually</a>.';

					} else {
						
						self::processFile( $_FILES['wpfileupload'], $groupselect ); 
					}
				} else {
					
					self::processFile( $_FILES['wpfileupload'], $groupselect );
				}
			}
		
		}
		
		return "Problem. Try again!";
	}
	
	static function processFile( $hashFile, $filegroup ) {
		
		// If no error
		if ( $hashFile['error'] == 0 ) {
			
			$mimes = array('application/vnd.ms-excel','text/plain','text/csv','text/tsv');
			if ( in_array( $hashFile['type'], $mimes) ) {
				
				$content = file_get_contents( $_FILES["wpfileupload"]["tmp_name"] );
				$content = "<proteowikiconf>".$content."</proteowikiconf>";
				$title = Title::newFromText( $filegroup, NS_PROTEOWIKICONF );
				
				$page = WikiPage::factory( $title );
				$page->doEdit( $content, "Updating configuration from form" );
				
				return "Done!";
				
				
			} else {
				
				return 'No CSV file!';
			}
			
		}
		
		return 'Try again!';
	}

}

# NO PHP Closing bracket "? >". This is pure code.

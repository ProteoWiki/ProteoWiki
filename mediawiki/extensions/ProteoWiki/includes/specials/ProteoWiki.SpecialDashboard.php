<?php
if (!defined('MEDIAWIKI')) { die(-1); } 
 
# Upload SpecialPage
class SpecialProteoWiki extends SpecialPage {
	
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
				'options' => $this->simpleArray2( $wgProteoWikiPages['Properties'] )
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
				$propertyText = "[[Has type::".$type."]]";
				
				self::prepareJob( $propertyTitle, $propertyText, "Creating property", "yes" );
			}
			
			// Process templates
			
			foreach ( $listParams as $template => $allparams ) {
				// TODO: Change NS of template for proper reference
				$templateTitle = "Template:".$template;
				$templateText = "<includeonly>";
			
				foreach ( $allparams as $param => $infoparam ) {
					$templateText.= "*".$infoparam["Label"].": [[".$infoparam["Property"]."::{{{".$param."|}}}]]\n"; // Optional visualization
				}
				
				$templateText = $templateText."[[Category:".$template."]]</includeonly>"; //TODO: Review if proper approach is category -> Handle with WikiPage in query
				self::prepareJob( $templateTitle, $templateText, "Creating template", "yes" );
			}
			
			// Process FORM NS
			global $wgProteoWikiForms;
			
			if ( array_key_exists( $groupselect, $wgProteoWikiForms ) ) {
				
				$commonTemplates = $wgProteoWikiForms[ $groupselect ];
				$commonText = "";
				
				foreach ( $commonTemplates as $commonTemplate ) {
					
					if ( array_key_exists( $commonTemplate, $listParams ) ) {
						
						$commonText = "{{{for template|".$commonTemplate."}}}\n";
	
						// First common Form
						foreach ( $listParams[ $commonTemplate ] as $param => $infoparam ) {
							
							$mandatory = "";
							$role = "";
							$values = "";
							
							if ( array_key_exists( 'Mandatory', $infoparam ) && $infoparam['Mandatory'] == 1 ) {
								$mandatory = "|mandatory";
							}
							
							if ( array_key_exists( 'Role', $infoparam ) && ( ! empty( $infoparam['Role'] ) ) ) {
								$role = "|restricted=".$infoparam['Role'];
							}
							// Careful confusion default and values in SForms
							if ( array_key_exists( 'Default', $infoparam ) && ( ! empty( $infoparam['Default'] ) ) ) {
								$values = "|values=".self::formatFormValues( $infoparam['Default'] );
							}
							
							$commonText.= "*".$infoparam["Label"].": {{{field|".$param.$mandatory.$role.$values."}}}\n";
						}
						
						$commonText.="{{{end template}}}\n";
					}
				}

				foreach ( $listParams as $template => $allparams ) {
					
					if ( in_array( $template, $commonTemplates ) ) {
						// Not consider common templates
						continue;
					}
					
					// TODO: Change NS of form for proper reference
					$formTitle = "Form:".$template;
					$formText = "{{{for template|".$template."}}}\n";

					foreach ( $allparams as $param => $infoparam ) {
						
						$mandatory = "";
						$role = "";
						$values = "";
						
						if ( array_key_exists( 'Mandatory', $infoparam ) && $infoparam['Mandatory'] == 1 ) {
							$mandatory = "|mandatory";
						}
						
						if ( array_key_exists( 'Role', $infoparam ) && ( ! empty( $infoparam['Role'] ) ) ) {
							$role = "|restricted=".$infoparam['Role'];
						}
						// Careful confusion default and values in SForms
						if ( array_key_exists( 'Default', $infoparam ) && ( ! empty( $infoparam['Default'] ) ) ) {
							$values = "|values=".self::formatFormValues( $infoparam['Default'] );
						}
						
						$formText.= "*".$infoparam["Label"].": {{{field|".$param.$mandatory.$role.$values."}}}\n";
					
					}
					
					$formText.="{{{end template}}}\n";
					
					self::prepareJob( $formTitle, self::formPre( $template, $commonTemplates )."\n".$commonText."\n".$formText."\n".self::formPost()."\n", "Creating form", "yes" );
				}
				

			}
			
			self::runJobs();
		}
		
	
	}
	
	private static function prepareJob( $pageName, $text, $summary, $overwrite ) {

		global $wgUser;
		#global $wgShowExceptionDetails;
		
		#$wgShowExceptionDetails = true;
		// Submit Job
		#$jobs = array();
		$jobParams = array();
		$jobParams['user_id'] = $wgUser->getId();
		$jobParams['edit_summary'] = $summary;
		$jobParams['for_pages_that_exist'] = $overwrite;
		$jobParams['text'] = $text;


		$title = Title::newFromText( $pageName ); // Gene:GeneName

		if ( is_null( $title ) ) {
			return true;

		} else {

			self::$jobs[] = new DTImportJob( $title, $jobParams );
			return true;
		}

	}
	
	private static function runJobs() {

		// MW 1.21+
		if ( class_exists( 'JobQueueGroup' ) ) {
			#print "<pre>";
			#print "</br>Number of jobs is ".count((self::$jobs))."</br>";
			#$output = print_r( self::$jobs, true);
			#file_put_contents($wgBioParserJobOut.'/listjobs.out', $output);

			JobQueueGroup::singleton()->push( self::$jobs );
		} else {
			Job::batchInsert( self::jobs );
		}
		
		global $wgRunJobsPath;
		global $wgProteoWikiJobOut;
		global $wgRunJobsProcs;

		# Should we trigger runJobs here?
		$descriptorspec = array(
				array('pipe', 'r'),               // stdin
				array('file', $wgProteoWikiJobOut.'/samples.out', 'a'), // stdout
				array('file', $wgProteoWikiJobOut.'/samples.err', 'a'),               // stderr -> Generate one temp?
		);

		$proc = proc_open("php $wgRunJobsPath --procs $wgRunJobsProcs &", $descriptorspec, $pipes);

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

	function simpleArray2( $input ) {

		$array = array();
		
		foreach ( $input as $in  ) {
			$array[$in] = $in;
		}
		
		return $array;
	}


	private static function formatFormValues( $values ) {
		
		$array = explode( ";", $values );
		
		$new_array = array();
		
		foreach ( $array as $arr ) {
			array_push( $new_array, trim( $arr ) );
		}
		
		return implode( ",", $new_array );
	}
	
	// TODO: Here to much pre assumptions :/ params should be better
	private static function formPre( $form, $common ) {

		$output = "<noinclude>\n{{#forminput:form=".$form."|query string=namespace=".$common[0]."}}\n</noinclude>\n<includeonly>";
		return $output;
	
	}
	
	private static function formPost( ) {
		
		$output = "{{{standard input|save}}}{{{standard input|cancel}}}</includeonly>";
		return $output;
	}
	
}

# NO PHP Closing bracket "? >". This is pure code.

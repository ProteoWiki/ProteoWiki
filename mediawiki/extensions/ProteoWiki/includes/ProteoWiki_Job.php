<?php

class ProteoWikiJob {

	protected static $jobs =array();

	public static function prepareJob( $pageName, $text, $summary, $overwrite='skip' ) {

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
			JobQueueGroup::singleton()->push( self::$jobs );
		} else {
			Job::batchInsert( $this->jobs );
		}
		
		global $wgProteoWikiRunJobsPath;
		global $wgProteoWikiJobOut;
		global $wgProteoWikiRunJobsProcs;

		# Should we trigger runJobs here?
		$descriptorspec = array(
				array('pipe', 'r'),               // stdin
				array('file', $wgProteoWikiJobOut.'/samples.out', 'a'), // stdout
				array('file', $wgProteoWikiJobOut.'/samples.err', 'a'),               // stderr -> Generate one temp?
		);

		$proc = proc_open("php $wgProteoWikiRunJobsPath --procs $wgProteoWikiRunJobsProcs &", $descriptorspec, $pipes);

	}

}
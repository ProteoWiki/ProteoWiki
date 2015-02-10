<?php
class ApiProteoWikiStatus extends ApiBase {

	public function execute() {
	
		$params = $this->extractRequestParams();
		
		// TODO: finish
		$jsonresult = ProteoWikiReadConf::readConf( $params['class'] );
		$output = json_decode( $jsonresult );
	
		$this->getResult()->addValue( null, $this->getModuleName(), array ( 'status' => $output->status, 'msg' => $output->msg ) );
	
		return true;
	}

	public function getAllowedParams() {
		return array(
			'class' => array(
				ApiBase::PARAM_TYPE => 'string',
				ApiBase::PARAM_REQUIRED => true
			)
		);
	}

	public function getDescription() {
		return array(
			'API for querying configuration from ProteoWiki'
		);
	}
	public function getParamDescription() {
		return array(
			'class' => 'Class type'
		);
	}

	public function getVersion() {
		return __CLASS__ . ': 1.1';
	}

}


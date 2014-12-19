<?php
class ApiProteoWikiConf extends ApiBase {

	public function execute() {

		$params = $this->extractRequestParams();

		// For compatibility with GET method, we process JSON
		$jsonresult = ProteoWikiImport::importConf( $params['text'], $params['title'], $params['delimiter'], $params['enclosure'] );
		$output = json_decode( $jsonresult );

		$this->getResult()->addValue( null, $this->getModuleName(), array ( 'status' => $output->status, 'msg' => $output->msg ) );

		return true;

	}
	public function getAllowedParams() {
		return array(
			'text' => array(
				ApiBase::PARAM_TYPE => 'string',
				ApiBase::PARAM_REQUIRED => true
			),
			'title' => array(
				ApiBase::PARAM_TYPE => 'string',
				ApiBase::PARAM_REQUIRED => true
			),
			'delimiter' => array(
				ApiBase::PARAM_TYPE => 'string',
				ApiBase::PARAM_REQUIRED => false
			),
			'enclosure' => array(
				ApiBase::PARAM_TYPE => 'string',
				ApiBase::PARAM_REQUIRED => false
			)
		);
	}

	public function getDescription() {
		return array(
			'API for importing configuration data into ProteoWiki'
		);
	}
	public function getParamDescription() {
		return array(
			'text' => 'Content to be processed'
		);
	}

	public function getVersion() {
		return __CLASS__ . ': 1.1';
	}
}
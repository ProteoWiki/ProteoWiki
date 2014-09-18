<?php
class ApiCreateFromFile extends ApiBase {

	public function execute() {

		$params = $this->extractRequestParams();

		// For compatibility with GET method, we process JSON
		$jsonresult = CreateFromFile::createfromSpreadJS( $params['text'], $params['template'], $params['title'], $params['delimiter'], $params['enclosure'], $params['userparam'], $params['start'], $params['username'], $params['extrainfo'] );
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
			'template' => array(
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
			),
			'userparam' => array(
				ApiBase::PARAM_TYPE => 'string',
				ApiBase::PARAM_REQUIRED => false
			),
			'start' => array(
				ApiBase::PARAM_TYPE => 'string',
				ApiBase::PARAM_REQUIRED => false
			),
			'username' => array(
				ApiBase::PARAM_TYPE => 'string',
				ApiBase::PARAM_REQUIRED => false
			),
			'extrainfo' => array(
				ApiBase::PARAM_TYPE => 'string',
				ApiBase::PARAM_REQUIRED => false
			)
		);
	}

	public function getDescription() {
		return array(
			'API for importing delimited separated information into MediaWiki'
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
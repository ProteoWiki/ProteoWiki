<?php
class ApiCreateFromFile extends ApiBase {

	public function execute() {

		$params = $this->extractRequestParams();

	}
	public function getAllowedParams() {
		return array(
			'text' => array(
				ApiBase::PARAM_TYPE => 'string',
				ApiBase::PARAM_REQUIRED => true
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
}
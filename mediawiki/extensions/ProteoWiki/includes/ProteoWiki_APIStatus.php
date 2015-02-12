<?php
class ApiProteoWikiStatus extends ApiBase {

	public function execute() {
	
		$params = $this->extractRequestParams();
		
		// TODO: Make it work in XML :O
		$jsonresult = ProteoWikiReadConf::readConf( $params['class'] );

		$outcome =  array( "status" => "OK", "result" => array() );
		foreach ( $jsonresult as $key => $value ) {
			$outcome['result'][ $key ] = $value;
		}

		//var_dump( $outcome );
		$this->getResult()->addValue( null, $this->getModuleName(), $outcome );
	
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


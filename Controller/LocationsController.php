<?php
// This controller is exclusively used for requestActions that return values
class LocationsController extends AppController {
	public $name = 'Locations';
	public $components = array('RequestHandler');
	public $uses = array('Location');

	public function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->allow('*');
	}

	public function beforeRender() {
		parent::beforeRender();
	}

	public function getStatesAndAbbreviations() {
		return $this->Location->getStatesAndAbbreviations();
	}
	
	public function getCountiesSimplified($state) {
		return $this->Location->getCountiesSimplified($state);
	}
	
	public function getCountiesFull($state) {
		return $this->Location->getCountiesFull($state);
	}
	
	public function getCountyIdFromFips($fips) {
		return $this->Location->getCountyIdFromFips($fips);
	}
	
	
	/* Not currently used */
	
	function getCountyID($county, $state) {
		return $this->Location->getCountyID($county, $state);
	}

	function getCountyProfilesLink($county, $state) {
		return $this->Location->getCountyProfilesLink($county, $state);
	}

	function getCountysSchoolCorps($county_id) {
		return $this->Location->getCountysSchoolCorps($county_id);
	}

	function getLocationName($loc_type_id, $loc_id, $append = false) {
		return $this->Location->getLocationName($loc_type_id, $loc_id, $append = false);
	}

	function simplify($location_name) {
		return $this->Location->simplify($location_name);
	}

	function getStateID($state_name) {
		return $this->Location->getStateID($state_name);
	}

	function getStateFullName($state) {
		return $this->Location->getStateFullName($state);
	}

	function setReportLocationNames($locations) {
		return $this->Location->setReportLocationNames($locations);
	}

	function getReportLocationNames($locations) {
		return $this->Location->getReportLocationNames($locations);
	}

	function getCountySimplifiedName($county, $state) {
		return $this->Location->getCountySimplifiedName($county, $state);
	}

	function getCountyFullName($county, $state, $append = false) {
		return $this->Location->getCountyFullName($county, $state, $append);
	}

	function getStateAbbreviation($state) {
		return $this->Location->getStateAbbreviation($state);
	}

	function getStateAbbreviations($lowercase = false) {
		return $this->Location->getStateAbbreviations($lowercase);
	}

	function setCountySimplifiedNames() {
		$this->Location->setCountySimplifiedNames();
		$this->render('/');
	}

	function getAllCommunities() {
		$this->loadModel('Community');
		return $this->Community->getAllCommunities();
	}

	function getCommunityIdFromFips($fips) {
		return $this->Location->getCommunityIdFromFips($fips);
	}
}
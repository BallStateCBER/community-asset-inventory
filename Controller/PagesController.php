<?php
App::uses('AppController', 'Controller');
class PagesController extends AppController {
	public $name = 'Pages';
	public $helpers = array('Html', 'Session');
	public $uses = array();

	public function home() {
		$this->set(array(
			'title_for_layout' => '',
			'content_wrapper_class' => 'two_col'
		));
	}
	
	public function state($id) {
		App::uses('Location', 'Model');
		$Location = new Location();
		$state_name = $Location->getStateFullName($id);
		$this->set(array(
			'title_for_layout' => $state_name,
			'counties' => $Location->getCountiesFull($id)
		));
	}

	function sources() {
		$this->set('title_for_layout', 'Data Sources and Methodology');	
	}

	function credits() {
		$this->set('title_for_layout', 'Credits');
	}

	function faq() {
		$this->set('title_for_layout', 'Frequently Asked Questions');
	}

	function clear_cache() {
		Cache::clear();
		clearCache();
		$this->Flash->set('Cache cleared');
		return $this->render('/Pages/home');
	}
}
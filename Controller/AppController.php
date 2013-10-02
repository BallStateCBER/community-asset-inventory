<?php
class AppController extends Controller {
	public $helpers = array(
		'Js' => array('Jquery'),
		'Html',
		'Cache'
	);
	public $components = array('DataCenter.Flash', 'RequestHandler', 'Session', 'Cookie');

	public function beforeRender() {
		if ($this->layout == 'default') {
			App::uses('DataCategory', 'Model');
			$DataCategory = new DataCategory();
			
			App::uses('Location', 'Model');
			$Location = new Location();
			
			$this->set(array(
				'parent_categories' => $DataCategory->getParentCategories(),
				'counties' => $Location->getCounties('IN')
			));	
		}
	}

	// Adds a string message with a class of 'success', 'error', or 'notification' (default)
	// OR adds a variable to dump and the class 'dump' 
	public function flash($message, $class = 'notification') {
		// Dot notation doesn't seem to allow for the equivalent of $messages['error'][] = $message	
		$stored_messages = $this->Session->read('FlashMessage');
		$stored_messages[] = compact('message', 'class');
		$this->Session->write('FlashMessage', $stored_messages);
	}
}
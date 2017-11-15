<?php
class AppController extends Controller {
	public $helpers = array(
		'Js' => array('Jquery'),
		'Html',
		'Cache'
	);
	public $components = array(
	    'DataCenter.Flash',
        'RequestHandler',
        'Session',
        'Cookie',
        'Security'
    );

    public function beforeFilter()
    {
        $this->Security->blackHoleCallback = 'forceSSL';
        $this->Security->requireSecure();
    }

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

    /**
     * Redirects the current request to HTTPS
     *
     * @return mixed
     */
    public function forceSSL()
    {
        return $this->redirect('https://' . env('SERVER_NAME') . $this->here);
    }
}
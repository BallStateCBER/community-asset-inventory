<?php
class DataCategoriesController extends AppController {
	var $name = 'DataCategories';
	var $components = array('RequestHandler');
	var $helpers = array('Form', 'Html');

	public function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->allow('*');
	}

	public function beforeRender() {
		parent::beforeRender();

	}

	public function index() {
		
	}

	public function recover() {
		list($start_usec, $start_sec) = explode(" ", microtime());
		set_time_limit(3600);
		$this->DataCategory->recover();
		list($end_usec, $end_sec) = explode(" ", microtime());
		$start_time = $start_usec + $start_sec;
		$end_time = $end_usec + $end_sec;
		$loading_time = $end_time - $start_time;
		$minutes = round($loading_time / 60);
		echo 'Done recovering data categories tree (Took '.$minutes.' minutes.';
    }

	public function getnodes() {
		
	    // retrieve the node id that Ext JS posts via ajax
	    $parent = intval($this->data['node']);

	    // find all the nodes underneath the parent node defined above
	    // the second parameter (true) means we only want direct children
	    $nodes = $this->DataCategory->children($parent, true);

	    App::uses('Datum', 'Model');
	    $Datum = new Datum();
	    foreach ($nodes as $key => $node) {

	    	// Check for data associated with this category
	    	$datum = $Datum->find('first', array(
	    		'conditions' => array('Datum.category_id' => $node['DataCategory']['id']),
	    		'fields' => array('Datum.id')
	    	));
	    	$nodes[$key]['DataCategory']['no_data'] = $datum == false;
	    }

	    // Visually note categories with no data
	    $showNoData = false;

	    // send the nodes to our view
	    $this->set(compact('nodes', 'showNoData'));

	}

	public function reorder() {

		// retrieve the node instructions from javascript
		// delta is the difference in position (1 = next node, -1 = previous node)

		$node = intval($this->params['form']['node']);
		$delta = intval($this->params['form']['delta']);

		if ($delta > 0) {
			$this->DataCategory->movedown($node, abs($delta));
		} elseif ($delta < 0) {
			$this->DataCategory->moveup($node, abs($delta));
		}

		// send success response
		exit('1');

	}

	public function reparent() {
		$node = intval($this->params['form']['node']);
		$parent = intval($this->params['form']['parent']);
		$position = intval($this->params['form']['position']);

		// save the node with the new parent id
		// this will move the node to the bottom of the parent list

		$this->DataCategory->id = $node;
		$this->DataCategory->saveField('parent_id', $parent);

		// If position == 0, then we move it straight to the top
		// otherwise we calculate the distance to move ($delta).
		// We have to check if $delta > 0 before moving due to a bug
		// in the tree behaviour (https://trac.cakephp.org/ticket/4037)

		if ($position == 0) {
			$this->DataCategory->moveup($node, true);
		} else {
			$count = $this->DataCategory->childcount($parent, true);
			$delta = $count-$position-1;
			if ($delta > 0) {
				$this->DataCategory->moveup($node, $delta);
			}
		}

		// send success response
		exit('1');

	}

	public function add() {
		if (empty($this->data)) {
			$this->flash('$this->data is empty.', 'error');
		} else {
			$inputted_names = trim($this->data['DataCategory']['name']);
			$split_category_names = explode("\n", $inputted_names);
			$level = 0;
			$root_parent_id = $this->data['DataCategory']['parent_id'];
			$parents = array($root_parent_id);
			foreach ($split_category_names as $line_num => $name) {
				$level = $this->DataCategory->getIndentLevel($name);
				$parents = array_slice($parents, 0, $level + 1);	// Discard any now-irrelevant data
				if ($level == 0) {
					$parent_id = $root_parent_id;
				} elseif (isset($parents[$level])) {
					$parent_id = $parents[$level];
				} else {
					$this->flash("Error with nested data category structure. Looks like there's an extra indent in line $line_num: \"$name\"", 'error');
					continue;
				}
				
				// Strip leading/trailing whitespace and hyphens used for indenting
				$name = ltrim($name, '-');
				$name = trim($name);
				
				if (! $name) {
					continue;
				}
				
				$this->DataCategory->create();
				if (! $this->data['DataCategory']['name']) {
					$this->flash('Data category name is blank.', 'error');
				} else {
					$data = array('DataCategory' => compact('name', 'parent_id'));
					if ($this->DataCategory->save($data)) {
						$this->flash('#'.$this->DataCategory->id.': '.$name, 'success');
						$parents[$level + 1] = $this->DataCategory->id;
					} else {
						$this->flash('Error adding new data category "'.$name.'".', 'error');
					}
				}
			}
		}
		$this->redirect('/data_categories');
	}

	public function auto_complete() {
		/*
		$results = $this->DataCategory->find('all', array(
			'conditions' => array(
				'DataCategory.name LIKE' => $this->data['DataCategory']['name'].'%'
			),
			'fields' => array('id', 'name'),
			'limit' => 10
		));
		*/

		$string_to_complete = $this->data['DataCategory']['name'];
		$limit = 20;
		$like_conditions = array(
			$string_to_complete.'%',
			'% '.$string_to_complete.'%',
			'%'.$string_to_complete.'%'
		);
		$select_statements = array();
		foreach ($like_conditions as $like) {
			$select_statements[] =
				"SELECT `DataCategory`.`id`, `DataCategory`.`name`
				FROM `data_categories` AS `DataCategory`
				WHERE `DataCategory`.`name` LIKE '$like'";
		}
		$query = implode("\nUNION\n", $select_statements)."\nLIMIT $limit";
		$results = $this->DataCategory->query($query);
		//print_r($results);

		$categories = array();
		foreach ($results as $result) {
			$categories[] = "{$result[0]['name']} ({$result[0]['id']})";
		}
		$this->set(compact('categories'));
		$this->layout = 'ajax';
	}

	public function trace_category($id) {
		$path = array();
		$target_category = $this->DataCategory->find('first', array(
			'conditions' => array('DataCategory.id' => $id),
			'fields' => array('DataCategory.id', 'DataCategory.name', 'DataCategory.parent_id'),
			'contain' => false
		));
		if ($target_category) {
			$path[] = "{$target_category['DataCategory']['name']} ({$id})";
			$parent_id = $target_category['DataCategory']['parent_id'];
			if ($parent_id) {
				$root_found = false;
				while (! $root_found) {
					$parent = $this->DataCategory->find('first', array(
						'conditions' => array('DataCategory.id' => $parent_id),
						'fields' => array('DataCategory.id', 'DataCategory.name', 'DataCategory.parent_id'),
						'contain' => false
					));
					if ($parent) {
						$path[] = "{$parent['DataCategory']['name']} ({$parent['DataCategory']['id']})";
						if (! $parent_id = $parent['DataCategory']['parent_id']) {
							$root_found = true;
						}
					} else {
						$path[] = "(Parent data category with id $parent_id not found)";
						break;
					}
				}
			}
		} else {
			$path[] = "(Data category with id $id not found)";
		}
		$this->layout = 'ajax';
		$path = array_reverse($path);
		$this->set(compact('path'));
	}
}
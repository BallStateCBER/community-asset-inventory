<?php
App::uses('AppModel', 'Model');
class DataCategory extends AppModel {
    var $name = 'DataCategory';
    var $displayField = 'name';
    var $actsAs = array('Containable', 'Tree');
    var $order = 'DataCategory.lft ASC';
    var $hasAndBelongsToMany = array('Source');
    
	public function getIndentLevel($name) {
    	$level = 0;
    	for ($i = 0; $i < strlen($name); $i++) {
			if ($name[$i] == "\t" || $name[$i] == '-') {
				$level++;	
			} else {
				break;	
			}
		}
		return $level;
    }
    
    public function getParentCategories() {
    	$results = $this->children(null, true, array('id', 'name'), 'weight');
    	$retval = array();
    	foreach ($results as $result) {
    		$id = $result['DataCategory']['id'];
    		$retval[$id] = $result['DataCategory']['name'];
    	}
    	return $retval;
    }
    
 	public function getDescription($id) {
		$this->id = $id;
		return $this->field('description');
 	}
 	
	public function getName($id) {
		$this->id = $id;
		return $this->field('name');
	}
	
	public function getChildren($id) {
		return array_flip($this->find('list', array(
			'conditions' => array('parent_id' => $id)
		)));
	}
	
	public function getSources($id) {
		$this->id = $id;
		$result = $this->find('first', array( 
			'conditions' => array('id' => $id),
			'fields' => array('id'),
			'contain' => array('Source')
		));
		return $result['Source'];
	}
	
	// Accepts a sluggified parent category name and returns its ID
	public function getIdFromSlug($category_slug) {
		$parent_categories = $this->getParentCategories();
		foreach ($parent_categories as $pc_id => $pc_name) {
			if (Inflector::slug($pc_name) == $category_slug) {
				return $pc_id;
			}
		}
		return null;
	}
}
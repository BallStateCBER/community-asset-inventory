<?php
App::uses('AppModel', 'Model');
class Datum extends AppModel {
    var $name = 'Datum';
    var $displayField = 'value';
    var $useTable = 'scores';
    var $actsAs = array('Containable');
	var $belongsTo = array(
		'DataCategory' => array(
			'className' => 'DataCategory',
			'foreignKey' => 'category_id'
		)
	);
	
	public function getGradesAndIndices($children_categories, $county_ids) {
		$data_results = $this->find('all', array(
			'conditions' => array(
				'category_id' => array_values($children_categories),
				'county_id' => $county_ids
			),
			'fields' => array('category_id', 'county_id', 'value'),
			'contain' => false
		));
		$grades = $indices = array();
		foreach ($data_results as $dr) {
			$county_id = $dr['Datum']['county_id'];
			$category_id = $dr['Datum']['category_id'];
			$value = $dr['Datum']['value'];
			if (isset($children_categories['Grade']) && $category_id == $children_categories['Grade']) {
				$grades[$county_id] = $value;
			} elseif ($category_id == $children_categories['Index']) {
				$indices[$county_id] = $value;
			}
		}
		return array($grades, $indices);	
	}
}
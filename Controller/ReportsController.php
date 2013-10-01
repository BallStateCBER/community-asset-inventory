<?php
App::uses('AppController', 'Controller');
class ReportsController extends AppController {
	public $name = 'Reports';
	public $helpers = array('Html', 'Session');
	public $uses = array();
	
	private function __cleanFilename($filename) {
		// Remove special accented characters - ie. sí.
		$clean_name = strtr($filename, 'ŠšŸÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÑÒÓÔÕÖØÙÚÛÜİàáâãäåçèéêëìíîïñòóôõöøùúûüıÿ', 'SZszYAAAAAACEEEEIIIINOOOOOOUUUUYaaaaaaceeeeiiiinoooooouuuuyy');
		$clean_name = strtr($clean_name, array('Ş' => 'TH', 'ş' => 'th', 'Ğ' => 'DH', 'ğ' => 'dh', 'ß' => 'ss', 'Œ' => 'OE', 'œ' => 'oe', 'Æ' => 'AE', 'æ' => 'ae', 'µ' => 'u'));
		return str_replace(array('/', '\\', '?', '%', '*', ':', '|', '"', '<', '>'), '', $clean_name);
	}

	private function __getCounties() {
		App::uses('Location', 'Model');
		$Location = new Location();
		$counties = $Location->getCounties('IN');
		$county_simplified_names = array();
		foreach ($counties as $county) {
			$id = $county['Location']['id'];
			$s_name = $county['Location']['simplified'];
			$county_simplified_names[$id] = $s_name;
		}
		return array($counties, $county_simplified_names);
	}
	
	/* All color assignments now in CSS file. I think.
	private function __getColors($indices, $grades) {
		$colors = array();
		
		// If grades are inapplicable to this map, color by index
		if (empty($grades)) {
			foreach ($indices as $county_id => $value) {
				$colors[$county_id] = $this->__getMapColor($value);
			}
			
		// Otherwise, color by grade
		// Note: If these colors are changed, they must also be changed in the CSS rules for #legend_grade
		} else {
			foreach ($grades as $county_id => $grade) {
				$grade = trim(strtolower(str_replace(array('+', '-'), '', $grade)));
				switch ($grade) {
					case 'a':
						$color = '#CC553E';
						break;
					case 'b':
						$color = '#E69F5A';
						break;
					case 'c':
						$color = '#E0C95A';
						break;
					case 'd':
						$color = '#85BEBF';
						break;
					case 'f':
						$color = '#4B88AA';
						break;
				}
				$colors[$county_id] = $color;
			}
		}
		return $colors;
	}
	
	private function __getMapColor($value) {		
		if ($value >= 115) {
			return '#CB8D85';
		} elseif ($value >= 105) {
			return '#E5BFA1';
		} elseif ($value >= 95) {
			return '#DDD5AC';
		} elseif ($value >= 85) {
			return '#96BCBC';
		} else { // < 85
			return '#869DA8';
		}
	}
	*/
	
	public function category($category) {
		App::uses('DataCategory', 'Model');
		$DataCategory = new DataCategory();
		App::uses('Datum', 'Model');
		$Datum = new Datum();
		
		$parent_category_id = is_numeric($category) ? $category : $DataCategory->getIdFromSlug($category);
		if (! $parent_category_id) {
			$this->set('message', 'Error: Category ('.$category.') not found.');
			return $this->render('/pages/message');
		}
		
		
		$category_name = $DataCategory->getName($parent_category_id);
		$category_description = $DataCategory->getDescription($parent_category_id);
		$children_categories = $DataCategory->getChildren($parent_category_id);
		$sources = $DataCategory->getSources($parent_category_id);
		list($counties, $county_simplified_names) = $this->__getCounties();
		$county_ids = array_keys($county_simplified_names);
		list($grades, $indices) = $Datum->getGradesAndIndices($children_categories, $county_ids);
		
		$this->set(compact(
			'parent_category_id',
			'category_name',
			'grades', 
			'indices',
			'counties',
			'county_simplified_names', 
			'legend',
			'children_categories',
			'sources',
			'category_description'
		));
		
		if (isset($_GET['print'])) {
			$this->layout = 'print';
			$this->render('/Reports/print/category');
		} else {
			$this->layout = 'ajax';
		}
	}
	
	/* Expected named parameters:
	 * 		type:	'excel2007', 'excel5', or 'csv'
	 * 		var_id:	DataCategory parent id (parent of the index and grade categories)
	 */
	public function download() {
		// Collect params
		$type = $this->params['named']['type'];
		$parent_category_id = $this->params['named']['var_id'];
		
		// Collect category info
		App::uses('DataCategory', 'Model');
		$DataCategory = new DataCategory();
		$category_name = $DataCategory->getName($parent_category_id);
		if (! $category_name) {
			$this->flash("Error: Invalid data category selected ($parent_category_id)", 'error');
			$this->redirect('/');
		}
		$children_categories = $DataCategory->getChildren($parent_category_id);
		$sources = $DataCategory->getSources($parent_category_id);
		
		// Collect counties
		App::uses('Location', 'Model');
		$Location = new Location();
		$county_names = $Location->getCountiesFull('IN');
		
		// Get grades and indices and arrange them in the $data array
		App::uses('Datum', 'Model');
		$Datum = new Datum();
		list($grades, $indices) = $Datum->getGradesAndIndices($children_categories, array_keys($county_names));
		$data = array();
		foreach ($grades as $county_id => $grade) {
			$data[$county_id]['grade'] = $grade;	
		}
		foreach ($indices as $county_id => $index) {
			$data[$county_id]['index'] = $index;	
		}
		
		// Prepare PHPExcel
		if ($type == 'excel2007' || $type == 'excel5') {
			switch ($type) {
				case 'excel2007':
					App::import('Vendor','PHPExcel', array('file' => 'excel/PHPExcel.php'));
					App::import('Vendor','PHPExcelWriter', array('file' => "excel/PHPExcel/Writer/Excel2007.php"));
					App::import('Vendor','PHPExcelAdvancedValueBinder',array('file' => 'excel/PHPExcel/Cell/AdvancedValueBinder.php'));
					break;
				case 'excel5':
					App::import('Vendor','PHPExcel', array('file' => 'excel/PHPExcel.php'));
					App::import('Vendor','PHPExcelWriter', array('file' => "excel/PHPExcel/Writer/Excel5.php"));
					App::import('Vendor','PHPExcelAdvancedValueBinder',array('file' => 'excel/PHPExcel/Cell/AdvancedValueBinder.php'));
					break;
			}
			App::uses('ExcelReport', 'Model');
			$ExcelReport = new ExcelReport();
			$ExcelReport->getOutput($type, $category_name, $data, $county_names, $sources);
			$this->set('objPHPExcel', $ExcelReport->objPHPExcel);
		}
		
		// Determine if a 'grade' column is necessary
		$has_grades = false;
		if ($type == 'excel2007' || $type == 'excel5') {
			$has_grades = $ExcelReport->hasGrades;
		} else {
			foreach ($data as $county_id => $county_data) {
				foreach ($county_data as $datum_name => $value) {
					if ($datum_name == 'grade') {
						$has_grades = true;
						break 2;	
					}
				}
			}
		}
		
		// Set layout and content type
		if (isset($_GET['debug'])) {
			$this->layout = 'blank';
		} else {
			$this->layout = "reports/$type";
			switch ($type) {
				case 'excel2007':
					$this->RequestHandler->respondAs('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
					break;
				case 'excel5':
					$this->RequestHandler->respondAs('application/vnd.ms-excel');
					break;
				case 'csv':
					$this->RequestHandler->respondAs('csv');
					break;
			}
		}
		
		// Set variables used in view
		$this->set(array(
			'filename' => $this->__cleanFilename(Inflector::camelize($category_name))
		));
		$this->set(compact(
			'type',  
			'category_name', 
			'results', 
			'years', 
			'data', 
			'county_names',
			'has_grades',
			'sources'
		));
	}
	
	private function __getCountyProfilesLink($county) {
		if ($cache = Configure::read('cache_location_queries')) {
			$cache_key = "getCountyProfilesLink($county)";
			if ($cached = Cache::read($cache_key)) {
				return $cached;	
			}
		}
		
		/* Because of a dumb sorting error, Newton and Noble counties' IDs are switched in County Profiles.
		 *  ID 		CP		EPA
		 *	56		Noble	Newton
		 *	57		Newton	Noble
		 */
		/*
		$state_id = 14; // Indiana
		App::uses('Location', 'Model');
		$Location = new Location();
		$county_id = is_numeric($county) ? $county : $Location->getCountyID($county, $state_id);
		if (! is_numeric($county_id)) {
			return;
		}
		if ($county_id == 56) {
			$county_id = 57;
		} elseif ($county_id == 57) {
			$county_id = 56;	
		}
		$result = "http://bsu.edu/mcobwin/county_profiles/index.php?county=$county_id";
		*/
		$result = 'http://profiles.cberdata.org/';
		if ($cache) {
			Cache::write($cache_key, $result);
		}
		return $result;
	}
	
	public function county($county) {
		// Gather data on selected county
		App::uses('Location', 'Model');
		$Location = new Location();
		$state_id = 14; // Indiana
		$county_id = is_numeric($county) ? $county : $Location->getCountyId($county, $state_id);
		$Location->setSource('counties');
		$county_info = $Location->find('first', array(
			'conditions' => array('id' => $county_id),
			'fields' => array('county_seat', 'founded', 'square_miles', 'description', 'name', 'simplified'),
			'contain' => false
		));
		$county_name = $county_info['Location']['name'];
		$county_simplified_name = $county_info['Location']['simplified'];
		
		// Get parent categories (main categories, with 'index' and 'grade' as their children)
		App::uses('DataCategory', 'Model');
		$DataCategory = new DataCategory();
		$parent_categories = $DataCategory->getParentCategories();
		
		// Gather data
		App::uses('Datum', 'Model');
		$Datum = new Datum();
		$report = array();
		foreach ($parent_categories as $pc_id => $pc_name) {
			$children_categories = $DataCategory->find('list', array('conditions' => array('parent_id' => $pc_id)));
			$data_results = $Datum->find('all', array(
				'conditions' => array(
					'category_id' => array_keys($children_categories),
					'county_id' => $county_id
				),
				'fields' => array('category_id', 'value'),
				'contain' => false
			));
			$index_key = array_search('Index', $children_categories);
			$grade_key = array_search('Grade', $children_categories);
			foreach ($data_results as $result) {
				$measurement = $children_categories[$result['Datum']['category_id']];
				$value = $result['Datum']['value'];
				if (is_numeric($value)) {
					$value = round($value, 1);	
				}
				$report[$pc_name][$measurement] = $value;
			}
		}
		
		$this->set(array(
			'county_id' => $county_id,
			'county_name' => $county_name,
			'county_simplified_name' => $county_simplified_name,
			'county_info' => $county_info,
			'parent_categories' => $parent_categories,
			'report' => $report,
			'profiles_url' => 'http://profiles.cberdata.org' //$this->__getCountyProfilesLink($county_id)
		));
		
		if (isset($_GET['print'])) {
			$this->layout = 'print';
			$this->render('/Reports/print/county');
		} else {
			$this->layout = 'ajax';
		}
	}
}
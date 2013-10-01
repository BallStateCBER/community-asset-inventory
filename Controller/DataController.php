<?php
class DataController extends AppController {
	var $name = 'Data';
	var $components = array('RequestHandler');
	var $helpers = array('Text');
	var $uses = array('Datum');	
	
	public function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->allow('*');
	}
	
	public function beforeRender() {
		parent::beforeRender();
	}
	
	public function import() {
		$file_path = "C:\\Users\\gtwatson\\Documents\\Quality of Place\\00 CAIR scorecard 2012 (errors corrected).txt";
		$fh = fopen($file_path, 'r');
		$header_row_count = 1;
		$result_messages = array();
		if ($fh) {
			// Process each row of the data file
			while (! feof($fh) && $row = fgets($fh)) {
				// Skip header rows
				if ($header_row_count > 0) {
					$header_row_count--;
					continue;
				}
				
				// Skip blank rows
				if (trim($row) == '') {
					continue;
				}
				
				$fields = explode("\t", $row);
				$fips = trim($fields[0]);
				if (! $county_id = $this->requestAction("/locations/getCountyIdFromFips/$fips")) {
					$this->flash('No county found corresponding to FIPS code "'.$fips.'"', 'error');
					continue;
				}
				$county_name = trim($fields[1]);
				$data_columns = array(	// col num => (name, category ID)
					 2 => array('People: Grade', 2),
					 3 => array('People: Index', 3),
					 
					 5 => array('Human Capital - Education: Grade', 5),
					 6 => array('Human Capital - Education: Index', 6),
					 
					 8 => array('Government Impact & Economy: Grade', 8),
					 9 => array('Government Impact & Economy: Index', 9),
					 
					 11 => array('Changeable Amenities (Public): Index', 11),
					 
					 12 => array('Relatively Static Amenities (Public): Index', 13),
					 
					 14 => array('Recreation (Private): Grade', 15),
					 15 => array('Recreation (Private): Index', 16),
					 
					 17 => array('Human Capital - Health: Grade', 18),
					 18 => array('Human Capital - Health: Index', 19)
				);
				foreach ($data_columns as $col_num => $category) {
					$category_name = $category[0];
					$category_id = $category[1];
					// To do: Confirm category_id is valid
					$value = trim($fields[$col_num]);
					$result_code = $this->__safeInsert(compact('category_id', 'county_id', 'value'));
					list($result_message, $result_class) = $this->__flashSafeInsertResultMsg($county_name, $category_name, $value, $result_code);
					$result_messages[] = '<p class="'.$result_class.'_message">'.$result_message.'</p>';
				}
			}
		} else {
			$this->flash('Can\'t open import file.', 'error');
		}
		$this->set('message', '<h1>Results of import:</h1>'.implode('', $result_messages));
		$this->render('/pages/message');
	}
	
	public function __safeInsert($params) {
		extract($params);
		$safety = false;
		$overwrite_data = true;

		if ($value === "") 					return 8;	// Value is blank, skipping
		//if (! is_numeric($value)) 			return 1.1;	// Value invalid
		if (! is_numeric($category_id))		return 1.2;	// Category ID invalid
		if (! is_numeric($county_id))		return 1.5; // Community ID invalid

		$redundancy_check = $this->Datum->find('all', array(
			'conditions' => compact('category_id', 'county_id'),
			'fields' => array('id', 'value'),
			'contain' => false
		));

		if ($redundancy_check === false) return 2; // Error checking for redundancy

		$redundancy_check_count = count($redundancy_check);
		if ($redundancy_check_count > 0) {
			$duplicate_data = $redundancy_check_count > 1; // Multiple entries for this datum, for some reason

			// If a duplicate data point needs to be removed from the database,
			// we'll perform the delete-reinsert function to correct that
			if ($duplicate_data && $overwrite_data) {
				$overwriting = true;

			// If a single data point is in the database that matches what we were going to insert,
			// note that this is redundant and take no action.
			} elseif ($value == $redundancy_check[0]['Datum']['value']) {
				return 3;

			// If a single data point is different from what we want to import AND overwriting is enabled,
			// then update that data point
			} elseif ($overwrite_data) {
				$overwriting = true;

			// If a single data point is different from what we want to import AND overwriting is disabled,
			// note that an overwrite is recommended and take no action
			} else {
				return 6;
			}
		} else {
			$overwriting = false;
		}

		if ($safety) return 5;

		$this->Datum->create();
		// One or more entries exist in the database for this datum
		if ($overwriting) {
			// Multiple entries exist in the database for this combination of
			// location, date, and category. That's weird. Delete all of them and
			// insert a new one so all is right with the world.
			if ($duplicate_data) {
				foreach ($redundancy_check as $row) {
					$this->Datum->delete($row['Datum']['id']);
				}
				$insert_result = $this->Datum->save(array('Datum' => compact('county_id', 'category_id', 'value')));
				if (! $insert_result) return 4;

			// One entry exists that needs to be overwritten
			} else {
				$this->Datum->id = $redundancy_check[0]['Datum']['id'];
				$update_result = $this->Datum->save(array('Datum' => compact('value')));
				if (! $update_result) return 4;
			}

		// A new entry needs to be added to the database
		} else {
			$insert_result = $this->Datum->save(array('Datum' => compact('county_id', 'category_id', 'value')));
			if (! $insert_result) return 4;
		}

		// Either 'Value revised' or 'Imported'
		return $overwriting ? 7 : 0;
	}
	
	// Returns array($message, $message_class)
	public function __flashSafeInsertResultMsg($county_name, $category_name, $value, $result_code) {
		$class = 'notification';
		switch($result_code) {
			case 0:
				$msg = 'Success.';
				$class = 'success';
				break;
			case 1.1:
				$msg = '$value is non-numeric.';
				$class = 'error';
				break;
			case 1.2:
				$msg = '$category_id is non-numeric.';
				$class = 'error';
				break;
			case 1.3:
				$msg = '$survey_date is non-numeric.';
				$class = 'error';
				break;
			case 1.5:
				$msg = '$community_id is non-numeric.';
				$class = 'error';
				break;
			case 1.6:
				$msg = '$source_id is non-numeric.';
				$class = 'error';
				break;
			case 2:
				$msg = 'Error checking for redundancy. Details: '.mysql_error();
				$class = 'error';
				break;
			case 3:
				$msg = 'Data insert would be redundant.';
				break;
			case 4:
				$msg = 'Error inserting data. Details: '.mysql_error();
				$class = 'error';
				break;
			case 5:
				$msg = 'Safety on.';
				break;
			case 6:
				$msg = 'Stored value is different from this value. Overwrite suggested.';
				break;
			case 7:
				$msg = 'Datum imported. Value revised.';
				$class = 'success';
				break;
			case 8:
				$msg = 'Value is blank. Skipping.';
				break;
			default:
				$msg = 'Unknown error.';
				$class = 'error';
		}
		return array("$county_name<br />$category_name &rarr; $value<br />$msg", $class);
	}
}
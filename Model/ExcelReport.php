<?php
App::uses('AppModel', 'Model');
class ExcelReport extends AppModel {
	var $useTable = false;
	var $author = 'Center for Business and Economic Research, Ball State University';
	var $footnote = '';
	var $objPHPExcel;
	var $current_row = 1; 	//Row iterator (first row is 1, not 0)
	var $mockup; 			//[col][row] => value array outputted during debugging
	var $table = array();
	var $hasGrades = false;
	
	/****** ****** Generation of PHPExcel object ****** ******/
	
	
	// Sets up $this->objPHPExcel so that it's ready for output
	function getOutput($type, $var_name, $data, $locations, $sources) {		
		// Start up
		PHPExcel_Cell::setValueBinder(new PHPExcel_Cell_AdvancedValueBinder());
		$this->objPHPExcel = new PHPExcel();
		$this->objPHPExcel->setActiveSheetIndex(0);
		
		// Determine if a 'grade' column is necessary
		foreach ($data as $county_id => $county_data) {
			foreach ($county_data as $datum_name => $value) {
				if ($datum_name == 'grade') {
					$this->hasGrades = true;
					break 2;	
				}
			}
		}
		
		// Populate the spreadsheet
		$this->__setMetaData(array(
			'author' => $this->author,
			'title' => $var_name,
			'description' => ''
		));
		$this->objPHPExcel->getDefaultStyle()->getFont()->setName('Arial');
		$this->objPHPExcel->getDefaultStyle()->getFont()->setSize(11);
		$this->__setTitle($var_name);
		$this->__setSources($sources);
		$this->__setColumnAndRowLabels($locations);
		$this->__setValues($data);
		
		// Reduce the width of the first column 
		//   (which contains only the title and sources and overflow over the unoccupied cells to the right)
		$this->objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(1.5);
		
		// Automatically adjust the width of all columns AFTER the first 
		$last_col = 2;
		for ($c = 1; $c <= $last_col; $c++) {
			$col_letter = $this->__convertNumToLetter($c);
			$this->objPHPExcel->getActiveSheet()->getColumnDimension($col_letter)->setAutoSize(true);
		}
	}
	
	function __setTitle($var_name) {
		$this->__setCell(0, 1, $var_name);
		$this->__setStylesFromArray('A1', 'A1', array(
			'font' => array('bold' => true, 'size' => 24)
		));
		$this->current_row += 1;	
	}
	
	function __setMetaData($metadata) {
		// Metadata
		$this->objPHPExcel->getProperties()
			->setCreator($metadata['author'])
			->setLastModifiedBy($metadata['author'])
			->setTitle($metadata['title'])
			->setSubject($metadata['title'])
			->setDescription($metadata['description']);
	}
	
	function __setSources($sources) {
		foreach ($sources as $source) {
			$this->__setCell(0, $this->current_row, "Source: {$source['name']}");
			$this->current_row++;
		}
		
		// Blank row after sources
		$this->current_row++;
	}
	
	// Note that column headers and values start on the SECOND column
	function __setColumnAndRowLabels($locations) {		
		// Write column labels 
		$col = 1;
		if ($this->hasGrades) {
			$col_labels = array('County', 'Grade', 'Points');
		} else {
			$col_labels = array('County', 'Points');
		}
		foreach ($col_labels as $label) {
			$this->__setCell($col, $this->current_row, $label);
			$col++;	
		}
		
		// Repeat column labels at top of every printed page
		$this->objPHPExcel->getActiveSheet()->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd($this->current_row, $this->current_row);
		
		// Style column labels
		$first_cell = 'B'.$this->current_row;
		$last_cell = $this->__convertNumToLetter(3).$this->current_row;
		$this->__setStylesFromArray($first_cell, $last_cell, array(
			'font' => array(
				'bold' => true,
				'size' => 12
			),
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER
			),
			'borders' => array(
				'bottom' => array(
					'style' => PHPExcel_Style_Border::BORDER_THIN
				)
			),
			'fill' => array(
				'type' => PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR,
				'rotation' => 90,
				'startcolor' => array(
					'argb' => 'FFFFFFFF'
				),
				'endcolor' => array(
					'argb' => 'FFDFDFDF'
				)
			)
		));
		
		// Enable autofilter on column headers
		$this->objPHPExcel->getActiveSheet()->setAutoFilter("$first_cell:$last_cell");
		
		$this->current_row++;
		
		// Write row labels
		$row_iter = $this->current_row;
		foreach ($locations as $loc_id => $location) {
			$row_label = trim($location).' County, IN';
			$this->__setCell(1, $row_iter++, $row_label);
		}
		
		// Style row labels
		$first_cell = 'B'.$this->current_row;
		$last_cell = 'B'.($this->current_row + count($locations) - 1);
		//echo "$first_cell-$last_cell";
		$this->__setStylesFromArray($first_cell, $last_cell, array(
			'font' => array(
				'bold' => true,
				'size' => 12
			),
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT
			),
			'borders' => array(
				'right' => array(
					'style' => PHPExcel_Style_Border::BORDER_THIN
				)
			)
		));
	}
	
	function __setStylesFromArray($first_cell, $last_cell, $styles) {
		$this->objPHPExcel->getActiveSheet()->getStyle("$first_cell:$last_cell")->applyFromArray($styles);
	}
	
	// Used in converting coordinates (0,0) to Excel cell identifiers (A1)
	// Currently does not work past the 26th column
	function __convertNumToLetter($number, $capitalize = true) {
		$letters = 'abcdefghijklmnopqrstuvwxyz';
		$letter = substr($letters, $number, 1);
		return $capitalize ? strtoupper($letter) : $letter;
	}
		
	/* Expects $data to be populated like this:
	 * 		$data[$county_id]['grade']
	 * 		$data[$county_id]['index']
	 */
	function __setValues($data) {
		// Freeze the location column when scrolling
		$this->objPHPExcel->getActiveSheet()->freezePane('C1');
		
		// Set values
		$row_num = 0;
		foreach ($data as $loc_id => $loc_data) {
			// Adjust row downward
			$row_num_adjusted = ($row_num + $this->current_row);
			
			// Round index value
			$index_val = round($loc_data['index'], 2);
			
			// Write values
			if ($this->hasGrades) {
				$this->__setCell(2, $row_num_adjusted, $loc_data['grade']);
				$this->__setCell(3, $row_num_adjusted, $index_val);
			} else {
				$this->__setCell(2, $row_num_adjusted, $index_val);
			}
			
			$row_num++;
		}
		
		// Style entire block of values
		$first_cell = 'C'.$this->current_row;
		$last_cell = $this->__convertNumToLetter(3).($this->current_row + count($data) - 1);
		$this->__setStylesFromArray($first_cell, $last_cell, array(
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT
			)
		));
		$this->current_row += count($data);
	}
	
	// $col_num and $row_num are zero-indexed and relative to the entire spreadsheet
	function __applyNumberFormatToCell($col_num, $row_num, $format) {
		$excel_cell = $this->__convertNumToLetter($col_num).($row_num);
		$this->objPHPExcel->getActiveSheet()->getStyle($excel_cell)->getNumberFormat()->setFormatCode($format);
	}

	function __formatValue($value, $mode = 'number', $precision = 0) {
		if ($value == '') {
			return $value;
		}
		switch ($mode) {
			case 'year':
				return substr($value, 0, 4);
			case 'number':
				return ($value < 1 ? '0.' : '').number_format($value, $precision);
			case 'percent':
				return number_format($value, $precision).'%'; //(($value < 1 && $value != 0) ? '0.' : '').
			case 'currency':
				return '$'.($value < 1 ? '0.' : '').number_format($value, $precision);
			case 'string':
			default:
				return $value;
		}
	}
	
	// Adds a footnote to the bottom of the spreadsheet
	// If a newline is in the footnote, splits up footnote into multiple rows
	function __setFootnote() {
		if ($this->footnote) {
			$this->current_row++; // Blank line before footnote
			$footnote_lines = explode("\n", $this->footnote);
			foreach ($footnote_lines as $footnote_line) {
				$this->__setCell(0, $this->current_row, $footnote_line);
				$coordinates = $this->__getExcelCoordinates(0, $this->current_row);
				$this->objPHPExcel->getActiveSheet()->getStyle($coordinates)->getAlignment()->setWrapText(false);
				$this->current_row++;
			}
		}
	}
	
	function __setCell($col, $row, $value) {
		if ($value !== null && $value !== false) {
			$this->objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $value);
		}
		$this->mockup[$col][$row] = $value;
	}
	
	function __getExcelCoordinates($col, $row) {
		return $this->__convertNumToLetter($col).($row);	
	}
	
	
	/****** ****** Individual topics below ****** ******/
	
	/* Used in Brownfields site
	// Variation: Array of dates instead of a year
	function population($county = 1) {
		// Gather data
		$category_id = array_pop($this->data_categories);
		foreach ($this->locations as $loc_key => $location) {
			list($this->dates[$loc_key], $this->values[$loc_key]) = $this->Datum->getSeries($category_id, $location[0], $location[1]);
		}
		
		// Finalize
		$this->mergeDates();
		$this->reverseTimeline();
		$this->columns = array_merge(array('Year'), $this->getLocationNames());
		$this->title = 'Population';
		$this->row_labels = $this->dates;
		$this->first_col_format = 'year';
		$this->data_format = 'number';
		$this->data_precision = 0;
	}
	*/
}
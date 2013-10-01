<?php
if ($type == 'csv') {
	function outputCsvLine(&$vals, $key, $filehandler) {
		fputcsv($filehandler, $vals, ',', '"');
	}
	function outputCsv($data) {
		$outstream = fopen("php://output", 'w');
		array_walk($data, 'outputCsvLine', $outstream);
		fclose($outstream);
	}
	
	if (isset($_GET['debug'])) {
		echo "Filename: ".$filename.".csv<pre>";
	}
	
	$output = array(
		array($category_name)
	);
	
	// If sources are provided, format and output them
	if (isset($sources) && ! empty($sources)) {
		foreach ($sources as $source) {
			$source = str_replace(array("\n", "\r"), ' ', $source['name']);
			$output[] = array('Source: '.$source);
		}
	}
	
	// Blank line
	$output[] = array();
	
	// Column headers
	if ($has_grades) {
		$output[] = array('', 'Grade', 'Points');
	} else {
		$output[] = array('', 'Points');	
	}
	
	// Row titles and values
	foreach ($data as $loc_id => $l_data) {
		$community_name = trim($county_names[$loc_id]).' County, IN';
		$row = array($community_name);
		if ($has_grades) {
			$row[] = $data[$loc_id]['grade'];
		}
		$row[] = $data[$loc_id]['index'];
		$output[] = $row;
	}
	
	outputCsv($output);
	
	if (isset($_GET['debug'])) {
		echo '</pre>';
	}
} elseif ($type == 'excel2007' || $type == 'excel5') {
	if (isset($_GET['debug'])) {
		echo '<pre>'.print_r($objPHPExcel, true).'</pre>';
	} else {
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, ucwords($type));
		$objWriter->save('php://output');
	}
}
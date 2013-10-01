<?php
$inline_color_assignments = array();
foreach ($counties as $county) {
	// Period in IDs (as for st._joseph) need to be escaped for jQuery to be able to select those elements
	$county_name = str_replace('.', '\\\.', $county_simplified_names[$county['Location']['id']]);
	
	if (isset($grades[$county['Location']['id']])) {
		$grade = strtolower($grades[$county['Location']['id']]);
		$grade = str_replace(array('-', '+'), '', $grade);
		$inline_color_assignments[] = "$('#in_map_$county_name').addClass('grade_$grade');";
	} else {
		$index = $indices[$county['Location']['id']];
		if ($index >= 115) {
			$index_grade = 115;
		} elseif ($index >= 105) {
			$index_grade = 105;
		} elseif ($index >= 95) {
			$index_grade = 95;
		} elseif ($index >= 85) {
			$index_grade = 85;
		} else {
			$index_grade = 70;
		}
		$inline_color_assignments[] = "$('#in_map_$county_name').addClass('index_$index_grade');";
	}
}

$this->Js->buffer("
	var assignColors = function() {
		".implode("\n", $inline_color_assignments)."
	};
	if ($('html').first().hasClass('no-inlinesvg')) {
		selectCategoryMode('table');
		$('.map_wrapper').first().html($('svg_not_supported'));
		$('svg_not_supported').show();
	} else if ($('html').first().hasClass('inlinesvg')) {
		setupMap();
		assignColors();
	}
");
?>


<div id="category_report">
	<aside>
		<h2>About <?php echo $category_name; ?></h2>
		<p><?php echo $category_description; ?></p>
		
		<h2>Sources</h2>
		<ul class="sources">
			<?php foreach ($sources as $source): ?>
				<li>
					<?php echo $source['name']; ?>
				</li>
			<?php endforeach; ?>
		</ul>
		
		<div id="report_view_controls">
			<a href="#" id="show_map" class="selected controls">
				<img src="/img/icons/map.png" />
				<span>Map</span>
			</a>
			<a href="#" id="show_table" class="controls">
				<img src="/img/icons/table.png" />
				<span>Table</span>
			</a>
			<?php echo $this->Html->link(
				'<img src="/img/icons/printer.png" /> <span>Print</span>',
				array('controller' => 'reports', 'action' => 'category', Inflector::slug($category_name), '?' => 'print'),
				array('escape' => false, 'target' => '_blank', 'class' => 'controls')
			); ?>
			<br />
			<a href="#" id="show_download_options" class="controls">
				<img src="/img/icons/drive-download.png" />
				<span>Download Options</span>
			</a>
			<br />
			<?php $this->Js->buffer("
				$('#show_download_options').click(function(event) {
					event.preventDefault();
					$('#download_options_wrapper').slideToggle(500);
				});
			"); ?>
			<div id="download_options_wrapper" style="display: none;">
				<div>
					<strong>Download this data set:</strong>
					<ul>
						<?php
							$url_params = array(
								'controller' => 'reports', 
								'action' => 'download',
								'var_id' => $parent_category_id
							); 
							$download_options = array(
								array(
									'displayed_type' => 'CSV',
									'icon' => 'icons/document-excel-csv.png',
									'type_param' => 'csv'
								),
								array(
									'displayed_type' => 'Excel 5.0',
									'icon' => 'icons/document-excel-table.png',
									'type_param' => 'excel5'
								),
								array(
									'displayed_type' => 'Excel 2007',
									'icon' => 'icons/document-excel-table.png',
									'type_param' => 'excel2007'
								)
							);
						?>
						<?php foreach ($download_options as $dl_opt): ?>
							<li>
								<?php echo $this->Html->link(
									$this->Html->image($dl_opt['icon']).' <span>'.$dl_opt['displayed_type'].'</span>',
									array_merge(array('type' => $dl_opt['type_param']), $url_params),
									array('escape' => false)
								); ?>
							</li>
						<?php endforeach; ?>
					</ul>
				</div>
			</div>
		</div>
		<?php $this->Js->buffer("
			$('#show_map').click(function (event) {
				event.preventDefault();
				selectCategoryMode('map');
			});
			$('#show_table').click(function (event) {
				event.preventDefault();
				selectCategoryMode('table');
			});
		"); ?>
	</aside>
	<h1><?php echo $category_name; ?></h1>
	
	<div id="svg_not_supported" style="display: none;">
		<?php echo $this->element('svg_not_supported'); ?>
	</div>
	
	<div class="map_wrapper">
		<p>Click on a county to view its full report profile.</p>
		<?php echo $this->element('indiana', array('width' => 330, 'height' => 500, 'classes' => array('interactive'))); ?>
		<?php echo $this->element('legend'); ?>
	</div>
	<div class="table_wrapper" style="display: none;">
		<?php echo $this->element('category_table'); ?>
	</div>
</div>

<div id="county_tooltips">
	<?php foreach ($counties as $county): ?>
		<div id="in_map_<?php echo $county['Location']['simplified']; ?>_details" style="display: none;">
			<h2>
				<?php echo $county['Location']['name']; ?> County
			</h2>
			<p>
				Points: <?php echo round($indices[$county['Location']['id']], 1); ?>
				<?php if (isset($grades[$county['Location']['id']])): ?>
					<br />
					Grade: <?php echo $grades[$county['Location']['id']]; ?>
				<?php endif; ?>
			</p>
		</div>
	<?php endforeach; ?>
</div>
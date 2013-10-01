<?php
$color_assignments = array();
$inline_color_assignments = array();
foreach ($colors as $county_id => $color) {
	$county_name = $county_simplified_names[$county_id];
	$color_assignments[] = "map.contentDocument.getElementById('$county_name').setAttributeNS(null, 'fill', '$color');";
	$inline_color_assignments[] = "$('$county_name').setAttributeNS(null, 'fill', '$color');";
}
$this->Js->buffer("	
	var assignColors = function() {
		".implode("\n", $inline_color_assignments)."
	};
	if ($('html').first().hasClass('mod-no-inlinesvg')) {
		$('.map_wrapper').first().html($('svg_not_supported'));
		$('svg_not_supported').show();
	} else if ($('html').first().hasClass('mod-inlinesvg')) {
		assignColors();
	}
	window.print();
");
?>

<div id="category_report">
	<h1><?php echo $category_name; ?></h1>
	<p><?php echo $category_description; ?></p>
		
	<h2>Sources</h2>
	<ul class="sources">
		<?php foreach ($sources as $source): ?>
			<li>
				<?php echo $source['name']; ?>
			</li>
		<?php endforeach; ?>
	</ul>
	
	<div id="svg_not_supported" style="display: none;">
		<?php echo $this->element('svg_not_supported'); ?>
	</div>
	
	<div class="map_wrapper">
		<?php echo $this->element('indiana', array('width' => 330, 'height' => 500, 'classes' => array('interactive'))); ?>
		<?php echo $this->element('legend'); ?>
	</div>
	<div class="table_wrapper">
		<?php echo $this->element('category_table'); ?>
	</div>
</div>
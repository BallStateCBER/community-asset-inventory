<?php
	// Wrap in the Data Center default layout 
	$this->extend('DataCenter.default');

	// Tell Javascript what the counties and categories are 
	// so that the processHash() function can interpret the hash fragment correctly
	$js_category_definitions = array(); 
	foreach ($parent_categories as $pc_id => $pc_name) {
		$js_category_definitions[] = "'".Inflector::slug($pc_name)."'";
	}
	$js_county_definitions = array();
	foreach ($counties as $county) {
		$js_county_definitions[] = "'{$county['Location']['simplified']}'";
	}
	$this->Js->buffer("
		var categories = [".implode(', ', $js_category_definitions)."];
		var counties = [".implode(', ', $js_county_definitions)."];
	");
	$this->Js->buffer("processHash(categories, counties);");

	// Load CSS files at the top of the page 
	$this->Html->css('/DataCenter/css/jquery.qtip.min.css', null, array('inline' => false));
	
	// Load JS files at the bottom of the page
	$this->Html->script('/DataCenter/js/jquery.svg.js', array('inline' => false));
	$this->Html->script('/DataCenter/js/jquery.svgdom.js', array('inline' => false));
	$this->Html->script('/DataCenter/js/jquery.qtip.js', array('inline' => false));
	
	// Define the subsite title block
	$this->start('subsite_title');
		echo '<h1 id="subsite_title" class="max_width">';
		echo $this->Html->link(
			'<img src="/img/CommtyAsset.png" alt="Indiana Community Asset Inventory and Rankings" />',
			array('controller' => 'pages', 'action' => 'home'),
			array('escape' => false)
		);
		echo '</h1>';
	$this->end();
	
	// Include the sidebar
	$this->assign('sidebar', $this->element('sidebar'));

	// Load Modernizr
	$this->Html->script('/data_center/js/modernizr-2.5.3.min.js', array('inline' => false));
	
	echo '<div id="content">'.$this->fetch('content').'</div>';
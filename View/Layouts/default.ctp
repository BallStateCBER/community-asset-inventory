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
	$this->Html->script('script', array('inline' => false));

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

	// Define the 'about' section of the footer
	$this->start('footer_about');

    $this->start('flash_messages');
	    echo $this->element('flash_messages', array(), array('plugin' => 'DataCenter'));
    $this->end();
?>
	<h3>
		About the Community Asset Inventory and Rankings
	</h3>
	<p>
		This site was created through a partnership between <a href="http://www.bsu.edu/bbc">Ball State's
		Building Better Communities</a> and the Center for Business and Economic Research.
	</p>
	<p>
		The <a href="http://www.cberdata.org/">CBER Data Center</a> is a product of the Center for Business
		and Economic Research at Ball State University.  CBER's mission is to conduct relevant and timely
		public policy research on a wide range of economic issues affecting the state and nation.
		<a href="http://www.bsu.edu/cber">Learn more</a>.
	</p>
<?php
	$this->end();

	// Load Modernizr
	$this->Html->script('/data_center/js/modernizr-2.5.3.min.js', array('inline' => false));

	echo '<div id="content">'.$this->fetch('content').'</div>';
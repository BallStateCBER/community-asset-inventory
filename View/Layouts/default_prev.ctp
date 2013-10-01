<?php 
	header('Content-type: text/html; charset=UTF-8');

	/* Tell Javascript what the counties and categories are 
	 * so that the processHash() function can interpret the hash fragment correctly */
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
?>
<!DOCTYPE html>
<html lang="en" class="">
	<head>
		<title>
			<?php if (isset($title_for_layout) && $title_for_layout): ?>
				<?php echo $title_for_layout; ?> -
			<?php endif; ?>
			Indiana Community Asset Inventory and Rankings
		</title>
		<meta charset="utf-8" />
		<meta name="title" content="Indiana Community Asset Inventory and Rankings" />
		<meta name="description" content="" />
		<meta name="author" content="Center for Business and Economic Research, Ball State University" />
		<meta name="language" content="en" />
		<link rel="stylesheet" type="text/css" href="/css/main.css" />
		<link rel="stylesheet" type="text/css" href="/css/tooltips.css" />
		<link rel="icon" type="image/png" href="/img/icons/chart.png" />
		<link href='http://fonts.googleapis.com/css?family=Cabin:400,700,400italic,700italic' rel='stylesheet' type='text/css' />
		<?php
			//<script src="/js/svgweb/svg.js" data-path="/js/svgweb"></script>
			echo $this->Html->script(array(
				'https://ajax.googleapis.com/ajax/libs/prototype/1.7.0.0/prototype.js',
				'https://ajax.googleapis.com/ajax/libs/scriptaculous/1.9.0/scriptaculous.js',
				'tooltips.js',
				'main.js',
				'modernizr.js'
			));
			echo $scripts_for_layout;
			/*
			<!--[if lt IE 9]>
			<script src="//html5shiv.googlecode.com/svn/trunk/html5.js"></script>
			<![endif]-->
			*/
		?>
	</head>
	<body>
		<?php echo $this->element('flash_messages'); ?>
		<div id="above_footer">
			<header id="header_top">
				<div class="inner_wrapper">
					<h1>
						<a href="http://bsu.edu/cber">
							Center for Business and Economic Research
						</a>
						-
						<a href="http://bsu.edu">
							Ball State University
						</a>
					</h1>
					<?php /*
					<div id="search">
						<form>
							<input type="text" name="search" value="Search CBERData.org" />
							<input type="submit" value="Go" />
						</form>
					</div>
					
					<div id="login_out">
						<a href="#">Login / Logout</a>
					</div>
					*/ ?>
					<br class="clear" />
					<a href="http://cberdata.org/">
						<img src="/img/DataCenter.png" id="data_center_logo" alt="Ball State CBER Data Center" title="Ball State CBER Data Center" />
					</a>
					
					<nav>
						<a href="http://cms.bsu.edu/Academics/CentersandInstitutes/BBR/CurrentStudiesandPublications.aspx">
							Projects and<br />Publications
						</a>
						<a href="http://www.bsu.edu/mcobwin/ibb/">
							Economic<br />Indicators
						</a>
						<a href="http://cberdata.org/commentaries">
							Weekly<br />Commentary
						</a>
						<a href="http://tax-comparison.cberdata.org/">
							Illinois to Indiana<br />Tax Savings Calculator
						</a>
						<a href="http://bsu.edu/mcobwin/county_profiles/index.php">
							County<br />Profiles
						</a>
						<?php echo $this->Html->link('Community<br />Asset Inventory',
							array('controller' => 'pages', 'action' => 'home'),
							array('escape' => false, 'class' => 'selected')
						); ?>
						<a href="http://brownfield.cberdata.org/">
							Brownfield Grant<br />Writers' Tool
						</a>
					</nav>
					<br class="clear" />
				</div>
			</header>
			<h1 id="subsite_title" class="inner_wrapper">
				<?php echo $this->Html->link(
					'<img src="/img/CommtyAsset.png" alt="Indiana Community Asset Inventory and Rankings" />',
					array('controller' => 'pages', 'action' => 'home'),
					array('escape' => false)
				); ?>
			</h1>
			<div id="content_wrapper" class="inner_wrapper <?php if (isset($content_wrapper_class)) echo $content_wrapper_class; ?>">
				<div id="content_inner_wrapper">	
					<div id="two_col_wrapper">
						<div id="menu_col_stretcher" class="col_stretcher"></div>
						<div id="content_col_stretcher" class="col_stretcher"></div>
						<div id="menu" class="col">
							<h3>
								<a href="#home" id="home_link">Home</a>
								<?php $this->Js->buffer("
									Event.observe('home_link', 'click', function(event) {
										showPage('home');
									});
								"); ?>
							</h3>
							<h3>All Counties by Category</h3>
							<ul id="categories" class="unstyled">
								<?php foreach ($parent_categories as $pc_id => $pc_name): ?>
									<?php $slug = Inflector::slug($pc_name); ?>
									<li>
										<a href="#<?php echo $slug; ?>" id="showmap_<?php echo $slug; ?>">
											<?php echo $pc_name; ?>
										</a>
										<?php $this->Js->buffer("
											Event.observe('showmap_$slug', 'click', function(event) {
												showMap('".Inflector::slug($pc_name)."');
											});
										"); ?>
									</li>
								<?php endforeach; ?>
							</ul>
							<h3 id="all_categories_header">
								All Categories by County
							</h3>
							<?php $this->Js->buffer("
								Event.observe('select_county', 'change', function(event) {
									if ($('select_county').value) {
										showFullReport();
									}
								});
								Event.observe('select_county_button', 'click', function(event) {
									if ($('select_county').value) {
										showFullReport();
									} else {
										alert('Please select a county from the drop-down menu');
									}
								});
							"); ?>
							<select id="select_county">
								<option value="">Select a county</option>
								<option value=""></option>
								<?php foreach ($counties as $county): ?>
									<option value="<?php echo $county['Location']['simplified']; ?>">
										<?php echo $county['Location']['name']; ?> County
									</option>
								<?php endforeach; ?>
							</select>
							<button id="select_county_button">Go</button>
							<h3>
								<a href="#sources" id="sources_link">Data Sources <br />and Methodology</a>
								<?php $this->Js->buffer("
									Event.observe('sources_link', 'click', function(event) {
										showPage('sources');
									});
								"); ?>
							</h3>
							
							<h3>
								Download
							</h3>
							<ul class="unstyled">
								<li>
									<a href="/files/CAIR-Report2012.pdf">
										Print Report 2012 (PDF)
									</a>
								</li>
								<li>
									<a href="/files/CAIR-RawData2012.xls">
										Raw Data Spreadsheet 2012 (Excel)
									</a>
								</li>
							</ul>
							
							<h3>
								<a href="#faq" id="faq_link">Frequently Asked Questions</a>
								<?php $this->Js->buffer("
									Event.observe('faq_link', 'click', function(event) {
										showPage('faq');
									});
								"); ?>
							</h3>
							
							<h3>
								<a href="#credits" id="credits_link">Credits</a>
								<?php $this->Js->buffer("
									Event.observe('credits_link', 'click', function(event) {
										showPage('credits');
									});
								"); ?>
							</h3>
							
							<ul id="extra_links">
								<li>
									<a href="http://cberdata.org">Ball State CBER Data Center</a>
								</li>
								<li>
									<a href="http://bsu.edu/cber">Center for Business and Economic Research</a>
								</li>
							</ul>
						</div>
						<div id="content" class="col">
							<div id="load_selection_wrapper">
								<div id="load_selection">
									<?php echo $content_for_layout ?>
								</div>
							</div>
							<br class="clear" />
						</div>
					</div>
				</div>
				<br class="clear" />
			</div>
		</div>
		<footer id="footer">
			<div class="inner_wrapper">
				<div id="cberlogo_copyright">
					<a href="http://www.bsu.edu/cber">
						<img src="/img/BallStateCBER-red.png" />
					</a>
					<p>
						&copy; Center for Business and Economic Research, Ball State University
					</p>
				</div>
				<section>
					<section>
						<h3>About Ball State CBER Data Center</h3>
						<p>
							Ball State CBER Data Center is one-stop shop for economic data including demographics, education, health, and social
							capital. Our easy-to-use, visual web tools offer data collection and analysis for grant writers, economic developers, policy
							makers, and the general public.
						</p>
						<p>
							Ball State CBER Data Center (<a href="http://www.cberdata.org">www.cberdata.org</a>) is a product of the Center for Business and Economic Research at Ball State
							University. CBER's mission is to conduct relevant and timely public policy research on a wide range of economic issues
							affecting the state and nation. <a href="http://www.bsu.edu/cber">Learn more</a>.
						</p>
					</section>
					<section>
						<h3>Center for Business and Economic Research</h3>
						<address>
							Ball State University &bull; Whitinger Business Building, room 149<br />
							2000 W. University Ave.<br />
							Muncie, IN 47306-0360
						</address>
						<dl>
							<dt>Phone:</dt>
							<dd>765-285-5926</dd>
							
							<dt>Email:</dt>
							<dd><a href="mailto:cber@bsu.edu">cber@bsu.edu</a></dd>
							
							<dt>Website:</dt>
							<dd><a href="http://www.bsu.edu/cber">www.bsu.edu/cber</a></dd>
							
							<dt>Facebook:</dt>
							<dd><a href="http://www.facebook.com/BallStateCBER">www.facebook.com/BallStateCBER</a></dd>
							
							<dt>Twitter:</dt>
							<dd><a href="http://www.twitter.com/BallStateCBER">www.twitter.com/BallStateCBER</a></dd>
						</dl>
					</section>
				</section>
				<?php if (false && Configure::read('debug') != 0): ?>
					<hr />
					<div>
						<a href="#sql_dump" onclick="$('sql_dump').toggle()">Show SQL dump</a>
					</div>
					<div id="sql_dump" style="display: none;">
						<a name="sql_dump"></a>
						<?php echo $this->element('sql_dump'); ?>
					</div>
				<?php endif; ?>
			</div>
		</footer>
		<?php echo $this->Js->writeBuffer(); ?>
	</body>
</html>
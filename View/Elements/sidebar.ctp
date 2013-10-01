<h3>
	<a href="#home" id="home_link">Home</a>
	<?php $this->Js->buffer("
		$('#home_link').click(function(event) {
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
				$('#showmap_$slug').click(function(event) {
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
	$('#select_county').change(function(event) {
		if ($('#select_county').val()) {
			showFullReport();
		}
	});
	$('#select_county_button').click(function(event) {
		if ($('#select_county').val()) {
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
		$('#sources_link').click(function(event) {
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
		$('#faq_link').click(function(event) {
			showPage('faq');
		});
	"); ?>
</h3>

<h3>
	<a href="#credits" id="credits_link">Credits</a>
	<?php $this->Js->buffer("
		$('#credits_link').click(function(event) {
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
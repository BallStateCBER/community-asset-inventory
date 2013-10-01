<?php
$this->Js->buffer("window.print();");
?>
<div id="full_report">
	<h2>
		<?php echo $county_name; ?> County's Full Asset Inventory Report
	</h2>
	<?php echo $this->element('county_table'); ?>
	<aside class="county_info">
		<?php echo $this->element('county_info'); ?>
	</aside>
</div>
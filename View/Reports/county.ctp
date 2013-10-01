<div id="full_report">
	<aside class="county_info">
		<?php echo $this->element('county_info'); ?>
		<p>
			Learn more about this county through CBER's 
			<a href="<?php echo $profiles_url; ?>">County Profiles</a>.
		</p>
	</aside>
	<h2>
		<?php echo $county_name; ?> County's Full Asset Inventory Report
	</h2>
	<p>
		Click on a category to view grades/points in all counties.
	</p>
	
	<?php echo $this->element('county_table'); ?>
</div>
<?php echo $this->Html->link(
	'<img src="/img/icons/printer.png" /> <span>Print</span>',
	array('controller' => 'reports', 'action' => 'county', Inflector::slug($county_name), '?' => 'print'),
	array('escape' => false, 'target' => '_blank', 'class' => 'controls')
); ?>
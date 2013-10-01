<div class="state_map">
	<?php echo $this->element('indiana', array('width' => 100, 'height' => 152, 'classes' => array('small'))); ?>
	<?php $this->Js->buffer("
		$('#in_map_$county_simplified_name').css('fill', '#000');
	"); ?>
</div>
<h2>About <?php echo $county_name; ?> County</h2>
<dl>
	<dt>County Seat:</dt>
		<dd><?php echo $county_info['Location']['county_seat']; ?></dd>
	<dt>Founded:</dt>
		<dd><?php echo $county_info['Location']['founded']; ?></dd>
	<dt>Area:</dt>
		<dd><?php echo $county_info['Location']['square_miles']; ?> square miles</dd>
	<br class="clear" />
</dl>
<p>
	<?php echo nl2br($county_info['Location']['description']); ?>
</p>
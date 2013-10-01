<table>
	<thead>
		<tr>
			<th>Category</th>
			<th>Grade</th>
			<th>Points</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($report as $measurement => $values): ?>
			<?php $pc_id = array_search($measurement, $parent_categories); ?>
			<?php $slug = Inflector::slug($measurement); ?>
			<tr>
				<th>
					<?php if ($this->layout == 'print'): ?>
						<?php echo $measurement; ?>
					<?php else: ?>
						<a href="#<?php echo $slug; ?>" id="showmap_fromreport_<?php echo $slug; ?>">
							<?php echo $measurement; ?>
						</a>
						<?php $this->Js->buffer("
							$('#showmap_fromreport_$slug').click(function(event) {
								showMap('$slug');
							});
						"); ?>
					<?php endif; ?>
				</th>
				<td class="grade">
					<?php if (isset($values['Grade'])): ?>
						<?php echo $values['Grade']; ?>
					<?php else: ?>
						<span class="na">n/a</span>
					<?php endif; ?>
				</td>
				<td class="index">
					<?php echo $values['Index']; ?>
				</td>
			</tr>
		<?php endforeach; ?>
	</tbody>
	<tfoot>
		<tr>
			<td colspan="0">
				N/A: Only points are used when assessing the <br /> 
				changeable and static amenities categories.
			</td>
		</tr>
	</tfoot>
</table>
<table>
	<thead>
		<tr>
			<th>County</th>
			<?php if (! empty($grades)): ?>
				<th>Grade</th>
			<?php endif; ?>
			<th colspan="1">Points</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($counties as $county): ?>
			<tr>
				<th>
					<?php if ($this->layout == 'print'): ?>
						<?php echo $county['Location']['name']; ?>
					<?php else: ?>
						<a href="#" id="showreport_<?php echo $county['Location']['id']; ?>">
							<?php echo $county['Location']['name']; ?>
						</a>
						<?php $this->Js->buffer("
							$('#showreport_{$county['Location']['id']}').click(function(event) {
								event.preventDefault();
								showFullReport('{$county['Location']['simplified']}');
							});
						"); ?>
					<?php endif; ?>
				</th>
				<?php if (! empty($grades)): ?>
					<td>
						<?php if (isset($grades[$county['Location']['id']])): ?>
							<?php echo $grades[$county['Location']['id']]; ?>
						<?php endif; ?>
					</td>
				<?php endif; ?>
				<td>
					<?php echo round($indices[$county['Location']['id']], 1); ?>
				</td>
			</tr>
		<?php endforeach; ?>
	</tbody>
</table>
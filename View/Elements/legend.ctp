<?php if (empty($grades)): ?>
	<ul id="legend_index">
		<li class="index115">115.0-129.9</li>
		<li class="index105">105.0-114.9</li>
		<li class="index95">95.0-104.9</li>
		<li class="index85">85.0-94.9</li>
		<li class="index70">70.0-84.9</li>
	</ul>
<?php else: ?>
	<ul id="legend_grades">
		<li class="grade_a">A</li>
		<li class="grade_b">B</li>
		<li class="grade_c">C</li>
		<li class="grade_d">D</li>
		<li class="grade_f">F</li>
	</ul>
<?php endif; ?>
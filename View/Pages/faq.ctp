<div id="faq">
	<h1>Frequently Asked Questions</h1>
	
	<h2>Why did you create this project?</h2>
	<p>
		The importance of community attributes in determining the economic vitality and prosperity of communities is well understood among those who seriously study economic development; however, few policy makers (and even fewer citizens) enjoy easy access to data and analysis that provides insight into where a specific community stands across different comparative data elements.  Site selectors and relocation consultants use these data sets routinely but typically maintain proprietary or subscription-based data sets.
	</p>
	  
	<h2>How did you choose the categories and variables?</h2>
	<p>
		Several studies across many disciplines link specific types of data to long-term prosperity (such as human capital, school performance, government efficiency, quality of place, and health outcomes).  Using readily available public data sets, we collected and categorized them into clear categories.  We hope to expand this project significantly as the need for specific types of data become more apparent.  
	</p>
	
	<h2>How did you derive grades?</h2>
	<p>
		The grades were given based on a curve, with equal numbers of As and Fs, and of Bs and Ds.  High-quality research hasn't yielded a sufficient weighting for each category, so we simply provided grades for each county based upon the average ranking of the data within each category.  The more variables in each, or the more analytically derived the data (like school test scores), the more reliable the ranking.  The reason for this is that data sets tend to be covariates or move together.  Regions with low levels of educational attainment tend to have fewer well-performing schools.  Places with high rates of one type of morbidity (poor health) tend to have high rates in several others.  So the use of more data in each category tends to minimize the statistical bias a single datum may have. 
	</p>
	<p>
		We also think we've hit on something in this study that should provide a good indicator of the effectiveness of these data sets.  In the following figure, we have graphed average population changes in communities based on their quality of place ranking, their human capital education and human capital wealth grades (see Figure 1).  The result is fairly telling of the relationship between population growth and quality of place.  We have also graphed the level of per capita income by these factors (see Figure 2).  Again, we believe our data sets tell a compelling story for communities in Indiana. 
	</p>
	
	<div class="img_wrapper" id="faq_fig1_img">
		<div class="figure_title">
			Figure 1: Population Change by County Grades<br />for Human Capital, 2000 - 2009
		</div>
		<img src="/img/faq_2009PopChange.png" />
		<div class="toggle">
			<a href="#" id="faq_showtable_fig1">View table</a>
			<?php $this->Js->buffer("
				console.log('buffered');
				$('#faq_showtable_fig1').click(function(event) {
					event.preventDefault();
					$('#faq_fig1_img').hide();
					$('#faq_fig1_table').show();
				});
			"); ?>
		</div>
	</div>
	<div id="faq_fig1_table" class="table_wrapper" style="display: none;">
		<table>
			<thead>
				<tr>
					<th colspan="0" class="figure_title">
						Figure 1: Population Change by County Grades for Human Capital, 2000 - 2009
					</th>
				</tr>
				<tr>
					<th>Grade</th>
					<th>Education</th>
					<th>Health</th>
					<th>Overall</th>
				</tr>
			</thead>
			<tbody>
				<tr><td>A</td><td>18.58%</td><td>12.83%</td><td>20.69%</td></tr>
				<tr><td>B</td><td>1.76%</td><td>5.43%</td><td>3.00%</td></tr>
				<tr><td>C</td><td>0.59%</td><td>1.51%</td><td>0.74%</td></tr>
				<tr><td>D</td><td>0.02%</td><td>-2.23%</td><td>-1.67%</td></tr>
				<tr><td>F</td><td>0.06%</td><td>-0.91%</td><td>-1.72%</td></tr>
			</tbody>
			<tfoot>
				<tr>
					<td colspan="0" class="toggle">
						<a href="#" id="faq_showimg_fig1">View chart</a>
						<?php $this->Js->buffer("
							$('#faq_showimg_fig1').click(function(event) {
								event.preventDefault();
								$('#faq_fig1_img').show();
								$('#faq_fig1_table').hide();
							});
						"); ?>
					</td>
				</tr>
			</tfoot>
		</table>
	</div>
	
	<div class="img_wrapper" id="faq_fig2_img">
		<div class="figure_title">
			Figure 2: Per Capita Income by County Grades<br />for Human Capital, 2009
		</div>
		<img src="/img/faq_2009Income.png" />
		<div class="toggle">
			<a href="#" id="faq_showtable_fig2">View table</a>
			<?php $this->Js->buffer("
				$('#faq_showtable_fig2').click(function(event) {
					event.preventDefault();
					$('#faq_fig2_img').hide();
					$('#faq_fig2_table').show();
				});
			"); ?>
		</div>
	</div>
	<div id="faq_fig2_table" class="table_wrapper" style="display: none;">
		<table>
			<thead>
				<tr>
					<th colspan="0" class="figure_title"> 
						Figure 2: Per Capita Income by County Grades for Human Capital, 2009
					</th>
				</tr>
				<tr>
					<th>Grade</th>
					<th>Education</th>
					<th>Health</th>
					<th>Overall</th>
				</tr>
			</thead>
			<tbody>
				<tr><td>A</td><td>$ 39,236</td><td>$ 34,875</td><td>$ 39,480</td></tr>
				<tr><td>B</td><td>$ 32,362</td><td>$ 33,538</td><td>$ 32,984</td></tr>
				<tr><td>C</td><td>$ 31,060</td><td>$ 31,753</td><td>$ 31,560</td></tr>
				<tr><td>D</td><td>$ 30,729</td><td>$ 30,618</td><td>$ 29,652</td></tr>
				<tr><td>F</td><td>$ 29,290</td><td>$ 28,630</td><td>$ 28,008</td></tr>
			</tbody>
			<tfoot>
				<tr>
					<td colspan="0" class="toggle">
						<a href="#" id="faq_showimg_fig2">View chart</a>
						<?php $this->Js->buffer("
							$('#faq_showimg_fig2').click(function(event) {
								event.preventDefault();
								$('#faq_fig2_img').show();
								$('#faq_fig2_table').hide();
							});
						"); ?>
					</td>
				</tr>
			</tfoot>
		</table>
	</div>
	
	
	<h2>Aren't you concerned that this will hurt the marketing image of these communities?</h2>
	<p>
		No.  All of the data we use is publicly available.  Anyone seriously considering relocating to a community will examine parts of these data sets, while any consultant advising companies to seriously expand or relocate to a community will most likely have already screened several candidate communities for attributes they find unattractive.  Bottom line, the folks who are thinking about coming to or staying in your community already know these things.  The truth itself is never bad, but ignoring the facts can be harmful. 
	</p>
	
	<h2>What is the greatest weakness of the Community Asset Inventory and Rankings?</h2>
	<p>
		These rankings are performed at the lowest level possible, the county level.  It would be equally important to also compare municipalities within counties.   There are also types of data missing that we'd like to add as part of our continual improvement.  Please provide your comments, suggestions for user experience, and/or recommendations for data we could reasonably include by emailing us at <a href="mailto:cber@bsu.edu">cber@bsu.edu</a>. 
	</p>
</div>
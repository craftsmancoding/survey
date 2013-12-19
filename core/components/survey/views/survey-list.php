<script>
	$(function(){
		$('#survey-list tr').on('click',function(){
			var survey_id = $(this).data('survey_id');
			window.location.href = mgr_controller_url+'update&survey_id='+survey_id;
		});
	});
</script>
<div class="container">

	<div class="cmp-msg">
		<div id="cmp-result"></div>
		<div id="cmp-result-msg"></div>
	</div>


		<div class="cmp-header clearfix">
			<div class="cmp-header-title">
				<h2>Surveys</h2>
			</div>
				
			<div class="cmp-buttons-wrapper">
	            <a class="btn btn-custom" href="<?php print $data['mgr_controller_url']; ?>create">Create New Survey</a>
			    <a class="btn" href="<?php print $data['manager_url']; ?>">Close</a>
			</div>
		</div>

		<div class="well">
			<div class="x-panel-body panel-desc x-panel-body-noheader x-panel-body-noborder" id="ext-gen68" >
				<p>Here you can Create New Survey or Choose which Survey you wish to edit.</p>
			</div>
			<div class="cmp-header clearfix">
				<table id="survey-list" class="table table-hover">
			        <thead>
			          <tr>
			            <th>ID</th>
			            <th>Name</th>
			            <th>Description</th>
			            <th>Active</th>
			          </tr>
			        </thead>
			        <tbody>
						<?php if (!empty($data['surveys']['results'])) : ?>
							<?php foreach($data['surveys']['results'] as $survey) : ?>
			                    <tr class="survey-row" data-survey_id="<?php print $survey['survey_id']; ?>">
						            <td><?php print $survey['survey_id']; ?></td>
						            <td><?php print $survey['name']; ?></td>
						            <td><?php print $survey['description']; ?></td>
						            <td><strong><?php print ($survey['is_active'] == 1) ? 'Yes' : 'No'; ?></strong></td>
						        </tr>
					    	<?php endforeach; ?>
			            <?php else : ?>
			                <tr><td colspan="4" style="text-align: center;">No Survey Found</td></tr>
						<?php endif; ?>
			        </tbody>
			      </table>

			</div>
		</div>


</div>



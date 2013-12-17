<script>
	$(function(){
		$('#update-survey').on('submit',function(e){
			var values = $(this).serialize();
			var url = connector_url + 'survey_save';

			$.post( url+"&action=update", values, function(data){
			    data = $.parseJSON(data);
			  $('.cmp-msg').show();
			    if (data.success) {
			       $('#cmp-result').html('Success');
			       $('#msg').addClass('success');
			    }
			    else {
			       $('#cmp-result').html('Error');                
			       $('#msg').addClass('error');
			    }
			    
			    $('#cmp-result-msg').html(data.msg);        
			    jQuery('.cmp-msg').delay(3200).fadeOut(300);
			});
		    e.preventDefault();
	    });
	});
</script>
<div class="cmp-msg">
	<div id="cmp-result"></div>
	<div id="cmp-result-msg"></div>
</div>


<div class="container">

	<form method="post" id="update-survey" action="#">

		<div class="cmp-header clearfix">
			<div class="cmp-header-title">
				<h2>Update Survey</h2>
			</div>
				
			<div class="cmp-buttons-wrapper">
	                <button type="submit" class="btn" id="survey-create">Save</button>
					<a class="btn" href="#">Close</a>
			</div>
		</div>
		
		<div class="well">
	           <table class="table no-top-border">
                    <tbody>
                         <tr>
                            <td style="vertical-align: top;">
                            	<input type="hidden" id="survey_id" name="survey_id" value="<?php print $data['survey_id']; ?>" />
                                 <label for="title">Name</label>
                                <input type="text" class="span8" id="name" name="name" value="<?php print $data['name']; ?>"/>
                                 <label for="description">Description</label>
                                <textarea id="description" class="span8" rows="6" name="description"><?php print $data['description']; ?></textarea>
                            </td>
                            <td style="vertical-align: top;">
                               
                                <label for="date_open">Date Open</label>
                                <input class="span4" type="text" id="date_open" name="date_open" value="<?php print $data['date_open']; ?>"/>
                                <label for="date_closed">Date Closed</label>
                                <input class="span4" type="text" id="date_closed" name="date_closed" value="<?php print $data['date_closed']; ?>"/>
								<label for="is_active">Is Active</label>
								<select class="span2" name="is_active" id="is_active">
									<option value="1" <?php print $data['is_active'] == 1 ? 'selected' : '' ; ?>>Yes</option>
									<option value="0" <?php print $data['is_active'] == 0 ? 'selected' : '' ; ?>>No</option>
								</select>
								<label for="is_editable">Is Editable</label>
								<select class="span2" name="is_editable" id="is_editable">
									<option value="1" <?php print $data['is_editable'] == 1 ? 'selected' : '' ; ?>>Yes</option>
									<option value="0" <?php print $data['is_editable'] == 0 ? 'selected' : '' ; ?>>No</option>
								</select>
                            </td>
                        </tr>
                    </tbody>
                </table>
			</div>

	</form>

	<div class="well">
	    <div class="panel-desc">
			<p>Here you can Create Questions or Choose which Question you wish to edit.</p>
		</div>
			<div class="buttons-wrapper">
		            <a class="btn btn-custom" data-toggle="modal" data-target="#question-modal" href="#" >New Question</a>
			</div>

			<!-- Modal -->
			<div class="modal fade" id="question-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
				<?php print $data['question-create']; ?>
			</div><!-- /.modal -->

			<table class="table table-hover">
		        <thead>
		          <tr>
		            <th>ID</th>
		            <th>Name</th>
		            <th>Type</th>
		            <th>Active</th>
		          </tr>
		        </thead>
		        <tbody>
					<?php if (!empty($data['questions']['results'])) : ?>
							<?php foreach($data['questions']['results'] as $question) : ?>
			                    <tr>
						            <td><?php print $question['question_id']; ?></td>
						            <td><?php print $question['text']; ?></td>
						            <td><?php print $question['type']; ?></td>
						            <td><strong><?php print ($question['is_active'] == 1) ? 'Yes' : 'No'; ?></strong></td>
						        </tr>
							<?php endforeach; ?>
						<?php else : ?>
			                <tr><td style="text-align: center;" colspan="4">No Question Found</td></tr>
					<?php endif; ?>
		        </tbody>
		      </table>

	</div>

</div>

	<script>
		$(function(){
			$('#question-tbl tr').on('click',function(){
				//$(".loader-ajax").show();
				var question_id = $(this).data('question_id');
				var url = connector_url + 'get_question&question_id='+question_id;
				$.ajax({ 
					type: "GET", 
					url: url, 
					success: function(response) { 
						$("#update-question-modal").html(response);
					}   
				}); 
			});
		});
	</script>
<div class="question-list-inner">
	<table id="question-tbl" class="table table-hover">
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
	                    <tr data-toggle="modal" data-question_id="<?php print $question['question_id']; ?>" data-target="#update-question-modal">
				            <td><?php print $question['question_id']; ?></td>
				            <td><?php print $question['text']; ?></td>
				            <td><?php print $question['type']; ?></td>
				            <td><strong><?php print ($question['is_active'] == 1) ? 'Yes' : 'No'; ?></strong></td>
				        </tr>
					<?php endforeach; ?>
				<?php else : ?>
	                <tr><td style="text-align: center;" colspan="4">You have not created any questions for this survey yet.</td></tr>
			<?php endif; ?>
	    </tbody>
	  </table>
</div>

<div class="modal fade" id="update-question-modal"></div><!--/.modal -->
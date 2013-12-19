<script>
	$(function() {
		$('#update-question').on('submit',function(e){
			var values = $(this).serialize();
			$.ajax({
                type: "POST",
                url: connector_url+"question_save&action=update",  
                data: values,  
                success: function( data )  
                {
                     data = $.parseJSON(data);

			    	if(data.success == true) {
			    		console.log(data)
			    		$('.modal-msg').addClass('alert-success').html(data.msg).show();
			    		$(".modal-msg").delay(1000).fadeOut(300);
			    		window.setTimeout(function(){
						     $('#update-question-modal').modal('hide');
						}, 1000);
						$( document ).on( "hidden.bs.modal", "#update-question-modal", function() {
							 get_questions();
						});
			    	} else{
			    		$('.modal-msg').addClass('alert-danger').html(data.msg).show();
			    		$(".modal-msg").delay(3200).fadeOut(300);
			    	}
			    	
                }
           });
		    e.preventDefault();
	    });

	   	$('#question-delete').on('click', function() {
		  	if(confirm('Are you sure you want to delete this Question?')) {
				var url = connector_url + 'question_save';
				var question_id = $('#question_id').val();
	            $.post( url+"&action=delete", { question_id: question_id }, function( data ){
			    	data = $.parseJSON(data);
			    	if(data.success == true) {
			    		$('.modal-msg').addClass('alert-success').html(data.msg).show();
			    		$(".modal-msg").delay(1000).fadeOut(300);
						window.setTimeout(function(){
						     $('#update-question-modal').modal('hide');
						}, 1000);
						$( document ).on( "hidden.bs.modal", "#update-question-modal", function() {
							 get_questions();
						});
			    	} else{
			    		$('.modal-msg').addClass('alert-danger').html(data.msg).show();
			    		$(".modal-msg").delay(3200).fadeOut(300);
			    	}
			    } );
	        }
			return false;
		});

	});
</script>
<div class="modal-dialog">
	<div class="modal-content">
	  <div class="modal-header">
	    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
	    <h4 class="modal-title" id="myModalLabel">Update Question</h4>
	  </div>
	  <div class="modal-msg alert">test</div>
	  
	  	<form id="update-question" class="form-horizontal" role="form">
		  	<div class="modal-body">
			  <div class="form-group">
			  	<input type="hidden" name="survey_id" id="survey_id" value="<?php print $data['survey_id'];  ?>">
			  	<input type="hidden" name="question_id" id="question_id" value="<?php print $data['question_id']; ?>">
			    <label for="question" class="control-label">Question</label>
			    	<textarea name="text" id="text" id="question"cols="30" rows="5"><?php print $data['text'];  ?></textarea>
			  </div>
			  <div class="form-group">
			    <label for="type" class="control-label">Type</label>
			    <select name="type" id="type">
			    	<option value="text" <?php print $data['type'] == 'text' ? 'selected' : '';  ?>>Text</option>
			    	<option value="dropdown" <?php print $data['type'] == 'dropdown' ? 'selected' : '';  ?>>Dropdown</option>
			    	<option value="textarea" <?php print $data['type'] == 'textarea' ? 'selected' : '';  ?>>Textarea</option>
			    </select>
			  </div>
			  <div id="options-wrap" class="form-group">
			    <label for="options" class="control-label">Options</label>
			   	<input type="text" name="options" id="options" value="<?php print htmlentities($data['options']); ?>">
			   	<p class="help-block">Example Option Value: {"m":"Male","f":"Female"}</p>
			  </div>
			  <div class="form-group">
			    <label for="is_active" class="control-label">Active</label>
			    <select name="is_active" id="is_active">
			    	<option value="1" <?php print $data['is_active'] == 1 ? 'selected' : '';  ?>>Yes</option>
			    	<option value="0" <?php print $data['is_active'] == 0 ? 'selected' : '';  ?>>No</option>
			    </select>
			  </div>
			  <div class="form-group">
			    <label for="is_required" class="control-label">Required</label>
			    <select name="is_required" id="is_required">
			    	<option value="1" <?php print $data['is_required'] == 1 ? 'selected' : '';  ?>>Yes</option>
			    	<option value="0" <?php print $data['is_required'] == 0 ? 'selected' : '';  ?>>No</option>
			    </select>
			  </div>
			
		  </div>
		  <div class="modal-footer">
		    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
		     <button class="btn" id="question-delete">Delete</button>
		    <button type="submit" id="save-question"  class="btn btn-custom">Save changes</button>
		  </div>
	  </form>
	</div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
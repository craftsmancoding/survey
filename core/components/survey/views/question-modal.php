<script>
	$(function() {
		$('#create-question').on('submit',function(e){
			var values = $(this).serialize();
           	var survey_id = $('#survey_id').val();
			$.ajax({
                type: "POST",
                url: connector_url+"question_save&action=create&survey_id="+survey_id,  
                data: values,  
                success: function( data )  
                {
                	console.log(data)
                     data = $.parseJSON(data);

			    	if(data.success == true) {
			    		$('#modal-msg').addClass('alert-success').html(data.msg).show();
			    		$("#modal-msg").delay(1000).fadeOut(300);
			    		window.setTimeout(function(){
						     $('#question-modal').modal('hide');
						}, 1000);

						$( document ).on( "hidden.bs.modal", "#question-modal", function() {
							 get_questions();
						});
			    	} else{
			    		$('#modal-msg').addClass('alert-danger').html(data.msg).show();
			    		$("#modal-msg").delay(1000).fadeOut(300);
			    	}
			    	
                }
           });
		    e.preventDefault();
	    });
	});
</script>
<div class="modal-dialog">
	<div class="modal-content">
	  <div class="modal-header">
	    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
	    <h4 class="modal-title" id="myModalLabel">Create Question</h4>
	  </div>
	  <div id="modal-msg" class="alert"></div>
	  
	  	<form id="create-question" class="form-horizontal" role="form">
		  	<div class="modal-body">
			  <div class="form-group">
			  	<input type="hidden" name="survey_id" id="survey_id" value="<?php print isset($data['survey_id']) ? $data['survey_id'] : '';  ?>">
			    <label for="question" class="control-label">Question</label>
			    <input type="text" name="text" class="form-control" id="question" value="<?php print isset($data['text']) ? $data['text'] : '';  ?>">
			  </div>
			  <div class="form-group">
			    <label for="type" class="control-label">Type</label>
			    <select name="type" id="type">
			    	<option value="text">Text</option>
			    	<option value="dropdown">Dropdown</option>
			    	<option value="textarea">Textarea</option>
			    </select>
			  </div>
			  <div id="options-wrap" class="form-group">
			    <label for="options" class="control-label">Options</label>
			   	<textarea name="options" id="options" cols="30" rows="5"></textarea>
			   	<p class="help-block">Example Option Value: Option 1==value1||Option 2==value2</p>
			  </div>
			  <div class="form-group">
			    <label for="is_active" class="control-label">Active</label>
			    <select name="is_active" id="is_active">
			    	<option value="1">Yes</option>
			    	<option value="0">No</option>
			    </select>
			  </div>
			  <div class="form-group">
			    <label for="is_required" class="control-label">Required</label>
			    <select name="is_required" id="is_required">
			    	<option value="1">Yes</option>
			    	<option value="0">No</option>
			    </select>
			  </div>
			
		  </div>
		  <div class="modal-footer">
		    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
		    <button type="submit"id="save-question"  class="btn btn-custom">Save changes</button>
		  </div>
	  </form>
	</div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
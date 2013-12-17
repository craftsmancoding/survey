<div class="modal-dialog">
	<div class="modal-content">
	  <div class="modal-header">
	    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
	    <h4 class="modal-title" id="myModalLabel">Create Question</h4>
	  </div>
	  <div class="modal-body">
	  	<form class="form-horizontal" role="form">
		  <div class="form-group">
		    <label for="question" class="control-label">Question</label>
		    <input type="text" name="text" class="form-control" id="question">
		  </div>
		  <div class="form-group">
		    <label for="type" class="control-label">Type</label>
		    <select name="type" id="type">
		    	<option value="text">Text</option>
		    	<option value="dropdown">Dropdown</option>
		    	<option value="textarea">Textarea</option>
		    </select>
		  </div>
		  <div class="form-group">
		    <label for="options" class="control-label">Options</label>
		   	<textarea name="options" id="options" cols="30" rows="5"></textarea>
		   	<p class="help-block">Comma Separated Value. Example: Yes,No</p>
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
		</form>
	  </div>
	  <div class="modal-footer">
	    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
	    <button type="button" class="btn btn-custom">Save changes</button>
	  </div>
	</div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
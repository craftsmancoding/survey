<script>
	$(function() {
		$('#create-survey').on('submit',function(e){
			var values = $(this).serialize();
			console.log(values);
			var url = connector_url + 'survey_save';

			$.post( url+"&action=create", values, function(data){
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
			    window.setTimeout(function() {
				    window.location.href = mgr_controller_url+'update&survey_id='+data.survey_id;
				}, 1500);
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

	<form method="post" id="create-survey" action="#">

		<div class="cmp-header clearfix">
			<div class="cmp-header-title">
				<h2>Create New Survey</h2>
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
                                 <label for="title">Name</label>
                                <input type="text" class="span8" id="name" name="name" value=""/>
                                 <label for="description">Description</label>
                                <textarea id="content" class="span8" rows="6" name="content"></textarea>
                            </td>
                            <td style="vertical-align: top;">
                               
                                <label for="date_open">Date Open</label>
                                <input class="span4" type="text" id="date_open" name="date_open" value=""/>
                                <label for="date_closed">Date Closed</label>
                                <input class="span4" type="text" id="date_closed" name="date_closed" value=""/>
								<label for="is_active">Is Active</label>
								<select class="span2" name="is_active" id="is_active">
									<option value="1">Yes</option>
									<option value="0">No</option>
								</select>
								<label for="is_editable">Is Editable</label>
								<select class="span2" name="is_editable" id="is_editable">
									<option value="1">Yes</option>
									<option value="0">No</option>
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
		            <a class="btn btn-custom" href="#" disabled>New Question</a>
			</div>

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
			         <tr><td style="text-align: center;" colspan="4">No Question Found</td></tr>
		        </tbody>
		      </table>

	</div>

</div>

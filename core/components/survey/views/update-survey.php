<script>
	
	function get_questions() {

		$(".loader-ajax").show();
		var survey_id = $('#survey_id').val();
		var url = connector_url + 'show_questions&survey_id='+survey_id;
		$.ajax({ 
			type: "GET", 
			url: url, 
			success: function(response) { 
				$('#question-list').html(response);
				$(".loader-ajax").hide();
			}   
		}); 
	}

	$(function(){

		$('.datepicker').datepicker();

		get_questions();
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

	    $('#survey-delete').on('click', function() {
		  	if(confirm('Are you sure you want to delete this Survey? Questions associated will also be removed.')) {
				var url = connector_url + 'survey_save';
				var survey_id = $('#survey_id').val();
	            $.post( url+"&action=delete", { survey_id: survey_id }, function( data ){
			    	data = $.parseJSON(data);
			    	if(data.success == true) {
						window.location.href = mgr_controller_url;
			    	} else{
			    		$('#cmp-result').html('Error');                
			       		$('#msg').addClass('error');
			    		$(".moxy-msg").delay(3200).fadeOut(300);
			    	}
			    } );
	        }
			return false;
		});

		$('#new-question').on('click',function(){
			$('#create-question').reset();
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
	                <button type="submit" class="btn">Save</button>
	                <button class="btn" id="survey-delete">Delete</button>
					<a class="btn" href="<?php print $data['mgr_controller_url']; ?>">Close</a>
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
                                <div class="input-append date datepicker" data-date="<?php echo date('Y-m-d') ?>" data-date-format="yyyy-mm-dd">
                                	<span class="add-on"><i class="icon icon-calendar"></i></span>
									  <input type="text" name="date_open" id="date_open" class="span3" maxlength="10" value="<?php print date('Y-m-d',strtotime($data['date_open'])); ?>">
									  
								</div>
                                <label for="date_closed">Date Closed</label>
                                 <div class="input-append date datepicker" data-date="<?php echo date('Y-m-d') ?>" data-date-format="yyyy-mm-dd">
                                	<span class="add-on"><i class="icon icon-calendar"></i></span>
									  <input type="text" name="date_closed" id="date_closed" class="span3" maxlength="10" value="<?php print date('Y-m-d',strtotime($data['date_closed'])); ?>">
									  
								</div>
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
			<p>Here you can create or edit questions for your survey.</p>
		</div>
			<div class="buttons-wrapper clearfix">
		            <a class="btn btn-custom"id="new-question" data-toggle="modal" data-target="#question-modal" href="#" >New Question</a>
			</div>
			<div class="clear">&nbsp;</div>

			<!-- Modal -->
			<div class="modal fade" id="question-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
				<?php print $data['question-modal']; ?>
			</div><!-- /.modal -->
			
			<div class="loader-ajax">
				<img src="<?php print $data['loader_path']; ?>" alt="">
			</div>
			
			<div id="question-list"></div>
	</div>

</div>

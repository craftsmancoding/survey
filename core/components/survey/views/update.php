<div class="cmp-msg">
	<div id="cmp-result"></div>
	<div id="cmp-result-msg"></div>
</div>


<div class="container">

	<form method="post" id="create-survey" action="#">

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
		            <a class="btn btn-custom" href="#" >New Question</a>
			</div>

			<table class="table table-hover">
		        <thead>
		          <tr>
		            <th>ID</th>
		            <th>Name</th>
		            <th>Description</th>
		            <th>Active</th>
		          </tr>
		        </thead>
		        <tbody>
					
		                    <tr>
					            <td>1</td>
					            <td>Test</td>
					            <td>Testing...</td>
					            <td><strong>Yes</strong></td>
					        </tr>
		        </tbody>
		      </table>

	</div>

</div>

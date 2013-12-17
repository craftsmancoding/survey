<div class="question-list-inner">
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
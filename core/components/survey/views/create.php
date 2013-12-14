<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" />

<script>
	$(function() {
		$('#cmp-tab').tabify();
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
	           
	                <button class="btn" id="product_update">Save</button>
	                <a class="btn" href="#" target="_blank">View</a>
					<a class="btn" href="#">Close</a>
			</div>
		</div>
		
		
		<ul id="cmp-tab" class="menu">
			<li class="survey-link active"><a href="#survey_tab">Survey</a></li>
			<li class="questions-link" ><a href="#questions_tab">Questions</a></li>
		</ul>

		<div id="survey_tab" class="content">
	            <table class="table no-top-border">
					<tbody>
						<tr>
							<td>
								<label for="name">Name</label>
							</td>
							<td>
								<input type="text" name="name" id="name" value="">
								<input type="hidden" name="product_id" id="product_id" value="">
							</td>
							<td>
								<label for="is_active">Active</label>
							</td>
							<td>
								<select name="is_active" id="is_active">
									<option value="1">Yes</option>
									<option value="0">No</option>
								</select>
							</td>

						</tr>

						<tr>
							<td>
								<label for="title">Browser Title</label>
							</td>
							<td>
								<input type="text" name="title" id="title" value="">
							</td>
							<td>
								<label for="alias">Alias</label>
							</td>
							<td>
								<input type="text" name="alias" id="alias" value="">
							</td>

						</tr>

						<tr>
							<td>
								<label for="category">Category</label>
							</td>
							<td>
								<select name="category" id="category">
	                                
								</select>
							</td>
							<td>
								<label for="template_id">Template</label>
							</td>
							<td>

								<select name="template_id" id="template_id">
	                                
								</select> 
							</td>
						</tr>
						<tr><td colspan="4">&nbsp;</td></tr>
						<tr>
							<td>
								<label for="description">Description</label>
							</td>
							<td colspan="3">
								<textarea name="description" id="description" style="width:680px;height:70px;"></textarea>
							</td>
						</tr>
						<tr>
							<td>
								<label for="description">Content</label>
							</td>
							<td colspan="3">
								<textarea name="content" id="content" class="modx-richtext" style="width:700px;height:120px;"></textarea>
							</td>
						</tr>

							
					</tbody>
				</table>

		</div>

		<div id="questions_tab" class="content">
			 <table class="table no-top-border">
					<tbody>
						<tr>
							<td>
								<label for="sku">SKU</label>
							</td>
							<td>
								<input type="text" name="sku" id="sku" value="">
							</td>
							<td>
								<label for="sku_vendor">Vendor SKU</label>
							</td>
							<td>
								<input type="text" name="sku_vendor" id="sku_vendor" value="">
							</td>
						</tr>
						<tr>
							<td>
								<label for="price">Price</label>
							</td>
							<td>
								<input type="text" name="price" id="price" value="">
							</td>
							<td>
								<label for="price_sale">Sale Price</label>
							</td>
							<td>
								<input type="text" name="price_sale" id="price_sale" value="">
							</td>

						</tr>

						<tr>
							<td>
								<label for="price_strike_thru">Strike-Through Price</label>
							</td>
							<td>
								<input type="text" name="price_strike_thru" id="price_strike_thru" value="">
							</td>
							<td>
								<label for="sale_start">Sale Start</label>
							</td>
							<td>
								<div class="input-append date datepicker" data-date="<?php echo date('Y-m-d') ?>" data-date-format="yyyy-mm-dd">
										  <input type="text" name="sale_start" id="sale_start" class="span2" maxlength="10" value="">
										  <span class="add-on"><i class="icon icon-calendar"></i></span>
								</div>
							</td>
						</tr>

						<tr>
							<td>
								<label for="currency_id">Currency</label>
							</td>
							<td>
								<select name="currency_id" id="currency_id">
	                                
								</select>
							</td>
							<td>
								<label for="sale_end">Sale End</label>
							</td>
							<td>
								<div class="input-append date datepicker" data-date="<?php echo date('Y-m-d') ?>" data-date-format="yyyy-mm-dd">
										  <input type="text" name="sale_end" id="sale_end" class="span2" maxlength="10" value="">
										  <span class="add-on"><i class="icon icon-calendar"></i></span>
								</div>
							</td>
						</tr>

						<tr>
							<td>
								<label for="qty_inventory">Inventory</label>
							</td>
							<td>
								<input type="text" name="qty_inventory" id="qty_inventory" value="">
							</td>
							<td>
								<label for="qty_min">Qty Min</label>
							</td>
							<td>
								<input type="text" name="qty_min" id="qty_min" value="">
							</td>

						</tr>
						<tr>
							<td>
								<label for="qty_alert">Alert Qty</label>
							</td>
							<td>
								<input type="text" name="qty_alert" id="qty_alert" value="">
							</td>
							<td>
								<label for="qty_max">Qty Max</label>
							</td>
							<td>
								<input type="text" name="qty_max" id="qty_max" value="">
							</td>

						</tr>

						<tr>
							<td>
								<label for="track_inventory">Track Inventory</label>
							</td>
							<td>
								<select name="track_inventory" id="track_inventory">
									<option value="1">Yes</option>
									<option value="0">No</option>
								</select>
							</td>
							<td>
								<label for="back_order_cap">Back Order Cap</label>
							</td>
							<td colspan="3">
								<input type="text" name="back_order_cap" id="back_order_cap" value="">
							</td>

						</tr>
						<tr>
							<td>
								<label for="type">Product Type</label>
							</td>
							<td>
								<select name="type" id="type">
	                               
								</select>
							</td>
							<td>
								<label for="store_id">Product Container</label>
							</td>
							<td>
								<select name="store_id" id="store_id">
									
								</select>
							</td>
						</tr>						
					</tbody>
				</table>
		</div>
	</form>

</div>

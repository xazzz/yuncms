<?php
defined ( 'IN_ADMIN' ) or exit ( 'No permission resources.' );
include $this->view ( 'header', 'admin' );
?>
<script type="text/javascript">
<!--
$(function(){
	$.formValidator.initConfig({
		autotip:true,
		formid:"myform",onerror:function(msg){}});
	$("#name")
		.formValidator({
			onshow:"<?php echo L('input').L('payment_mode').L('name')?>",
			onfocus:"<?php echo L('payment_mode').L('name').L('empty')?>"
		})
		.inputValidator({
			min:1,
			max:999,
			onerror:"<?php echo L('payment_mode').L('name').L('empty')?>"
		});
})
//-->
</script>
<div class="pad-10">
	<div class="common-form">
		<form name="myform"
			action="?app=admin&controller=payment&action=<?php echo $_GET['action']?>"
			method="post" id="myform">
			<fieldset>
				<legend><?php echo L('basic_config')?></legend>
				<table width="100%" class="table_form">
					<tr>
						<td width="120"><?php echo L('payment_mode')?></td>
						<td><?php echo $pay_name?></td>
					</tr>
					<tr>
						<td width="120"><?php echo L('payment_mode').L('name')?></td>
						<td>
							<input type="text" name="name"
								value="<?php echo $name ? $name : $pay_name?>"
								class="input-text" id="name"></input>
						</td>
					</tr>
					<tr>
						<td><?php echo L('payment_mode').L('desc')?></td>
						<td>
							<textarea name="description" rows="2" cols="10" id="description"
								class="inputtext"><?php echo $pay_desc?></textarea>
<?php echo Form::editor('description', 'desc');?>
</td>
					</tr>
					<tr>
						<td width="120"><?php echo L('listorder')?></td>
						<td>
							<input type="text" name="pay_order"
								value="<?php echo $pay_order?>" class="input-text"
								id="pay_order" size="3"></input>
						</td>
					</tr>
					<tr>
						<td width="120"><?php echo L('online')?>?</td>
						<td><?php echo $is_online ? L('yes'):L('no')?></td>
					</tr>
					<tr>
						<td width="120"><?php echo L('pay_factorage')?>：</td>
						<td id="paymethod">
							<input name="pay_method" value="1" type="radio"
								<?php echo ($pay_method == 2) ? '': 'checked'?>> <?php echo L('pay_method_rate')?>&nbsp;&nbsp;&nbsp;<input
								name="pay_method" value="2" type="radio"
								<?php echo ($pay_method == 1) ? '': 'checked'?>> <?php echo L('pay_method_fix')?>&nbsp;&nbsp;&nbsp; </td>
					</tr>
					<tr>
						<td></td>
						<td>
							<div id="rate"
								<?php if($pay_method != 1) echo 'class="hidden"';?>>
<?php echo L('pay_rate')?><input type="text" size="3"
									value="<?php echo $pay_fee?>" name="pay_rate">&nbsp;%&nbsp;&nbsp;&nbsp;&nbsp;<?php echo L('pay_method_rate_desc')?>
</div>
							<div id="fix"
								<?php if($pay_method != 2) echo 'class="hidden"';?>>
<?php echo L('pay_fix')?><input type="text" name="pay_fix" size="3"
									value="<?php echo $pay_fee?>">&nbsp;&nbsp;&nbsp;&nbsp; <?php echo L('pay_method_fix_desc')?>
</div>
						</td>
					</tr>
				</table>
			</fieldset>
			<div class="bk15"></div>
			<fieldset>
				<legend><?php echo L('parameter_config')?></legend>
				<table width="100%" class="table_form">
				<?php
				foreach ($config as $key => $value) {?>
					<tr>
						<td><?php echo $value['label']?></td>
						<td>
							<?php if($value['type'] == 'text'){?>
								<input type="<?php echo $value['type']?>" class="input-text" name="config[<?php echo $key?>]" id="<?php echo $key?>" value="<?php echo $value['value']?>" size="40"></input>
							<?php } elseif ($value['type'] == "textarea"){?>
      							<textarea name="config[<?php echo $key?>]" cols="80" rows="5"><?php echo $value['value']?></textarea>
							<?php } elseif($value['type'] == 'select') { ?>
								<select name="config[<?php echo $key?>]" value="0">
									<?php foreach ($value['range'] as $k => $v) {?>
									<option value="<?php echo $k?>" <?php if($k == $value['value']){ ?> selected="" <?php } ?>><?php echo $v?></option><?php }?>
								</select>
						<?php }?>
						</td>
					</tr>
<?php }?>
	</table>
			</fieldset>

			<div class="bk15"></div>
			<input type="hidden" name="pay_name" value="<?php echo $pay_name?>" />
			<input type="hidden" name="id" value=<?php echo $id?> />
			<input type="hidden" name="pay_code" value=<?php echo $pay_code?> />
			<input type="hidden" name="is_cod" value=<?php echo $is_cod?> />
			<input type="hidden" name="is_online" value=<?php echo $is_online?> />
			<input name="dosubmit" type="submit" value="<?php echo L('submit')?>"
				class="dialog" id="dosubmit">
		</form>
	</div>
</div>
</body>
</html>
<script type="text/javascript">

$(document).ready(function() {
	$("#paymethod input[type='radio']").click( function () {
		if($(this).val()== 1){
			$("#rate").removeClass('hidden');
			$("#fix").addClass('hidden');
			$("#rate input").val('0');
		} else {
			$("#fix").removeClass('hidden');
			$("#rate").addClass('hidden');
			$("#fix input").val('0');
		}
	});
});
</script>



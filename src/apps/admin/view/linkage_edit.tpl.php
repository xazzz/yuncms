<?php
defined ( 'IN_ADMIN' ) or exit ( 'No permission resources.' );
include $this->view ( 'header' );
?>
<script type="text/javascript">
  $(document).ready(function() {
	$.formValidator.initConfig({
		autotip:true,
		formid:"myform",
		onerror:function(msg){}
	});
	$("#name")
		.formValidator({
			onshow:"<?php echo L('input').L('linkage_name')?>",
			onfocus:"<?php echo L('linkage_name').L('not_empty')?>"
		})
		.inputValidator({
			min:1,
			max:999,
			onerror:"<?php echo L('linkage_name').L('not_empty')?>"
		});
  })
</script>
<div class="pad_10">
	<div class="common-form">
		<form name="myform" action="?app=admin&controller=linkage&action=edit"
			method="post" id="myform">
			<table width="100%" class="table_form contentWrap">
<?php
if (isset ( $_GET ['parentid'] )) {
	?>
<tr>
					<td><?php echo L('linkage_parent_menu')?></td>
					<td>
<?php echo Form::select_linkage($info['keyid'], 0, 'info[parentid]', 'parentid', L('cat_empty'), $_GET['parentid'])?>
  </td>
				</tr>
  <?php } ?>
<tr>
					<td><?php echo L('linkage_name')?></td>
					<td><input type="text" name="info[name]" value="<?php echo $name?>"
						class="input-text" id="name" size="30"></input></td>
				</tr>

				<tr>
					<td><?php echo L('linkage_desc')?></td>
					<td><textarea name="info[description]" rows="2" cols="20"
							id="description" class="inputtext"
							style="height: 45px; width: 300px;"><?php echo $description?></textarea>
					</td>
				</tr>
<?php
if (isset ( $_GET ['parentid'] )) {
	?>
<input type="hidden" name="linkageid" value="<?php echo $linkageid?>">
				<input name="dosubmit" type="submit"
					value="<?php echo L('submit')?>" class="dialog" id="dosubmit">
<?php } else { ?>
<tr>
					<td><?php echo L('linkage_menu_style')?></td>
					<td><input name="info[style]" value="0" type="radio"
						<?php if($style==0) {?> checked <?php }?>>&nbsp;<?php echo L('linkage_option_style')?>&nbsp;&nbsp;<input
						name="info[style]" value="1" type="radio" <?php if($style==1) {?>
						checked <?php }?>>&nbsp;<?php echo L('linkage_pop_style')?>
</td>
				</tr>

				<input type="hidden" name="linkageid"
					value="<?php echo $linkageid?>">
				<input type="hidden" name="info[keyid]" value="<?php echo $keyid?>">
				<input name="dosubmit" type="submit"
					value="<?php echo L('submit')?>" class="dialog" id="dosubmit">

  <?php } ?>
</table>

		</form>
	</div>
</div>
</body>
</html>

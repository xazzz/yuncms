<?php
defined('IN_ADMIN') or exit('No permission resources.');
include $this->view('header','admin');
?>
<script type="text/javascript">
<!--
	$(function(){
	$.formValidator.initConfig({
		formid:"myform",
		autotip:true,
		onerror:function(msg,obj){
			window.top.art.dialog.alert(msg);
			$(obj).focus();
		}
	});
	$("#type_name")
		.formValidator({
			onshow:"<?php echo L("input").L('type_name')?>",
			onfocus:"<?php echo L("input").L('type_name')?>"
		})
		.inputValidator({
			min:1,
			onerror:"<?php echo L("input").L('type_name')?>"
		})
		.ajaxValidator({
			type : "get",
			url : "?app=link&controller=link&action=public_check_name&typeid=<?php echo $typeid;?>",
			data :"",
			datatype : "html",
			async:'false',
			success : function(data){
				if( data == "1" ){
					return true;
				}else{
					return false;
				}
			},
			buttons: $("#dosubmit"),
			onerror : "<?php echo L('type_name').L('exists')?>",
			onwait : "<?php echo L('connecting')?>"
		})
		.defaultPassed();
	})
//-->
</script>
<div class="pad-lr-10">
<form action="?app=link&controller=link&action=edit_type&typeid=<?php echo $typeid; ?>" method="post" name="myform" id="myform">
<table cellpadding="2" cellspacing="1" class="table_form" width="100%">

	<tr>
		<th width="60"><?php echo L('type_name')?>：</th>
		<td><input type="text" name="type[name]" id="type_name"
			size="30" class="input-text" value="<?php echo $name;?>"></td>
	</tr>

	<tr>
		<th width="100"><?php echo L('link_type_listorder')?>：</th>
		<td><input type="text" name="type[listorder]" id="listorder"
			size="5" class="input-text" value="<?php echo $listorder;?>"></td>
	</tr>

	<tr>
		<th><?php echo L('type_description')?>：</th>
		<td><textarea name="type[description]" id="description" cols="50"
			rows="6"><?php echo $description;?></textarea></td>
	</tr>
	<input
		type="submit" name="dosubmit" id="dosubmit" class="dialog"
		value=" <?php echo L('submit')?> ">


</table>
</form>
</div>
</body>
</html>

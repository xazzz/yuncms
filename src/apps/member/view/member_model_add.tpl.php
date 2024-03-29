<?php
defined('IN_ADMIN') or exit('No permission resources.');
include $this->view('header', 'admin');
?>
<script language="javascript" type="text/javascript" src="<?php echo JS_PATH?>formvalidator.js" charset="UTF-8"></script>
<script language="javascript" type="text/javascript" src="<?php echo JS_PATH?>formValidatorRegex.js" charset="UTF-8"></script>
<script type="text/javascript">
<!--
$(function(){
	$.formValidator.initConfig({
		autotip:true,
		formid:"myform",
		onerror:function(msg){}
	});

	$("#modelname")
		.formValidator({
			onshow:"<?php echo L('input').L('model_name')?>",
			onfocus:"<?php echo L('model_name').L('between_2_to_20')?>"
		})
		.inputValidator({
			min:2,
			max:20,
			onerror:"<?php echo L('model_name').L('between_2_to_20')?>"
		})
		.regexValidator({
			regexp:"username",
			datatype:"enum",
			onerror:"<?php echo L('model_name').L('format_incorrect')?>"
		})
		.ajaxValidator({
	    	type : "get",
			url : "?app=member&controller=member_model&action=public_checkmodelname_ajax",
			data :"",
			datatype : "html",
			async:'false',
			success : function(data){
            	if( data == "1" ) {
                	return true;
				} else {
                	return false;
				}
			},
			buttons: $("#dosubmit"),
			onerror : "<?php echo L('modelname_already_exist')?>",
			onwait : "<?php echo L('connecting_please_wait')?>"
		});
	$("#tablename")
		.formValidator({
			onshow:"<?php echo L('input').L('table_name')?>",
			onfocus:"<?php echo L('table_name').L('format_incorrect')?>",
			oncorrect:"<?php echo L('table_name').L('format_right')?>"
		})
		.inputValidator({
			min:2,
			max:8,
			onerror:"<?php echo L('table_name').L('between_2_to_8')?>"
		})
		.regexValidator({
			regexp:"letter_l",
			datatype:"enum",
			onerror:"<?php echo L('table_name').L('format_incorrect')?>"
		})
		.ajaxValidator({
	    	type : "get",
			url : "?app=member&controller=member_model&action=public_checktablename_ajax",
			data :"",
			datatype : "html",
			async:'false',
			success : function(data){
            	if( data == "1" ) {
                	return true;
				} else {
                	return false;
				}
			},
			buttons: $("#dosubmit"),
			onerror : "<?php echo L('tablename_already_exist')?>",
			onwait : "<?php echo L('connecting_please_wait')?>"
		});
});
//-->
</script>
<div class="pad-10">
<div class="common-form">
<form name="myform" action="?app=member&controller=member_model&action=add" method="post" id="myform" enctype="multipart/form-data">
<fieldset>
	<legend><?php echo L('basic_configuration')?></legend>
	<table width="100%" class="table_form">
		<tr>
			<td width="80"><?php echo L('model_name')?></td>
			<td><input type="text" name="info[modelname]"  class="input-text" id="modelname" size="30"></input></td>
		</tr>
		<tr>
			<td><?php echo L('table_name')?></td>
			<td>
			<?php echo $this->db->db_tablepre?>member_<input type="text" name="info[tablename]" value="" class="input-text" id="tablename" size="16"></input>
			</td>
		</tr>
		<tr>
			<td><?php echo L('model_description')?></td>
			<td>
			<input type="text" name="info[description]" value="" class="input-text" id="description" size="80"></input>
			</td>
		</tr>
		<tr>
			<td><?php echo L('model_import')?></td>
			<td>
			<input type="file" name="model_import" value="" class="input-text" id="model_import"></input><?php echo L('create_new_model_can_empty')?>
			</td>
		</tr>
	</table>
</fieldset>
    <div class="bk15"></div>
    <input name="dosubmit" type="submit" id="dosubmit" value="<?php echo L('submit')?>" class="dialog">
</form>
</div>
</div>
</body>
</html>
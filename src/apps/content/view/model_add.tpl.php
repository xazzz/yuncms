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
		$("#name")
			.formValidator({
				onshow:"<?php echo L("input").L('model_name')?>",
				onfocus:"<?php echo L("input").L('model_name')?>",
				oncorrect:"<?php echo L('input_right');?>"
			})
			.inputValidator({
				min:1,
				onerror:"<?php echo L("input").L('model_name')?>"
			});
		$("#tablename")
			.formValidator({
				onshow:"<?php echo L("input").L('model_tablename')?>",
				onfocus:"<?php echo L("input").L('model_tablename')?>"
			})
			.regexValidator({
				regexp:"^([a-zA-Z0-9]|[_]){0,20}$",
				onerror:"<?php echo L("model_tablename_wrong");?>"
			})
			.inputValidator({
				min:1,
				onerror:"<?php echo L("input").L('model_tablename')?>"
			})
			.ajaxValidator({
				type : "get",
				url : "?app=content&controller=model&action=public_check_tablename",
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
				onerror : "<?php echo L('model_tablename').L('exists')?>",
				onwait : "<?php echo L('connecting')?>"
			});
	})
//-->
</script>
<div class="pad-lr-10">
<form action="<?php echo U('content/model/add');?>" method="post" id="myform">
<fieldset>
	<legend><?php echo L('basic_configuration')?></legend>
	<table width="100%"  class="table_form">
  <tr>
    <th width="120"><?php echo L('model_name')?>：</th>
    <td class="y-bg"><input type="text" class="input-text" name="info[name]" id="name" size="30" /></td>
  </tr>
  <tr>
    <th><?php echo L('model_tablename')?>：</th>
    <td class="y-bg"><input type="text" class="input-text" name="info[tablename]" id="tablename" size="30" /></td>
  </tr>
    <tr>
    <th><?php echo L('description')?>：</th>
    <td class="y-bg"><input type="text" class="input-text" name="info[description]" id="description"  size="30"/></td>
  </tr>
</table>
</fieldset>
<div class="bk15"></div>
<fieldset>
	<legend><?php echo L('template_setting')?></legend>
	<table width="100%"  class="table_form">
	<tr>
  <th width="200"><?php echo L('available_styles');?></th>
        <td>
		<?php echo Form::select($style_list, '', 'name="info[default_style]" id="default_style" onchange="load_file_list(this.value)"', L('please_select'))?>
		</td>
</tr>
		<tr>
        <th width="200"><?php echo L('category_index_tpl')?>：</th>
        <td  id="category_template">
		</td>
      </tr>
	  <tr>
        <th width="200"><?php echo L('category_list_tpl')?>：</th>
        <td  id="list_template">
		</td>
      </tr>
	  <tr>
        <th width="200"><?php echo L('content_tpl')?>：</th>
        <td  id="show_template">
		</td>
      </tr>
</table>
</fieldset>
<div class="bk15"></div>
    <input type="submit" class="dialog" id="dosubmit" name="dosubmit" value="<?php echo L('submit');?>" />
</form>
</div>
<script language="JavaScript">
<!--
	function load_file_list(id) {
		$.getJSON('?app=admin&controller=category&action=public_tpl_file_list&style='+id+'&catid=', function(data){$('#category_template').html(data.category_template);$('#list_template').html(data.list_template);$('#show_template').html(data.show_template);});
	}
	//-->
</script>
</body>
</html>
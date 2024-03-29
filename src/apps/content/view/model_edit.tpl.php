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
	})
//-->
</script>
<div class="pad-lr-10">
<form action="?app=content&controller=model&action=edit" method="post" id="myform">
<fieldset>
	<legend><?php echo L('basic_configuration')?></legend>
	<table width="100%"  class="table_form">
  <tr>
    <th width="120"><?php echo L('model_name')?>：</th>
    <td class="y-bg"><input type="text" class="input-text" name="info[name]" id="name" size="30" value="<?php echo $name;?>"/></td>
  </tr>
  <tr>
    <th><?php echo L('model_tablename')?>：</th>
    <td class="y-bg"><input type="text" class="input-text" name="info[tablename]" id="tablename" size="30" value="<?php echo $tablename;?>" disabled/></td>
  </tr>
    <tr>
    <th><?php echo L('description')?>：</th>
    <td class="y-bg"><input type="text" class="input-text" name="info[description]" id="description"  size="30" value="<?php echo $description;?>"/></td>
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
		<?php echo Form::select($style_list, $default_style, 'name="info[default_style]" id="template_list" onchange="load_file_list(this.value)"', L('please_select'))?>
</td>
</tr>
	<tr>
      <th width="200"><?php echo L('category_index_tpl');?></th>
      <td id="category_template">
      <?php echo Form::select_template($default_style,'content', $category_template, 'name="setting[category_template]" id="template_category"', 'category')?></td>
    </tr>
	<tr>
      <th ><?php echo L('category_list_tpl');?></th>
      <td id="list_template"><?php echo Form::select_template($default_style,'content', $list_template, 'name="setting[list_template]" id="template_list"', 'list')?></td>
    </tr>
	<tr>
      <th><?php echo L('content_tpl');?></th>
      <td id="show_template"><?php echo Form::select_template($default_style,'content', $show_template, 'name="setting[show_template]" id="template_show"','show')?></td>
    </tr>
</table>
</fieldset>
<div class="bk15"></div>
	<input type="hidden" name="modelid" value="<?php echo $modelid;?>" />
    <input type="submit" class="dialog" id="dosubmit" name="dosubmit" value="<?php echo L('submit');?>" />
</form>
</div>
<script language="JavaScript">
<!--
function load_file_list(id) {
	$.getJSON('?app=admin&controller=category&action=public_tpl_file_list&style='+id, function(data){$('#category_template').html(data.category_template);$('#list_template').html(data.list_template);$('#show_template').html(data.show_template);});
}
	//-->
</script>
</body>
</html>
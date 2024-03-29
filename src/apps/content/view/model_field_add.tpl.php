<?php
defined('IN_ADMIN') or exit('No permission resources.');
include $this->view('header','admin');
?>
<script type="text/javascript">
<!--
$(function(){
	$.formValidator.initConfig({
		autotip:true,
		formid:"myform"
	});
	$("#field")
		.formValidator({
			onshow:"<?php echo L('input').L('fieldname')?>",
			onfocus:"<?php echo L('fieldname').L('between_1_to_20')?>"
		})
		.regexValidator({
			regexp:"^[a-zA-Z]{1}([a-zA-Z0-9]|[_]){0,19}$",
			onerror:"<?php echo L('fieldname_was_wrong');?>"
		})
		.inputValidator({
			min:1,
			max:20,
			onerror:"<?php echo L('fieldname').L('between_1_to_20')?>"
		})
		.ajaxValidator({
	    	type : "get",
			url : "?app=content&controller=model_field&action=public_checkfield&modelid=<?php echo $modelid?>",
			data : "",
			datatype : "html",
			cached:false,
			getdata:{issystem:'issystem'},
			async:'false',
			success : function(data){
            	if( data == "1" ){
                	return true;
				} else {
                	return false;
				}
			},
			buttons: $("#dosubmit"),
			onerror : "<?php echo L('fieldname').L('already_exist')?>",
			onwait : "<?php echo L('connecting_please_wait')?>"
		});
	$("#formtype")
		.formValidator({
			onshow:"<?php echo L('select_fieldtype');?>",
			onfocus:"<?php echo L('select_fieldtype');?>",
			oncorrect:"<?php echo L('input_right');?>",
			defaultvalue:""
		})
		.inputValidator({
			min:1,
			onerror: "<?php echo L('select_fieldtype');?>"
		});
	$("#name")
		.formValidator({
			onshow:"<?php echo L('input_nickname');?>",
			onfocus:"<?php echo L('nickname_empty');?>",
			oncorrect:"<?php echo L('input_right');?>"
		})
		.inputValidator({
			min:1,
			onerror:"<?php echo L('input_nickname');?>"
		});
})

//-->
</script>
<div class="pad_10">
<div class="subnav">
  <h2 class="title-1 line-x f14 fb blue lh28"><?php echo L('model_manage');?>--<?php echo $m_r['name'].L('field_manage');?></h2>
	<div class="content-menu ib-a blue line-x">
	<a class="add fb" href="?app=content&controller=model_field&action=add&modelid=<?php echo $modelid?>&menuid=<?php echo $_GET['menuid']?>"><em><?php echo L('add_field');?></em></a>
　	<a href="?app=content&controller=model_field&action=init&modelid=<?php echo $modelid?>&menuid=<?php echo $_GET['menuid']?>"><em><?php echo L('manage_field');?></em></a><span>|</span></div>
  <div class="bk10"></div>
</div>
<form name="myform" id="myform" action="?app=content&controller=model_field&action=add" method="post">
<div class="common-form">

<table width="100%" class="table_form contentWrap">
	<tr>
      <th><strong><?php echo L('field_type');?></strong><br /></th>
      <td>
<?php echo Form::select($all_field,'','name="info[formtype]" id="formtype" onchange="javascript:field_setting(this.value);"',L('select_fieldtype'));?>
	  </td>
    </tr>
	<tr>
      <th><strong><?php echo L('issystem_field');?></strong></th>
      <td>
      <input type="hidden" name="issystem" id="issystem" value="0">
      <input type="radio" name="info[issystem]" id="field_basic_table1" value="1" onclick="$('#issystem').val('1');"> <?php echo L('yes');?> <input type="radio" id="field_basic_table0" name="info[issystem]" value="0" onclick="$('#issystem').val('0');" checked> <?php echo L('no');?></td>
    </tr>
	<tr>
      <th width="25%"><font color="red">*</font> <strong><?php echo L('fieldname');?></strong><br />
	  <?php echo L('fieldname_tips');?>
	  </th>
      <td><input type="text" name="info[field]" id="field" size="20" class="input-text"></td>
    </tr>
	<tr>
      <th><font color="red">*</font> <strong><?php echo L('field_nickname');?></strong><br /><?php echo L('nickname_tips');?></th>
      <td><input type="text" name="info[name]" id="name" size="30" class="input-text"></td>
    </tr>
	<tr>
      <th><strong><?php echo L('field_tip');?></strong><br /><?php echo L('field_tips');?></th>
      <td><textarea name="info[tips]" rows="2" cols="20" id="tips" style="height:40px; width:80%"></textarea></td>
    </tr>

	<tr>
      <th><strong><?php echo L('relation_parm');?></strong><br /><?php echo L('relation_parm_tips');?></th>
      <td><div id="setting"></div></td>
    </tr>

	<tr id="formattribute">
      <th><strong><?php echo L('form_attr');?></strong><br /><?php echo L('form_attr_tips');?></th>
      <td><input type="text" name="info[formattribute]" value="" size="50" class="input-text"></td>
    </tr>
	<tr id="css">
      <th><strong><?php echo L('form_css_name');?></strong><br /><?php echo L('form_css_name_tips');?></th>
      <td><input type="text" name="info[css]" value="" size="10" class="input-text"></td>
    </tr>

	<tr>
      <th><strong><?php echo L('string_size');?></strong><br /><?php echo L('string_size_tips');?></th>
      <td><?php echo L('minlength');?>：<input type="text" name="info[minlength]" id="field_minlength" value="0" size="5" class="input-text"> <?php echo L('maxlength');?>：<input type="text" name="info[maxlength]" id="field_maxlength" value="" size="5" class="input-text"></td>
    </tr>
	<tr>
      <th><strong><?php echo L('data_preg');?></strong><br /><?php echo L('data_preg_tips');?></th>
      <td><input type="text" name="info[pattern]" id="pattern" value="" size="40" class="input-text">
<select name="pattern_select" onchange="javascript:$('#pattern').val(this.value)">
<option value=""><?php echo L('often_preg');?></option>
<option value="/^[0-9.-]+$/"><?php echo L('figure');?></option>
<option value="/^[0-9-]+$/"><?php echo L('integer');?></option>
<option value="/^[a-z]+$/i"><?php echo L('letter');?></option>
<option value="/^[0-9a-z]+$/i"><?php echo L('integer_letter');?></option>
<option value="/^[\w\-\.]+@[\w\-\.]+(\.\w+)+$/">E-mail</option>
<option value="/^[0-9]{5,20}$/">QQ</option>
<option value="/^http:\/\//"><?php echo L('hyperlink');?></option>
<option value="/^(1)[0-9]{10}$/"><?php echo L('mobile_number');?></option>
<option value="/^[0-9-]{6,13}$/"><?php echo L('tel_number');?></option>
<option value="/^[0-9]{6}$/"><?php echo L('zip');?></option>
</select>
	  </td>
    </tr>
	<tr>
      <th><strong><?php echo L('data_passed_msg');?></strong></th>
      <td><input type="text" name="info[errortips]" value="" size="50" class="input-text"></td>
    </tr>
	<tr>
      <th><strong><?php echo L('unique');?></strong></th>
      <td><input type="radio" name="info[isunique]" value="1" id="field_allow_isunique1"> <?php echo L('yes');?> <input type="radio" name="info[isunique]" value="0" id="field_allow_isunique0" checked> <?php echo L('no');?></td>
    </tr>
	<tr>
      <th><strong><?php echo L('basic_field');?></strong><br /><?php echo L('basic_field_tips');?></th>
      <td><input type="radio" name="info[isbase]" value="1"  checked> <?php echo L('yes');?> <input type="radio" name="info[isbase]" value="0"> <?php echo L('no');?> </td>
    </tr>
	<tr>
      <th><strong><?php echo L('as_search_field');?></strong></th>
      <td><input type="radio" name="info[issearch]" value="1" id="field_allow_search1"> <?php echo L('yes');?> <input type="radio" name="info[issearch]" value="0" id="field_allow_search0" checked> <?php echo L('no');?></td>
    </tr>
	<tr>
      <th><strong><?php echo L('allow_contributor');?></strong></th>
      <td><input type="radio" name="info[isadd]" value="1" checked /> <?php echo L('yes');?> <input type="radio" name="info[isadd]" value="0" /> <?php echo L('no');?></td>
    </tr>
	<tr>
      <th><strong><?php echo L('as_fulltext_field');?></strong></th>
      <td><input type="radio" name="info[isfulltext]" value="1" id="field_allow_fulltext1" checked/> <?php echo L('yes');?> <input type="radio" name="info[isfulltext]" value="0" id="field_allow_fulltext0" /> <?php echo L('no');?></td>
    </tr>
	<tr>
      <th><strong><?php echo L('as_omnipotent_field');?></strong><br><?php echo L('as_omnipotent_field_tips');?></th>
      <td><input type="radio" name="info[isomnipotent]" value="1" /> <?php echo L('yes');?> <input type="radio" name="info[isomnipotent]" value="0" checked/> <?php echo L('no');?></td>
    </tr>
	<tr>
      <th><strong><?php echo L('as_postion_info');?></strong></th>
      <td><input type="radio" name="info[isposition]" value="1" /> <?php echo L('yes');?> <input type="radio" name="info[isposition]" value="0" checked/> <?php echo L('no');?></td>
    </tr>
		<tr>
      <th><strong><?php echo L('disabled_groups_field');?></strong></th>
      <td><?php echo Form::checkbox($grouplist,'','name="unsetgroupids[]" id="unsetgroupids"',0,'100');?></td>
    </tr>
	<tr>
      <th><strong><?php echo L('disabled_role_field');?></strong></th>
      <td><?php echo Form::checkbox($roles,'','name="unsetroleids[]" id="unsetroleids"',0,'100');?> </td>
    </tr>
</table>

</div>
    <div class="bk15"></div>
    <input name="info[modelid]" type="hidden" value="<?php echo $modelid?>">
    <div class="btn"><input name="dosubmit" type="submit" value="<?php echo L('submit')?>" class="button"></div>
	</form>

<script type="text/javascript">
	<!--
	function field_setting(fieldtype) {
	$('#formattribute').css('display','none');
	$('#css').css('display','none');
	$.each( ['<?php echo implode("','",$att_css_js);?>'], function(i, n){
		if(fieldtype==n) {
			$('#formattribute').css('display','');
			$('#css').css('display','');
		}
	});

		$.getJSON("?app=content&controller=model_field&action=public_field_setting&fieldtype="+fieldtype, function(data){
			if(data.field_basic_table=='1') {
				$('#field_basic_table0').attr("disabled",false);
				$('#field_basic_table1').attr("disabled",false);
			} else {
				$('#field_basic_table0').attr("checked",true);
				$('#field_basic_table0').attr("disabled",true);
				$('#field_basic_table1').attr("disabled",true);
			}
			if(data.field_allow_search=='1') {
				$('#field_allow_search0').attr("disabled",false);
				$('#field_allow_search1').attr("disabled",false);
			} else {
				$('#field_allow_search0').attr("checked",true);
				$('#field_allow_search0').attr("disabled",true);
				$('#field_allow_search1').attr("disabled",true);
			}
			if(data.field_allow_fulltext=='1') {
				$('#field_allow_fulltext0').attr("disabled",false);
				$('#field_allow_fulltext1').attr("disabled",false);
			} else {
				$('#field_allow_fulltext0').attr("checked",true);
				$('#field_allow_fulltext0').attr("disabled",true);
				$('#field_allow_fulltext1').attr("disabled",true);
			}
			if(data.field_allow_isunique=='1') {
				$('#field_allow_isunique0').attr("disabled",false);
				$('#field_allow_isunique1').attr("disabled",false);
			} else {
				$('#field_allow_isunique0').attr("checked",true);
				$('#field_allow_isunique0').attr("disabled",true);
				$('#field_allow_isunique1').attr("disabled",true);
			}
			$('#field_minlength').val(data.field_minlength);
			$('#field_maxlength').val(data.field_maxlength);
			$('#setting').html(data.setting);

		});
	}

//-->
</script>
</body>
</html>
<?php
defined('IN_ADMIN') or exit('No permission resources.');
include $this->view('header', 'admin');
if(ACTION=='manage') {?>
<form name="myform" action="?app=member&controller=member_menu&action=listorder" method="post">
<div class="pad-lr-10">
<div class="table-list">
    <table width="100%" cellspacing="0">
        <thead>
            <tr>
            <th width="80"><?php echo L('listorder');?></th>
            <th width="100">id</th>
            <th><?php echo L('menu_name');?></th>
			<th><?php echo L('operations_manage');?></th>
            </tr>
        </thead>
	<tbody>
    <?php echo $categorys;?>
	</tbody>
    </table>

    <div class="btn"><input type="submit" class="button" name="dosubmit" value="<?php echo L('listorder')?>" /></div>  </div>
</div>
</div>
</form>
</body>
</html>
<?php } elseif(ACTION=='add') {?>
<script type="text/javascript">
<!--
	$(function(){
		$.formValidator.initConfig({
			formid:"myform",
			autotip:true,
			onerror:function(msg,obj){
				window.top.art.dialog({
					content:msg,
					lock:true,width:'200',height:'50'},
					function(){this.close();$(obj).focus();})
			}
		});
		$("#language")
			.formValidator({
				onshow:"<?php echo L("input", '', 'admin').L('chinese_name')?>",
				onfocus:"<?php echo L("input").L('chinese_name')?>",
				oncorrect:"<?php echo L('input_right');?>"
			})
			.inputValidator({
				min:1,
				onerror:"<?php echo L("input").L('chinese_name')?>"
			});
		$("#name")
			.formValidator({
				onshow:"<?php echo L("input").L('menu_name')?>",
				onfocus:"<?php echo L("input").L('menu_name')?>",
				oncorrect:"<?php echo L('input_right');?>"
			})
			.inputValidator({
				min:1,
				onerror:"<?php echo L("input").L('menu_name')?>"
			});
		$("#application")
			.formValidator({
				onshow:"<?php echo L("input").L('application_name')?>",
				onfocus:"<?php echo L("input").L('application_name')?>",
				oncorrect:"<?php echo L('input_right');?>"
			})
			.inputValidator({
				min:1,
				onerror:"<?php echo L("input").L('application_name')?>"
			});
		$("#controller")
			.formValidator({
				onshow:"<?php echo L("input").L('controller_name')?>",
				onfocus:"<?php echo L("input").L('controller_name')?>",
				oncorrect:"<?php echo L('input_right');?>"
			})
			.inputValidator({
				min:1,
				onerror:"<?php echo L("input").L('controller_name')?>"
			});
		$("#action")
			.formValidator({
				tipid:'a_tip',
				onshow:"<?php echo L("input").L('action_name')?>",
				onfocus:"<?php echo L("input").L('action_name')?>",
				oncorrect:"<?php echo L('input_right');?>"
			})
			.inputValidator({
				min:1,
				onerror:"<?php echo L("input").L('action_name')?>"
			});
	})
//-->
</script>
<div class="common-form">
<form name="myform" id="myform" action="?app=member&controller=member_menu&action=add" method="post">
<table width="100%" class="table_form contentWrap">
      <tr>
        <th width="200"><?php echo L('menu_parentid')?>：</th>
        <td><select name="info[parentid]" >
        <option value="0"><?php echo L('no_parent_menu')?></option>

</select></td>
      </tr>
      <tr>
        <th> <?php echo L('chinese_name')?>：</th>
        <td><input type="text" name="language" id="language" class="input-text" ></td>
      </tr>

      <tr>
        <th><?php echo L('menu_name')?>：</th>
        <td><input type="text" name="info[name]" id="name" class="input-text" ></td>
      </tr>
<?php if(!isset($_GET['isurl']) || (isset($_GET['isurl']) && $_GET['isurl']==0)) {?>
	<tr>
        <th><?php echo L('application_name')?>：</th>
        <td><input type="text" name="info[application]" id="application" class="input-text" ></td>
      </tr>
	<tr>
        <th><?php echo L('controller_name')?>：</th>
        <td><input type="text" name="info[controller]" id="controller" class="input-text" ></td>
      </tr>
	<tr>
        <th><?php echo L('action_name')?>：</th>
        <td><input type="text" name="info[action]" id="action" class="input-text" > <span id="a_tip"></span><?php echo L('ajax_tip')?></td>
      </tr>
	<tr>
        <th><?php echo L('att_data')?>：</th>
        <td><input type="text" name="info[data]" class="input-text" ></td>
      </tr>
<?php }?>
	<tr>
        <th><?php echo L('menu_display')?>：</th>
        <td><input type="radio" name="info[display]" value="1" checked> <?php echo L('yes')?><input type="radio" name="info[display]" value="0"> <?php echo L('no')?></td>
      </tr>

	<tr>
        <th><?php echo L('isurl')?>：</th>
        <td><input type="radio" name="info[isurl]" value="1" onclick="redirect('<?php echo Base_Request::get_url().'&isurl=1';?>')" <?php if(isset($_GET['isurl']) && $_GET['isurl']==1) echo 'checked';?>> <?php echo L('yes')?><input type="radio" name="info[isurl]" value="0" <?php if(!isset($_GET['isurl']) || (isset($_GET['isurl']) && $_GET['isurl']==0)) echo 'checked';?> onclick="redirect('<?php echo Base_Request::get_url().'&isurl=0';?>')"> <?php echo L('no')?></td>
      </tr>
<?php if(isset($_GET['isurl']) && $_GET['isurl']==1) {?>
	<tr>
		<th><?php echo L('url')?>：</th>
		<td><input type="text" name="info[url]" class="input-text" size=80></td>
	</tr>
<?php }?>
</table>
<!--table_form_off-->
</div>
    <div class="bk15"></div>
	<div class="btn"><input type="submit" id="dosubmit" class="button" name="dosubmit" value="<?php echo L('submit')?>"/></div>
</div>

</form>

<?php } elseif(ACTION=='edit') {?>
<script type="text/javascript">
<!--
	$(function(){
		$.formValidator.initConfig({
			formid:"myform",
			autotip:true,
			onerror:function(msg,obj){
				window.top.art.dialog({
					content:msg,lock:true,width:'200',height:'50'}, functi
					on(){this.close();$(obj).focus();})}});
		$("#language")
			.formValidator({
				onshow:"<?php echo L("input", '', 'admin').L('chinese_name')?>",
				onfocus:"<?php echo L("input").L('chinese_name')?>",
				oncorrect:"<?php echo L('input_right');?>"
			})
			.inputValidator({
				min:1,
				onerror:"<?php echo L("input").L('chinese_name')?>"
			});
		$("#name")
			.formValidator({
				onshow:"<?php echo L("input").L('menu_name')?>",
				onfocus:"<?php echo L("input").L('menu_name')?>",
				oncorrect:"<?php echo L('input_right');?>"
			})
			.inputValidator({
				min:1,
				onerror:"<?php echo L("input").L('menu_name')?>"
			});
		$("#application")
			.formValidator({
				onshow:"<?php echo L("input").L('application_name')?>",
				onfocus:"<?php echo L("input").L('application_name')?>",
				oncorrect:"<?php echo L('input_right');?>"
			})
			.inputValidator({
				min:1,
				onerror:"<?php echo L("input").L('application_name')?>"
			});
		$("#controller")
			.formValidator({
				onshow:"<?php echo L("input").L('controller_name')?>",
				onfocus:"<?php echo L("input").L('controller_name')?>",
				oncorrect:"<?php echo L('input_right');?>"
			})
			.inputValidator({
				min:1,
				onerror:"<?php echo L("input").L('file_name')?>"
			});
		$("#action")
			.formValidator({
				tipid:'a_tip',
				onshow:"<?php echo L("input").L('action_name')?>",
				onfocus:"<?php echo L("input").L('action_name')?>",
				oncorrect:"<?php echo L('input_right');?>"
			})
			.inputValidator({
				min:1,
				onerror:"<?php echo L("input").L('action_name')?>"
			});
	})
//-->
</script>
<div class="common-form">
<form name="myform" id="myform" action="?app=member&controller=member_menu&action=edit" method="post">
<table width="100%" class="table_form contentWrap">
      <tr>
        <th width="200"><?php echo L('menu_parentid')?>：</th>
        <td><select name="info[parentid]" style="width:200px;">
 <option value="0"><?php echo L('no_parent_menu')?></option>

</select></td>
      </tr>
      <tr>
        <th> <?php echo L('for_chinese_lan')?>：</th>
        <td><input type="text" name="language" id="language" class="input-text" value="<?php echo L($name,'','',1)?>"></td>
      </tr>
      <tr>
        <th><?php echo L('menu_name')?>：</th>
        <td><input type="text" name="info[name]" id="name" class="input-text" value="<?php echo $name?>"></td>
      </tr>
<?php if(empty($isurl)) {?>
	<tr>
        <th><?php echo L('application_name')?>：</th>
        <td><input type="text" name="info[application]" id="application" class="input-text" value="<?php echo $application?>"></td>
      </tr>
	<tr>
        <th><?php echo L('controller_name')?>：</th>
        <td><input type="text" name="info[controller]" id="controller" class="input-text" value="<?php echo $controller?>"></td>
      </tr>
	<tr>
        <th><?php echo L('action_name')?>：</th>
        <td><input type="text" name="info[action]" id="action" class="input-text" value="<?php echo $action?>">  <span id="a_tip"></span><?php echo L('ajax_tip')?></td>
      </tr>
	<tr>
        <th><?php echo L('att_data')?>：</th>
        <td><input type="text" name="info[data]" class="input-text" value="<?php echo $data?>"></td>
      </tr>
<?php }?>
	<tr>
        <th><?php echo L('menu_display')?>：</th>
        <td><input type="radio" name="info[display]" value="1" <?php if($display) echo 'checked';?>> <?php echo L('yes')?><input type="radio" name="info[display]" value="0" <?php if(!$display) echo 'checked';?>> <?php echo L('no')?></td>
      </tr>

	<tr>
        <th><?php echo L('isurl')?>：</th>
        <td>
		<?php if($isurl) {?>
			<input type="radio" name="info[isurl]" value="1" checked> <?php echo L('yes')?>
		<?php } else {?>
			<input type="radio" name="info[isurl]" value="0" checked> <?php echo L('no')?>
		<?php }?>
		</td>
      </tr>
<?php if((isset($_GET['isurl']) && $_GET['isurl']==1) || $isurl) {?>
	<tr>
		<th><?php echo L('url')?>：</th>
		<td><input type="text" name="info[url]" class="input-text" size=80 value="<?php echo $url?>"></td>
	</tr>
<?php }?>
</table>
<!--table_form_off-->
</div>
    <div class="bk15"></div>
	<input name="id" type="hidden" value="<?php echo $id?>">
    <div class="btn"><input type="submit" id="dosubmit" class="button" name="dosubmit" value="<?php echo L('submit')?>"/></div>
</div>

</form>
<?php }?>
</body>
</html>
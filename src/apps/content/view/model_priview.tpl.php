<?php
defined('IN_ADMIN') or exit('No permission resources.');
$addbg=1;
include $this->view('header','admin');
?>
<script type="text/javascript">
<!--
	var charset = '<?php echo CHARSET;?>';
	var uploadurl = '<?php echo C('attachment','upload_url')?>';
//-->
</script>
<script language="javascript" type="text/javascript" src="<?php echo JS_PATH?>content_addtop.js"></script>
<script language="javascript" type="text/javascript" src="<?php echo JS_PATH?>colorpicker.js"></script>
<script language="javascript" type="text/javascript" src="<?php echo JS_PATH?>hotkeys.js"></script>
<script type="text/javascript">var catid=<?php echo isset($catid) ? $catid : 0 ;?></script>
<div class="addContent">
<div class="crumbs"><?php echo L('priview_model_position');?><?php echo $r['name'];?></div>
<div class="col-right">
    	<div class="col-1">
        	<div class="content pad-6">
<?php
if(isset($forminfos['senior']) && is_array($forminfos['senior'])) {
 foreach($forminfos['senior'] as $field=>$info) {
	if($info['isomnipotent']) continue;
	if($info['formtype']=='omnipotent') {
		foreach($forminfos['base'] as $_fm=>$_fm_value) {
			if($_fm_value['isomnipotent']) {
				$info['form'] = str_replace('{'.$_fm.'}',$_fm_value['form'],$info['form']);
			}
		}
		foreach($forminfos['senior'] as $_fm=>$_fm_value) {
			if($_fm_value['isomnipotent']) {
				$info['form'] = str_replace('{'.$_fm.'}',$_fm_value['form'],$info['form']);
			}
		}
	}
 ?>
	<h6><?php if($info['star']){ ?> <font color="red">*</font><?php } ?> <?php echo $info['name']?></h6>
	 <?php echo $info['form']?><?php echo $info['tips']?>
<?php
} }
?>
<?php if($_SESSION['roleid']==1) {?>
<h6><?php echo L('c_status');?></h6>
<span class="ib" style="width:90px"><label><input type="radio" name="status" value="99" checked/> <?php echo L('c_publish');?> </label></span>
<?php if(isset($workflowid)) { ?><label><input type="radio" name="status" value="1" > <?php echo L('c_check');?> </label><?php }?>
<?php }?>
          </div>
        </div>
    </div>
    <div class="col-auto">
    	<div class="col-1">
        	<div class="content pad-6">
<table width="100%" cellspacing="0" class="table_form">
	<tbody>
<?php
if(isset($forminfos['base']) && is_array($forminfos['base'])) {
 foreach($forminfos['base'] as $field=>$info) {
	 if($info['isomnipotent']) continue;
	 if($info['formtype']=='omnipotent') {
		foreach($forminfos['base'] as $_fm=>$_fm_value) {
			if($_fm_value['isomnipotent']) {
				$info['form'] = str_replace('{'.$_fm.'}',$_fm_value['form'],$info['form']);
			}
		}
		foreach($forminfos['senior'] as $_fm=>$_fm_value) {
			if($_fm_value['isomnipotent']) {
				$info['form'] = str_replace('{'.$_fm.'}',$_fm_value['form'],$info['form']);
			}
		}
	}
 ?>
	<tr>
      <th width="80"><?php if($info['star']){ ?> <font color="red">*</font><?php } ?> <?php echo $info['name']?>
	  </th>
      <td><?php echo $info['form']?>  <?php echo $info['tips']?></td>
    </tr>
<?php
} }
?>

    </tbody></table>
                </div>
        	</div>
        </div>

    </div>
</div>

<div class="fixed-bottom">
	<div class="fixed-but text-c">
    <div class="button"><input value="<?php echo L('save_close');?>" type="submit" name="dosubmit" class="cu" style="width:145px;"></div>
    <div class="button"><input value="<?php echo L('save_continue');?>" type="submit" name="dosubmit_continue" class="cu" style="width:130px;" title="Alt+X"></div>
    <div class="button"><input value="<?php echo L('c_close');?>" type="button" name="close" onclick="close_window()" class="cu" style="width:70px;"></div>
      </div>
</div>

</body>
</html>
<script type="text/javascript">
<!--
//只能放到最下面
$(function(){
	$.formValidator.initConfig({
		formid:"myform",
		autotip:true,
		onerror:function(msg,obj){
			window.top.art.dialog.alert(msg);
			$(obj).focus();
			boxid = $(obj).attr('id');
			if($('#'+boxid).attr('boxid')!=undefined) {
				check_content(boxid);
			}
		}
	});
	<?php echo $formValidator;?>

/*
 * 加载禁用外边链接
 */

	$('#linkurl').attr('disabled',true);
	$('#islink').attr('checked',false);
	$('.edit_content').hide();
	jQuery(document).bind('keydown', 'Alt+x', function (){close_window();});
})
document.title='<?php echo L('priview_modelfield');?>';
//-->
</script>
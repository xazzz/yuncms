<?php
defined('IN_ADMIN') or exit('No permission resources.');
include $this->view('header', 'admin');
?>
<div class="pad_10">
<div class="table-list">
<form action="" method="get">
<input type="hidden" name="app" value="dbsource" />
<input type="hidden" name="controller" value="data" />
<input type="hidden" name="action" value="del" />
    <table width="100%" cellspacing="0">
        <thead>
		<tr>
		<th width="80"><input type="checkbox" value="" id="check_box" onclick="selectall('id[]');"></th>
		<th><?php echo L('name')?></th>
		<th><?php echo L('output_mode')?></th>
		<th><?php echo L('stdcall')?></th>
		<th><?php echo L('data_call')?></th>
		<th width="150"><?php echo L('operations_manage')?></th>
		</tr>
        </thead>
        <tbody>
<?php
if(is_array($list)):
	foreach($list as $v):
?>
<tr>
<td width="80" align="center"><input type="checkbox" value="<?php echo $v['id']?>" name="id[]"></td>
<td align="center"><?php echo $v['name']?></td>
<td align="center"><?php switch($v['dis_type']){case 1:echo 'json';break;case 2:echo 'xml';break;case 3:echo 'js';break;}?></td>
<td align="center"><?php switch($v['type']){case 0:echo L('model_configuration');break;case 1:echo L('custom_sql');break;}?></td>
<td align="center"><input type="text" ondblclick="copy_text(this)" value="<?php if($v['dis_type']==3){ echo  htmlspecialchars('<script type="text/javascript" src="'.SITE_URL.'index.php?app=dbsource&controller=call&action=get&id='.$v['id'].'"></script>')?><?php } else { echo SITE_URL?>index.php?app=dbsource&controller=call&action=get&id=<?php echo $v['id']?><?php }?>" size="30" /></td>
<td align="center"><a href="javascript:edit(<?php echo $v['id']?>, '<?php echo htmlspecialchars(String::addslashes($v['name']))?>')"><?php echo L('edit')?></a> | <a href="?app=dbsource&controller=data&action=del&id=<?php echo $v['id']?>" onclick="return confirm('<?php echo htmlspecialchars(String::addslashes(L('confirm', array('message'=>$v['name']))))?>')"><?php echo L('delete')?></a></td>
</tr>
<?php
	endforeach;
endif;
?>
</tbody>
</table>
<div class="btn">
<label for="check_box"><?php echo L('select_all')?>/<?php echo L('cancel')?></label> <input type="submit" class="button" name="dosubmit" value="<?php echo L('delete')?>" onclick="return confirm('<?php echo L('sure_deleted')?>')"/>
</div>
</from>
</div>
</div>
<div id="pages"><?php echo $pages?></div>
<script type="text/javascript">
<!--
function edit(id, name) {
	window.top.art.dialog.open('?app=dbsource&controller=data&action=edit&id='+id,{
		title:'<?php echo L('editing_data_sources_call')?>《'+name+'》',
		id:'edit',
		width:'700px',
		height:'500px',
		ok: function(iframeWin, topWin){
			var form = iframeWin.document.getElementById('dosubmit');
			form.click();
			return false;
		},
		   cancel: function(){}
	});
}

function copy_text(matter){
	matter.select();
	js1=matter.createTextRange();
	js1.execCommand("Copy");
	alert('<?php echo L('copy_code');?>');
}

//-->
</script>
</body>
</html>
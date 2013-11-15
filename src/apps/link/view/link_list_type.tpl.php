<?php
defined('IN_ADMIN') or exit('No permission resources.');
$show_dialog = 1;
include $this->view('header', 'admin');
?>
<div class="pad-lr-10">
<form name="myform" action="?app=link&controller=link&action=delete_type" method="post" onsubmit="checkuid();return false;">
<div class="table-list">
<table width="100%" cellspacing="0">
	<thead>
		<tr>
			<th width="35" align="center"><input type="checkbox" value="" id="check_box" onclick="selectall('typeid[]');"></th>
			<th width="80"><?php echo L('link_type_listorder')?></th>
			<th><?php echo L('type_name')?></th>
			<th width="12%" align="center"><?php echo L('type_id')?></th>
			<th width="20%" align="center"><?php echo L('operations_manage')?></th>
		</tr>
	</thead>
<tbody>
<tr>
		<td align="center" width="35"><input type="checkbox"
			name="typeid[]" value="0" disabled></td>
		<td align="center"><input name='listorders[0]' type='text' size='3' value='0' class="input_center"></td>
		<td>默认分类</td>
		<td align="center" width="12%"> 0</td>
		 <td align="center" width="20%" style="color: #999"> 修改  |  删除</td>
	</tr>
<?php
if(is_array($infos)){
	foreach($infos as $info){
?>
	<tr>
		<td align="center" width="35"><input type="checkbox"
			name="typeid[]" value="<?php echo $info['typeid']?>"></td>
		<td align="center"><input name='listorders[<?php echo $info['typeid']?>]' type='text' size='3' value='<?php echo $info['listorder']?>' class="input_center"></td>
		<td><?php echo $info['name']?></td>
		<td align="center" width="12%"> <?php echo $info['typeid'];?></td>
		 <td align="center" width="20%"><a href="###"
			onclick="edit(<?php echo $info['typeid']?>, '<?php echo String::addslashes($info['name'])?>')"
			title="<?php echo L('edit')?>"><?php echo L('edit')?></a> |  <a
			href='?app=link&controller=link&action=delete_type&typeid=<?php echo $info['typeid']?>'
			onClick="return confirm('<?php echo L('confirm', array('message' => String::addslashes($info['name'])))?>')"><?php echo L('delete')?></a>
		</td>
	</tr>
	<?php
	}
}
?>
</tbody>
</table>
<div class="btn"><a href="#"
	onClick="javascript:$('input[type=checkbox]').attr('checked', true)"><?php echo L('selected_all')?></a>/<a
	href="#"
	onClick="javascript:$('input[type=checkbox]').attr('checked', false)"><?php echo L('cancel')?></a>
<input name="submit" type="submit" class="button"
	value="<?php echo L('remove_all_selected')?>"
	onClick="return confirm('<?php echo L('confirm', array('message' => L('selected')))?>')">&nbsp;&nbsp;</div>
</form>
</div>
</body>
</html>
<script type="text/javascript">
function edit(id, name) {
	window.top.art.dialog.open('?app=link&controller=link&action=edit_type&typeid='+id,{
		title:'<?php echo L('edit')?> '+name+' ',
		id:'edit',
		width:'450px',
		height:'280px',
		ok: function(iframeWin, topWin){
			var form = iframeWin.document.getElementById('dosubmit');
			form.click();
			return false;
		},
		cancel: function(){}
	});
}
function checkuid() {
	var ids='';
	$("input[name='typeid[]']:checked").each(function(i, n){
		ids += $(n).val() + ',';
	});
	if(ids=='') {
		window.top.art.dialog.alert("<?php echo L('before_select_operations')?>");
		return false;
	} else {
		myform.submit();
	}
}
</script>

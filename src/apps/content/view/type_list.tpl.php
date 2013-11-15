<?php
defined('IN_ADMIN') or exit('No permission resources.');
include $this->view('header','admin');
?>
<form name="myform" action="<?php echo U('content/type/listorder');?>" method="post">
<div class="pad_10">
<div class="table-list">
    <table width="100%" cellspacing="0" >
        <thead>
	<tr>
	<th width="5%"><?php echo L('listorder');?></td>
	<th width="5%">ID</th>
	<th width="20%"><?php echo L('type_name');?></th>
	<th width="*"><?php echo L('description');?></th>
	<th width="30%"><?php echo L('operations_manage');?></th>
	</tr>
        </thead>
    <tbody>


<?php
foreach($datas as $r) {
?>
<tr>
<td align="center"><input type="text" name="listorders[<?php echo $r['typeid']?>]" value="<?php echo $r['listorder']?>" size="3" class='input-text-c'></td>
<td align="center"><?php echo $r['typeid']?></td>
<td align="center"><?php echo $r['name']?></td>
<td ><?php echo $r['description']?></td>
<td align="center"><a href="javascript:edit('<?php echo $r['typeid']?>','<?php echo trim(String::addslashes($r['name']))?>')"><?php echo L('edit');?></a> | <a href="javascript:;" onclick="data_delete(this,'<?php echo $r['typeid']?>','<?php echo trim(String::addslashes($r['name']));?>')"><?php echo L('delete')?></a> </td>
</tr>
<?php } ?>
	</tbody>
    </table>

    <div class="btn"><input type="submit" class="button" name="dosubmit" value="<?php echo L('listorder')?>" /></div>  </div>
	<div id="pages"><?php echo $pages;?></div>
</div>

</div>
</form>

<script type="text/javascript">
<!--
window.top.$('#display_center_id').css('display','none');
function edit(id, name) {
	window.top.art.dialog.open('?app=content&controller=type&action=edit&typeid='+id,{
		title:'<?php echo L('edit_type');?>《'+name+'》',
		id:'edit',
		width:'780px',
		height:'500px',
		ok: function(iframeWin, topWin){
			var form = iframeWin.document.getElementById('dosubmit');
			form.click();
			return false;
		},
		cancel: function(){}
	});
}
function data_delete(obj,id,name){
	window.top.art.dialog({content:name, fixed:true, style:'confirm', id:'data_delete'},
	function(){
	$.get('?app=content&controller=type&action=delete&typeid='+id,function(data){
				if(data) {
					$(obj).parent().parent().fadeOut("slow");
				}
			})
		 },
	function(){});
};
//-->
</script>
</body>
</html>

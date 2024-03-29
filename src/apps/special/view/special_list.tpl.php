<?php
defined('IN_ADMIN') or exit('No permission resources.');
include $this->view('header', 'admin');
?>
<div class="pad-lr-10">
<form name="myform" action="?app=special&controller=special&action=listorder" method="post">
    <table width="100%" cellspacing="0" class="table-list nHover">
        <thead>
            <tr>
            <th width="40"><input type="checkbox" value="" id="check_box" onclick="selectall('id[]');"></th>
			<th width="40" align="center">ID</th>
			<th width="80" align="center"><?php echo L('listorder')?></th>
			<th ><?php echo L('special_info')?></th>
			<th width="160"><?php echo L('operations_manage')?></th>
            </tr>
        </thead>
        <tbody>
 <?php
if(is_array($infos)){
	foreach($infos as $info){
?>
	<tr>
	<td align="center" width="40"><input class="inputcheckbox" name="id[]" value="<?php echo $info['id'];?>" type="checkbox"></td>
	<td width="40" align="center"><?php echo $info['id']?></td>
	<td width="80" align="center"><input type='text' name='listorder[<?php echo $info['id']?>]' value="<?php echo $info['listorder']?>" class="input-text-c" size="4"></td>
	<td>
    <div class="col-left mr10" style="width:146px; height:112px">
<a href="<?php echo $info['url']?>" target="_blank"><img src="<?php echo $info['thumb']?>" width="146" height="112" style="border:1px solid #eee" align="left"></a>
</div>
<div class="col-auto">
    <h2 class="title-1 f14 lh28 mb6 blue"><a href="<?php echo $info['url']?>" target="_blank"><?php echo $info['title']?></a></h2>
    <div class="lh22"><?php echo $info['description']?></div>
<p class="gray4"><?php echo L('create_man')?>：<a href="#" class="blue"><?php echo $info['username']?></a>， <?php echo L('create_time')?>：<?php echo Format::date($info['createtime'], 1)?></p>
</div>
	</td>
	<td align="center"><span style="height:22"><a href='?app=special&controller=content&action=init&specialid=<?php echo $info['id']?>' onclick="javascript:openwinx('?app=special&controller=content&action=add&specialid=<?php echo $info['id']?>','')"><?php echo L('add_news')?></a></span> |
<span style="height:22"><a href='javascript:import_c(<?php echo $info['id']?>);void(0);'><?php echo L('import_news')?></a></span><br />
<span style="height:22"><a href='?app=special&controller=content&action=init&specialid=<?php echo $info['id']?>'><?php echo L('manage_news')?></a></span> |
<span style="height:22"><a href='?app=special&controller=template&specialid=<?php echo $info['id']?>' style="color:red" target="_blank"><?php echo L('template_manage')?></a></span><br/>
<span style="height:22"><a href='?app=special&controller=special&action=elite&value=<?php if($info['elite']==0) {?>1<?php } elseif($info['elite']==1) { ?>0<?php }?>&id=<?php echo $info['id']?>'><?php if($info['elite']==0) { echo L('elite_special'); } else {?><font color="red"><?php echo L('remove_elite')?></font><?php }?></a></span> |
<span style="height:22"><a href="javascript:comment('<?php echo id_encode('special', $info['id'])?>', '<?php echo addslashes(htmlspecialchars($info['title']))?>');void(0);"><?php echo L('special_comment')?></a></span><br/>
<span style="height:22"><a href="?app=special&controller=special&action=edit&specialid=<?php echo $info['id']?>&menuid=<?php echo $_GET['menuid']?>"><?php echo L('edit_special')?></a></span> |
<span style="height:22"><a href="?app=special&controller=special&action=delete&id=<?php echo $info['id']?>" onclick="return confirm('<?php echo L('confirm', array('message'=>addslashes(htmlspecialchars($info['title']))))?>')"><?php echo L('del_special')?></a></span></td>
	</tr>
<?php
	}
}
?>
</tbody>
    </table>

    <div class="btn"><label for="check_box"><?php echo L('selected_all')?>/<?php echo L('cancel')?></label>
        <input name='dosubmit' type='submit' class="button" value='<?php echo L('listorder')?>'>&nbsp;
        <input type="submit" class="button" value="<?php echo L('delete')?>" onclick="if(confirm('<?php echo L('confirm', array('message' => L('selected')))?>')){document.myform.action='?app=special&controller=special&action=delete';}else{return false;}"/>
        &nbsp;<input type="submit" class="button" value="<?php echo L('update')?>html" onclick="document.myform.action='?app=special&controller=special&action=html'"/></div>
 <div id="pages"><?php echo $this->db->pages;?></div><script>window.top.$("#display_center_id").css("display","none");</script>
</form>
</div>
<script type="text/javascript">
<!--
function comment(id, name) {
	window.top.art.dialog.open('?app=comment&controller=comment_admin&action=lists&show_center_id=1&commentid='+id,{
		title:'<?php echo L('see_comment')?>',
		id:'comment',
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
function import_c(id) {
	window.top.art.dialog.open('?app=special&controller=special&action=import&specialid='+id,{
		title:'<?php echo L('import_news')?>',
		id:'import',
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
</script>
</body>
</html>
<?php
defined('IN_ADMIN') or exit('No permission resources.');
include $this->view('header', 'admin');
?>
<div class="subnav">
  <h2 class="title-1 line-x f14 fb blue lh28"><?php echo L('ad_list')?></h2>
<div class="content-menu ib-a blue line-x">
　 <?php if(isset($big_menu)) echo '<a class="add fb" href="'.$big_menu[0].'"><em>'.$big_menu[1].'</em></a>　';?><a class="on" href="?app=poster&controller=space"><em><?php echo L('space_list')?></em></a></div>
</div>
<div class="pad-lr-10">
<form name="myform" action="?app=poster&controller=poster&action=listorder" method="post">
<div class="table-list">
    <table width="100%" cellspacing="0" class="contentWrap">
        <thead>
            <tr>
            <th width="30" align="center"><input type="checkbox" value="" id="check_box" onclick="selectall('id[]');"></th>
			<th width="35">ID</th>
			<th width="70"><?php echo L('listorder')?></th>
			<th align="center"><?php echo L('poster_title')?></th>
			<th width="70" align="center"><?php echo L('poster_type')?></th>
			<th width='200' align="center"><?php echo L('for_postion')?></th>
			<th width="50" align="center"><?php echo L('status')?></th>
			<th width='50' align="center"><?php echo L('hits')?></th>
			<th width="130" align="center"><?php echo L('addtime')?></th>
			<th width="110" align="center"><?php echo L('operations_manage')?></th>
            </tr>
        </thead>
        <tbody>
 <?php
if(is_array($infos)){
	foreach($infos as $info){
		$space = $this->s_db->field( 'name')->where(array('spaceid'=>$info['spaceid']))->find();
?>
	<tr>
	<td align="center">
	<input type="checkbox" name="id[]" value="<?php echo $info['id']?>">
	</td>
	<td align="center"><?php echo $info['id']?></td>
	<th width="70"><input type="text" size="5" name="listorder[<?php echo $info['id']?>]" value="<?php echo $info['listorder']?>" id="listorder"></th>
	<td><?php echo $info['name']?></td>
	<td align="center"><?php echo $types[$info['type']]?></td>
	<td align="center"><?php echo $space['name']?></td>
	<td align="center"><?php if($info['disabled']) { echo L('stop'); } elseif((strtotime($info['enddate'])<TIME) && (strtotime($info['enddate'])>0)) { echo L('past'); } else { echo L('start'); }?></td>
	<td align="center"><?php echo $info['clicks']?></td>
	<td align="center"><?php echo Format::date($info['addtime'], 1);?></td>
	<td align="center"><a href="###" onclick="edit(<?php echo $info['id']?>, '<?php echo $info['name']?>')" title="<?php echo L('edit')?>" ><?php echo L('edit')?></a>|<a href="?app=poster&controller=poster&action=stat&id=<?php echo $info['id']?>&spaceid=<?php echo $_GET['spaceid'];?>"><?php echo L('stat')?></a></td>
	</tr>
<?php
	}
}
?>
</tbody>
    </table>

    <div class="btn"><label for="check_box"><?php echo L('selected_all')?>/<?php echo L('cancel')?></label>
    	<input name='submit' type='submit' class="button" value='<?php echo L('listorder')?>'>&nbsp;
        <input name='submit' type='submit' class="button" value='<?php echo L('start')?>' onClick="document.myform.action='?app=poster&controller=poster&action=public_approval&passed=0'">&nbsp;
        <input name='submit' type='submit' class="button" value='<?php echo L('stop')?>' onClick="document.myform.action='?app=poster&controller=poster&action=public_approval&passed=1'">&nbsp;
		<input name="submit" type="submit" class="button" value="<?php echo L('delete')?>" onClick="document.myform.action='?app=poster&controller=poster&action=delete';return confirm('<?php echo L('confirm', array('message' => L('selected')))?>')">&nbsp;&nbsp;</div>  </div>
 <div id="pages"><?php echo $this->db->pages;?></div>
</form>
</div>
<script type="text/javascript">
<!--
	function edit(id, name) {
	window.top.art.dialog.open('?app=poster&controller=poster&action=edit&id='+id ,{
		title:'<?php echo L('edit_ads')?>--'+name,
		id:'edit',
		width:'600px',
		height:'430px',
		ok: function(iframeWin, topWin){
			var form = iframeWin.document.getElementById('dosubmit');
			form.click();
			return false;
		},
    	cancel: function(){}
    });
}
//-->
</script>
</body>
</html>
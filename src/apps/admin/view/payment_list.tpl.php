<?php
defined ( 'IN_ADMIN' ) or exit ( 'No permission resources.' );
$show_dialog = 1;
include $this->view ( 'header', 'admin' );
?>
<div class="pad_10">
	<div class="table-list">
		<table width="100%" cellspacing="0">
			<thead>
				<tr>
					<th align="left"><?php echo L('payment_mode').L('name')?></th>
					<th width="40%"><?php echo L('desc')?></th>
					<th><?php echo L('plus_version')?></th>
					<th><?php echo L('plus_author')?></th>
					<th><?php echo L('short_pay_fee')?></th>
					<th><?php echo L('listorder')?></th>
					<th><?php echo L('operations_manage')?></th>
				</tr>
			</thead>
			<tbody>
 <?php
	if (is_array ( $infos ['data'] )) {
		foreach ( $infos ['data'] as $info ) {
			?>
	<tr>
					<td><?php echo $info['pay_name']?></td>
					<td><?php echo $info['pay_desc']?></td>
					<td align="center"><?php echo $info['version']?></td>
					<td align="center">
						<a href="<?php echo $info['website']?>" target="_blank"><?php echo $info['author']?></a>
					</td>
					<td align="center"><?php echo $info['pay_fee']?></td>
					<td align="center"><?php echo $info['pay_order']?></td>
					<td align="center">
	<?php if ($info['enabled']) {?>
	<a
							href="javascript:edit('<?php echo $info['pay_code']?>', '<?php echo $info['pay_name']?>')"><?php echo L('edit')?></a>
						|
						<a
							href="<?php echo art_confirm(L('confirm',array('message'=>$info['pay_name'])), '?app=admin&controller=payment&action=delete&id='.$info['id'])?>"><?php echo L('plus_uninstall')?></a>
	<?php } else {?>
	<a
							href="javascript:add('<?php echo $info['pay_code']?>', '<?php echo $info['pay_name']?>')"><?php echo L('plus_install')?></a>
	<?php }?>
	</td>
				</tr>
<?php
		}
	}
	?>
    </tbody>
		</table>

		<div class="btn"></div>
	</div>

	<div id="pages"> <?php echo $pages?></div>
</div>
</div>
</body>
</html>
<script type="text/javascript">
<!--
	function add(id, name) {
		window.top.art.dialog.open('?app=admin&controller=payment&action=add&code='+id ,{
			title:'<?php echo L('add')?>--'+name,
			id:'add',
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
	function edit(id, name) {
		window.top.art.dialog.open('?app=admin&controller=payment&action=edit&code='+id ,{
			title:'<?php echo L('edit')?>--'+name,
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
//-->
</script>
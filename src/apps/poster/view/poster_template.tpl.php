<?php
defined('IN_ADMIN') or exit('No permission resources.');
$show_dialog = $show_header = 1;
include $this->view('header', 'admin');
?>
<div class="subnav">
    <div class="content-menu ib-a blue line-x">
    <?php if(isset($big_menu)) echo '<a class="add fb" href="'.$big_menu[0].'"><em>'.$big_menu[1].'</em></a>　';?>
    <?php echo Web_Admin::submenu($_GET['menuid'],$big_menu); ?><span>|</span><a href="javascript:window.top.art.dialog.open('?app=poster&controller=space&action=setting',{id:'setting',title:'<?php echo L('module_setting')?>', width:'540px', height:'320px',ok: function(iframeWin, topWin){var form = iframeWin.document.getElementById('dosubmit');form.click();return false;},cancel: function(){}});void(0);"><em><?php echo L('module_setting')?></em></a>
    </div>
</div>
<div class="pad-lr-10">
<div class="table-list">
    <table width="100%" cellspacing="0">
        <thead>
            <tr>
            <th width="50" align="center"><?php echo L('template_name')?></th>
			<th width="24%" align="center"><?php echo L('operations_manage')?></th>
            </tr>
        </thead>
    <tbody>
 <?php
if(is_array($templates)){
	foreach($templates as $info){
?>
	<tr>
	<td><?php if ($poster_template[$info]['name']) { echo $poster_template[$info]['name'].' ('.$info.')'; } else { echo $info; }?></td>
	<td align="center">
	<a href="javascript:<?php if ($poster_template[$info]['iscore']) {?>check<?php } else {?>edit<?php }?>('<?php echo addslashes(htmlspecialchars($info))?>', '<?php if($poster_template[$info]['name']) echo addslashes(htmlspecialchars($poster_template[$info]['name']));else echo L('edit');?>');void(0);"><?php if ($poster_template[$info]['iscore']) { echo L('check_template'); } else { echo '<font color="#009933">'.L('setting_template').'</font>'; }?></a> | <a href="?app=poster&controller=space&action=public_tempate_del&id=<?php echo $info?>"><?php echo L('delete')?></a>
	</td>
	</tr>
<?php
	}
}
?>
</tbody>
    </table>  </div>
</div>
<script type="text/javascript">
<!--
	function edit(id, name) {
		window.top.art.dialog.open('?app=poster&controller=space&action=public_tempate_setting&template='+id ,{
			title:name,
			id:'edit',
			width:'540px',
			height:'360px',
			ok: function(iframeWin, topWin){
				var form = iframeWin.document.getElementById('dosubmit');
				form.click();
				return false;
			},
		    cancel: function(){}
		});
	};

	function check(id, name) {
		window.top.art.dialog.open('?app=poster&controller=space&action=public_tempate_setting&template='+id ,{
			title:name,
			id:'edit',
			width:'540px',
			height:'360px',
			yesFn: function(iframeWin, topWin){

			}
		});
	};

//-->
</script>
</body>
</html>
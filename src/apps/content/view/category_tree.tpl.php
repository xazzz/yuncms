<?php
defined('IN_ADMIN') or exit('No permission resources.');
include $this->view('header','admin');
?>
<div class="bk10"></div>
<link rel="stylesheet" href="<?php echo CSS_PATH;?>jquery.treeview.css" type="text/css" />
<script type="text/javascript" src="<?php echo JS_PATH;?>jquery.cookie.js"></script>
<script type="text/javascript" src="<?php echo JS_PATH;?>jquery.treeview.js"></script>
<SCRIPT LANGUAGE="JavaScript">
<!--
$(document).ready(function(){
    $("#category_tree").treeview({
			control: "#treecontrol",
			persist: "cookie",
			cookieId: "treeview-black"
	});
});
function open_list(obj) {
	window.top.$("#current_pos_attr").html($(obj).html());
}

//-->
</SCRIPT>
 <style type="text/css">
.filetree *{white-space:nowrap;}
.filetree span.folder, .filetree span.file{display:auto;padding:1px 0 1px 16px;}
 </style>
 <div id="treecontrol">
 <span style="display:none">
		<a href="#"></a>
		<a href="#"></a>
		</span>
		<a href="#"><img src="<?php echo IMG_PATH;?>minus.gif" /> <img src="<?php echo IMG_PATH;?>application_side_expand.png" /> 展开/收缩</a>
</div>
<?php
 if($_GET['from']=='block') {
?>
<ul class="filetree  treeview"><li class="collapsable"><div class="hitarea collapsable-hitarea"></div><span><img src="<?php echo IMG_PATH.'icon/home.png';?>" width="15" height="14">&nbsp;<a href='?app=block&controller=admin&action=public_visualization&type=index' target='right'><?php echo L('block_site_index');?></a></span></li></ul>
<?php } else { ?>
<ul class="filetree  treeview"><li class="collapsable"><div class="hitarea collapsable-hitarea"></div><span><img src="<?php echo IMG_PATH.'icon/box-exclaim.gif';?>" width="15" height="14">&nbsp;<a href='?app=content&controller=content&action=public_checkall&menuid=59' target='right'><?php echo L('checkall_content');?></a></span></li></ul>
<?php } echo $categorys; ?>
<?php
defined ( 'IN_ADMIN' ) or exit ( 'No permission resources.' );
include $this->view ( 'header', 'admin' );
?>
<div class="pad-10">
	<form method="post"
		action="?app=announce&controller=admin_announce&action=add"
		name="myform" id="myform">
		<table class="table_form" width="100%" cellspacing="0">
			<tbody>
				<tr>
					<th width="80"><strong><?php echo L('announce_title')?></strong></th>
					<td><input name="announce[title]" id="title" class="input-text"
						type="text" size="50"></td>
				</tr>
				<tr>
					<th><strong><?php echo L('startdate')?>：</strong></th>
					<td><?php echo Form::date('announce[starttime]', date('Y-m-d'))?></td>
				</tr>
				<tr>
					<th><strong><?php echo L('enddate')?>：</strong></th>
					<td><?php echo Form::date('announce[endtime]','');?></td>
				</tr>
				<tr>
					<th><strong><?php echo L('announce_content')?></strong></th>
					<td><textarea name="announce[content]" id="content"></textarea><?php echo Form::editor('content');?></td>
				</tr>
				<tr>
					<th><strong><?php echo L('available_style')?>：</strong></th>
					<td>
		<?php echo Form::select($template_list, C('template','name'), 'name="announce[style]" id="style" onchange="load_file_list(this.value)"', L('please_select'))?>
		</td>
				</tr>
				<tr>
					<th><strong><?php echo L('template_select')?>：</strong></th>
					<td id="show_template"><script type="text/javascript">$.getJSON('?app=admin&controller=category&action=public_tpl_file_list&style=<?php echo C('template','name')?>&application=announce&templates=show&name=announce', function(data){$('#show_template').html(data.show_template);});</script></td>
				</tr>
				<tr>
					<th><strong><?php echo L('announce_status')?></strong></th>
					<td><input name="announce[passed]" type="radio" value="1" checked>&nbsp;<?php echo L('pass')?>&nbsp;&nbsp;<input
						name="announce[passed]" type="radio" value="0">&nbsp;<?php echo L('unpass')?></td>
				</tr>
			</tbody>
		</table>
		<input type="submit" name="dosubmit" id="dosubmit"
			value=" <?php echo L('ok')?> " class="dialog">&nbsp;<input
			type="reset" class="dialog" value=" <?php echo L('clear')?> ">
	</form>
</div>
</body>
</html>
<script type="text/javascript">
function load_file_list(id) {
	if (id=='') return false;
	$.getJSON('?app=admin&controller=category&action=public_tpl_file_list&style='+id+'&application=announce&templates=show&name=announce', function(data){$('#show_template').html(data.show_template);});
}

$(document).ready(function(){
	$.formValidator.initConfig({formid:"myform",autotip:true,onerror:function(msg,obj){window.top.art.dialog.alert(msg);$(obj).focus();}});
	$('#title').formValidator({onshow:"<?php echo L('input_announce_title')?>",onfocus:"<?php echo L('title_min_3_chars')?>",oncorrect:"<?php echo L('right')?>"}).inputValidator({min:1,onerror:"<?php echo L('title_cannot_empty')?>"}).ajaxValidator({type:"get",url:"?app=announce&controller=admin_announce&action=public_check_title",data:"",datatype:"html",cached:false,async:'true',success : function(data) {if( data == "1" ){return true;}else{return false;}},error: function(){alert("<?php echo L('server_no_data')?>");},onerror : "<?php echo L('announce_exist')?>",onwait : "<?php echo L('checking')?>"});
	$('#starttime').formValidator({onshow:"<?php echo L('select_stardate')?>",onfocus:"<?php echo L('select_stardate')?>",oncorrect:"<?php echo L('right_all')?>"});
	$('#endtime').formValidator({onshow:"<?php echo L('select_downdate')?>",onfocus:"<?php echo L('select_downdate')?>",oncorrect:"<?php echo L('right_all')?>"});
	$("#content")
		.formValidator({
			autotip:true,onshow:"",
			onfocus:"<?php echo L('announcements_cannot_be_empty')?>"
		})
		.functionValidator({
	    	fun:function(val,elem){
	        if(!UE.getEditor('content').hasContents()){
	    	    return "<?php echo L('announcements_cannot_be_empty')?>"
	        } else {
	    		return true;
	    	}}});
	$('#style').formValidator({onshow:"<?php echo L('select_style')?>",onfocus:"<?php echo L('select_style')?>",oncorrect:"<?php echo L('right_all')?>"}).inputValidator({min:1,onerror:"<?php echo L('select_style')?>"});
});
</script>
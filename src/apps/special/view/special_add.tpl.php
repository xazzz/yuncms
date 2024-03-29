<?php
defined ( 'IN_ADMIN' ) or exit ( 'No permission resources.' );
$show_validator = $show_scroll = $show_dialog = 1;
include $this->view ( 'header', 'admin' );
?>
<form method="post" action="?app=special&controller=special&action=add"
	id="myform">
	<div class="pad-10">
		<div class="col-tab">
			<ul class="tabBut cu-li">
				<li id="tab_setting_1" class="on"
					onclick="SwapTab('setting','on','',6,1);"><?php echo L('catgory_basic', '', 'admin');?></li>
				<li id="tab_setting_2" onclick="SwapTab('setting','on','',6,2);"><?php echo L('extend_setting')?></li>
			</ul>
			<div id="div_setting_1" class="contentList pad-10">
				<table width="100%" class="table_form ">
					<tbody>
						<tr>
							<th width="200"><?php echo L('special_title')?>：</th>
							<td><input name="special[title]" id="title" class="input-text"
								type="text" size="40"></td>
						</tr>
						<tr>
							<th><?php echo L('special_banner')?>：</th>
							<td><?php echo Form::images('special[banner]', 'banner', '', 'special', '', 40)?></td>
						</tr>
						<tr>
							<th><?php echo L('sepcial_thumb')?>：</th>
							<td><?php echo Form::images('special[thumb]', 'thumb', '', 'special', '', 40, '', '', '', array(350, 350))?></td>
						</tr>
						<tr>
							<th><?php echo L('special_intro')?>：</th>
							<td><textarea name="special[description]" id="description"
									cols="50" rows="6"></textarea></td>
						</tr>
						<tr>
							<th align="right" valign="top"><?php echo L('ishtml')?>：</th>
							<td valign="top"><?php echo Form::radio(array('1'=>L('yes'), '0'=>L('no')),'1', 'name="special[ishtml]"');?>
	        </td>
						</tr>
						<tr id="file_div" style="display: 'block';">
							<th align="right" valign="top"><?php echo L('special_filename')?>：<br />
							<span style="font-size: 9px; color: #ff4400"><?php echo L('submit_no_edit')?></span></th>
							<td valign="top"><input type="text" name="special[filename]"
								id="filename" class="input-text"
								value="<?php echo C('system','html_root')?>" size="20"></td>
						</tr>
						 <tr>
	    	<th><?php echo L('special_type')?>：<a href="javascript:addItem()" title="<?php echo L('add')?>"><span style="color:red;" >+</span></a></th>
	        <td valign="top"><div id="option_list">
	        	<div class="mb6"><span><?php echo L('type_name')?>：<input type="text" id="type_name" name="type[1][name]" class="input-text" size="15">&nbsp;&nbsp;<?php echo L('type_path')?>：<input type="text" name="type[1][typedir]" id="type_path" class="input-text" size="15">&nbsp;&nbsp;<?php echo L('listorder')?>：<input type="text" name="type[<?php echo $k?>][listorder]" value="1" size="6" class="input-text" ></span>&nbsp;<span id="typeTip"></span></div>
	        </div>
	        </td>
	    </tr>
					</tbody>
				</table>
			</div>
			<div id="div_setting_2" class="contentList pad-10 hidden">
				<table width="100%" class="table_form ">
					<tr>
						<th width="200"><?php echo L('pics_news')?>：</th>
						<td><span id="relation"></span><input type="button" class="button"
							value="<?php echo L('choose_pic_news')?>"
							onclick="import_info('?app=special&controller=special&action=public_get_pics','<?php echo L('choose_pic_news')?>', 'msg_id', 'relation', 'pics');"><input
							type="hidden" name="special[pics]" value="" id="pics"><span
							class="onShow">(<?php echo L('choose_pic_model')?>)</span></td>
					</tr>
					<tr>
						<th><?php echo L('add_vote')?>：</th>
						<td><span id="vote_msg"></span><input type="button" class="button"
							value="<?php echo L('choose_exist_vote')?>"
							onclick="import_info('?app=vote&controller=vote&action=public_get_votelist&from_api=1&target=dialog','<?php echo L('choose_vote')?>', 'msg_id', 'vote_msg', 'voteid');"><input
							type="hidden" name="special[voteid]" value="" id="voteid">&nbsp;<input
							type="button" class="button"
							value="<?php echo L('add_new_vote')?>"
							onclick="import_info('?app=vote&controller=vote&action=add&from_api=1&target=dialog','<?php echo L('add_new_vote')?>', 'subject_title', 'vote_msg', 'voteid');"></td>
					</tr>
					<tr>
						<th align="right" valign="top"><?php echo L('index_page')?>：</th>
						<td valign="top"><?php echo Form::radio(array('0'=>L('no'), '1'=>L('yes')), '0', 'name="special[ispage]"');?>
        </td>
					</tr>
					<tr>
						<th align="right" valign="top"><?php echo L('special_status')?>：</th>
						<td valign="top"><?php echo Form::radio(array('0'=>L('open'), '1'=>L('pause')), '0', 'name="special[disabled]"');?>
        </td>
					</tr>
					<tr>
						<th align="right" valign="top"><?php echo L('template_style')?>：</th>
						<td valign="top"><?php echo Form::select($template_list, $info['name'], 'name="special[style]" id="style" onchange="load_file_list(this.value)"', L('please_select'))?>
    	<script type="text/javascript">$.getJSON('?app=admin&controller=category&action=public_tpl_file_list&style=<?php echo $info['name']?>&application=special&templates=index|list|show&name=special', function(data){$('#index_template').html(data.index_template);$('#list_template').html(data.list_template);$('#show_template').html(data.show_template);});</script>
						</td>
					</tr>
					<tr>
						<th align="right" valign="top"><?php echo L('special_template')?>：</th>
						<td valign="top" id="index_template"><?php echo Form::select_template('default', 'special', '', 'name="special[index_template]"', 'index');?>
        </td>
					</tr>
					<tr>
						<th align="right" valign="top"><?php echo L('special_type_template')?>：</th>
						<td valign="top" id="list_template"><?php echo Form::select_template('default', 'special', '', 'name="special[list_template]"', 'list');?>
        </td>
					</tr>
					<tr>
						<th align="right" valign="top"><?php echo L('special_content_template')?>：</th>
						<td valign="top" id="show_template"><?php echo Form::select_template('default', 'special', '', 'name="special[show_template]"', 'show');?>
        </td>
					</tr>
				</table>
			</div>
			<div class="bk15"></div>
			<input name="dosubmit" type="submit" value="<?php echo L('submit')?>"
				class="button">
		</div>
	</div>
</form>
</body>
</html>
<script type="text/javascript">
function load_file_list(id) {
	$.getJSON('?app=admin&controller=category&action=public_tpl_file_list&style='+id+'&application=special&templates=index|list|show&name=special', function(data){$('#index_template').html(data.index_template);$('#list_template').html(data.list_template);$('#show_template').html(data.show_template);});
}

function import_info(url, title, msgID, htmlID, valID) {
	window.top.art.dialog.open(url,{
		id:'selectid',
		 title:title,
		 width:'600px',
		 height:'400px',
		 lock:true,
		 ok: function(iframeWin, topWin){
			 	var form = iframeWin.document.getElementById(msgID);
				var text = form.value;
				var data = text.split('|');
				if (data[2]) {
					$('#'+htmlID).html('<ul id="relation_'+htmlID+'" class="list-dot"><li><span>'+data[2]+'</span><a onclick="remove_relation(\''+htmlID+'\', \''+valID+'\')" class="close" href="javascript:;"></a></li></ul>');
				} else {
					var dosubmit = d.document.getElementById('dosubmit');
					dosubmit.click();
					$('#'+htmlID).html('<ul id="relation_'+htmlID+'" class="list-dot"><li><span>'+text+'</span><a onclick="remove_relation(\''+htmlID+'\', \''+valID+'\')" class="close" href="javascript:;"></a></li></ul>');
				}
				$('#'+valID).val(text);
				this.close();
				return false;
			},
		cancel: function(){}
	});
	void(0);
}

function remove_relation(htmlID, valID) {
	$('#relation_'+htmlID).html('');
	$('#'+valID).val('');
}

function addItem() {
	var n = $('#option_list').find('input[name]').length/3+1;
	var newOption =  '<div class="mb6"><span><?php echo L('type_name')?>：<input type="text" name="type['+n+'][name]" class="input-text" size="15">&nbsp;&nbsp;<?php echo L('type_path')?>：<input type="text" name="type['+n+'][typedir]" class="input-text" size="15">&nbsp;&nbsp;<?php echo L('listorder')?>：<input type="text" name="type['+n+'][listorder]" value="'+n+'" size="6" class="input-text" ></span>&nbsp;<a href="javascript:;" onclick="descItem(this, '+n+');"><?php echo L('remove')?></a></div>';
	$('#option_list').append(newOption);
}

function descItem(a, id) {
	$(a).parent().append('<input type="hidden" name="type['+id+'][del]" value="1">');
	$(a).parent().fadeOut();
}

function SwapTab(name,cls_show,cls_hide,cnt,cur){
	for(i=1;i<=cnt;i++){
		if(i==cur){
			 $('#div_'+name+'_'+i).show();
			 $('#tab_'+name+'_'+i).attr('class',cls_show);
		}else{
			 $('#div_'+name+'_'+i).hide();
			 $('#tab_'+name+'_'+i).attr('class',cls_hide);
		}
	}
}

$(document).ready(function(){
	$.formValidator.initConfig({
		formid:"myform",
		autotip:true,
		onerror:function(msg,obj){
			window.top.art.dialog.alert(msg);
			$(obj).focus();
		}
	});
	$('#title')
	.formValidator({
		autotip:true,
		onshow:"<?php echo L('please_input_special_title')?>",
		onfocus:"<?php echo L('min_3_title')?>",
		oncorrect:"<?php echo L('true')?>"
	})
	.inputValidator({
		min:1,
		onerror:"<?php echo L('please_input_special_title')?>"
	})
	.ajaxValidator({
		type:"get",
		url:"?app=special&controller=special&action=public_check_special",
		data:"",
		datatype:"html",
		cached:false,
		async:'true',
		success : function(data) {
        	if( data == "1" ){
            	return true;
			}else{
            	return false;
			}
		},
		error: function(){alert("<?php echo L('server_no_data')?>");},
		onerror : "<?php echo L('special_exist')?>",
		onwait : "<?php echo L('checking')?>"
	});
	$('#banner')
		.formValidator({
			autotip:true,
			onshow:"<?php echo L('please_upload_banner')?>",
			oncorrect:"<?php echo L('true')?>"
		})
		.inputValidator({
			min:1,
			onerror:"<?php echo L('please_upload_banner')?>"
		});
	$('#thumb')
		.formValidator({
			autotip:true,
			onshow:"<?php echo L('please_upload_thumb')?>",
			oncorrect:"<?php echo L('true')?>"
		})
		.inputValidator({
			min:1,
			onerror:"<?php echo L('please_upload_thumb')?>"
		});
	$('#filename')
		.formValidator({
			autotip:true,
			onshow:"<?php echo L('special_file')?>",
			onfocus:'<?php echo L('use_letters')?>',
			oncorrect:"<?php echo L('true')?>"
		})
		.functionValidator({
	    	fun:function(val,elem){
        		if($("input:radio[type='radio'][checked]").val()==0){
		    		return true;
	    		} else if($("input:radio[type='radio'][checked]").val()==1 && val==''){
		    		return "<?php echo L('please_input_name')?>"
	    		} else {
					return true;
				}
			}
		})
		.regexValidator({
			regexp:"^\\w*$",
			onerror:"<?php echo L('error')?>"
		});
	$("#type_name")
		.formValidator({
			tipid:"typeTip",
			onshow:"<?php echo L('input_type_name')?>",
			onfocus:"<?php echo L('input_type_name')?>",
			oncorrect:"<?php echo L('true')?>"
		})
		.inputValidator({
			min:1,
			onerror:"<?php echo L('input_type_name')?>"
		});
	$('#type_path')
		.formValidator({
			tipid:"typeTip",
			onshow:"<?php echo L('input_type_path')?>",
			onfocus:"<?php echo L('input_type_path')?>",
			oncorrect:"<?php echo L('true')?>"
		})
		.inputValidator({
			min:2,
			onerror:"<?php echo L('input_type_path')?>"
		})
		.regexValidator({
			regexp:"^\\w*$",
			onerror:"<?php echo L('error')?>"
		});
});
$("input:radio[name='special[ishtml]']").click(function (){
	if($("input:radio[name='special[ishtml]'][checked]").val()==0) {
		$("#file_div").hide();
	} else if($("input:radio[type='radio'][checked]").val()==1) {
		$("#file_div").show();
	}
});
</script>
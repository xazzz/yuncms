<?php
defined('IN_ADMIN') or exit('No permission resources.');
$show_dialog = $show_validator = $show_header = 1;
include $this->view('header','admin');
?>
<style type="text/css">
html,body{ background:#e2e9ea}
</style>
<script type="text/javascript">
<!--
	var charset = '<?php echo CHARSET?>';
	var uploadurl = '<?php echo C('attachment','upload_url')?>';
//-->
</script>
<script language="javascript" type="text/javascript" src="<?php echo JS_PATH?>content_addtop.js"></script>
<script language="javascript" type="text/javascript" src="<?php echo JS_PATH?>colorpicker.js"></script>
<script language="javascript" type="text/javascript" src="<?php echo JS_PATH?>cookie.js"></script>
<form name="myform" id="myform" action="?app=special&controller=content&action=add&specialid=<?php echo $_GET['specialid']?>" method="post" enctype="multipart/form-data">
<div class="addContent">
<div class="crumbs"><?php echo L('pos_info')?></div>
<div class="col-right">
    	<div class="col-1">
        	<div class="content pad-6">
	<h6> <?php echo L('content_thumb')?></h6>
	 <div class="upload-pic img-wrap"><div class="bk10"></div><input type="hidden" name="info[thumb]" id="thumb">
		<a href="javascript:;" onclick="javascript:flashupload('thumb_images', '<?php echo L('file_upload')?>','thumb',thumb_images,'1,jpg|jpeg|gif|bmp|png,300,300','content','39','<?php echo upload_key('1,jpg|jpeg|gif|bmp|png,300,300')?>')"><img src="static/image/icon/upload-pic.png" id="thumb_preview" width="135" height="113" style="cursor:hand" /></a><input type="button" style="width: 66px;" class="button" onclick="crop_cut($('#thumb').val());return false;" value="<?php echo L('crop_thumb')?>"><script type="text/javascript">
		function crop_cut(id){
			if (id=='') {
				alert('<?php echo L('please_upload_thumb')?>');
				return false;
			}
			window.top.art.dialog.open('index.php?app=content&controller=content&action=public_crop&application=xtcms&picurl='+encodeURIComponent(id)+'&input=thumb&preview=thumb_preview',{
				title:'<?php echo L('crop_thumb')?>',
				id:'crop',
				width:'680px',
				height:'480px',
				ok: function(iframeWin, topWin){iframeWin.uploadfile();return false;},cancel: function(){}});
};</script><input type="button" value="<?php echo L('cancel_thumb')?>" onclick="$('#thumb_preview').attr('src','static/image/icon/upload-pic.png');$('#thumb').val(' ');return false;" class="button" style="width: 66px;"></div>
	<h6> <?php echo L('author')?></h6>
	 <input type="text" name="data[author]" value="" size="30">
	<h6> <?php echo L('islink')?></h6>
	 <input type="text" name="linkurl" id="linkurl" value="" size="30" maxlength="255" disabled> <input name="info[islink]" type="checkbox" id="islink" value="1" onclick="ruselinkurl();" > <font color="red"><?php echo L('islink')?></font>
	<h6> <?php echo L('inputtime')?></h6> <?php echo Form::date('info[inputtime]', Format::date(TIME, 1) , 1);?>
	<h6> <?php echo L('template_style')?></h6> <?php echo Form::select($template_list, $style, 'name="data[style]" id="style" onchange="load_file_list(this.value)"', L('please_select'))?>
	<h6> <?php echo L('show_template')?></h6> <span id="show_template"><script type="text/javascript">$.getJSON('?app=admin&controller=category&action=public_tpl_file_list&style=<?php echo $style?>&application=special&templates=show&id=<?php echo $show_template?>&name=data', function(data){$('#show_template').html(data.show_template);});</script></span>
          </div>
        </div>
    </div>
    <div class="col-auto">
    	<div class="col-1">
        	<div class="content pad-6">
<table width="100%" cellspacing="0" class="table_form">
	<tbody>
	<tr>
      <th width="80"> <font color="red">*</font> <?php echo L('for_type')?>	  </th>
      <td><?php echo Form::select($types, '', 'name="info[typeid]" id="typeid"', L('please_choose_type'))?>  </td>
    </tr>
	<tr>
      <th width="80"> <font color="red">*</font> <?php echo L('content_title')?>	  </th>
      <td><input type="text" style="width:350px;" name="info[title]" id="title" class="measure-input " onBlur="$.post('api.php?c=keyword&a=get&number=3&sid='+Math.random()*5, {data:$('#title').val()}, function(data){if(data && $('#keywords').val()=='') $('#keywords').val(data); })"/>
		<input type="hidden" name="info[style_color]" id="style_color" value="">
		<input type="hidden" name="info[style_font_weight]" id="style_font_weight" value="">
		<input type="button" class="button" id="check_title_alt" value="<?php echo L('check_exist')?>" onclick="$.get('?app=special&controller=content&action=public_check_title&sid='+Math.random()*5, {data:$('#title').val(), specialid:'<?php echo $_GET['specialid']?>'}, function(data){ if(data=='1') {$('#check_title_alt').val('<?php echo L('title_exist')?>');$('#check_title_alt').css('background-color','#FFCC66');} else if(data=='0') {$('#check_title_alt').val('<?php echo L('title_no_exist')?>');$('#check_title_alt').css('background-color','#F8FFE1')}})"/> <img src="static/image/icon/colour.png" width="15" height="16" onclick="colorpicker('title_colorpanel','set_title_color');" style="cursor:hand"/>
		<img src="static/image/icon/bold.png" width="10" height="10" onclick="input_font_bold()" style="cursor:hand"/> <span id="title_colorpanel" style="position:absolute; z-index:200" class="colorpanel"></span>  </td>
    </tr>
    <tr>
      <th width="80"> <?php echo L('keywords')?>	  </th>
      <td><input type='text' name='info[keywords]' id='keywords' value='' style='50'  >  <?php echo L('more_keywords_with_blanks')?></td>
    </tr>
	<tr>
      <th width="80"> <?php echo L('description')?>	  </th>
      <td><textarea name="info[description]" id="description" style='width:98%;height:46px;' onkeyup="strlen_verify(this, 'description_len', 255)"></textarea> 还可输入<B><span id="description_len">255</span></B> 个字符 </td>
    </tr>
	<tr>
      <th width="80"> <font color="red">*</font> <?php echo L('content')?>	  </th>
      <td><div id='content_tip'></div><textarea name="data[content]" id="content" boxid="content"></textarea><?php echo form::editor('content', 'full', 'content', '', '', 1, '', '')?><div class="content_attr"><label><input name="add_introduce" type="checkbox"  value="1" checked><?php echo L('iscutcontent')?></label><input type="text" name="introcude_length" value="200" size="3"><?php echo L('characters_to_contents')?>
<label><input type='checkbox' name='auto_thumb' value="1" checked><?php echo L('iscutcotent_pic')?></label><input type="text" name="auto_thumb_no" value="1" size="2" class=""><?php echo L('picture2thumb')?>
</div></td>
	<tr>
      <th width="80"> <?php echo L('paginationtype')?>	  </th>
      <td><select name="data[paginationtype]" id="paginationtype" onchange="if(this.value==1)$('#paginationtype1').css('display','');else $('#paginationtype1').css('display','none');">
                <option value="0"><?php echo L('no_page')?></option>
                <option value="1"><?php echo L('collate_copies')?></option>
                <option value="2"><?php echo L('manual_page')?></option>
            </select>
			<span id="paginationtype1" style="display:none"><input name="data[maxcharperpage]" type="text" id="maxcharperpage" value="2000" size="8" maxlength="8"><?php echo L('number_of_characters')?></span>  </td>
    </tr>

    </tbody></table>
                </div>
        	</div>
        </div>

    </div>
</div>
<div class="fixed-bottom">
	<div class="fixed-but text-c">
    <div class="button"><input value="<?php echo L('save')?>" type="submit" class="cu" name="dosubmit" onclick="refersh_window();"></div>
    <div class="button"><input value="<?php echo L('save_and_add')?>" type="submit" class="cu" name="dosubmit_continue" onclick="refersh_window();"></div>
    <div class="button"><input value="<?php echo L('close')?>" type="button" name="close" class="cu" onclick="refersh_window();close_window()"></div>
      </div>
</div>
</form>

</body>
</html>
<script type="text/javascript">

function load_file_list(id) {
	$.getJSON('?app=admin&controller=category&action=public_tpl_file_list&style='+id+'&application=special&templates=show&name=data', function(data){$('#show_template').html(data.show_template);});
}

//只能放到最下面
$(function(){
	$.formValidator.initConfig({
		formid:"myform",
		autotip:true,
		onerror:function(msg,obj){
			window.top.art.dialog.alert(msg);
			$(obj).focus();
			boxid = $(obj).attr('id');
			if($('#'+boxid).attr('boxid')!=undefined) {
				check_content(boxid);
			}
		}
	});
	$("#typeid")
		.formValidator({
			autotip:true,
			onshow:"<?php echo L('please_choose_type')?>",
			onfocus:"<?php echo L('please_choose_type')?>"
		})
		.inputValidator({
			min:1,
			onerror:"<?php echo L('please_choose_type')?>"
		});
	$("#title")
		.formValidator({
			autotip:true,
			onshow:"<?php echo L('please_input_title')?>",
			onfocus:"<?php echo L('please_input_title')?>"
		})
		.inputValidator({
			min:1,
			onerror:"<?php echo L('please_input_title')?>"
		});
	$("#content")
		.formValidator({
			autotip:true,
			onshow:"",
			onfocus:"<?php echo L('content_empty')?>"
		})
		.functionValidator({
	    	fun:function(val,elem){
	    	//获取编辑器中的内容
			var oEditor = CKEDITOR.instances.content;
			var data = oEditor.getData();

        	if($('#islink').attr('checked')){
		    	return true;
	    	}else if(($('#islink').attr('checked')==false) && (data=='')){
		    	return "<?php echo L('content_empty')?>"
	    	} else {
				return true;
			}
		}
	});
/*
 * 加载禁用外边链接
 */
	$('#linkurl').attr('disabled',true);
	$('#islink').attr('checked',false);
	$('.edit_content').hide();
});
self.moveTo(0, 0);
function refersh_window() {
	setcookie('refersh_time', 1);
}
//-->
</script>
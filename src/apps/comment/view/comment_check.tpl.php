<?php
defined('IN_ADMIN') or exit('No permission resources.');
include $this->view('header', 'admin');
?>
<div class="pad_10">
<div class="subnav">
  <h1 class="title-2"><?php echo L('comment_check')?> (<?php echo L('for_audit_several')?>：<span id="wait" style="color:red"><?php echo $total?></span>)</h1>
</div>
</div>
<div class="pad-lr-10">
<div class="comment">
<?php if(is_array($comment_check_data)) foreach($comment_check_data as $v) :
$this->comment_data_db->table_name($v['tableid']);
$data = $this->comment_data_db->getby_id($v['comment_data_id']);
?>
<div  id="tbody_<?php echo $data['id']?>">
<h5 class="title fn" ><span class="rt"><input type="button" value="<?php echo L('pass')?>" class="button" onclick="check(<?php echo $data['id']?>, 1, '<?php echo $data['commentid']?>')" /> <input  class="button"  type="button" value="<?php echo L('delete')?>" onclick="check(<?php echo $data['id']?>, -1, '<?php echo $data['commentid']?>')" />
</span><?php echo $data['username']?> (<?php echo $data['ip']?>) <?php echo L('chez')?> <?php echo Format::date($data['creat_at'], 1)?> <?php echo L('release')?> </h5>
    <div class="content">
    	<pre><?php echo $data['content']?></pre>
    </div>
    <div class="bk20 hr mb8"></div>
</div>
<?php endforeach;?>
</div>
</div>
<script type="text/javascript">
window.top.$('#display_center_id').css('display','none');
function check(id, type, commentid) {
	if(type == -1 && !confirm('<?php echo L('are_you_sure_you_want_to_delete')?>')) {
		return false;
	}
	$.get('?app=comment&controller=check&action=ajax_checks&id='+id+'&type='+type+'&commentid='+commentid+'&'+Math.random(), function(data){if(data!=1){if(data==0){alert('<?php echo L('illegal_parameters')?>')}else{alert(data)}}else{$('#tbody_'+id).remove();

	$.getJSON('?app=comment&controller=check&action=public_get_one&'+Math.random(), function(data){
		if (data) {
			$('#wait').html(data.total);
			val = data.data;
			if (val.content) {
			html = '<div id="tbody_'+val.id+'"><h5 class="title fn" ><span class="rt"><input type="button" value="<?php echo L('pass')?>" class="button" onclick="check('+val.id+', 1, \''+val.commentid+'\')" /> <input  class="button"  type="button" value="<?php echo L('delete')?>" onclick="check('+val.id+', -1, \''+val.commentid+'\')" /></span>'+val.username+' ('+val.ip+') <?php echo L('chez')?> '+val.creat_at+' <?php echo L('release')?> </h5><div class="content"><pre>'+val.content+'</pre></div><div class="bk20 hr mb8"></div></div>';
			$('.comment').append(html);
			}
		}
		});

	}});
}
</script>
</body>
</html>
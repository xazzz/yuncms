<?php
defined('IN_ADMIN') or exit('No permission resources.');
$show_header = $show_validator = $show_scroll = 1;
include $this->view('header','admin');
?>
<br />
<div class="pad-lr-10">
<div id="searchid" style="display:">
<form name="searchform" action="" method="get" >
<input type="hidden" value="special" name="app">
<input type="hidden" value="special" name="controller">
<input type="hidden" value="public_get_pics" name="action">
<table width="100%" cellspacing="0" class="search-form">
    <tbody>
		<tr>
		<td>
		<div class="explain-col">
 			<?php echo $model_form?>&nbsp;&nbsp;
<span id="catids"></span>&nbsp;&nbsp; <span id="title" style="display:none;"><?php echo L('title')?>：<input type="text" name="title" size="20"></span>
				<?php echo L('input_time')?>：
				<?php $start_f = $_GET['start_time'] ? $_GET['start_time'] : Format::date(TIME-2592000);$end_f = $_GET['end_time'] ? $_GET['end_time'] :Format::date(TIME+86400);?>
				<?php echo Form::date('start_time', $start_f, 1)?> - <?php echo Form::date('end_time', $end_f, 1)?>
				 <input type="submit" name="search" class="button" value="<?php echo L('search')?>" />
	</div>
		</td>
		</tr>
    </tbody>
</table>
</form>
</div>
<div class="table-list">
    <table width="100%">
        <thead>
            <tr>
			<th><?php echo L('content_title')?></th>
			</tr>
        </thead>
<tbody>
    <?php if(is_array($data)) { foreach ($data as $r) {?>
        <tr>
		<td><label style="display:block"><input type="radio" onclick="choosed(<?php echo $r['id']?>, <?php echo $r['catid']?>, '<?php echo $r['title']?>')" class="inputcheckbox " name='ids' value="<?php echo $r['id'];?>">		  <?php echo $r['title'];?></label></td>
		</tr>
     <?php } }?>
</tbody>
     </table>
    <div class="btn"> <input type="hidden" name="msg_id" id="msg_id"> </div>
    <div id="pages"><?php echo $pages;?></div>
</div>
</div>
</body>
</html>
<script type="text/javascript">

	function choosed(contentid, catid, title) {
		var msg = contentid+'|'+catid+'|'+title;
		$('#msg_id').val(msg);
	}

	function select_categorys(modelid, id) {
		if(modelid) {
			$.get('', {app: 'special', controller: 'special', action: 'public_categorys_list', modelid: modelid, catid: id, hash: hash }, function(data){
				if(data) {
					$('#catids').html(data);
					$('#title').show();
				} else {
					$('#catids').html('');
					$('#title').hide();
				}
			});
		}
	}
	select_categorys(<?php echo $_GET['modelid']?>, <?php echo $_GET['catid']?>);
</script>
<?php
defined ( 'IN_ADMIN' ) or exit ( 'No permission resources.' );
include $this->view ( 'header' );
?>
<script type="text/javascript">
<!--
$(function(){
	$.formValidator.initConfig({
		autotip:true,
		formid:"myform",
		onerror:function(msg){}
	});
	$("#sitename")
		.formValidator({onshow:"<?php echo L('input').L('mirrors_name')?>",onfocus:"<?php echo L('downserver_name').L('downserver_not_empty')?>"})
		.inputValidator({min:1,max:999,onerror:"<?php echo L('downserver_name').L('downserver_not_empty')?>"});
	$("#siteurl")
		.formValidator({onshow:"<?php echo L('downserver_url')?>",onfocus:"<?php echo L('downserver_url')?>"})
		.inputValidator({onerror:"<?php echo L('downserver_error')?>"})
		.regexValidator({regexp:"([a-zA-Z]+):\/\/(.+)[^\/]$",onerror:"<?php echo L('downserver_error')?>"});
})
//-->
</script>

<div class="pad_10">
  <div class="common-form">
    <form name="myform" action="<?php echo U('admin/downserver/edit');?>"
			method="post" id="myform">
      <input type="hidden" name="id" value="<?php echo $id?>">
      </input>
      <table width="100%" class="table_form">
        <tr>
          <td width="80"><?php echo L('mirrors_name')?></td>
          <td><input type="text" name="info[sitename]" class="input-text"
						value="<?php echo $sitename?>" id="sitename">
            </input></td>
        </tr>
        <tr>
          <td width="80"><?php echo L('mirror_address')?></td>
          <td><input type="text" name="info[siteurl]" class="input-text"
						value="<?php echo $siteurl?>" id="siteurl" size="40">
            </input></td>
        </tr>
      </table>
      <input name="dosubmit" type="submit" value="<?php echo L('submit')?>"
				class="dialog" id="dosubmit">
    </form>
  </div>
</div>
</body></html>
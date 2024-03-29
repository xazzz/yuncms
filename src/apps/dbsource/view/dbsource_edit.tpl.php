<?php
defined('IN_ADMIN') or exit('No permission resources.');
include $this->view('header', 'admin');
?>
<script type="text/javascript">
<!--
	$(function(){
		$.formValidator.initConfig({
			formid:"myform",
			autotip:true,
			onerror:function(msg,obj){
				window.top.art.dialog.alert(msg);
				$(obj).focus();
			}
		});
		$("#host")
			.formValidator({
				onshow:"<?php echo L('input').L('server_address')?>",
				onfocus:"<?php echo L('input').L('server_address')?>"
			})
			.inputValidator({
				min:1,
				onerror:"<?php echo L('input').L('server_address')?>"
			});
		$("#port")
			.formValidator({
				onshow:"<?php echo L('input').L('server_port')?>",
				onfocus:"<?php echo L('input').L('server_port')?>"
			})
			.inputValidator({
				min:1,
				onerror:"<?php echo L('input').L('server_port')?>"
			})
			.regexValidator({
				regexp:"intege1",
				datatype:"enum",
				param:'i',
				onerror:"<?php echo L('server_ports_must_be_positive_integers')?>"
			});
		$("#dbtablepre")
			.formValidator({
				onshow:"<?php echo L('input').L('dbtablepre')?>",
				onfocus:"<?php echo L('tip_pre')?>"
			});
		$("#username")
			.formValidator({
				onshow:"<?php echo L('input').L('username')?>",
				onfocus:"<?php echo L('input').L('username')?>"
			})
			.inputValidator({
				min:1,
				onerror:"<?php echo L('input').L('username')?>"
			});
		$("#password")
			.formValidator({
				onshow:"<?php echo L('input').L('password')?>",
				onfocus:"<?php echo L('input').L('password')?>"
			})
			.inputValidator({
				min:1,
				onerror:"<?php echo L('input').L('password')?>"
			});
		$("#dbname")
			.formValidator({
				onshow:"<?php echo L('input').L('database')?>",
				onfocus:"<?php echo L('input').L('database')?>"
			})
			.inputValidator({
				min:1,
				onerror:"<?php echo L('input').L('database')?>"
			});
	})
	//-->
</script>
<div class="pad-10">
<form action="?app=dbsource&controller=dbsource_admin&action=edit&id=<?php echo $id?>" method="post" id="myform">
<div>
<fieldset>
	<legend><?php echo L('configure_the_external_data_source')?></legend>
	<table width="100%"  class="table_form">
  <tr>
    <th width="80"><?php echo L('dbsource_name')?>：</th>
    <td class="y-bg"><?php echo $data['name']?></td>
  </tr>
  <tr>
    <th><?php echo L('server_address')?>：</th>
    <td class="y-bg"><input type="text" class="input-text" name="host" id="host" size="30" value="<?php echo $data['host']?>" /></td>
  </tr>
    <tr>
    <th><?php echo L('server_port')?>：</th>
    <td class="y-bg"><input type="text" class="input-text" name="port" id="port" size="30" value="<?php echo $data['port']?>" /></td>
  </tr>
    <tr>
    <th><?php echo L('username')?>：</th>
    <td class="y-bg"><input type="text" class="input-text" name="username" id="username"  size="30" value="<?php echo $data['username']?>" /></td>
  </tr>
      <tr>
    <th><?php echo L('password')?>：</th>
    <td class="y-bg"><input type="password" class="input-text" name="password" id="password"  size="30" value="<?php echo $data['password']?>" /></td>
  </tr>
        <tr>
    <th><?php echo L('database')?>：</th>
    <td class="y-bg"><input type="text" class="input-text" name="dbname" id="dbname"  size="30"  value="<?php echo $data['dbname']?>" /></td>
  </tr>
  <tr>
    <th><?php echo L('dbtablepre');?>：</th>
    <td class="y-bg"><input type="text" class="input-text" name="dbtablepre" id="dbtablepre" value="<?php echo $data['dbtablepre']?>" size="30"/> </td>
  </tr>
      <tr>
    <th><?php echo L('charset')?>：</th>
    <td class="y-bg"><?php echo Form::select(array('gbk'=>'GBK', 'utf8'=>'UTF-8', 'gb2312'=>'GB2312', 'latin1'=>'Latin1'), $data['charset'], 'name="charset" id="charset"')?></td>
  </tr>
      <tr>
    <th></th>
    <td class="y-bg"><input type="button" class="button" value="<?php echo L('test_connections')?>" onclick="test_connect()" /></td>
  </tr>
</table>
</fieldset>
<div class="bk15"></div>
    <input type="submit" class="dialog" id="dosubmit" name="dosubmit" value="" />
</div>
</div>
</form>
<script type="text/javascript">
<!--
	function test_connect() {
		$.get('?app=dbsource&controller=dbsource_admin&action=public_test_mysql_connect', {host:$('#host').val(),username:$('#username').val(), password:$('#password').val(), port:$('#port').val()}, function(data){if(data==1){alert('<?php echo L('connect_success')?>')}else{alert('<?php echo L('connect_failed')?>')}});
}
//-->
</script>
</body>
</html>
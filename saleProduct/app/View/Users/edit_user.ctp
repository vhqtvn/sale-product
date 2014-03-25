<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
   <?php echo $this->Html->charset(); ?>
    <title>用户编辑</title>
    <meta http-equiv="pragma" content="no-cache"/>
	<meta http-equiv="cache-control" content="no-cache"/>

   <?php
   include_once ('config/config.php');
   
		echo $this->Html->meta('icon');
		echo $this->Html->css('../js/grid/jquery.llygrid');
		echo $this->Html->css('../js/validator/jquery.validation');
		echo $this->Html->css('../js/dialog/jquery.dialog');
		echo $this->Html->css('default/style');
		echo $this->Html->css('../js/listselectdialog/jquery.listselectdialog');
		echo $this->Html->css('../js/tab/jquery.ui.tabs');
		
		echo $this->Html->script('jquery');
		echo $this->Html->script('common');
		echo $this->Html->script('jquery-ui');
		echo $this->Html->script('jquery.json');
		echo $this->Html->script('grid/jquery.llygrid');
		echo $this->Html->script('grid/query');
		echo $this->Html->script('tab/jquery.ui.tabs');
		echo $this->Html->script('validator/jquery.validation');
		echo $this->Html->script('listselectdialog/jquery.listselectdialog');
		echo $this->Html->script('dialog/jquery.dialog');
		echo $this->Html->script('calendar/WdatePicker');
		echo $this->Html->script('modules/users/edit_user');
		
		$u = null ;
		if(!empty($User)){
			$u = $User[0]['sc_user'] ;
		}
		
	?>
  	<script>
			var userId = '<?php echo $u['ID'] ;?>';
  	</script>
</head>

<body class="container-popup">
	<!-- apply 主场景 -->
	<div class="apply-page">
		<!-- 页面标题 -->
		<div class="page-title">
			<h2>用户信息</h2>
		</div>
		<div class="container-fluid">

	        <form id="personForm" action="#" data-widget="validator,ajaxform" class="form-horizontal" >
	        	<input type="hidden" id="id" value="<?php echo $u['ID'];?>"/>
				<!-- panel 头部内容  此场景下是隐藏的-->
				<div class="panel apply-panel">
					<!-- panel 中间内容-->
					<div class="panel-content">
						<!-- 数据列表样式 -->
						<table class="form-table" >
							<caption>基本信息</caption>
							<tbody>										   
								<tr>
									<th>登录名：</th>
									<td><input type="text"  data-validator="required"
										id="account" value="<?php echo  $u['LOGIN_ID'];?>"
										<?php if( !empty($u['LOGIN_ID']) ) echo "disabled" ;?>
										/></td>
									<th>用户名：</th>
									<td><input type="text"  data-validator="required"
										id="name" value="<?php echo  $u['NAME'];?>"
										/></td>
								</tr>
								<?php /*
								<tr>
									<th>用户组：</th><td>
										<select id="group"  data-validator="required">
											<option value=''>--</option>
											<?php
												foreach( $Groups as $group ){
													$code = $group['sc_security_groups']["CODE"] ;
													$name = $group['sc_security_groups']["NAME"] ;
													if(  $u['GROUP_CODE'] == $code ){
														echo "<option selected value='$code'>$name</option>" ;
													}else{
														echo "<option value='$code'>$name</option>" ;
													}
													
												}
											?>
											
										</select>
									</td>
									
								</tr>
								*/?>
								<tr>
									<th>密码：</th><td><input type="password"  
										data-validator="equalToField[repassword]" id="password"/></td>
									<th>确认密码：</th><td><input type="password"  id="repassword"/></td>
								</tr>
								<tr>
									<th>电话：</th>
									<td><input type="text"
										id="phone" value="<?php echo  $u['PHONE'];?>"/></td>
									<th>邮箱：</th>
									<td><input type="text"
										id="email" data-validator="email" value="<?php echo  $u['EMAIL'];?>"/></td>
								</tr>
							</tbody>
						</table>
						<?php 
							if( !empty($u['ID']) ){
						?>		
						<table class="form-table" >
							<caption>所属用户组&nbsp;&nbsp;&nbsp;<button class="btn btn-primary  select-group">选择</button></caption>
							<tbody>		
								<tr>
									<td>
									<ul  class="group-container"></ul>
									</td>
								</tr>
							</tbody>
						</table>
						<?php 
							}
						?>
					</div>
					
					<!-- panel脚部内容-->
                    <div class="panel-foot">
						<div class="form-actions">
							<button type="button" class="btn btn-primary save-user">提&nbsp;交</button>
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>
</body>
</html>
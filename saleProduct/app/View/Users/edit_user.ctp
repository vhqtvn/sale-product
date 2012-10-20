<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
   <?php echo $this->Html->charset(); ?>
    <title>llygrid demo</title>
    <meta http-equiv="pragma" content="no-cache"/>
	<meta http-equiv="cache-control" content="no-cache"/>

   <?php
		echo $this->Html->meta('icon');
		echo $this->Html->css('default/style');

		echo $this->Html->script('jquery');
		echo $this->Html->script('common');
		echo $this->Html->script('jquery.json');
		
		$login_id = '' ;
		$name = '' ;
		$id  ="" ;
		$groupCode = '' ;
		 if( $User !=null){
		 	$id =$User[0]['sc_user']["ID"] ;
			$login_id =$User[0]['sc_user']["LOGIN_ID"] ;
			$name =$User[0]['sc_user']["NAME"] ;
			$groupCode =$User[0]['sc_user']["GROUP_CODE"] ;
		 }
	?>
  
   <style>
   		*{
   			font:12px "微软雅黑";
   		}

		.rule-content-item{
			clear:both;
		}

		.item-label,.item-relation,.item-value,.item-value{
			float:left;
		}
   </style>

   <script>
		$(function(){
			if( $("#login_id").val()  ){
				$("#login_id").attr("disabled",true) ;
			}
			
			$("button").click(function(){
				if( !$("#name").val()  ){
					alert("用户名不能为空!") ;
					return ;
				}
				if( !$("#login_id").val()  ){
					alert("登录名不能为空!") ;
					return ;
				}
				
				if(!$("#id").val()){
					if( !$("#password").val()  ){
						alert("密码不能为空!") ;
						return ;
					}
				}
				
				if($("#password").val() != $("#repassword").val()){
					alert("密码和确认密码不一致!") ;
					return ;
				}
				
				$.ajax({
					type:"post",
					url:"/saleProduct/index.php/users/saveUser",
					data:{
						id:$("#id").val(),
						name:$("#name").val(),
						login_id:$("#login_id").val(),
						password:$("#password").val(),
						group:$("#group").val()
					},
					cache:false,
					dataType:"text",
					success:function(result,status,xhr){
						window.opener.location.reload() ;
						window.close() ;
					}
				}); 
			})
		})
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

	        <form id="personForm" action="#" data-widget="validator" class="form-horizontal" >
	        <input type="hidden" id="id" value="<?php echo $id;?>"/>
				<!-- panel 头部内容  此场景下是隐藏的-->
				<div class="panel apply-panel">
					<!-- panel 中间内容-->
					<div class="panel-content">
						<!-- 数据列表样式 -->
						<table class="form-table col2" >
							<caption>基本信息</caption>
							<tbody>										   
								<tr>
									<th>登录名：</th><td><input type="text" id="login_id" value="<?php echo $login_id;?>"/></td>
								</tr>
								<tr>
									<th>用户名：</th><td><input type="text" id="name" value="<?php echo $name;?>"/></td>
								</tr>
								<tr>
									<th>用户组：</th><td>
										<select id="group">
											<option value=''>--</option>
											<?php
												foreach( $Groups as $group ){
													$code = $group['sc_security_groups']["CODE"] ;
													$name = $group['sc_security_groups']["NAME"] ;
													if( $groupCode == $code ){
														echo "<option selected value='$code'>$name</option>" ;
													}else{
														echo "<option value='$code'>$name</option>" ;
													}
													
												}
											?>
											
										</select>
									</td>
								</tr>
								<tr>
									<th>密码：</th><td><input type="password" id="password"/></td>
								</tr>
								<tr>
									<th>确认密码：</th><td><input type="password" id="repassword"/></td>
								</tr>
							</tbody>
						</table>
					</div>
					
					<!-- panel脚部内容-->
                    <div class="panel-foot">
						<div class="form-actions col2">
							<button type="button" class="btn btn-primary">提&nbsp;交</button>
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>
</body>
</html>
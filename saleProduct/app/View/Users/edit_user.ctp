<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
   <?php echo $this->Html->charset(); ?>
    <title>llygrid demo</title>
    <meta http-equiv="pragma" content="no-cache"/>
	<meta http-equiv="cache-control" content="no-cache"/>

   <?php
		echo $this->Html->meta('icon');
		echo $this->Html->css('../grid/redmond/ui');
		echo $this->Html->css('../grid/grid');

		echo $this->Html->script('jquery');
		echo $this->Html->script('../grid/query');
		echo $this->Html->script('jquery.json');
		echo $this->Html->script('../grid/grid');
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
<body>
<input type="hidden" id="id" value="<?php echo $id;?>"/>
	<table>
		<tr>
			<td>登录名：</td><td><input type="text" id="login_id" value="<?php echo $login_id;?>"/></td>
		</tr>
		<tr>
			<td>用户名：</td><td><input type="text" id="name" value="<?php echo $name;?>"/></td>
		</tr>
		<tr>
			<td>用户组：</td><td>
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
			<td>密码：</td><td><input type="password" id="password"/></td>
		</tr>
		<tr>
			<td>确认密码：</td><td><input type="password" id="repassword"/></td>
		</tr>
		<tr>
			<td></td><td><button>保存</button></td>
		</tr>
	</table>

</html>
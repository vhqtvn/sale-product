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
		echo $this->Html->css('../js/validator/jquery.validation');
		echo $this->Html->css('style-all');

		echo $this->Html->script('jquery');
		echo $this->Html->script('common');
		echo $this->Html->script('../grid/query');
		echo $this->Html->script('jquery.json');
		echo $this->Html->script('../grid/grid');
		echo $this->Html->script('validator/jquery.validation');
		
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
			$("button").click(function(){
				if( !$.validation.validate('#personForm').errorInfo ) {
					var json = $("#personForm").toJson() ;
				
					$.ajax({
						type:"post",
						url:"/saleProduct/index.php/users/saveFunctoin",
						data:json,
						cache:false,
						dataType:"text",
						success:function(result,status,xhr){
							window.opener.location.reload() ;
							window.close() ;
						}
					}); 
				}
			})
		})
   </script>

</head>
<body>
<form id="personForm" action="#" data-widget="validator,ajaxform">
	<input type="hidden" id="id" value="<?php echo $function[0]['sc_security_function']['ID'];?>"/>
	<table class="table">
		<tr>
			<td>功能名称：</td><td><input type="text" id="name"   data-validator="required"
				value="<?php echo $function[0]['sc_security_function']['NAME'];?>"/></td>
		</tr>
		
		<tr>
			<td>功能类别：</td><td>
				<select id="type"   data-validator="required">
					<?php
						if( $function[0]['sc_security_function']['TYPE'] == 'MENU'){
							echo "<option value='MENU' selected>菜单</option>
							<option value='FUNCTION'>功能</option>";
						}else if( $function[0]['sc_security_function']['TYPE'] == 'FUNCTION'){
							echo "<option value='MENU' >菜单</option>
							<option value='FUNCTION' selected>功能</option>";
						}else{
							echo "<option value='' >-</option><option value='MENU' >菜单</option>
							<option value='FUNCTION'>功能</option>";
						}
					?>
					
				</select>
			</td>
		</tr>
		<tr>
			<td>父编号：</td><td><input type="text" id="parentId" 
				value="<?php echo  $function[0]['sc_security_function']['PARENT_ID'];?>"/></td>
		</tr>
		<tr>
			<td>功能编码：</td><td><input type="text" id="code"  data-validator="required"
				value="<?php echo  $function[0]['sc_security_function']['CODE'];?>"/></td>
		</tr>
		<tr>
			<td>功能URL：</td><td><input type="text" id="url" 
				value="<?php echo  $function[0]['sc_security_function']['URL'];?>"/></td>
		</tr>
		<tr>
			<td></td><td><button class="btn btn-primary">保存</button></td>
		</tr>
	</table>
</form>
</html>
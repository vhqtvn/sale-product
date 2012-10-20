<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
   <?php echo $this->Html->charset(); ?>
    <title>成本编辑</title>
    <meta http-equiv="pragma" content="no-cache"/>
	<meta http-equiv="cache-control" content="no-cache"/>

   <?php
		echo $this->Html->meta('icon');
		echo $this->Html->css('../js/validator/jquery.validation');
		echo $this->Html->css('default/style');

		echo $this->Html->script('jquery');
		echo $this->Html->script('common');
		echo $this->Html->script('jquery.json');
		echo $this->Html->script('validator/jquery.validation');
		
		$userId  = $_COOKIE["userId"] ; 
		App::import('Model', 'User') ;
		$u = new User() ;
		$user1 = $u->queryUserByUserName($userId) ;
		$user = $user1[0]['sc_user'] ;
		
		$loginId = $user["GROUP_CODE"] ;//transfer_specialist cashier purchasing_officer general_manager product_specialist
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
		
		fieldset legend {
			margin-bottom:5px;
		}
		fieldset {
			margin-bottom:8px;
		}
   </style>

   <script>
   		var groupCode = '<?php echo $loginId;?>'
   
		$(function(){

			
			$("button").click(function(){
				if( !$.validation.validate('#personForm').errorInfo ) {
					var json = $("#personForm").toJson() ;
				
					$.ajax({
						type:"post",
						url:"/saleProduct/index.php/amazonaccount/saveConfigItem",
						data:json,
						cache:false,
						dataType:"text",
						success:function(result,status,xhr){
							window.opener.$(".grid-content").llygrid("reload",{}) ;
							window.close() ;
						},error:function(){
							alert("操作出现异常！") ;
						}
					}); 
				};
				return false ;
			}) ;
			
			if( $("#NAME").val() ){
				$("#NAME").attr("readonly",true);
				$("#TYPE").attr("disabled",true);
			}
			
		})
		
   </script>
</head>
<body>
<form id="personForm" action="#" data-widget="validator,ajaxform">

<input type="hidden" id="ID" value="<?php echo $configItem[0]["sc_amazon_config"]["ID"];?>"/>

<fieldset>
	<legend>配置项</legend>
	<table>
		<tr>
			<td>类型：</td><td>
				<select id="TYPE" data-validator='required'>
					<option value=""></option>
					<option value="strategy" <?php if($configItem[0]["sc_amazon_config"]["TYPE"] =='strategy' )echo 'selected' ; ?>>定价策略</option>
					<option value="template" <?php if($configItem[0]["sc_amazon_config"]["TYPE"] =='template' )echo 'selected' ; ?>>模板</option>
				</select>
			</td>
		</tr>
		<tr>
			<td>显示名称：</td><td><input class="cost"  data-validator='required'  type="text" id="LABEL" value="<?php echo $configItem[0]["sc_amazon_config"]["LABEL"];?>"/></td>
		</tr>
		<tr>
			<td>名称（英文）：</td><td><input class="cost"  data-validator='required'  type="text" id="NAME" value="<?php echo $configItem[0]["sc_amazon_config"]["NAME"];?>"/></td>
		</tr>
		<tr>
			<td>值：</td><td><textarea class="cost span4" id="VALUE" data-validator='required' style="height:100px;"
				><?php echo $configItem[0]["sc_amazon_config"]["VALUE"];?></textarea></td>
		</tr>
		
		<tr>
			<td>备注：</td><td><textarea class="cost span4" id="MEMO" style="height:100px;"><?php echo $configItem[0]["sc_amazon_config"]["MEMO"];?></textarea></td>
		</tr>
	</table>
</fieldset>


	<table>
		<tr>
			<td></td><td><button type="submit" class="btn btn-primary">保存</button></td>
		</tr>
	</table>
</form>
</html>
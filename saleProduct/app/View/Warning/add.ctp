<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
   <?php echo $this->Html->charset(); ?>
    <title>预警列表</title>
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
		
		$user = $this->Session->read("product.sale.user") ;
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
						url:"/saleProduct/index.php/warning/save",
						data:json,
						cache:false,
						dataType:"text",
						success:function(result,status,xhr){
							window.opener.$(".grid-content").llygrid("reload") ;
							window.close() ;
						},error:function(){
							alert("操作出现异常！") ;
						}
					}); 
				};
				return false ;
			}) ;
			
		})
   </script>
</head>
<body>
<form id="personForm" action="#" data-widget="validator,ajaxform">

<input type="hidden" id="accountId" value="<?php echo $accountId;?>"/

<fieldset>
	<legend>预警列表</legend>
	<table>
		<tr>
			<td>代码：</td><td><input class="cost"  data-validator='required'  type="text" 
				<?php $code = $item[0]["sc_account_product_warning"]["CODE"];
					if(!empty($code)){
						echo 'readonly' ;
					}else{
						echo '' ;
					}
				?>
			 id="code" value="<?php echo $item[0]["sc_account_product_warning"]["CODE"];?>"/></td>
		</tr>
		<tr>
			<td>名称：</td><td><input class="cost"  data-validator='required'  type="text" id="name" value="<?php echo $item[0]["sc_account_product_warning"]["NAME"];?>"/></td>
		</tr>
		<tr>
			<td>值1：</td><td><input class="cost"  type="text" id="value1" value="<?php echo $item[0]["sc_account_product_warning"]["VALUE1"];?>"/></td>
		</tr>
		<tr>
			<td>值2：</td><td><input class="cost"  type="text" id="value2" value="<?php echo $item[0]["sc_account_product_warning"]["VALUE2"];?>"/></td>
		</tr>
		<tr>
			<td>备注：</td><td><textarea class="cost span4" id="memo" style="height:100px;"><?php echo $item[0]["sc_account_product_warning"]["MEMO"];?></textarea></td>
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
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
   <?php echo $this->Html->charset(); ?>
    <title>联盟卖家</title>
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
						url:"/saleProduct/index.php/unionSeller/save",
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

<input type="hidden" id="accountId" value="<?php echo $accountId;?>"/>
<input type="hidden" id="id" value="<?php echo $item[0]["sc_union_seller"]["ID"];?>"/>

<fieldset>
	<legend>配置项</legend>
	<table>
		<tr>
			<td>卖家名称：</td><td><input class="cost"  data-validator='required'  type="text" id="name" value="<?php echo $item[0]["sc_union_seller"]["NAME"];?>"/></td>
		</tr>
		<tr>
			<td>卖家地址：</td><td><input class="cost"  data-validator='required'  type="text" id="url" value="<?php echo $item[0]["sc_union_seller"]["URL"];?>"/></td>
		</tr>
		<tr>
			<td>备注：</td><td><textarea class="cost span4" id="memo" style="height:100px;"><?php echo $item[0]["sc_union_seller"]["MEMO"];?></textarea></td>
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
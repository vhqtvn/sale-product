<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
   <?php echo $this->Html->charset(); ?>
    <title>供应商</title>
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
		
		
		$domain =  $account[0]['sc_amazon_account']['DOMAIN'];
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
		
		input{
			width:300px;
		}
   </style>

   <script>
		$(function(){

			$("button").click(function(){
				
				if( !$.validation.validate('#personForm').errorInfo ) {
					var json = $("#personForm").toJson() ;

					$.ajax({
						type:"post",
						url:"/saleProduct/index.php/amazonaccount/saveAccount",
						data:json,
						cache:false,
						dataType:"text",
						success:function(result,status,xhr){
							window.opener.location.reload() ;
							window.close() ;
						}
					}); 
				};
			})
		})
   </script>

</head>
<body>
<form id="personForm" action="#" data-widget="validator,ajaxform">
<input type="hidden" id="ID" value="<?php echo $account[0]['sc_amazon_account']['ID'];?>"/>
	<table class="table">
		<tr>
			<td style="width:150px;">账户名称：</td><td><input data-validator="required" type="text" id="NAME" value="<?php echo $account[0]['sc_amazon_account']['NAME'];?>"/></td>
		</tr>
		<tr>
			<td>账户CODE：</td><td><input data-validator="required" type="text" id="CODE" value="<?php echo $account[0]['sc_amazon_account']['CODE'];?>"/>
			<br/>
			<div class="alert alert-success" style="margin:0px;padding:0px;">取值http://www.amazon.com/s/?me=XXXXXXXXX，XXXXXXXXX为账户CODE</div>
			</td>
		</tr>
		<tr>
			<td>操作主机：</td><td><input type="text"  id="DOMAIN" value="<?php echo $domain;?>"/></td>
		</tr>
		<tr>
			<td>AWS_ACCESS_KEY_ID：</td><td><input type="text" id="AWS_ACCESS_KEY_ID" value="<?php echo $account[0]['sc_amazon_account']['AWS_ACCESS_KEY_ID'];?>"/></td>
		</tr>
		<tr>
			<td>AWS_SECRET_ACCESS_KEY：</td><td><input type="text" id="AWS_SECRET_ACCESS_KEY" value="<?php echo $account[0]['sc_amazon_account']['AWS_SECRET_ACCESS_KEY'];?>"/></td>
		</tr>
		<tr>
			<td>APPLICATION_NAME：</td><td><input type="text" id="APPLICATION_NAME" value="<?php echo $account[0]['sc_amazon_account']['APPLICATION_NAME'];?>"/></td>
		</tr>
		<tr>
			<td>APPLICATION_VERSION：</td><td><input type="text" id="APPLICATION_VERSION" value="<?php echo $account[0]['sc_amazon_account']['APPLICATION_VERSION'];?>"/></td>
		</tr>
		<tr>
			<td>MERCHANT_ID：</td><td><input type="text" id="MERCHANT_ID" value="<?php echo $account[0]['sc_amazon_account']['MERCHANT_ID'];?>"/></td>
		</tr>
		<tr>
			<td>MARKETPLACE_ID：</td><td><input type="text" id="MARKETPLACE_ID" value="<?php echo $account[0]['sc_amazon_account']['MARKETPLACE_ID'];?>"/></td>
		</tr>
		<tr>
			<td>MERCHANT_IDENTIFIER：</td><td><input type="text" id="MERCHANT_IDENTIFIER" value="<?php echo $account[0]['sc_amazon_account']['MERCHANT_IDENTIFIER'];?>"/></td>
		</tr>
		<tr>
			<td></td><td><button type="submit" class="btn btn-primary">保存</button></td>
		</tr>
	</table>
</form>
</html>
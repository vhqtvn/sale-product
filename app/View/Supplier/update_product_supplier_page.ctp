<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
   <?php echo $this->Html->charset(); ?>
    <title>供应商询价</title>
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
		
		.table td{
			padding-top:1px;
			padding-bottom:1px;
		}
   </style>

   <script>
		$(function(){
			if( $("#login_id").val()  ){
				$("#login_id").attr("disabled",true) ;
			}
			/*
			$("button").click(function(){
				
				if( !$.validation.validate('#personForm').errorInfo ) {
					var json = $("#personForm").toJson() ;
				
					$.ajax({
						type:"post",
						url:"/saleProduct/index.php/supplier/saveProductSupplierXJ",
						data:json,
						cache:false,
						dataType:"text",
						success:function(result,status,xhr){
							window.opener.location.reload() ;
							window.close() ;
						}
					}); 
				};
			})*/
		})
		function validateForm(){
			if( !$.validation.validate('#personForm').errorInfo ) {
				return true ;
			}
			return false ;
		}
   </script>

</head>
<body>
<form action="/saleProduct/index.php/supplier/saveProductSupplierXJ" id="personForm" data-widget="validator"
	method="post"  enctype="multipart/form-data">
		   
	<input type="hidden" id="id" name="id" value="<?php echo $productSupplier[0]['sc_product_supplier']['ID'];?>"/>
	<table>
		<tr>
			<td>供应商名称：</td><td><?php echo $productSupplier[0]['sc_supplier']['NAME']?></td>
		</tr>
		<tr>
			<td>产品重量：</td><td><input type="text" id="weight"  name="weight" 
				value="<?php echo $productSupplier[0]['sc_product_supplier']['WEIGHT'];?>"/></td>
		</tr>
		<tr>
			<td>产品尺寸：</td><td><input type="text" id="productSize"  name="productSize" 
				value="<?php echo $productSupplier[0]['sc_product_supplier']['PRODUCT_SIZE'];?>"/></td>
		</tr>
		<tr>
			<td>包装尺寸：</td><td><input type="text" id="packageSize"  name="packageSize" 
				value="<?php echo $productSupplier[0]['sc_product_supplier']['PACKAGE_SIZE'];?>"/></td>
		</tr>
		<tr>
			<td>生产周期：</td><td><input type="text" id="cycle" name="cycle" 
				value="<?php echo $productSupplier[0]['sc_product_supplier']['CYCLE'];?>"/></td>
		</tr>
		<tr>
			<td>包装方式：</td><td><input type="text" id="package" name="package" 
				value="<?php echo $productSupplier[0]['sc_product_supplier']['PACKAGE'];?>"/></td>
		</tr>
		<tr>
			<td>付款方式：</td><td><input type="text" id="payment" name="payment" 
				value="<?php echo $productSupplier[0]['sc_product_supplier']['PAYMENT'];?>"/></td>
		</tr>
		<tr>
			<td>产品网址：</td><td><input type="text" id="url" name="url" 
				value="<?php echo $productSupplier[0]['sc_product_supplier']['URL'];?>"/></td>
		</tr>
		<tr>
			<td>产品图片：</td>
			<td><input name="supplierProductImage" type="file"/></td>
		</tr>
		<tr>
			<td>报价方式：</td><td>
				<table class="table">
					<tr><th>数量<th><th>报价<th></tr>
					<tr>
						<td><input type="text" id="num1" name="num1"  data-validator="required"
				value="<?php echo $productSupplier[0]['sc_product_supplier']['NUM1'];?>"/><td>
						<td><input type="text" id="offer1" name="offer1"   data-validator="required"
				value="<?php echo $productSupplier[0]['sc_product_supplier']['OFFER1'];?>"/><td>
					</tr>
					<tr>
						<td><input type="text" id="num2" name="num2" 
				value="<?php echo $productSupplier[0]['sc_product_supplier']['NUM2'];?>"/><td>
						<td><input type="text" id="offer2" name="offer2" 
				value="<?php echo $productSupplier[0]['sc_product_supplier']['OFFER2'];?>"/><td>
					</tr>
					<tr>
						<td><input type="text" id="num3" name="num3" 
				value="<?php echo $productSupplier[0]['sc_product_supplier']['NUM3'];?>"/><td>
						<td><input type="text" id="offer3" name="offer3" 
				value="<?php echo $productSupplier[0]['sc_product_supplier']['OFFER3'];?>"/><td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td>备注：</td><td>
				<textarea id="memo" name="memo"  style="width:80%;height:130px;"><?php echo $productSupplier[0]['sc_product_supplier']['MEMO'];?></textarea></td>
		</tr>
		<tr>
			<td></td><td><input type="submit" class="btn btn-primary" value="保存"/></td>
		</tr>
	</table>
</form>
</html>
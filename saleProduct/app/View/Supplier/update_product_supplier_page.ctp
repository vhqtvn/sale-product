<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
   <?php echo $this->Html->charset(); ?>
    <title>供应商询价</title>
    <meta http-equiv="pragma" content="no-cache"/>
	<meta http-equiv="cache-control" content="no-cache"/>

   <?php
  		include_once ('config/config.php');
   
		echo $this->Html->meta('icon');
		echo $this->Html->css('../grid/grid');
		echo $this->Html->css('../js/validator/jquery.validation');
		echo $this->Html->css('default/style');

		echo $this->Html->script('jquery');
		echo $this->Html->script('common');
		echo $this->Html->script('../grid/query');
		echo $this->Html->script('jquery.json');
		echo $this->Html->script('../grid/grid');
		echo $this->Html->script('validator/jquery.validation');

		$SqlUtils  = ClassRegistry::init("SqlUtils") ;
		
		if( empty( $supplierId ) ){
			$productSupplier = null ;
		}else{
			$productSupplier = $SqlUtils->getObject("sql_purchase_plan_product_inquiry",array('sku'=>$sku,'planId'=>$planId,'supplierId'=>$supplierId)) ;
		}
		
		$suppliers = $SqlUtils->exeSql("sql_getProductSuppliersBySku",array('realSku'=>$sku)) ;
	?>
  
   <script>
		$(function(){
			if( $("#login_id").val()  ){
				$("#login_id").attr("disabled",true) ;
			}
		})
		function validateForm(){
			if( !$.validation.validate('#personForm').errorInfo ) {
				return true ;
			}
			return false ;
		}
   </script>

</head>
<body class="container-popup">
<!-- apply 主场景 -->
	<div class="apply-page">
		<div class="page-title">
			<h2>供应商询价</h2>
		</div>
		<!-- 页面标题 -->
		<div class="container-fluid">
			<form action="<?php echo $contextPath;?>/supplier/saveProductSupplierXJ" id="personForm" data-widget="validator"
				method="post"  enctype="multipart/form-data"  class="form-horizontal" >
					   
				<input type="hidden" id="id" name="id" value="<?php echo $productSupplier['ID'];?>"/>
				<input type="hidden" id="planId" name="planId" value="<?php echo $planId;?>"/>
				<input type="hidden" id="sku" name="sku" value="<?php echo $sku;?>"/>
				<div class="panel apply-panel">
					<!-- panel 中间内容-->
					<div class="panel-content">
					
				<table class="form-table">
					<tr>
						<th>供应商名称：</th><td>
						<select id="supplierId"  name="supplierId"   >
											<option value="">--</option>
											<?php
													foreach($suppliers as $suppli){
														$suppli = $SqlUtils->formatObject($suppli) ;
														$temp = '' ;
														if( $supplierId == $suppli['ID']){
															$temp = "selected" ;
														}
														echo "<option $temp value='".$suppli['ID']."'>".$suppli['NAME']."</option>" ;
													}
											?>
						</select>
						</td>
					</tr>
					<tr>
						<th>产品重量：</th><td><input type="text" id="weight"  name="weight" 
							value="<?php echo $productSupplier['WEIGHT'];?>"/></td>
					</tr>
					<tr>
						<th>产品尺寸：</th><td><input type="text" id="productSize"  name="productSize" 
							value="<?php echo $productSupplier['PRODUCT_SIZE'];?>"/></td>
					</tr>
					<tr>
						<th>包装尺寸：</th><td><input type="text" id="packageSize"  name="packageSize" 
							value="<?php echo $productSupplier['PACKAGE_SIZE'];?>"/></td>
					</tr>
					<tr>
						<th>生产周期：</th><td><input type="text" id="cycle" name="cycle" 
							value="<?php echo $productSupplier['CYCLE'];?>"/></td>
					</tr>
					<tr>
						<th>包装方式：</th><td><input type="text" id="package" name="package" 
							value="<?php echo $productSupplier['PACKAGE'];?>"/></td>
					</tr>
					<tr>
						<th>付款方式：</th><td><input type="text" id="payment" name="payment" 
							value="<?php echo $productSupplier['PAYMENT'];?>"/></td>
					</tr>
					<tr>
						<th>产品网址：</th><td><input type="text" id="url" name="url" 
							value="<?php echo $productSupplier['URL'];?>"/></td>
					</tr>
					<tr>
						<th>产品图片：</th>
						<td><input name="supplierProductImage" type="file"/></td>
					</tr>
					<tr>
						<th>报价方式：</th><td>
							<table class="table">
								<tr><th style="text-align:center;">数量</th><th style="text-align:center;">报价</th></tr>
								<tr>
									<td><input type="text" id="num1" name="num1"  data-validator="required"
							value="<?php echo $productSupplier['NUM1'];?>"/></td>
									<td><input type="text" id="offer1" name="offer1"   data-validator="required"
							value="<?php echo $productSupplier['OFFER1'];?>"/></td>
								</tr>
								<tr>
									<td><input type="text" id="num2" name="num2" 
							value="<?php echo $productSupplier['NUM2'];?>"/></td>
									<td><input type="text" id="offer2" name="offer2" 
							value="<?php echo $productSupplier['OFFER2'];?>"/></td>
								</tr>
								<tr>
									<td><input type="text" id="num3" name="num3" 
							value="<?php echo $productSupplier['NUM3'];?>"/></td>
									<td><input type="text" id="offer3" name="offer3" 
							value="<?php echo $productSupplier['OFFER3'];?>"/></td>
								</tr>
							</table>
						</td>
					</tr>
					<tr>
						<th>备注：</th><td>
							<textarea id="memo" name="memo"  style="width:80%;height:130px;"><?php echo $productSupplier['MEMO'];?></textarea></td>
					</tr>
				</table>
				</div>
				</div>
				<div class="panel-foot" style="position:fixed;bottom:0px;right:0px;left:0px;z-index:1;background-color:#FFF;">
					<div class="form-actions  ">
						<input type="submit" class="btn btn-primary" value="保存"/>
					</div>
				</div>
			</form>
		</div>
</div>
</body>
</html>
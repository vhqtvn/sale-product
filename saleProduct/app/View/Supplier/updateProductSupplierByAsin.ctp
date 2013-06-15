<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
   <?php echo $this->Html->charset(); ?>
    <title>产品开发询价</title>
    <meta http-equiv="pragma" content="no-cache"/>
	<meta http-equiv="cache-control" content="no-cache"/>

   <?php
  		include_once ('config/config.php');
   
		echo $this->Html->meta('icon');
		echo $this->Html->css('../grid/grid');
		echo $this->Html->css('../js/validator/jquery.validation');
		echo $this->Html->css('../js/listselectdialog/jquery.listselectdialog');
		echo $this->Html->css('default/style');

		echo $this->Html->script('jquery');
		echo $this->Html->script('common');
		echo $this->Html->script('../grid/query');
		echo $this->Html->script('jquery.json');
		echo $this->Html->script('listselectdialog/jquery.listselectdialog');
		echo $this->Html->script('validator/jquery.validation');

		$SqlUtils  = ClassRegistry::init("SqlUtils") ;
		
		$productSupplier = null ;
		$type = $params['arg1'] ;
		$value = $params['arg2'] ;
		$inquiryId = $params['arg3'] ;
		if( !empty($inquiryId) ){
			$productSupplier = $SqlUtils->getObject("sql_getSupplierInquiryByInquiryId",array('id'=>$inquiryId)) ;
		}
		
		$asin = "" ;
		$sku = "" ;
		$suppliers = array() ;
		if( $type =='asin' ){
			$asin = $value ;
		}else if($type == 'sku'){
			$sku = $value ;
			$suppliers = $SqlUtils->exeSql("sql_getProductSuppliersBySku",array('realSku'=>$sku)) ;
		}
	?>
  
   <script>
		$(function(){
				var chargeGridSelect = {
							title:'用户供应商',
							defaults:[],//默认值
							key:{value:'ID',label:'NAME'},//对应value和label的key
							multi:false,
							width:800,
							height:600,
							grid:{
								title:"供应商选择",
								params:{
									sqlId:"sql_supplier_list"
								},
								ds:{type:"url",content:contextPath+"/grid/query"},
								pagesize:10,
								columns:[//显示列
									{align:"center",key:"ID",label:"编号",width:"20%"},
									{align:"center",key:"NAME",label:"名称",sort:true,width:"30%"},
									{align:"center",key:"ADDRESS",label:"地址",sort:true,width:"46%"},
									{align:"center",key:"searchKey",label:"关键字",query:true,hide:true,queryOptions:{placeHolder:"供应商名称、SKU、ASIN、产品名称",style:'width:230px;'}}
								],
								actions:[
								     {label:"添加供应商",action:"window.opener.addSupplier"}
								]
							}
					   } ;
					   
					$(".select-supplier").listselectdialog( chargeGridSelect,function(){
						var args = jQuery.dialogReturnValue() ;
						var value = args.value ;
						var label = args.label ;
						$("#supplierId").val(value) ;
						$("#supplierName").val(label) ;
						return false;
					}) ;

					$(".supplier-select").click(function(){
						openCenterWindow(contextPath+"/supplier/listsSelectBySku/<?php echo $sku;?>",800,600,function(){
							window.location.reload() ;
						}) ;
					}) ;

		})
		
		function addSupplier(){
			openCenterWindow(contextPath+"/supplier/add/asin/<?php echo $asin;?>", 800,600,function(){
				var result = jQuery.dialogReturnValue() ;
				var value = result.id ;
				var label = result.name ;
				$("#supplierId").val(value) ;
				$("#supplierName").val(label) ;
			}) ;
			this.close();
		}
		
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
			<h2>产品开发询价</h2>
		</div>
		<!-- 页面标题 -->
		<div class="container-fluid">
			<form action="<?php echo $contextPath;?>/supplier/saveProductSupplierXJ" id="personForm" data-widget="validator"
				method="post"  enctype="multipart/form-data"  class="form-horizontal" >
				<input type="hidden" id="asin" name="asin" value="<?php echo $asin;?>"/>	 
				<input type="hidden" id="sku" name="sku" value="<?php echo $sku;?>"/>	   
				<input type="hidden" id="id" name="id" value="<?php echo $productSupplier['ID'];?>"/>
				<div class="panel apply-panel">
					<!-- panel 中间内容-->
					<div class="panel-content">
					
				<table class="form-table">
					<?php  if( !empty($sku) ){ ?>
					<tr>
						<th>供应商名称：</th><td>
						<select id="supplierId"  name="supplierId"   >
											<option value="">--</option>
											<?php
													foreach($suppliers as $suppli){
														$suppli = $SqlUtils->formatObject($suppli) ;
														$temp = '' ;
														if( $productSupplier['SUPPLIER_ID'] == $suppli['ID']){
															$temp = "selected" ;
														}
														echo "<option $temp value='".$suppli['ID']."'>".$suppli['NAME']."</option>" ;
													}
											?>
						</select>
						<button class="supplier-select btn">选择供应商</button>
						</td>
					</tr>
					<?php }else{ ?>
						<tr>
						<th>供应商名称：</th><td>
							<input id="supplierId" name="supplierId"  type="hidden" value="<?php echo $productSupplier['SUPPLIER_ID']; ?>"/>
							<input id="supplierName" name="supplierName"  type="text" readonly value="<?php echo $productSupplier['SUPPLIER_NAME']; ?>"/>
							<button class="btn select-supplier">选择供应商</button>
						</td>
					</tr>
					<?php }?>
					
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
						<th>装箱规格：</th><td><input type="text" id="packingSpecifications" name="packingSpecifications" 
							value="<?php echo $productSupplier['PACKINGS_PECIFICATIONS'];?>"/></td>
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
						<th>备注：</th>
						<td>
							<textarea id="memo" name="memo"  style="width:80%;height:130px;" placeHolder="供应商信息与其他"><?php echo $productSupplier['MEMO'];?></textarea></td>
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
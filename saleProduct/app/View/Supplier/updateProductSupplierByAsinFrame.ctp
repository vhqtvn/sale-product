<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
   <?php echo $this->Html->charset(); ?>
    <title>产品开发询价</title>
    <meta http-equiv="pragma" content="no-cache"/>
	<meta http-equiv="cache-control" content="no-cache"/>

   <?php
  		include_once ('config/config.php');
  		include_once ('config/header.php');
   

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
							height:500,
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
								     {label:"添加供应商",action:"dialog.settings.opener.addSupplier",afterClose:true}
								]
							}
					   } ;
					  /*
					$(".select-supplier").listselectdialog( chargeGridSelect,function(){
						var args = jQuery.dialogReturnValue() ;
						if(!args) return ;
						var value = args.value ;
						var label = args.label ;
						$("#supplierId").val(value) ;
						$("#supplierName").val(label) ;
						return false;
					},{showType:"dialog"}) ;

					$(".supplier-select").click(function(){
						openCenterWindow(contextPath+"/supplier/listsSelectBySku/<?php echo $sku;?>",800,400,function(){
							var dr = $.dialogReturnValue()  ;
							if( dr ){
								window.location.reload() ;
							}
						},{showType:"dialog"}) ;
					}) ;*/

				   $(".add-supplier").click(function(){
					   <?php  if( !empty($sku) ){ ?>
					   addSupplierBySku() ;
					   <?php }else{ ?>
					   addSupplier() ;
					   <?php }?>
					 }) ;

		});
		var isAddSupplierBySku = false ;
		function addSupplierBySku(){
			isAddSupplierBySku = true ;
			openCenterWindow(contextPath+"/supplier/add/sku/<?php echo $sku;?>", 850,500,function(){
				var result = jQuery.dialogReturnValue() ;
				if(!result) return ;
				var value = result.id ;
				var label = result.name ;
				$("select[name='supplierId']").append( "<option value='"+value+"' selected='selected'>"+label+"</option>" );
				$("select[name='supplierId']").val(value) ;
				$("#supplierId").comboBox().setValue(value);
			},{showType:"dialog"}) ;
			//this.close();
		}
		
		function addSupplier(){
			openCenterWindow(contextPath+"/supplier/add/asin/<?php echo $asin;?>", 850,500,function(){
				var result = jQuery.dialogReturnValue() ;
				if(!result) return ;
				var value = result.id ;
				var label = result.name ;
				$("select[name='supplierId']").append( "<option value='"+value+"' selected='selected'>"+label+"</option>" );
				$("select[name='supplierId']").val(value) ;
				$("#supplierId").comboBox().setValue(value);
				return false ;
			},{showType:"dialog"}) ;
			//this.close();
		}
		
		function validateForm(){
			if( !$.validation.validate('#personForm').errorInfo ) {
				return true ;
			}
			return false ;
		}

		 function saveXj(){
			 $('#personForm').submit() ;
		 }

		 $(function(){
			    $("#supplierId").comboBox({
					source : 'remote',
				    CommandName : 'sqlId:list_supplier_4_autocomplete',
				    variableName : 'INPUT_VALUE',
				    params : {
				    	catalogId : 'sale'
				    }
				});
		 }) ;
   </script>

</head>
<body class="container-popup">
<!-- apply 主场景 -->
	<div class="apply-page">
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
						<button class="add-supplier btn"  title="添加供应商">+</button>
						</td>
						<th>产品图片：</th>
						<td><input name="supplierProductImage" type="file"/></td>
					</tr>
					<?php }else{ ?>
					<tr>
						<th>供应商名称：</th><td>
							<?php /*
							<input id="supplierId1" name="supplierId11"  type="hidden" value="<?php echo $productSupplier['SUPPLIER_ID']; ?>"/>
							<input id="supplierName111" name="supplierName11"  type="text"  value="<?php echo $productSupplier['SUPPLIER_NAME']; ?>"/>
							*/
							 ?>
							<select id="supplierId"  name="supplierId"  >
								<option value="<?php echo $productSupplier['SUPPLIER_ID']; ?>" selected="selected"><?php echo $productSupplier['SUPPLIER_NAME']; ?></option>
							</select>
							<button class="btn add-supplier" title="添加供应商">+</button>
						</td>
						<th>产品图片：</th>
						<td><input name="supplierProductImage" type="file"/></td>
					</tr>
					<?php }?>
					
					<tr>
						<th>产品重量(kg)：</th><td><input type="text" id="weight"  name="weight"   data-validator="double"
							value="<?php echo $productSupplier['WEIGHT'];?>"/></td>
						<th>包装尺寸(cm)：</th><td>
							长<input type="text" id="productLength"  style="width:50px;" name="productLength"    data-validator="double"
							value="<?php echo $productSupplier['PRODUCT_LENGTH'];?>"/>*
							宽<input type="text" id="productWidth"  name="productWidth"   style="width:50px;"   data-validator="double"
							value="<?php echo $productSupplier['PRODUCT_WIDTH'];?>"/>*
							高<input type="text" id="productHeight"  name="productHeight"   style="width:50px;"   data-validator="double"
							value="<?php echo $productSupplier['PRODUCT_HEIGHT'];?>"/>	
						</td>
					</tr>
					<tr>
						<th>生产周期：</th><td>
							<select  id="cycle" name="cycle" >
												<option value="">--</option>
												<option value="1"  <?php if( $productSupplier['CYCLE'] == '1' ) echo 'selected' ;?>>常备库存</option>
												<option value="2" <?php if( $productSupplier['CYCLE'] == '2' ) echo 'selected' ;?>>少量库存</option>
												<option value="3" <?php if( $productSupplier['CYCLE'] == '3' ) echo 'selected' ;?>>3天内</option>
												<option value="7" <?php if( $productSupplier['CYCLE'] == '7' ) echo 'selected' ;?>>7天内</option>
												<option value="15" <?php if( $productSupplier['CYCLE'] == '15' ) echo 'selected' ;?>>15天以内</option>
												<option value="30" <?php if( $productSupplier['CYCLE'] == '30' ) echo 'selected' ;?>>30天以内</option>
												<option value="31" <?php if( $productSupplier['CYCLE'] == '31' ) echo 'selected' ;?>>30天以上</option>
											</select>
							</td>
							<th>付款方式：</th><td>
							<select  id="payment" name="payment" >
								<option value="">--</option>
								<option value="dh"  <?php if( $productSupplier['PAYMENT'] == 'dh' ) echo 'selected' ;?>>电汇</option>
								<option value="zfb" <?php if( $productSupplier['PAYMENT'] == 'zfb' ) echo 'selected' ;?>>支付宝</option>
								<option value="df" <?php if( $productSupplier['PAYMENT'] == 'df' ) echo 'selected' ;?>>物流代收</option>
								<option value="zqzf" <?php if( $productSupplier['PAYMENT'] == 'zqzf' ) echo 'selected' ;?>>账期支付</option>
							</select>
						</td>
					</tr>
					<tr>
						<th>包装方式：</th><td>
						<select  id="package" name="package" >
							<option value="">--</option>
							<option value="1"  <?php if( $productSupplier['PACKAGE'] == '1' ) echo 'selected' ;?>>祼包装</option>
							<option value="2" <?php if( $productSupplier['PACKAGE'] == '2' ) echo 'selected' ;?>>塑料袋</option>
							<option value="3" <?php if( $productSupplier['PACKAGE'] == '3' ) echo 'selected' ;?>>塑料盒</option>
							<option value="4" <?php if( $productSupplier['PACKAGE'] == '4' ) echo 'selected' ;?>>吸塑</option>
							<option value="5" <?php if( $productSupplier['PACKAGE'] == '5' ) echo 'selected' ;?>>瓦楞盒</option>
							<option value="6" <?php if( $productSupplier['PACKAGE'] == '6' ) echo 'selected' ;?>>白盒</option>
							<option value="7" <?php if( $productSupplier['PACKAGE'] == '7' ) echo 'selected' ;?>>彩盒</option>
						</select>
						</td>
						<th>产品网址：</th><td><input type="text" id="url" name="url" 
							value="<?php echo $productSupplier['URL'];?>"/></td>
					</tr>
					<tr>
						<th>装箱规格：</th><td colspan="3"><input type="text" id="packingSpecifications" name="packingSpecifications" 
							value="<?php echo $productSupplier['PACKINGS_PECIFICATIONS'];?>"/></td>
					</tr>
					<tr>
					<th>报价方式(RMB)：
						数量/报价(RMB)/运费(RMB)
					</th><td colspan="3">
							<table class="table">
								<tr><th style="text-align:center;">报价一</th><th style="text-align:center;">报价二</th><th style="text-align:center;">报价三</th></tr>
								<tr>
									<td><input type="text" id="num1" name="num1"  data-validator="required,integer"  style="width:50px" placeHolder="数量"
							value="<?php echo $productSupplier['NUM1'];?>"/>/
									<input type="text" id="offer1" name="offer1"   data-validator="required,double" style="width:50px"  placeHolder="报价"
							value="<?php echo $productSupplier['OFFER1'];?>"/>/
									<select id="num1ShipFee" name="num1ShipFee"  style="width:80px"  >
										<option value="">-运费-</option>
										<option value="1"  <?php echo $productSupplier['NUM1_SHIP_FEE']==1?"selected":"";?>>包邮</option>
										<option value="2"  <?php echo $productSupplier['NUM1_SHIP_FEE']==2?"selected":"";?>>免邮</option>
										<option value="3"  <?php echo $productSupplier['NUM1_SHIP_FEE']==3?"selected":"";?>>卖家支付</option>
									</select>
							</td>
									<td><input type="text" id="num2" name="num2"  data-validator="integer" style="width:50px" placeHolder="数量"
							value="<?php echo $productSupplier['NUM2'];?>"/>/
								<input type="text" id="offer2" name="offer2"   data-validator="double" style="width:50px"  placeHolder="报价"
							value="<?php echo $productSupplier['OFFER2'];?>"/>/
								<select id="num2ShipFee" name="num2ShipFee"  style="width:80px"  >
										<option value="">-运费-</option>
										<option value="1"  <?php echo $productSupplier['NUM2_SHIP_FEE']==1?"selected":"";?>>包邮</option>
										<option value="2"  <?php echo $productSupplier['NUM2_SHIP_FEE']==2?"selected":"";?>>免邮</option>
										<option value="3"  <?php echo $productSupplier['NUM2_SHIP_FEE']==3?"selected":"";?>>卖家支付</option>
									</select>
							</td>
									<td><input type="text" id="num3" name="num3"   data-validator="integer" style="width:50px" placeHolder="数量"
												value="<?php echo $productSupplier['NUM3'];?>"/>/
									<input type="text" id="offer3" name="offer3"   data-validator="double" style="width:50px"  placeHolder="报价"
												value="<?php echo $productSupplier['OFFER3'];?>"/>
									/<select id="num3ShipFee" name="num3ShipFee"  style="width:80px"  >
										<option value="">-运费-</option>
										<option value="1"  <?php echo $productSupplier['NUM3_SHIP_FEE']==1?"selected":"";?>>包邮</option>
										<option value="2"  <?php echo $productSupplier['NUM3_SHIP_FEE']==2?"selected":"";?>>免邮</option>
										<option value="3"  <?php echo $productSupplier['NUM3_SHIP_FEE']==3?"selected":"";?>>卖家支付</option>
									</select>
							</td>
								</tr>
							</table>
						</td>
					</tr>
					
					<tr>
						<th>备注：</th>
						<td colspan="3">
							<textarea id="memo" name="memo"  style="width:80%;height:50px;" placeHolder="供应商信息与其他"><?php echo $productSupplier['MEMO'];?></textarea></td>
					</tr>
				</table>
				</div>
				</div>
			</form>
		</div>
</div>
</body>
</html>
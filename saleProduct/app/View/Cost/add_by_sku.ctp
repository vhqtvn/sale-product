<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
   <?php echo $this->Html->charset(); ?>
    <title>成本编辑</title>
    <meta http-equiv="pragma" content="no-cache"/>
	<meta http-equiv="cache-control" content="no-cache"/>

   <?php
   	   include_once ('config/config.php');
   
   		echo $this->Html->meta('icon');
		echo $this->Html->css('../js/validator/jquery.validation');
		echo $this->Html->css('default/style');

		echo $this->Html->script('jquery');
		echo $this->Html->script('common');
		echo $this->Html->script('jquery.json');
		echo $this->Html->script('validator/jquery.validation');
		
		$security  = ClassRegistry::init("Security") ;
		$loginId   = $user['LOGIN_ID'] ;
		
		$COST_EDIT_PURCHASE  				= $security->hasPermission($loginId , 'COST_EDIT_PURCHASE') ;
		$COST_EDIT_LOGISTIC  					= $security->hasPermission($loginId , 'COST_EDIT_LOGISTIC') ;
		$COST_EDIT_PRODUCT_CHANNEL 	= $security->hasPermission($loginId , 'COST_EDIT_PRODUCT_CHANNEL') ;
		$COST_EDIT_FEE    							= $security->hasPermission($loginId , 'COST_EDIT_FEE') ;
		$COST_EDIT_OTHER   						= $security->hasPermission($loginId , 'COST_EDIT_OTHER') ;
		$COST_EDIT_SALEPRICE   				= $security->hasPermission($loginId , 'COST_EDIT_SALEPRICE') ;
		$COST_EDIT_PROFIT   						= $security->hasPermission($loginId , 'COST_EDIT_PROFIT') ;
		
		$COST_VIEW_TOTAL  						= $security->hasPermission($loginId , 'COST_VIEW_TOTAL') ;
		$COST_VIEW_PROFIT  						= $security->hasPermission($loginId , 'COST_VIEW_PROFIT') ||$COST_EDIT_PROFIT  ;
		$COST_VIEW_PURCHASE  				= ( $security->hasPermission($loginId , 'COST_VIEW_PURCHASE') )||$COST_EDIT_PURCHASE ;
		$COST_VIEW_LOGISTIC  					= ( $security->hasPermission($loginId , 'COST_VIEW_LOGISTIC') )|| $COST_EDIT_LOGISTIC ;
		$COST_VIEW_PRODUCT_CHANNEL =(  $security->hasPermission($loginId , 'COST_VIEW_PRODUCT_CHANNEL')  )|| $COST_EDIT_PRODUCT_CHANNEL ;
		$COST_VIEW_FEE  							= ( $security->hasPermission($loginId , 'COST_VIEW_FEE')  )|| $COST_EDIT_FEE ;
		$COST_VIEW_OTHER  						=(  $security->hasPermission($loginId , 'COST_VIEW_OTHER')  )|| $COST_EDIT_OTHER ;
		$COST_VIEW_SALEPRICE					= ( $security->hasPermission($loginId , 'COST_VIEW_SALEPRICE') )|| $COST_EDIT_SALEPRICE ;
		
		$COST_EDIT = $COST_EDIT_PURCHASE || $COST_EDIT_LOGISTIC || $COST_EDIT_PRODUCT_CHANNEL || $COST_EDIT_FEE||$COST_EDIT_OTHER||$COST_EDIT_SALEPRICE||$COST_EDIT_PROFIT ;
	
		$type = $params['arg1'] ;
		$value = $params['arg2'] ;
		$id = $params['arg3'] ;

		$asin = "" ;
		$sku = "" ;
		$suppliers = array() ;
		if( $type =='asin' ){
			$asin = $value ;
		}else if($type == 'sku'){
			$sku = $value ;
		}
		
		$productCost = null ;
		if( !empty($id) ){
			$Cost  = ClassRegistry::init("Cost") ;
			$productCost =  $Cost->getProductCost( $id  ) ;
		}
	?>
   <style>

		th{
			width:120px!important;
		}
		
		.form-table{
			margin-bottom:5px!important;
		}
		
		caption{
			height:25px;
			line-height:25px;
		}
   </style>

   <script>
   		var groupCode = '<?php echo $loginId;?>' ;

   		$(function(){
   					
   					$(".save-btn").click(function(){
   						if( !$.validation.validate('#personForm').errorInfo ) {
   							var json = $("#personForm").toJson() ;
   							
   							$.dataservice("model:Cost.saveCost" , json , function(){
   								window.close() ;
   							})

   						};
   						return false ;
   					}) ;
   					
   					$(".cost,.sale-price").keyup(function(){
   						calcTotalCost() ;
   					}) ;
   					$(".cost,.sale-price").blur(function(){
   						calcTotalCost() ;
   					}) ;
   					
   					calcTotalCost() ;

   					//////////////////////
   					$(".profit-confirm").click(function(){
   						calcProfit() ;
   						return false ;
   					}) ;	
   		}) ;
   				
   				function calcTotalCost(){
   						var totalCost = 0 ;
   						$(".cost").each(function(){
   							totalCost = totalCost + parseFloat($(this).val()||0) ;
   						}) ;
   						$("#TOTAL_COST").val(totalCost.toFixed(2)) ;//成本
   						
   				}

   		function calcProfit(){
   			var totalCost = 0 ;
   			$(".cost").each(function(){
   				totalCost = totalCost + parseFloat($(this).val()||0) ;
   			}) ;
   			//销售价格
   			var salePrice = $(".sale-price").val() ;
   			//计算利润 profit-num  profit-margins
   			var profitNum =  (salePrice - totalCost.toFixed(2)).toFixed(2)  ;
   			var profitMargin = ((profitNum/totalCost.toFixed(2)).toFixed(4)*100).toFixed(2)+"%" ;
   			$(".profit-num").val( salePrice - totalCost.toFixed(2) ) ;
   			$(".profit-margins").val( profitMargin ) ;
   		}
   </script>
</head>


<body class="container-popup">
	<!-- apply 主场景 -->
	<div class="apply-page">
		<!-- 页面标题 -->
		<div class="page-title">
			<h2>成本信息</h2>
		</div>
		<div class="container-fluid">
	        <form id="personForm" action="#" data-widget="validator,ajaxform" class="form-horizontal" >
				<div class="panel apply-panel">
					<!-- panel 中间内容-->
					<div class="panel-content">
						<!-- 数据列表样式 -->
						<input type="hidden" id="ID" value="<?php echo $id;?>"/>
						<input type="hidden" id="SKU" value="<?php echo $sku;?>"/>
						<input type="hidden" id="ASIN" value="<?php echo $asin;?>"/>
						
						<table class="form-table" >
							<tr>
								<th>成本类型：</th>
								<td colspan="5">
									<select id="TYPE" class=" span2"  <?php echo $COST_EDIT_PRODUCT_CHANNEL?"":"disabled"?>>
										<option value=""></option>
										<option value="FBM" <?php if($productCost[0]["sc_product_cost"]["TYPE"] == 'FBM') echo 'selected';?> >FBM</option>
										<option value="FBA" <?php if($productCost[0]["sc_product_cost"]["TYPE"] == 'FBA') echo 'selected';?>>FBA</option>
									</select>
								</td>
							</tr>
						</table>
						
						<table  class="form-table" style="<?php echo $COST_VIEW_PURCHASE?'':'display:none;'?>">
							<caption>采购成本</caption>
							<tr>
								<th>采购费用：</th>
								<td><input class="cost span2"  type="text" 
									data-validator="double"
									<?php echo $COST_EDIT_PURCHASE?'':'disabled'?>
									id="PURCHASE_COST" value="<?php echo $productCost[0]["sc_product_cost"]["PURCHASE_COST"];?>"/></td>
							</tr>
						</table>
						
						<table  class="form-table" style="<?php echo $COST_VIEW_LOGISTIC?'':'display:none;'?>">
							<caption>物流成本</caption>
							<tr>
								<th>入库前物流费用：</th><td><input class="cost span1"  <?php echo $COST_EDIT_LOGISTIC?'':'disabled'?>  data-validator="double" type="text" id="BEFORE_LOGISTICS_COST" value="<?php echo $productCost[0]["sc_product_cost"]["BEFORE_LOGISTICS_COST"];?>"/></td>
								<th>关税：</th><td><input class="cost span1" <?php echo $COST_EDIT_LOGISTIC?'':'disabled'?> data-validator="double"   type="text" id="TARIFF" value="<?php echo $productCost[0]["sc_product_cost"]["TARIFF"];?>"/></td>
								<th>仓储费用 ：</th><td><input class="cost span1" <?php echo $COST_EDIT_LOGISTIC?'':'disabled'?> data-validator="double"   type="text" id="WAREHOURSE_COST" value="<?php echo $productCost[0]["sc_product_cost"]["WAREHOURSE_COST"];?>"/></td>
								<th>USPS邮费 ：</th><td><input  class="cost span1"  <?php echo $COST_EDIT_LOGISTIC?'':'disabled'?> data-validator="double"   type="text" id="USPS_COST" value="<?php echo $productCost[0]["sc_product_cost"]["USPS_COST"];?>"/></td>
							</tr>
							
						</table>
						
						<table  class="form-table" style="<?php echo $COST_VIEW_PRODUCT_CHANNEL?'':'display:none;'?>">
							<caption>产品成本</caption>
							<tr>
								<th>amazon佣金：</th><td><input class="cost span2"  <?php echo $COST_EDIT_PRODUCT_CHANNEL?'':'disabled'?>  data-validator="double"  type="text" id="AMAZON_FEE" value="<?php echo $productCost[0]["sc_product_cost"]["AMAZON_FEE"];?>"/></td>
								<th>可变关闭费用：</th><td><input class="cost span2"  <?php echo $COST_EDIT_PRODUCT_CHANNEL?'':'disabled'?>  data-validator="double"   type="text" id="VARIABLE_CLOSURE_COST" value="<?php echo $productCost[0]["sc_product_cost"]["VARIABLE_CLOSURE_COST"];?>"/></td>
								<th>标签费用 ：</th><td><input class="cost span2"   <?php echo $COST_EDIT_PRODUCT_CHANNEL?'':'disabled'?>  data-validator="double"  type="text" id="TAG_COST" value="<?php echo $productCost[0]["sc_product_cost"]["TAG_COST"];?>"/></td>
							</tr>
							<tr>
								<th>打包费：</th><td><input class="cost span2"   <?php echo $COST_EDIT_PRODUCT_CHANNEL?'':'disabled'?>  data-validator="double"  type="text" id="PACKAGE_COST" value="<?php echo $productCost[0]["sc_product_cost"]["PACKAGE_COST"];?>"/></td>
								<th>订单处理费：</th><td><input class="cost span2"  <?php echo $COST_EDIT_PRODUCT_CHANNEL?'':'disabled'?>  data-validator="double"   type="text" id="OORDER_PROCESSING_FEE" value="<?php echo $productCost[0]["sc_product_cost"]["OORDER_PROCESSING_FEE"];?>"/></td>
								<th>称重费 ：</th><td><input class="cost span2"  <?php echo $COST_EDIT_PRODUCT_CHANNEL?'':'disabled'?>   data-validator="double"  type="text" id="STABLE_COST" value="<?php echo $productCost[0]["sc_product_cost"]["STABLE_COST"];?>"/></td>
							</tr>
						</table>
						
						<table  class="form-table" style="<?php echo $COST_VIEW_FEE?'':'display:none;'?>">
							<caption>会计</caption>
							<tr>
								<th>当地税费  ：</th><td><input class="cost span2"  <?php echo $COST_EDIT_FEE?'':'disabled'?>  data-validator="double"  type="text" id="LOST_FEE" value="<?php echo $productCost[0]["sc_product_cost"]["LOST_FEE"];?>"/></td>
								<th>人工成本：</th><td><input  class="cost span2"   <?php echo $COST_EDIT_FEE?'':'disabled'?>  data-validator="double"  type="text" id="LABOR_COST" value="<?php echo $productCost[0]["sc_product_cost"]["LABOR_COST"];?>"/></td>
								<th> 服务成本  ：</th><td colspan="3"><input class="cost span2"   <?php echo $COST_EDIT_FEE?'':'disabled'?>   data-validator="double"  type="text" id="SERVICE_COST" value="<?php echo $productCost[0]["sc_product_cost"]["SERVICE_COST"];?>"/></td>
							</tr>
						</table>
						
						<table  class="form-table" style="<?php echo $COST_VIEW_OTHER?'':'display:none;'?>">
							<tr>
								<th>其他成本 ：</th><td><input <?php echo $COST_EDIT_OTHER?'':'disabled'?> class="cost span2"  data-validator="double"  type="text" id="OTHER_COST" value="<?php echo $productCost[0]["sc_product_cost"]["OTHER_COST"];?>"/></td>
							</tr>
						</table>
						
						<div class="alert alert-info area" style="width:50%;"  style="<?php echo $COST_VIEW_TOTAL?'':'display:none;'?>">
						总成本:&nbsp;<input type="text" id="TOTAL_COST"  data-validator="double"  readonly="readonly"  value="<?php echo $productCost[0]["sc_product_cost"]["TOTAL_COST"];?>"/>
						</div>
						
						<table  class="form-table">
							<tr style="<?php echo $COST_VIEW_SALEPRICE?'':'display:none;'?>">
								<th>销售价格  ：</th><td colspan="4"  ><input  <?php echo $COST_EDIT_SALEPRICE?'':'disabled'?> class="sale-price span2"  data-validator="double"  type="text" id="SALE_PRICE" value="<?php echo $productCost[0]["sc_product_cost"]["SALE_PRICE"];?>"/></td>
							</tr>
							<tr  style="<?php echo $COST_VIEW_PROFIT?'':'display:none;'?>">
								<th>利润  ：</th><td><input class=" span2  profit-num"   disabled  type="text"  id="PROFIT_NUM"  value="<?php echo $productCost[0]["sc_product_cost"]["PROFIT_NUM"];?>"/></td>
								<th>利润率：</th><td><input  class=" span2 profit-margins"  disabled type="text"    id="PROFIT_MARGINS"   value="<?php echo $productCost[0]["sc_product_cost"]["PROFIT_MARGINS"];?>"/></td>
								<td><style="<?php echo $COST_EDIT_PROFIT?'':'display:none;'?>"  button class="btn btn-primary profit-confirm">利润确认</button></td>
							</tr>
						</table>
					</div>
					
					<?php if($COST_EDIT){ ?>
					<!-- panel脚部内容-->
                    <div class="panel-foot">
						<div class="form-actions">
							<button type="submit" class="btn btn-primary save-btn">保存</button>
							<button type="button" class="btn" onclick="window.close()">关闭</button>
						</div>
					</div>
					<?php } ?>
				</div>
			</form>
		</div>
	</div>
</body>

</html>
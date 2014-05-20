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
		echo $this->Html->script('modules/cost/cost');
		
		$security  = ClassRegistry::init("Security") ;
		$config  = ClassRegistry::init("Config") ;
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
	
		$asin = $params['arg1'] ;
	
		$SqlUtils  = ClassRegistry::init("SqlUtils") ;//COST_TAG  COST_LABOR  COST_TAX_RATE
		$Cost  = ClassRegistry::init("Cost") ;
		
		$costTag = $config->getAmazonConfig("COST_TAG",0) ;
		$costLabor = $config->getAmazonConfig("COST_LABOR",0) ;
		$costTaxRate = $config->getAmazonConfig("COST_TAX_RATE",0.0) ;
		
		$Cost->initDevCost($asin,$user['LOGIN_ID']) ;
		$sql = "SELECT sc_product_cost.* FROM sc_product_cost where asin = '$asin'";
		$productCost = $SqlUtils->getObject($sql,array()) ;
		
		//获取开发Listing成本信息
		//$sql = "select * from sc_view_devproduct_cost where asin = '{@#realId#}'  order by type" ;
		$sql = "sql_cost_new_DevCostEvlate" ;
		$listing = $SqlUtils->exeSqlWithFormat($sql,array("asin"=>$asin)) ;
		
		//获取最近的询价
		$sql = "SELECT * FROM  sc_purchase_supplier_inquiry spsi
										WHERE spsi.ASIN = '{@#asin#}'
										ORDER BY spsi.CREATE_TIME DESC
										LIMIT 0,1  " ;
		$inquiryCost = $SqlUtils->getObject($sql,array("asin"=>$asin)) ;
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
		.listing-cost th{
			text-align: center!important;
		}
   </style>

   <script>
   		var groupCode = '<?php echo $loginId;?>' ;
		var  $listing = <?php echo  json_encode($listing) ; ?>;

   		$(function(){
		   			$(".amazon-asyn-listing").click(function(){
						var row = $(this).closest("tr") ;
						var asin = row.find("[name='ASIN']").val() ;
						$(this).text("同步中..") ;
						$.ajax({
							type:"post",
							url:contextPath+"/taskFetch/formatAsinFee/"+asin,
							data:{},
							cache:false,
							dataType:"text",
							success:function(result,status,xhr){
								window.location.reload() ;
							},error:function(){
								alert("操作出现异常！") ;
								$(this).text("同步") ;
							}
						}); 
					}) ;

		   			
		   			$(".amazon-asin-cost").click(function(){
						var row = $(this).closest("tr") ;
						var asin = row.find("[name='ASIN']").val() ;
		   				openCenterWindow(contextPath+"/page/forward/Cost.editAsinCost/"+asin,600,350,function(){
							window.location.reload() ;
						}) ;
					}) ;
   	   		
   				
   					$(".save-btn").click(function(){
   						if( !$.validation.validate('#personForm').errorInfo ) {
   							var productCost = $(".product-cost").toJson() ;
   							var listingCosts = [] ;
   							$(".listing-cost .data-row").each(function(index,row){
									var listingCost = $(this).toJson() ;
									listingCosts.push(listingCost) ;
   	   						});

   							//alert(  $.json.encode({productCost:productCost,listingCosts:listingCosts}) ) ;
   							//return ;
   							$.dataservice("model:Cost.saveCostAsin" , {productCost:productCost,listingCosts:listingCosts} , function(){
   								window.location.reload();
   							})

   						};
   						return false ;
   					}) ;

   					addAlert();
   		}) ;

   		function addAlert(){
			$("._cost").each(function(){
				if(!$(this).val() || $(this).val()== '0'){
					$(this).addClass("alert-danger") ;	
				}else{
					$(this).removeClass("alert-danger") ;	
				}
			}) ;
   	   	}

   		/**
		   *实时计算
		   */
	   	function calcCostOntime(){
    	 var purchaseCost = $("#PURCHASE_COST").val()||0  ;
    	 var baseCost =parseFloat( purchaseCost )+ parseFloat($("#LOGISTICS_COST").val()||0 )+parseFloat($("#OTHER_COST").val()||0)
    	 										 + parseFloat(<?php echo $costTag;?>)+parseFloat(<?php echo $costLabor;?>)  ;
			 var $costTaxRate = parseFloat(<?php echo $costTaxRate ;?>) ;
			 var purcharRate =parseFloat( (purchaseCost*$costTaxRate).toFixed(2)) ;
			 $(".purchaseRate").html(purcharRate);
			 baseCost = baseCost + purcharRate ;

			 $( $listing  ).each(function(index,item){
				var rowId = item.ASIN+"_"+item.TYPE ;
				var cost = new Cost() ;
				cost.setProductCost( baseCost , 6.04  ) ;// item.EXCHANGE_RATE
				cost.setChannel(  item.TYPE ) ;
				cost.setSellPrice( $("[name='SELLER_COST']","#"+rowId).val()   ) ;
				cost.setChannelFeeRatio( item.COMMISSION_RATIO ) ;
				cost.setVariableCloseFee( item.VARIABLE_CLOSING_FEE ) ;
				cost.setCommissionLowlimit( item.COMMISSION_LOWLIMIT ) ;
				cost.setFbaCost(  item._FBA_COST ) ;
				cost.setTransferUnitPrice(5.41);// item.TRANSFER_WH_PRICE ) ;
				cost.setFbcOrderRate(16.22);// item.FBC_ORDER_RATE ) ;
				cost.setFbmOrderRate(16.22);// item.FBM_ORDER_RATE ) ;
				cost.setTransferProperties( {weight: '<?php echo $inquiryCost['WEIGHT'];?>' ,packageWeight: '<?php echo $inquiryCost['PACKAGE_WEIGHT'];?>' } ) ;

				var costValue = cost.evlate() ;
				var _cost = (parseFloat(costValue.cost)).toFixed(2);
				var  totalProfile =  (parseFloat(costValue.profile)).toFixed(2);
				var  profileRate =   costValue.profileRatio ;

				$(".COMMISSION_FEE","#"+rowId).html( cost.getChannelFee() +"("+cost.getChannelFeeRatioFormat()+")") ;
				$(".transferCost","#"+rowId).html( cost.getTransferCost() ) ;
				$(".totalCost","#"+rowId).html( _cost ) ;
				$(".payCost","#"+rowId).html( cost.getCalcCostAbale() ) ;
				$(".fbaCost","#"+rowId).html( cost.getFbaCost() ) ;
				$(".inventoryCenterFee","#"+rowId).html( cost.getInventoryCenterFee() ) ;
				$(".orderTransferCost","#"+rowId).html( cost.getOrderTransferCost() ) ;
				
				$(".totalProfile","#"+rowId).html( totalProfile+"["+profileRate+"]" ) ;//profile profileRatio
			 }) ;
	   	}

	   	$(function(){
	   	   	calcCostOntime() ;
	   	   	$("input[type='text']").keyup(function(){
	   	   	  calcCostOntime() ;
	   	   	});
	   	   	
	   	 	$("input[type='text']").blur(function(){
	   	   	  calcCostOntime() ;
	   	   	});
	   	}) ;
   	
   </script>
</head>


<body class="container-popup" >
	<!-- apply 主场景 -->
	<div class="apply-page" >
		<!-- 页面标题 -->
		<div class="container-fluid">
	        <form id="personForm" action="#" data-widget="validator,ajaxform" class="form-horizontal" >
				<div class="panel apply-panel">
					<!-- panel 中间内容-->
					<div class="panel-content" style="margin-bottom:50px;">
						<!-- 数据列表样式 -->
						
						<table  class="form-table  product-cost" style="<?php echo $COST_VIEW_PURCHASE?'':'display:none;'?>">
							<caption>货品成本</caption>
							<tr>
								<th>采购成本：</th>
								<td colspan="5">
								<input type="hidden" id="ASIN" value="<?php echo $asin;?>"/>
								
								<input class="cost span2"  type="text" 
									data-validator="double"
									id="PURCHASE_COST" value="<?php echo $inquiryCost["OFFER1"];?>"/>
								</td>
								<th>采购物流费用 ：</th><td ><input class="_cost span2"      style="width:50px!important;"
										data-validator="double"  type="text" id="LOGISTICS_COST" value="<?php echo $productCost["LOGISTICS_COST"];?>"/></td>
							</tr>
							<tr>
									<th>标签费用 ：</th><td >
										<?php echo $costTag ; ?>
									</td>
									<th>人工成本：</th><td><?php echo $costLabor ; ?></td>	
									<th>国内税费：</th>
									<td class="purchaseRate"></td>
									<th>其他成本：</th><td><input      style="width:50px!important;"
										data-validator="double"  type="text" id="OTHER_COST" value="<?php echo $productCost["OTHER_COST"];?>"/></td>
							</tr>
						</table>
						<!--  
						spcd.LOGISTICS_COST, 
						spcd.FEE, 
						spcd.OTHER_COST,  
						spcd.WEIGHT_HANDLING_FEE, 
						spcd.ORDER_HANDLING_FEE, 
						spcd.FBA_DELIVERRY_SERVICES_FEE, 
						spcd.COMMISSION_FEE, 
						spcd.PICK_AND_PACK_FEE, 
						spcd.STORAGE_FEE, 
						spcd.VARIABLE_CLOSING_FEE, 
						spcd.COMMISSION_RATIO, -->
						<table  class="form-table table  listing-cost" >
							<caption>Listing成本</caption>
							<tr>
								<th>操作</th>
								<th>ASIN</th>
								<th>渠道</th>
								<th>售价</th>
								<th>总成本</th>
								<th>支付成本</th>
								<th>利润</th>
								<th>转仓物流成本</th>
								<th>订单物流成本</th>
								<th>税费</th>
								<th>渠道佣金</th>
								<th>可变关闭费</th>
								<th>FBA费用</th>
								<!--
								<th>称重费</th>
								<th>订单处理费</th>
								<th>FBA运输服务费</th>
								<th>打包费</th>
								<th>仓储费</th>
								  -->
								<th>库存集中费</th>
								
								<th>其他成本</th>
							</tr>
							<?php  	foreach( $listing as $item  ){ 
									
							//debug($item) ;
								?>
							<tr  class="data-row"  id="<?php echo $item['ASIN'];?>_<?php echo $item['TYPE'];?>">
								<td>
										<a href="#"  class="amazon-asyn-listing">同步</a>
										<a href="#"  class="amazon-asin-cost">编辑</a>
								</td>
								<td>
									<input type="hidden" name="ASIN"   value="<?php echo $item['ASIN'];?>" style="width:50px;"/>
									<input type="hidden" name="TYPE"   value="<?php echo $item['TYPE'];?>" style="width:50px;"/>
									<a href="#" offer-listing="<?php echo $item['ASIN'];?>"><?php echo $item['ASIN'];?></a>
								</td>
								<td><?php echo $item['TYPE'];?></td>
								<td>
								<input type="text" class="_cost " 
										name="SELLER_COST" value="<?php echo $item['TOTAL_PRICE'];?>" style="width:50px!important;"/>
								</td>
								<td  class="totalCost"></td>
								<td  class="payCost"></td>
								<td  class="totalProfile"></td>
								<td class="transferCost"></td>
								<td  class="orderTransferCost"></td>
								<td class="fee"></td>
								<td  class="COMMISSION_FEE"></td>
								<td><?php echo round($item['VARIABLE_CLOSING_FEE'],3 ) ;?></td>
								<td  class="fbaCost"></td>
								<td class="inventoryCenterFee"><?php echo round( $item['INVENTORY_CENTER_FEE'],3);?></td>
								<td><input type="text"  name="OTHER_COST"  value="<?php echo round($item['OTHER_COST'],2);?>" style="width:50px;"/></td>
							</tr>
							<?php  	} ?>
						</table>
						
					</div>
					
					<!-- panel脚部内容-->
                    <div class="panel-foot"  style="background:#FFF;">
						<div class="form-actions">
							<button type="submit" class="btn btn-primary save-btn">保存</button>
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>
</body>

</html>
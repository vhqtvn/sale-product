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
	
		$realId = $params['arg1'] ;

		$SqlUtils  = ClassRegistry::init("SqlUtils") ;//COST_TAG  COST_LABOR  COST_TAX_RATE
		
		$costTag = $config->getAmazonConfig("COST_TAG",0) ;
		$costLabor = $config->getAmazonConfig("COST_LABOR",0) ;
		$costTaxRate = $config->getAmazonConfig("COST_TAX_RATE",0.0) ;
		
		$sql = "SELECT sc_product_cost.* FROM sc_product_cost where real_id = '$realId'";
		$productCost = $SqlUtils->getObject($sql,array()) ;

		
		$sql = "SELECT saap.ACCOUNT_ID,
							       saap.SKU,
								  saap.FULFILLMENT_CHANNEL,
				                  saap.ASIN,
							       saap.LOWEST_PRICE,
							       saap.LOWEST_FBA_PRICE,
				                saa.name as ACCOUNT_NAME,
								saa.TRANSFER_WH_PRICE,
								saa.FBC_ORDER_RATE,
				                saa.FBM_ORDER_RATE,
								spcd.COMMISSION_RATIO ,
								'' as TOTAL_COST,
								'' as TRANSFER_COST,
								'' as LOGISTICS_COST ,
								saa.FEE_RATIO,
								spcd.FBA_COST as _FBA_COST,
								spcd.COMMISSION_RATIO,
								spcd.VARIABLE_CLOSING_FEE,
								saa.INVENTORY_CENTER_FEE,
								spcd.OTHER_COST,
				                srp.WEIGHT,
								srp.PACKAGE_WEIGHT,
				                srp.LENGTH,
				                srp.WIDTH,
				                srp.HEIGHT,
				              (select ser.EXCHANGE_RATE from sc_exchange_rate ser where ser.id = saa.EXCHANGE_ID ) as EXCHANGE_RATE
					 FROM sc_amazon_account_product saap,
				              sc_amazon_account saa,
				             sc_real_product srp ,
					        sc_real_product_rel srpr
					LEFT JOIN sc_product_cost_details spcd
					       ON spcd.ACCOUNT_ID = srpr.ACCOUNT_ID
					AND spcd.LISTING_SKU = srpr.SKU
					WHERE saap.ACCOUNT_ID = srpr.ACCOUNT_ID
					AND saap.SKU = srpr.SKU
				   and saa.id = saap.account_id
				   and srp.id = srpr.real_id
					AND srpr.REAL_ID = '{@#realId#}' ";
		
		//$sql = "select * from sc_view_listing_cost where id = '{@#realId#}'" ;
		//$listing = null ;
		$listing = $SqlUtils->exeSqlWithFormat($sql,array("realId"=>$realId)) ;
		//debug($listing) ;
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
		var  $productCost = <?php echo  json_encode($productCost) ; ?>;
		var  $listing = <?php echo  json_encode($listing) ; ?>;

   		$(function(){
					$(".amazon-asyn-listing").click(function(){
						var row = $(this).closest("tr") ;
						var accountId = row.find("[name='ACCOUNT_ID']").val() ;
						var listingSku = row.find("[name='LISTING_SKU']").val() ;
						$(this).text("同步中..") ;
						$.ajax({
							type:"post",
							url:contextPath+"/taskFetch/formatListingFee/"+accountId+"/"+listingSku,
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
   	   		
					$(".asyn-amazon-fee").click(function(){
						var productCost = $(".product-cost").toJson() ;
						var listingCosts = [] ;
						$(".listing-cost .data-row").each(function(index,row){
							var listingCost = $(this).toJson() ;
							listingCosts.push(listingCost) ;
   						});

						$.dataservice("model:Cost.saveCostFix" , {productCost:productCost,listingCosts:listingCosts} , function(){
							$.ajax({
								type:"post",
								url:contextPath+"/taskFetch/formatRealFee/<?php echo $realId;?>",
								data:{},
								cache:false,
								dataType:"text",
								success:function(result,status,xhr){
									//window.location.reload() ;
								},error:function(){
									alert("操作出现异常！") ;
								}
							}); 
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

   							$.dataservice("model:Cost.saveCostFix" , {productCost:productCost,listingCosts:listingCosts} , function(){
   								//window.location.reload();
   							});
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
 
      	 //"PURCHASE_COST":"8.8","OTHER_COST":"0","TAG_COST":"1","LABOR_COST":"0","FEE":"0","REAL_ID":"1","LOGISTICS_COST":"0"}
        	 //渠道佣金 $item['COMMISSION_RATIO']
        	 var purchaseCost = $("#PURCHASE_COST").val()||0  ;
        	 var baseCost =parseFloat( purchaseCost )+ parseFloat($("#LOGISTICS_COST").val()||0 )+parseFloat($("#OTHER_COST").val()||0)
        	 										 + parseFloat(<?php echo $costTag;?>)+parseFloat(<?php echo $costLabor;?>)  ;
			 var $costTaxRate = parseFloat(<?php echo $costTaxRate ;?>) ;
			 var purcharRate =parseFloat( (purchaseCost*$costTaxRate).toFixed(2)) ;
			 $(".purchaseRate").html(purcharRate);
			 baseCost = baseCost + purcharRate ;

			 $( $listing  ).each(function(index,item){
				var rowId = item.ACCOUNT_ID+"_"+item.SKU ;

				var cost = new Cost() ;
				cost.setProductCost( baseCost , item.EXCHANGE_RATE  ) ;
				cost.setChannel(  item.FULFILLMENT_CHANNEL ) ;
				cost.setSellPrice( $("[name='TOTAL_PRICE']","#"+rowId).val()   ) ;
				cost.setChannelFeeRatio( item.COMMISSION_RATIO ) ;
				cost.setVariableCloseFee( item.VARIABLE_CLOSING_FEE ) ;
				cost.setFbaCost(  item._FBA_COST ) ;
				cost.setTransferUnitPrice( item.TRANSFER_WH_PRICE ) ;
				cost.setFbcOrderRate( item.FBC_ORDER_RATE ) ;
				cost.setFbmOrderRate( item.FBM_ORDER_RATE ) ;
				cost.setTransferProperties( {weight:item.WEIGHT , length:item.LENGTH , width:item.WIDTH , height:item.HEIGHT,packageWeight: item.PACKAGE_WEIGHT } ) ;

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
						
						<table  class="form-table  product-cost" >
							<caption>货品成本</caption>
							<tr>
								<th>采购成本：</th>
								<td colspan="5">
								<input type="hidden" id="REAL_ID" value="<?php echo $realId;?>"/>
								
								<input class="cost span2"  type="text" 
									data-validator="double"
									<?php echo $COST_EDIT_PURCHASE?'':'disabled'?>
									id="PURCHASE_COST" value="<?php echo $productCost["PURCHASE_COST"];?>"/>
								</td>
								<th>采购物流费用 ：</th><td ><input class="_cost span2"      style="width:50px!important;"
										data-validator="double"  type="text" id="LOGISTICS_COST" value="<?php echo $productCost["LOGISTICS_COST"];?>"/></td>
							</tr>
							<tr>
									
									<th>标签费用 ：</th><td >
										<?php echo $costTag ; ?>
									</td>
									<th>人工成本：</th><td><?php echo $costLabor ; ?></td>	
									<th>国内税费：</th><td  class="purchaseRate">
										<?php echo round( $productCost['PURCHASE_COST'] * $costTaxRate,2 )  ; ?>
									</td>
									<th>其他成本：</th><td><input      style="width:50px!important;"
										data-validator="double"  type="text" id="OTHER_COST" value="<?php echo $productCost["OTHER_COST"];?>"/></td>
							</tr>
						</table>
						<!--  spcd.LOGISTICS_COST, 
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
								<th>Listing SKU</th>
								<th>ASIN</th>
								<th>账号</th>
								<th>销售渠道</th>
								<th>FBM发货仓库</th>
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
							<?php  
							    foreach( $listing as $item  ){ 
							//debug($item) ;
								?>
							
							<tr  class="data-row"  id="<?php echo $item['ACCOUNT_ID'];?>_<?php echo $item['SKU'];?>">
								<td>
										<a href="#"  class="amazon-asyn-listing">同步</a>
								</td>
								<td>
									<input type="hidden" name="ACCOUNT_ID"   value="<?php echo $item['ACCOUNT_ID'];?>" style="width:50px;"/>
									<input type="hidden" name="LISTING_SKU"   value="<?php echo $item['SKU'];?>" style="width:50px;"/>
									<input type="hidden" name="COMMISSION_RATIO"   value="<?php echo $item['COMMISSION_RATIO'];?>" style="width:50px;"/>
									<input type="hidden" name="FULFILLMENT_CHANNEL"   value="<?php echo $item['FULFILLMENT_CHANNEL'];?>" style="width:50px;"/>
									<a href="#" product-detail="<?php echo $item['ASIN'];?>"><?php echo $item['SKU'];?></a>
								</td>
								<td><a href="#" offer-listing="<?php echo $item['ASIN'];?>"> <?php echo $item['ASIN'];?></a></td>
								<td><?php echo $item['ACCOUNT_NAME'];?></td>
								<td><?php echo $item['FULFILLMENT_CHANNEL'];?></td>
								<td>
									<?php //echo $item['FULFILLMENT_CHANNEL']=='Merchant'?$item['FBM_WAREHOUSE_NAME']:"";?>
								</td>
								<td>
								<?php 
								$_ = 0 ;
								$_text = "" ;
								if( $item['FULFILLMENT_CHANNEL']=='Merchant'){
									$_ = round($item['LOWEST_PRICE'],3);
								}else{
									$_ = round($item['LOWEST_FBA_PRICE'],3);
								} ?>
									<input type="text"   name="TOTAL_PRICE"  value="<?php echo $_ ; ?>" style="width:30px;"/><br/>
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
								
								<td><input type="text"  name="OTHER_COST"  value="<?php echo round($item['OTHER_COST'],2);?>" style="width:30px;"/></td>
							</tr>
							<?php  	} ?>
						</table>
						
					</div>
					
					<?php if($COST_EDIT){ ?>
					<!-- panel脚部内容-->
                    <div class="panel-foot"  style="background:#FFF;">
						<div class="form-actions">
							<button type="submit" class="btn btn-primary asyn-amazon-fee">同步Amazon费用</button>
							<button type="submit" class="btn btn-primary save-btn">保存</button>
						</div>
					</div>
					<?php } ?>
				</div>
			</form>
		</div>
	</div>
</body>

</html>
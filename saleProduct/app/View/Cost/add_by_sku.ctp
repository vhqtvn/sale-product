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
	
		$realId = $params['arg1'] ;

		$SqlUtils  = ClassRegistry::init("SqlUtils") ;
		
		$sql = "SELECT sc_product_cost.* FROM sc_product_cost where real_id = '$realId'";
		$productCost = $SqlUtils->getObject($sql,array()) ;

		
		$sql = "SELECT saap.SKU AS SELLER_SKU, srp.*,
				saap.ACCOUNT_ID,
						saa.name as ACCOUNT_NAME,
						saap.FULFILLMENT_CHANNEL,
				        spcd.LOGISTICS_COST, 
						spcd.FEE, 
						spcd.AMAZON_FEE, 
						spcd.ORDER_COST, 
						spcd.OTHER_COST,
						(select name from sc_warehouse sw where sw.id = saa.fbm_warehouse) as FBM_WAREHOUSE_NAME
				 FROM sc_real_product srp,
							sc_real_product_rel srpr,
				            sc_amazon_account saa,
							sc_amazon_account_product saap
					        left join sc_product_cost_details spcd
				           on spcd.account_id = saap.account_id
				             and spcd.listing_sku = saap.sku
							WHERE srp.ID = srpr.REAL_ID
							AND srpr.SKU = saap.SKU
				and saap.account_id = saa.id
							and srp.id = '{@#realId#}'" ;
		
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
   </style>

   <script>
   		var groupCode = '<?php echo $loginId;?>' ;

   		$(function(){
   				
   					$(".save-btn").click(function(){
   						if( !$.validation.validate('#personForm').errorInfo ) {
   							var productCost = $(".product-cost").toJson() ;
   							var listingCosts = [] ;
   							$(".listing-cost .data-row").each(function(index,row){
									var listingCost = $(this).toJson() ;
									listingCosts.push(listingCost) ;
   	   						});

   							
   							$.dataservice("model:Cost.saveCostFix" , {productCost:productCost,listingCosts:listingCosts} , function(){
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
								<input type="hidden" id="REAL_ID" value="<?php echo $realId;?>"/>
								
								<input class="cost span2"  type="text" 
									data-validator="double"
									<?php echo $COST_EDIT_PURCHASE?'':'disabled'?>
									<?php echo empty($productCost["PURCHASE_COST"])?'':'disabled'?>
									id="PURCHASE_COST" value="<?php echo $productCost["PURCHASE_COST"];?>"/>
									<span class="alert" style="padding:2px;">不能修改，由采购价格自动更新</span>
								</td>
								<th>采购物流费用 ：</th><td ><input class="_cost span2"      style="width:50px!important;"
										data-validator="double"  type="text" id="LOGISTICS_COST" value="<?php echo $productCost["LOGISTICS_COST"];?>"/></td>
							</tr>
							<tr>
									
									<th>标签费用 ：</th><td ><input class="_cost span2"      style="width:50px!important;"
										data-validator="double"  type="text" id="TAG_COST" value="<?php echo $productCost["TAG_COST"];?>"/></td>
									<th>人工成本：</th><td><input  class="_cost "     style="width:50px!important;"
										data-validator="double"  type="text" id="LABOR_COST" value="<?php echo $productCost["LABOR_COST"];?>"/></td>	
									<th>国内税费：</th><td><input  class="_cost "     style="width:50px!important;"
										data-validator="double"  type="text" id="FEE" value="<?php echo $productCost["FEE"];?>"/></td>
									<th>其他成本：</th><td><input      style="width:50px!important;"
										data-validator="double"  type="text" id="OTHER_COST" value="<?php echo $productCost["OTHER_COST"];?>"/></td>
							</tr>
						</table>
						
						<table  class="form-table table  listing-cost" >
							<caption>Listing成本</caption>
							<tr>
								<th>Listing SKU</th>
								<th>账号</th>
								<th>销售渠道</th>
								<th>FBM发货仓库</th>
								<th>物流成本</th>
								<th>税费</th>
								<th>渠道佣金</th>
								<th>FBA费用/FBM订单发货成本</th>
								<th>其他成本</th>
							</tr>
							<?php  	foreach( $listing as $item  ){ ?>
							
							<tr  class="data-row">
								<td>
									<input type="hidden" name="ACCOUNT_ID"   value="<?php echo $item['ACCOUNT_ID'];?>" style="width:50px;"/>
									<input type="hidden" name="LISTING_SKU"   value="<?php echo $item['SELLER_SKU'];?>" style="width:50px;"/>
									<?php echo $item['SELLER_SKU'];?>
								</td>
								<td><?php echo $item['ACCOUNT_NAME'];?></td>
								<td><?php echo $item['FULFILLMENT_CHANNEL'];?></td>
								<td>
									<?php echo $item['FULFILLMENT_CHANNEL']=='Merchant'?$item['FBM_WAREHOUSE_NAME']:"";?>
								</td>
								<td><input type="text" class="_cost"  name="LOGISTICS_COST" value="<?php echo $item['LOGISTICS_COST'];?>" style="width:50px;"/></td>
								<td><input type="text" class="_cost"  name="FEE"  value="<?php echo $item['FEE'];?>" style="width:50px;"/></td>
								<td><input type="text" class="_cost"  name="AMAZON_FEE"  value="<?php echo $item['AMAZON_FEE'];?>" style="width:50px;"/></td>
								<td><input type="text"  name="ORDER_COST"  value="<?php echo $item['ORDER_COST'];?>" style="width:50px;"/>
								</td>
								<td><input type="text"  name="OTHER_COST"  value="<?php echo $item['OTHER_COST'];?>" style="width:50px;"/></td>
							</tr>
							<?php  	} ?>
						</table>
						
					</div>
					
					<?php if($COST_EDIT){ ?>
					<!-- panel脚部内容-->
                    <div class="panel-foot"  style="background:#FFF;">
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
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
		
		$costTag = $config->getAmazonConfig("COST_TAG",0) ;
		$costLabor = $config->getAmazonConfig("COST_LABOR",0) ;
		$costTaxRate = $config->getAmazonConfig("COST_TAX_RATE",0.0) ;
		
		$sql = "SELECT sc_product_cost.* FROM sc_product_cost where asin = '$asin'";
		$productCost = $SqlUtils->getObject($sql,array()) ;
		
		//判断是否有初始化成本数据，如果没有则初始化成本数据
		if( empty( $productCost ) ){
			$costId = $SqlUtils->create_guid() ;
			$SqlUtils->exeSql("sql_cost_insert_new", array("ASIN"=>$asin,"ID"=>$costId,"loginId"=>$user['LOGIN_ID'])) ;
		}else{
			$costId = $productCost['ID'] ;
		}
		
		//判断是否有成本明细
		$sql = "SELECT * FROM sc_product_cost_details where asin = '$asin' and type='FBA'";
		$productCostDetails = $SqlUtils->getObject($sql,array()) ;
		if( empty( $productCostDetails ) ){
			$costId_ = $SqlUtils->create_guid() ;
			$SqlUtils->exeSql("sql_cost_details_insert_new", array("ASIN"=>$asin,'COST_ID'=>$costId,'TYPE'=>'FBA',"ID"=>$costId_,"loginId"=>$user['LOGIN_ID'])) ;
		}
		
		$sql = "SELECT * FROM sc_product_cost_details where asin = '$asin' and type='FBM'";
		$productCostDetails = $SqlUtils->getObject($sql,array()) ;
		if( empty( $productCostDetails ) ){
			$costId_ = $SqlUtils->create_guid() ;
			$SqlUtils->exeSql("sql_cost_details_insert_new", array("ASIN"=>$asin,'COST_ID'=>$costId,'TYPE'=>'FBM',"ID"=>$costId_,"loginId"=>$user['LOGIN_ID'])) ;
		}
		
		
		$sql = "select * from sc_view_devproduct_cost where asin = '{@#realId#}'" ;
		
		$listing = $SqlUtils->exeSqlWithFormat($sql,array("realId"=>$asin)) ;
		
		if( empty( $listing ) ){
			//初始化成本数据
			$Cost  = ClassRegistry::init("Cost") ;
		}
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

   		$(function(){
					$(".asyn-amazon-fee").click(function(){
						

						var productCost = $(".product-cost").toJson() ;
						var listingCosts = [] ;
						$(".listing-cost .data-row").each(function(index,row){
							var listingCost = $(this).toJson() ;
							listingCosts.push(listingCost) ;
   						});

						$.dataservice("model:Cost.saveCostAsin" , {productCost:productCost,listingCosts:listingCosts} , function(){
							$.block() ;
							$.ajax({
								type:"post",
								url:contextPath+"/taskFetch/formatDevProductFee/<?php echo $asin;?>",
								data:{},
								cache:false,
								dataType:"text",
								success:function(result,status,xhr){
									$.unblock() ;
									window.location.reload(true) ;
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

   							//alert(  $.json.encode({productCost:productCost,listingCosts:listingCosts}) ) ;
   							//return ;
   							$.dataservice("model:Cost.saveCostAsin" , {productCost:productCost,listingCosts:listingCosts} , function(){
   								//window.location.reload();
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
								<input type="hidden" id="ASIN" value="<?php echo $asin;?>"/>
								
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
									<th>国内税费：</th><td>
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
								<th>平台</th>
								<th>ASIN</th>
								<th>渠道</th>
								<th>售价</th>
								<th>总成本</th>
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
							
							<tr  class="data-row">
								<td>
									<input type="hidden" name="ID"   value="<?php echo $item['COST_DETAIL_ID'];?>" style="width:50px;"/>
									<input type="hidden" name="ASIN"   value="<?php echo $item['ASIN'];?>" style="width:50px;"/>
									<input type="hidden" name="TYPE"   value="<?php echo $item['TYPE'];?>" style="width:50px;"/>
									<?php echo $item['ACCOUNT_NAME'];?>
								</td>
								<td><?php echo $item['ASIN'];?></td>
								<td><?php echo $item['TYPE'];?></td>
								<td>
								<input type="text" class="_cost " 
										name="SELLER_COST" value="<?php echo $item['TOTAL_PRICE'];?>" style="width:50px!important;"/>
								</td>
								<td><?php echo round($item['TOTAL_COST'],3);?></td>
								<td><?php 
											$totalProfile =round(  $item['TOTAL_PRICE'] - $item['TOTAL_COST'],2)   ;
											if( $item['TOTAL_COST'] == 0 ){
												echo '-' ;
											}else{
												$totalRate = round( ( $totalProfile/$item['TOTAL_COST'] ) *100 ,2).'%' ;
												echo $totalProfile."($totalRate)" ;
											}
											
								?></td>
								<td>
									<input type="text" class="_cost " 
										name="_TRANSFER_COST" value="<?php echo $item['TRANSFER_COST'];?>" style="width:50px!important;"/>
								</td>
								<td>
								<?php if( $item['TYPE'] == 'FBM' ){ ?>
									<input type="text" class="_cost " 
									name="LOGISTICS_COST" value="<?php echo $item['LOGISTICS_COST'];?>" style="width:50px!important;"/></td>
								<?php }else{
									echo "-" ;
								} ?>
								
								<td>
									<?php 
										echo  round( $item['FEE'],3 ) ;
									?>
								</td>
								<td><?php echo round($item['COMMISSION_FEE'],3 ) ;?>(<?php 
									echo round($item['COMMISSION_RATIO']*100,2)."%" ;   ?>)</td>
								<td><?php echo round($item['VARIABLE_CLOSING_FEE'],3 ) ;?></td>
								
								<td><?php if( $item['TYPE'] == 'FBM' ){ ?>
											-
										<?php  }else{
											echo round($item['FBA_COST'],3 ) ;
										} ?>
										</td>
								<td>-</td>
								
								<td><input type="text"  name="OTHER_COST"  value="<?php echo round($item['OTHER_COST'],2);?>" style="width:50px;"/></td>
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
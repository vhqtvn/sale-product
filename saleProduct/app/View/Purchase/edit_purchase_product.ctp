<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
   <?php echo $this->Html->charset(); ?>
    <title>采购任务产品</title>
    <meta http-equiv="pragma" content="no-cache"/>
	<meta http-equiv="cache-control" content="no-cache"/>

   <?php
   		include_once ('config/config.php');
   		
		echo $this->Html->meta('icon');
		echo $this->Html->css('../js/grid/jquery.llygrid');
		echo $this->Html->css('../js/validator/jquery.validation');
		echo $this->Html->css('../js/dialog/jquery.dialog');
		echo $this->Html->css('default/style');
		echo $this->Html->css('../js/listselectdialog/jquery.listselectdialog');
		echo $this->Html->css('../js/tab/jquery.ui.tabs');
		
		echo $this->Html->script('jquery');
		echo $this->Html->script('common');
		echo $this->Html->script('jquery-ui');
		echo $this->Html->script('jquery.json');
		echo $this->Html->script('grid/jquery.llygrid');
		echo $this->Html->script('grid/query');
		echo $this->Html->script('tab/jquery.ui.tabs');
		echo $this->Html->script('validator/jquery.validation');
		echo $this->Html->script('listselectdialog/jquery.listselectdialog');
		echo $this->Html->script('modules/purchase/edit_purchase_product');
		echo $this->Html->script('dialog/jquery.dialog');
		echo $this->Html->script('calendar/WdatePicker');
		echo $this->Html->script('modules/cost/cost');
		
		$purchaseProductId = $params['arg1'] ;
		$actionType = $params['arg2'] ;
		
		$Sale  = ClassRegistry::init("Sale") ;
		$security  = ClassRegistry::init("Security") ;
		$SqlUtils  = ClassRegistry::init("SqlUtils") ;
		$Supplier  = ClassRegistry::init("Supplier") ;
		$PurchaseService  = ClassRegistry::init("PurchaseService") ;
		
		$purchaseProduct = $SqlUtils->getObject("sql_purchase_new_getById",array("id"=>$purchaseProductId)) ;
		
		$reqPlanId = $purchaseProduct["REQ_PRODUCT_ID"]  ;
		$realId =  $purchaseProduct["REAL_ID"]  ;
		$sku = $purchaseProduct["REAL_SKU"]  ;
		$suppliers = $Supplier->getProductSuppliersBySku( $sku  ) ;

		$loginId = $user['LOGIN_ID'] ;
		
		$pp_edit 								= $security->hasPermission($loginId , 'ppp_edit') ;
		$ppp_callback                      = $security->hasPermission($loginId , 'ppp_callback') ;//回退
		$ppp_add_product				= $security->hasPermission($loginId , 'ppp_add_product') ;
		$ppp_export							= $security->hasPermission($loginId , 'ppp_export') ;
		$ppp_audit							= $security->hasPermission($loginId , 'ppp_audit') ;
		$pptp_audit							= $security->hasPermission($loginId , 'pptp_audit') ;//采购产品任务审批
		//修改采购数量
		$pptp_quantity							= $security->hasPermission($loginId , 'pptp_updateQuantity') ;//采购产品任务审批
		
		$ppp_deal								= $security->hasPermission($loginId , 'pptp_deal') ;//采购产品任务审批
		$ppp_receviced					= $security->hasPermission($loginId , 'pptp_receviced') ;//采购产品任务审批
		
		$ppp_setlimitprice				= $security->hasPermission($loginId , 'ppp_setlimitprice') ;
		$ppp_assign_executor			= $loginId == $purchaseProduct['EXECUTOR'] || $security->hasPermission($loginId , 'ppp_assign_executor') ;//分配执行人，计划负责人等于当前用户
		$ppp_qc								= $security->hasPermission($loginId , 'ppp_qc') ;
		$ppp_inwarehouse				= $security->hasPermission($loginId , 'ppp_inwarehouse') ;
		$ppp_confirm 						= $security->hasPermission($loginId , 'ppp_confirm') ;

		$ppp_sendfba 						= $security->hasPermission($loginId , 'ppp_sendfba') ;
		$reedit_pp_product				= $security->hasPermission($loginId , 'reedit_pp_product') ;//在编辑功能
		$hasSetSupplierPermission = $security->hasPermission($loginId, 'SET_PRODUCT_SUPPLIER_FLAG') ;
		$hasViewRelListing =  $security->hasPermission($loginId , 'view_rp_rel_listing') ;
		
		$endPurchase = $security->hasPermission($loginId , 'ppp_end') ;
		
		//成本利润查看权限
		//成本权限
		//成本权限
		$COST_EDIT_PROFIT   						= $security->hasPermission($loginId , 'COST_EDIT_PROFIT') ;
		$COST_VIEW_TOTAL  						= $security->hasPermission($loginId , 'COST_VIEW_TOTAL') ;//cen
		$COST_VIEW_PROFIT  						= $security->hasPermission($loginId , 'COST_VIEW_PROFIT') ||$COST_EDIT_PROFIT  ;
		
		//获取货品供应商询价
		$suppliers = $SqlUtils->exeSql("sql_purchase_plan_product_inquiry",array('planId'=>'' ,'sku'=>$purchaseProduct["REAL_SKU"])) ;//$product["PLAN_ID"]
		
		$status = $purchaseProduct['STATUS'] ;
		$executor = $purchaseProduct['EXECUTOR'] ;
		$executorName = $purchaseProduct['EXECUTOR_NAME'] ;
		/*if( empty($executor) ){
			$executor = $product['PURCHASE_CHARGER'] ;
			$executorName = $product['PURCHASE_CHARGER_NAME'] ;
		}*/
		
		$isOwner = $loginId == $purchaseProduct['EXECUTOR'] ;
		
		$riskTypeItems = $SqlUtils->exeSqlWithFormat("select * from sc_config where type= 'riskType'",array()) ;
	?>
  
   <style>
   		*{
   			font:12px "微软雅黑";
   		}
   		
   		#details_tab.ui-corner-all{
			border:none;
   		}
   		
   		.nav-tabs ul li span{
			margin-top:5px!important;
   			display:block;
   		}
   </style>
   
   <style type="text/css">
		.flow-node{
			min-width:50px; 
			height:20px; 
			border:5px solid #0FF; 
			border-radius:5px;
			font-weight:bold;
		}
		
		.flow-node.active{
			border-color:#3809F7 ;
			background-color:#3809F7 ;
			color:#EEE;
		}
		
		.flow-node.passed{
			border-color:#92E492 ;
			background-color:#92E492 ;
			
		}
		
		.flow-node.termination{
			color:red;
	        background-color:pink ;
			border-color:pink;
		    white-space: nowrap;
		}
		
		.flow-node.disabled{
			border-color:#CCC ;
			background-color:#CCC ;
			color:#EEE;
		}
		
		.flow-table{
			text-align:center;
			
		}
		
		.flow-bar{
			width:100%;margin:3px auto;text-align:center;
			position:fixed;
			left:0px;
			right:0px;
			top:0px;
			height:80px;
			z-index:1000;
			background: #FFF;
		}
		
		body{
			padding-top:78px;
		}
		
		.flow-action{
			position:absolute;;
			right:10px;
			top:48px;
			z-index:100;
		}
		
		.flow-split{
			font-size:30px;
		}
		
		.end-danger{
			color:red;
			font-weight:bold;
		}
		
		.memo{
			position:absolute;
			top:85px;
			z-index:1;
			right:10px;
			width:300px;
			height:50px;
			background:#ffd700;
			display:none;
		}
		
		.memo-control{
			display:none;
		}
		
		.tag-container li{
			float:left;
			list-style: none;
			margin:2px 5px;
		 	padding:2px;
		}
		
		.cost-container li{
			font-weight:bold;
			list-style: none;
		}
	</style>

   <script type="text/javascript">
    var $COST_VIEW_TOTAL = '<?php echo $COST_VIEW_TOTAL;?>' ;
    var  $COST_VIEW_PROFIT = '<?php echo $COST_VIEW_PROFIT;?>' ;
    var sku = '<?php echo $purchaseProduct['REAL_SKU'];?>' ;
    var realId =  '<?php echo $purchaseProduct['REAL_ID'];?>' ;
    var reqProductId =  '<?php echo $purchaseProduct['REQ_PRODUCT_ID'];?>' ;
    var reqPlanId =  '<?php echo $reqPlanId;?>' ;
    var actionType = '<?php echo $actionType;?>' ;
    var riskTypeItems = <?php echo json_encode($riskTypeItems);?> ;
   
    var $pp_edit = <?php echo $pp_edit?"true":"false" ;?> ;
	var $ppp_add_product = <?php echo $ppp_add_product?"true":"false" ;?> ;
	var $ppp_export= <?php echo $ppp_export?"true":"false" ;?> ;
	var $ppp_audit = <?php echo $ppp_audit?"true":"false" ;?> ;
	var $ppp_deal = <?php echo $ppp_deal?"true":"false" ;?> ;
	var $ppp_setlimitprice = <?php echo $ppp_setlimitprice?"true":"false" ;?> ;
	var $ppp_assign_executor = <?php echo $ppp_assign_executor?"true":"false" ;?> ;
	var $ppp_qc = <?php echo $ppp_qc?"true":"false" ;?> ;
	var $ppp_inwarehouse = <?php echo $ppp_inwarehouse?"true":"false" ;?> ;
	var $ppp_confirm = <?php echo $ppp_confirm?"true":"false" ;?> ;
	var $reedit_pp_product = <?php echo $reedit_pp_product?"true":"false" ;?> ;

	var id = '<?php echo $purchaseProduct['ID'] ;?>' ;
	var currentStatus = "<?php echo $purchaseProduct['STATUS'];?>" ;

	 var flowData = [
	        		{status:45,label:"待采购",memo:true
	        			,actions:[{}
									<?php if( $isOwner || $ppp_assign_executor) { ?>,{label:"保存",action:function(){ ForceAuditAction(45,"保存") }}  <?php  }?>
									<?php if( $isOwner || $ppp_callback ) { ?>,{label:"完成采购下单",action:function(){ AuditAction(51,"已保存采购") } }<?php  }?>
									<?php if(  $endPurchase || $isOwner ) { ?>,{label:"终止采购",clazz:"btn-danger",action:function(){ PausePurchase(80,"终止采购",true) } }<?php } ?>
        				],format:function(node){
							if( currentStatus == 46  ){
								node.label = "再询价" ;
								node.status = 46 ;
							}
            			}
	        		},{status:51,label:"利润评估",memo:true
						,actions:[{}
						<?php if(  $ppp_callback ){ ?>,{label:"回退",action:function(){ ForceAuditAction(45,"回退")  }}<?php  }?>
						<?php if( $isOwner|| $ppp_deal ) { ?>,{label:"保存",action:function(){ ForceAuditAction(51,"保存") }}<?php  } ?>
						<?php if( $isOwner|| $ppp_deal ) { ?>,{label:"评估完成",action:function(){ ForceAuditAction(48,"评估完成") } }<?php   }   ?>
						<?php if(  $endPurchase ) { ?>,{label:"终止采购",clazz:"btn-danger",action:function(){ PausePurchase(80,"终止采购",true) } }<?php } ?>
						]
					},
	        		<?php /*
	        		{status:46,label:"交易申请",memo:true
	        			,actions:[{}
									<?php if( $isOwner&& $ppp_callback ){ ?>,{label:"回退",action:function(){ ForceAuditAction(45,"回退") }}<?php  }?>
									<?php if( $isOwner || $ppp_assign_executor) { ?>,{label:"保存",action:function(){ ForceAuditAction(46,"保存") }}<?php   } ?>
									<?php if( $isOwner ) { ?>,{label:"提交申请",action:function(){ AuditAction(47,"提交申请") } }<?php  } ?>
									<?php if(  $endPurchase ) { ?>,{label:"终止采购",clazz:"btn-danger",action:function(){ ForceAuditAction(80,"终止采购") } }<?php } ?>
        				]
	        		},
	        		{status:47,label:"采购审批",memo:true
	        			,actions:[{}
						<?php if( $ppp_callback ){ ?>,{label:"回退再询价",action:function(){ ForceAuditAction(46,"回退再询价") }}<?php   } ?>
						<?php if( $isOwner  || $pptp_audit ) { ?>,{label:"保存",action:function(){ ForceAuditAction(47,"保存") }}<?php  } ?>
						<?php if( $pptp_audit ) { ?>,{label:"审批通过",action:function(){ AuditAction(48,"审批通过") } }<?php   }   ?>
						<?php if(  $endPurchase ) { ?>,{label:"终止采购",clazz:"btn-danger",action:function(){ ForceAuditAction(80,"终止采购",true) } }<?php } ?>
						]
					},*/?>
	        		
	        		{status:48,label:"执行交易",memo:true
	        			,actions:[{}
									<?php if(  $ppp_callback ){ ?>,{label:"回退",action:function(){ ForceAuditAction(51,"回退") }}<?php   } ?>
									<?php if( $isOwner || $ppp_deal) { ?>,{label:"保存",action:function(){ ForceAuditAction(48,"保存") }}<?php  } ?>
									<?php if( $isOwner|| $ppp_deal ) { ?>,{label:"已交易",action:function(){ AuditAction(49,"已交易") } }<?php   }   ?>
									<?php if(  $endPurchase ) { ?>,{label:"终止采购",clazz:"btn-danger",action:function(){ PausePurchase(80,"终止采购",true) } }<?php } ?>
        				]
	        		},
	        		{status:49,label:"待收货",memo:true //48->49
	        			,actions:[
	        			          {}
									<?php if(  $ppp_callback ){ ?>,{label:"回退",action:function(){ ForceAuditAction(48,"回退") }}<?php  }?>
									<?php if( $isOwner || $ppp_assign_executor) { ?>,{label:"保存",action:function(){ ForceAuditAction(49,"保存") }}<?php  } ?>
									<?php if( $isOwner || $ppp_receviced) { ?>,{label:"已到货",action:function(){ AuditAction(50,"已到货") } }<?php } ?>
									<?php if(  $endPurchase ) { ?>,{label:"终止采购",clazz:"btn-danger",action:function(){ PausePurchase(80,"终止采购",true) } }<?php } ?>
        				]
	        		},{status:50,label:"QC验货",memo:true
	        			,actions:[{}
		      	        		<?php if( $ppp_qc) { ?>
									<?php if( $ppp_callback ){ ?>
									,{label:"回退",action:function(){ ForceAuditAction(49,"回退") }},
									<?php }?>
									,{label:"保存",action:function(){ ForceAuditAction(50,"保存") }},
		      	        			{label:"验货完成",action:function(){ AuditAction(60,"验货完成") } }
								<?php };?>
									<?php if(  $endPurchase ) { ?>,{label:"终止采购",clazz:"btn-danger",action:function(){ PausePurchase(80,"终止采购",true) } }<?php } ?>
        				]
	        			
	        		},{status:60,label:"货品入库",memo:true
	        			
	        			,actions:[{}
								<?php if( $ppp_inwarehouse) { ?>
									<?php if( $ppp_callback ){ ?>
									,{label:"回退",action:function(){ ForceAuditAction(50,"回退") }},
									<?php }?>
									,{label:"保存",action:function(){ ForceAuditAction(60,"保存") }},
		      	        			{label:"入库确认",action:function(){ WarehouseInAction(80,"入库确认") } }
								<?php };?>
								<?php if(  $endPurchase ) { ?>,{label:"终止采购",clazz:"btn-danger",action:function(){ PausePurchase(80,"终止采购",true) } }<?php } ?>
        				]
	        		},
	        		<?php /*
	        		{status:75,label:"发货FBA",memo:true
	        			
	        			,actions:[{}
							<?php if( $ppp_sendfba) { ?>
									,{label:"保存",action:function(){ ForceAuditAction(75,"保存") }},
		      	        			{label:"已发货FBA",action:function(){ AuditAction(80,"已发货FBA") } }
									<?php if(  $endPurchase ) { ?>,{label:"终止采购",clazz:"btn-danger",action:function(){ ForceAuditAction(80,"终止采购",true) } }<?php } ?>
									<?php };?>
		      	        			]
        			,format:function(node){
            			if( currentStatus==75 ){
            				<?php  if( $actionType == 'audit' ){ ?>
        								node.actions = [ {label:"采购审计",action:function(){ AuditAction(2,"采购审计") } }] ;
            				<?php }	?>
                		}
            		}
	        			
	        		},*/?>
	        		{status:80,label:"结束",format:function(node){
						if( '<?php echo $purchaseProduct['IS_TERMINATION']?>' == 1  ){
							node.label = "终止采购" ;
							node.status = 80 ;
							node.statusClass = "end-danger" ;
							<?php  if( $user['LOGIN_ID'] == 'lixh' ){ ?>
							node.actions = [ {label:"恢复采购",action:function(){ AuditAction(45,"恢复采购") } }] ;
							<?php } ?>
						}

						if( currentStatus==80 ){
            				<?php  if( $actionType == 'audit' ){ ?>
        								node.actions = [ {label:"采购审计",action:function(){ AuditAction(2,"采购审计") } }] ;
            				<?php }	?>
                		}
        			}}
	        	] ;

	 <?php  //if( $user['LOGIN_ID'] == 'lixh' ){ ?>
	 	$(function(){
		 	var _lastPurchase = null ;
	 		Cost.get('<?php echo $purchaseProduct['REAL_ID'] ;?>',function(result){

	 				$(".cost-container ul").empty() ;
		 			
		 		    //alert( $.json.encode(result) ) ;
                    var costs = result.returnCosts ;
                    var lastPurchase = result.lastPurchase|| null ;
                    _lastPurchase = lastPurchase ;
		 		
                    var purchaseCost = 0 ;
                    var logisticsCost = 0 ;
                    var providorName = "" ;
                    var providorId = "" ;
                    var profileInfo = null ;
                   $(costs).each(function(){
                	   purchaseCost = this.purchaseCost ;
                	   logisticsCost = this.logisticsCost ;
                	   providorName = this.providorName ;
                	   providorId = this.providorId ;
                	   
                	   var salesNum = this.salesNum ;
                	   
					    var accountId = this.accountId ;
						var listingSku = this.listingSku ;
						var listingRow = $("[rowListing='"+accountId+"_"+listingSku+"']") ;
						var purchaseQuantity = listingRow.find(".purchaseQuantity").val() ;

						if( listingRow.length >=1 ){
							//var profileRateParse = parseFloat(this.profileRate) ;
							listingRow.find(".lastest-sale-num").html( this.saleString ) ;
							if( this.error ){
								listingRow.find(".profit").html("<span class='alert-danger'>" +this.error+"</span>" ) ;//支付成本/总利润/利润率
						 	}else{
						 		listingRow.find(".profit").html( this.costAvalibe+"/"+this.totalProfile+"/"+this.profileRate ) ;//支付成本/总利润/利润率
						 		if( purchaseQuantity>0 ){
									var profileRate = parseFloat(this.profileRate) ;
									if(  profileRate < -20 || profileRate > 100 ){
										profileInfo = {style:"alert-danger",message:"利润率不正常，需要检查！"} ;
									}else if(  profileRate < 8 ){
										profileInfo = {style:"alert-danger",message:"低利润，不建议采购！"} ;
									}else if(  profileRate < 12 ){
										profileInfo = {style:"alert-message",message:"利润较低，量大可采购！"} ;
									}else{
										profileInfo = {style:"alert-success",message:"利润正常，可采购！"} ;
									}
							 	}
							}
						}
                   }) ;

                   if( _lastPurchase ){
                	   $("<li><b>上次采购信息</b></li>").appendTo(  $(".cost-container ul") ) ;
	                   $("<li>单价(￥)："+ lastPurchase.REAL_QUOTE_PRICE+"</li>").appendTo(  $(".cost-container ul") ) ;
	                   $("<li>运费(￥)："+(( lastPurchase.REAL_SHIP_FEE=='null'?"":lastPurchase.REAL_SHIP_FEE)||"") +"</li>").appendTo(  $(".cost-container ul") ) ;
	                   $("<li>数量："+ lastPurchase.QUALIFIED_PRODUCTS_NUM +"</li>").appendTo(  $(".cost-container ul") ) ;
	                   $("<li>时间："+ lastPurchase.WAREHOUSE_TIME +"</li>").appendTo(  $(".cost-container ul") ) ;
	                   $("<li>供应商：<a href='#' supplier-id='"+providorId+"'>"+providorName+"</a></li>").appendTo(  $(".cost-container ul") ) ;
	                   if( currentStatus == 45 || currentStatus == 46 ){
	                       $("<li><button class='btn btn-success  use-providor'>直接采用</button></li>").appendTo(  $(".cost-container ul") ) ;
	                   }
                   }else{
                	   $("<li><div class='alert alert-success' style='width:100px;'>首次采购</div></li>").appendTo(  $(".cost-container ul") ) ;
                   }
                   if( profileInfo ){
                	   $("<li><div class='alert "+profileInfo.style+"' style='width:150px;padding:4px;'>"+profileInfo.message+"</div></li>").appendTo(  $(".cost-container ul") ) ;
                   }
		   }) ;

			 $(".use-providor").live("click",function(){
				 if( _lastPurchase ){
						$("#providor").val( _lastPurchase.PROVIDOR ) ;
						$("#quotePrice").val( _lastPurchase.QUOTE_PRICE ) ;
						$("#shipFeeType").val(_lastPurchase.SHIP_FEE_TYPE ) ;
						$("#shipFee").val( _lastPurchase.SHIP_FEE==0?"":_lastPurchase.SHIP_FEE ) ;
						$("#payType").val( _lastPurchase.PAY_TYPE ) ;
						$("#promiseDeliveryDate").val( _lastPurchase.PROMISE_DELIVERY_DATE ) ;
				 }
			 }) ;
		 }) ;
		 <?php // }?>
   </script>
</head>


<body class="container-popup">
	<div  class="flow-bar">
		<center>
			<table class="flow-table">
				
			</table>
			<div class="flow-action">
			</div>
		</center>
	</div>
	<!-- apply 主场景 -->
	<div class="apply-page">
		<div class="container-fluid">
		
			<div id="details_tab"></div>
			<div id="base-info">
		        <form id="personForm" action="#" data-widget="validator,ajaxform" class="form-horizontal" >
		        	<input id="id" type="hidden" value='<?php echo $purchaseProduct['ID'] ;?>' />
		        	<input id="realId" type="hidden" value='<?php echo $purchaseProduct['REAL_ID'] ;?>' />
		        	<input id="reqProductId" type="hidden" value='<?php echo $purchaseProduct['REQ_PRODUCT_ID'] ;?>' />
					<!-- panel 头部内容  此场景下是隐藏的-->
					<div class="panel apply-panel">
						<!-- panel 中间内容-->
						<div class="panel-content">
							<table class="form-table" >
								<caption>基本信息<?php if( $reedit_pp_product  && $status <= 47){ 
								echo "<img src='/$fileContextPath/app/webroot/img/edit.png' class='reedit'>" ;
								}?></caption>
								<tbody>
									<tr>
										<th>货品：</th><td><a class="product-realsku"  sku="<?php echo $purchaseProduct['REAL_SKU'] ;?>"  href="#">
										<?php echo  $purchaseProduct['REAL_SKU']  ;?></a>（<?php echo  $purchaseProduct['TITLE']  ;?>）
										<input type="hidden"  name="sku" value="<?php echo  $purchaseProduct['REAL_SKU']  ;?>"/>
										</td>
										<th>执行人：</th><td>
											<input type="hidden"   id="executor"   
											value="<?php echo $purchaseProduct['EXECUTOR'];?>"/>
											<input type="text"   id="executorName"  style="width:60px;" readonly value='<?php echo $executorName;?>'
													value=""/>
											<button class="btn btn-charger 10-input  input"  disabled="disabled">选择</button>
										</td>
										<td rowspan="4" style="width:270px;vertical-align: top;"  class="cost-container">
											<div  class="alert" style="padding:1px;font-size:11px;width:230px;margin-bottom:0px;">利润过低或过高可能是由于成本数据缺失</div>
											<ul></ul>
										</td>
									</tr>
									<tr>
										<th>标签：</th>
										<td colspan=3>
											<ul class="tag-container" style="list-style: none;">
											</ul>
										</td>
									</tr>
									<tr>
										<th>采购时限：</th>
										<td colspan=3><?php echo $purchaseProduct['START_TIME'];?>&nbsp;&nbsp;&nbsp;到&nbsp;&nbsp;&nbsp;<?php echo $purchaseProduct['END_TIME'];?></td>
									</tr>
									<tr>
										<th>计划采购数量：</th>
										<td><input type="text" disabled="disabled" style="width:100px;" name="planNum" value="<?php echo  $purchaseProduct['PLAN_NUM']  ;?>"/></td>
										<th>采购限价：</th>
										<td><input type="text" class="input 51-input" style="width:100px;" name="limitPrice" value="<?php echo $purchaseProduct['LIMIT_PRICE'];?>"/></td>
									</tr>
								</tbody>
						</table>
						
						<table class="form-table" >
								<caption>采购需求<?php if( ($reedit_pp_product)  && $status <= 51){ 
								echo "<img src='/$fileContextPath/app/webroot/img/edit.png' class='reedit'>" ;
								}?></caption>
								<thead>
									<tr>
										<th style="width:160px;">标签</th>
										<th  style="font-weight:bold;">账号</th>
										<th style="font-weight:bold;">Listing SKU</th>
										<th style="font-weight:bold;">FNSKU</th>
										<th style="font-weight:bold;">渠道</th>
										<th style="font-weight:bold;">采购数量</th>
										<th style="font-weight:bold;" style="width:100px;">采购数量(修正)</th>
										<th style="font-weight:bold;" >当前库存</th>
										<th style="font-weight:bold;">总成本/利润/利润率</th>
										<th style="font-weight:bold;">销量(7/14/30)</th>
									</tr>
								</thead>
								<tbody>
									<?php 
										$sqlId = "sql_supplychain_requirement_plan_product_details_list_ALL" ;
										$reqList = $SqlUtils->exeSqlWithFormat($sqlId,array("realId"=>$purchaseProduct['REAL_ID'],"reqProductId"=>$purchaseProduct['REQ_PRODUCT_ID'])) ;
										$count  = count($reqList) ;
										$planNum = $purchaseProduct['PLAN_NUM'] ;
										
										foreach( $reqList as $req){
											$IS_ANALYSIS = $req['IS_ANALYSIS'] ;
											//if( $IS_ANALYSIS == 0 ) continue ;
											$purchaseQuantity = $req['PURCHASE_QUANTITY'] ;
											if( $count == 1 && ( empty($req['PURCHASE_QUANTITY']) || $req['PURCHASE_QUANTITY'] == 0)  ){
												$purchaseQuantity = $planNum ;
											}
										?>
									  <tr   class="edit-data-row"   rowListing="<?php echo $req['ACCOUNT_ID'] ;?>_<?php echo $req['LISTING_SKU'] ;?>">
										<td>
											<?php  if( $purchaseQuantity >0  ) { ?>
										  <button class='btn print-btn-a2  no-disabled'>A2</button>
										  &nbsp;
										  <button class='btn print-btn-a4  no-disabled'>A4</button>
										  <?php  }?>
										</td>
										<td><?php echo $req['ACCOUNT_NAME'] ;?></td>
										<td><a href='#'  offer-listing="<?php echo $req['ASIN'] ;?>"><?php echo $req['LISTING_SKU'] ;?></a></td>
										<td><?php echo $req['FC_SKU'] ;?></td>
										<td><?php echo $req['FULFILLMENT_CHANNEL'] ;?></td>
										<td>
											<?php echo $req['FIX_QUANTITY'];?>
										</td>
										<td>
											<input type="hidden" class="fulfillment"   value='<?php echo $req['FULFILLMENT_CHANNEL'] ;?>'/>
											<input type="hidden" class="accountId"   value='<?php echo $req['ACCOUNT_ID'] ;?>'/>
											<input type="hidden" class="supplyQuantity"   value='<?php echo $req['TOTAL_SUPPLY_QUANTITY'] ;?>'/>
											<input type="hidden" class="listingSku"   value='<?php echo $req['LISTING_SKU'] ;?>'/>
											<input type="text" 	   class="purchaseQuantity input 45-input"  value='<?php echo $purchaseQuantity ;?>' style="width:50px;"/>
										</td>
										<td><?php echo $req['TOTAL_SUPPLY_QUANTITY'] ;?></td>
										<td class="profit"></td>
										<td class="lastest-sale-num"></td>
									</tr>
									<?php  } ?>
								</tbody>
						</table>
						
						<table class="form-table" >
								<caption>入库信息<?php if( $reedit_pp_product  && $status < 80){ 
								echo "<img src='/$fileContextPath/app/webroot/img/edit.png' class='reedit'>" ;
								}?></caption>
								<tbody>
									<tr>
										<th>入库仓库：</th>
										<td colspan=1>
											<select   id="warehouseId"  class="60-input input">
										    	<option value="">--选择--</option>
											   <?php 
											     // sql_warehouse_lists
											     $warehouses = $SqlUtils->exeSql("sql_warehouse_lists",array()) ;
					                             foreach($warehouses as $w){
					                             	  $w = $SqlUtils->formatObject( $w ) ;
					                             	  $selected = $purchaseProduct['WAREHOUSE_ID'] == $w['ID'] ?"selected":"" ;
					                             	  echo "<option $selected value='".$w['ID']."'>".$w['NAME']."</option>" ;
					                             }
											   ?>
											</select>
										</td>
										<th>入库时间：</th>
										<td><input id="warehouseTime" class="60-input input"  data-options="{'isShowWeek':'true','dateFmt':'yyyy-MM-dd HH:mm:ss'}"
											  type="text"  data-widget="calendar"
													<?php echo $status>=60?"data-validator='required'":"" ?>
													value='<?php 
													if( $purchaseProduct['WAREHOUSE_TIME'] == "0000-00-00 00:00:00"){
														echo "";
													} else{
														echo $purchaseProduct['WAREHOUSE_TIME'];
													};?>' /></td>
									</tr>
								</tbody>
						</table>
						<table class="form-table" >
								<caption>采购信息<?php if( $reedit_pp_product && $status < 80 ){ 
								echo "<img src='/$fileContextPath/app/webroot/img/edit.png' class='reedit'>" ;
								}?></caption>
								<tbody>
									
									<tr class="real-purchase-tr">
										<th>计划供应商：</th>
										<td >
										<select id="providor"   class="45-input 46-input   input"  data-validator='required''>
											<option value="">--</option>
										<?php
										
											foreach($suppliers as $suppli){
												$suppli = $SqlUtils->formatObject($suppli) ;
												$temp = "" ;
												if( $suppli['SUP_ID'] == $purchaseProduct['PROVIDOR'] ){
													$temp = "selected" ;
												}
												echo "<option $temp value='".$suppli['SUP_ID']."'>".$suppli['NAME']."</option>" ;
											}
										?>
										</select> 
										<?php if( !empty($purchaseProduct['PROVIDOR']) ){ ?>
										<a href="#" supplier-id="<?php echo $purchaseProduct['PROVIDOR'] ;?>">查看</a>
										<?php } ?>
										<button sku="<?php echo $sku ;?>" class="btn edit_supplier 45-input 46-input  input">编辑</button>
										</td>
										<th>供应商报价：</th>
										<td><input id="quotePrice"   class="45-input 46-input input"   type="text" 
													data-validator="double<?php echo $status>=45?",required":"" ?>"
													 value='<?php echo $purchaseProduct['QUOTE_PRICE'] ;?>' /></td>
										</td>
									</tr>
									<tr>
										
										<th>运费支付：</th>
										<td>
											<select id="shipFeeType"  class="45-input 46-input input  ship-fee"   <?php echo $status>=45?"data-validator='required'":"" ?> >
												<option value="">选择</option>
												<option value="by" <?php if( $purchaseProduct['SHIP_FEE_TYPE'] == 'by' ) echo 'selected' ;?>>卖家承担</option>
												<option value="hdfk" <?php if($purchaseProduct['SHIP_FEE_TYPE']  == 'hdfk' ) echo 'selected' ;?> >到付</option>
												<option value="mjds" <?php if($purchaseProduct['SHIP_FEE_TYPE']  == 'mjds' ) echo 'selected' ;?> >卖家代收</option>
											</select>
										</td>
										<th>运费：</th>
										<td>
											<input id="shipFee"   class="45-input 46-input input"   type="text"   data-validator="double"
													value='<?php echo $purchaseProduct['SHIP_FEE'] ;?>' />
										</td>
									</tr>
									<tr>
										<th>支付方式：</th>
										<td>
											<select  id="payType" class="45-input 46-input  input"  <?php echo $status>=45?"data-validator='required'":"" ?>>
												<option value="">--</option>
												<option value="dh"  <?php if( $purchaseProduct['PAY_TYPE'] == 'dh' ) echo 'selected' ;?>>电汇</option>
												<option value="zfb" <?php if( $purchaseProduct['PAY_TYPE'] == 'zfb' ) echo 'selected' ;?>>支付宝</option>
												<option value="df" <?php if( $purchaseProduct['PAY_TYPE'] == 'df' ) echo 'selected' ;?>>物流代收</option>
												<option value="zqzf" <?php if( $purchaseProduct['PAY_TYPE'] == 'zqzf' ) echo 'selected' ;?>>账期支付</option>
											</select>
										</td>
										<th>承诺交期：</th>
										<td>
											<select  id="promiseDeliveryDate" class="45-input 46-input input"  <?php echo $status>=45?"data-validator='required'":"" ?>>
												<option value="">--</option>
												<option value="1"  <?php if( $purchaseProduct['PROMISE_DELIVERY_DATE'] == '1' ) echo 'selected' ;?>>常备库存</option>
												<option value="2" <?php if( $purchaseProduct['PROMISE_DELIVERY_DATE'] == '2' ) echo 'selected' ;?>>少量库存</option>
												<option value="3" <?php if( $purchaseProduct['PROMISE_DELIVERY_DATE'] == '3' ) echo 'selected' ;?>>3天内</option>
												<option value="7" <?php if( $purchaseProduct['PROMISE_DELIVERY_DATE'] == '7' ) echo 'selected' ;?>>7天内</option>
												<option value="15" <?php if( $purchaseProduct['PROMISE_DELIVERY_DATE'] == '15' ) echo 'selected' ;?>>15天以内</option>
												<option value="30" <?php if( $purchaseProduct['PROMISE_DELIVERY_DATE'] == '30' ) echo 'selected' ;?>>30天以内</option>
												<option value="31" <?php if( $purchaseProduct['PROMISE_DELIVERY_DATE'] == '31' ) echo 'selected' ;?>>30天以上</option>
											</select>
										</td>
									</tr>
									<tr>
										<th>实际供应商：</th>
										<td  colspan="3">
										<select id="realProvidor"   class=" 70-input  input" 
											<?php echo $actionType == 'audit' ?"data-validator='required'":"" ?>
										>
											<option value="">--</option>
										<?php
											foreach($suppliers as $suppli){
												$suppli = $SqlUtils->formatObject($suppli) ;
												$temp = "" ;
												if( $suppli['SUP_ID'] == $purchaseProduct['REAL_PROVIDOR'] ){
													$temp = "selected" ;
												}
												echo "<option $temp value='".$suppli['SUP_ID']."'>".$suppli['NAME']."</option>" ;
											}
										?>
										</select> 
										<?php if( !empty($purchaseProduct['REAL_PROVIDOR']) ){ ?>
										<a href="#" supplier-id="<?php echo $purchaseProduct['REAL_PROVIDOR'] ;?>">查看</a>
										<?php } ?>
										<button sku="<?php echo $sku ;?>" class="btn edit_supplier  70-input  input">编辑</button>
										</td>
										
									</tr>
									<tr class="check-purchase-tr">
										<th>实际采购时间：</th>
										<td><input id="realPurchaseDate"  data-widget="calendar" 
											data-options="{'isShowWeek':'true','dateFmt':'yyyy-MM-dd HH:mm:ss'}"
											 type="text"   class="70-input input" 
											<?php echo $actionType == 'audit'?"data-validator='required'":"" ?>
											value='<?php echo $purchaseProduct['REAL_PURCHASE_DATE'] ;?>' /></td>
										<th>实际采购价：</th>
										<td><input id="realQuotePrice"   type="text"   class="70-input input" 
													data-validator="double<?php echo $actionType == 'audit'?",required":"" ?>"
											value='<?php echo $purchaseProduct['REAL_QUOTE_PRICE'] ;?>' /></td>
									</tr>
									<tr class="check-purchase-tr">
										<th>实际运费支付：</th>
										<td>
											<select id="realShipFeeType"  class="70-input input ship-fee"   <?php echo $actionType == 'audit'?"data-validator='required'":"" ?> >
												<option value="">选择</option>
												<option value="by" <?php if( $purchaseProduct['REAL_SHIP_FEE_TYPE'] == 'by' ) echo 'selected' ;?>>卖家承担</option>
												<option value="hdfk" <?php if($purchaseProduct['REAL_SHIP_FEE_TYPE']  == 'hdfk' ) echo 'selected' ;?> >到付</option>
												<option value="mjds" <?php if($purchaseProduct['REAL_SHIP_FEE_TYPE']  == 'mjds' ) echo 'selected' ;?> >卖家代收</option>
											</select>
										</td>
										<th>实际运费：</th>
										<td>
											<input id="realShipFee" class="70-input input"   type="text"   data-validator="double"
													value='<?php echo $purchaseProduct['REAL_SHIP_FEE'] ;?>' />
										</td>
									</tr>
									<tr class="check-purchase-tr">
										<th>合格数量：</th>
										<td><input id="qualifiedProductsNum" class="50-input input"   type="text" 
													<?php echo $status>=50?"data-validator='required'":"" ?>
													value='<?php echo $purchaseProduct['QUALIFIED_PRODUCTS_NUM'] ;?>' /></td>
										<th>缺货数量：</th>
										<td><input id="outOfNum" class="50-input input"  type="text"
													<?php echo $status>=50?"data-validator='required'":"" ?>
													value='<?php echo $purchaseProduct['OUT_OF_NUM'] ;?>' /></td>
									</tr>
									<tr class="check-purchase-tr">
										<th>残品数量：</th>
										<td>
													<input id="badProductsNum" class="50-input input"  type="text"
														<?php echo $status>=50?"data-validator='required'":"" ?>
													value='<?php echo $purchaseProduct['BAD_PRODUCTS_NUM'] ;?>' />
										</td>
										<th>与需求不一致：</th>
										<td>
													<input id="noConsistencyNum" class="50-input input"  type="text"
															<?php echo $status>=50?"data-validator='required'":"" ?>
															value='<?php echo $purchaseProduct['NO_CONSISTENCY_NUM'] ;?>' />
										</td>
									</tr>
									<tr class="check-purchase-tr">
										
									</tr>
									<tr>
										<th>采购地区：</th><td colspan=3>
										<select id="area"  class="45-input input" >
											<option value="china" <?php if( $purchaseProduct['AREA'] == 'china' ) echo 'selected' ;?>>大陆</option>
											<option value="taiwan" <?php if($purchaseProduct['AREA']  == 'taiwan' ) echo 'selected' ;?> >台湾</option>
											<option value="american" <?php if($purchaseProduct['AREA']  == 'american' ) echo 'selected' ;?>>美国</option>
										</select>
										</td>
									</tr>
									
									<tr>
										<th>备注：</th><td colspan=3>
										<textarea class="10-input input" style="width:500px;height:80px;" id="memo"><?php echo $purchaseProduct['MEMO'] ;?></textarea>
										</td>
									</tr>
								</tbody>
							</table>
						</div>
						
						<!-- panel脚部内容
	                    <div class="panel-foot">
							<div class="form-actions">
								<button type="button" class="btn btn-primary btn-save">提&nbsp;交</button>
							</div>
						</div>
						-->
					</div>
				</form>
			</div>
			<?php if( $hasViewRelListing ) { ?>
			<div id="ref-asins" style="padding:10px;">
				<div class="grid-content-details" style="width:940px;"></div>
			</div>
			<?php } ?>
			<div id="tracks" style="padding:10px;">
				<div class="grid-track" style="width:940px;"></div>
			</div>
		</div>
	</div>
</body>

</html>
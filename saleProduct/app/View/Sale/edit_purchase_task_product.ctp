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
		echo $this->Html->script('modules/sale/edit_purchase_task_product');
		echo $this->Html->script('dialog/jquery.dialog');
		echo $this->Html->script('calendar/WdatePicker');
		
		$planProductId = $params['arg1'] ;
		$taskId = $params['arg2'] ;
		
		$Sale  = ClassRegistry::init("Sale") ;
		$security  = ClassRegistry::init("Security") ;
		$SqlUtils  = ClassRegistry::init("SqlUtils") ;
		$Supplier  = ClassRegistry::init("Supplier") ;
		
		$product = $Sale->getProductPlanProduct($planProductId) ;
		$taskProduct =  $SqlUtils->getObject("select * from sc_purchase_task_products where task_id = '{@#taskId#}' and product_id = '{@#productId#}'",
				array("taskId"=> $taskId,"productId"=>$planProductId)) ;
		
		
		$reqPlanId = $product["REQ_PLAN_ID"]  ;
		$realId =  $product["REAL_PRODUCT_ID"]  ;
		$sku = $product["REAL_PRODUCT_SKU"]  ;
		$suppliers = $Supplier->getProductSuppliersBySku( $sku  ) ;

		$loginId = $user['LOGIN_ID'] ;
		
		//获取计划信息
		$plan = $SqlUtils->getObject("select * from sc_purchase_plan where id = '{@#planId#}'",array("planId"=> $product["PLAN_ID"] )) ;
		//debug($plan) ;
		
		$pp_edit 								= $security->hasPermission($loginId , 'ppp_edit') ;
		$ppp_callback                      = $security->hasPermission($loginId , 'ppp_callback') ;//回退
		$ppp_add_product				= $security->hasPermission($loginId , 'ppp_add_product') ;
		$ppp_export							= $security->hasPermission($loginId , 'ppp_export') ;
		$ppp_audit							= $security->hasPermission($loginId , 'ppp_audit') ;
		$pptp_audit							= $security->hasPermission($loginId , 'pptp_audit') ;//采购产品任务审批
		
		$ppp_deal								= $security->hasPermission($loginId , 'pptp_deal') ;//采购产品任务审批
		$ppp_receviced					= $security->hasPermission($loginId , 'pptp_receviced') ;//采购产品任务审批
		
		$ppp_setlimitprice				= $security->hasPermission($loginId , 'ppp_setlimitprice') ;
		$ppp_assign_executor			= $loginId == $plan['EXECUTOR'] || $security->hasPermission($loginId , 'ppp_assign_executor') ;//分配执行人，计划负责人等于当前用户
		$ppp_qc								= $security->hasPermission($loginId , 'ppp_qc') ;
		$ppp_inwarehouse				= $security->hasPermission($loginId , 'ppp_inwarehouse') ;
		$ppp_confirm 						= $security->hasPermission($loginId , 'ppp_confirm') ;
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
		$suppliers = $SqlUtils->exeSql("sql_purchase_plan_product_inquiry",array('planId'=>$product["PLAN_ID"] ,'sku'=>$product["REAL_PRODUCT_SKU"])) ;
		
		$status = $taskProduct['STATUS'] ;
		$executor = $product['EXECUTOR'] ;
		$executorName = $product['EXECUTOR_NAME'] ;
		if( empty($executor) ){
			$executor = $product['PURCHASE_CHARGER'] ;
			$executorName = $product['PURCHASE_CHARGER_NAME'] ;
		}
		
		$isOwner = $loginId == $product['EXECUTOR'] ;
		/*
		$id = $product["ID"] ;
		$asin = $product["ASIN"] ;
		$sku = $product["SKU"] ;
		$title = $product["TITLE"] ;
		$planId = $product["PLAN_ID"] ;
		
		$cost = $product["COST"] ;
		$plan_num = $product["PLAN_NUM"] ;
		$quote_price = $product["QUOTE_PRICE"] ;
		$providor = $product["PROVIDOR"] ;
		$sample_code = $product["SAMPLE_CODE"] ;
		$sample  = $product["SAMPLE"] ;
		$area = $product["AREA"] ;
		$status = $product['STATUS']; ;
		*/
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
	</style>

   <script type="text/javascript">
    var $COST_VIEW_TOTAL = '<?php echo $COST_VIEW_TOTAL;?>' ;
    var  $COST_VIEW_PROFIT = '<?php echo $COST_VIEW_PROFIT;?>' ;
    var sku = '<?php echo $product['REAL_PRODUCT_SKU'];?>' ;
    var planId =  '<?php echo $product['PLAN_ID'];?>' ;
    var realId =  '<?php echo $product['REAL_PRODUCT_ID'];?>' ;
    var reqPlanId =  '<?php echo $reqPlanId;?>' ;
   
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

	var id = '<?php echo $product['ID'] ;?>' ;
	var taskId =  '<?php echo $taskProduct['TASK_ID'] ;?>' ;
	var currentStatus = "<?php echo $taskProduct['STATUS'];?>" ;

	 var flowData = [
	        		{status:45,label:"询价",memo:true
	        			,actions:[{}
									<?php if( $isOwner || $ppp_assign_executor) { ?>,{label:"保存",action:function(){ ForceAuditAction(45,"保存") }}  <?php  }?>
									<?php if( $isOwner ) { ?>,{label:"已询价，提交审批",action:function(){ AuditAction(47,"已询价，提交审批") } }<?php  }?>
									<?php if(  $endPurchase ) { ?>,{label:"终止采购",clazz:"btn-danger",action:function(){ ForceAuditAction(80,"终止采购") } }<?php } ?>
        				],format:function(node){
							if( currentStatus == 46  ){
								node.label = "再询价" ;
								node.status = 46 ;
							}
            			}
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
	        		*/?>
	        		{status:47,label:"交易审批",memo:true
	        			,actions:[{}
									<?php if( $ppp_callback ){ ?>,{label:"回退再询价",action:function(){ ForceAuditAction(46,"回退再询价") }}<?php   } ?>
									<?php if( $isOwner  || $pptp_audit ) { ?>,{label:"保存",action:function(){ ForceAuditAction(47,"保存") }}<?php  } ?>
									<?php if( $pptp_audit ) { ?>,{label:"审批通过",action:function(){ AuditAction(48,"审批通过") } }<?php   }   ?>
									<?php if(  $endPurchase ) { ?>,{label:"终止采购",clazz:"btn-danger",action:function(){ ForceAuditAction(80,"终止采购") } }<?php } ?>
        				]
	        		},
	        		{status:48,label:"待交易",memo:true
	        			,actions:[{}
									<?php if(  $ppp_callback ){ ?>,{label:"回退",action:function(){ ForceAuditAction(47,"回退") }}<?php   } ?>
									<?php if( $isOwner || $ppp_deal) { ?>,{label:"保存",action:function(){ ForceAuditAction(48,"保存") }}<?php  } ?>
									<?php if( $isOwner|| $ppp_deal ) { ?>,{label:"已交易",action:function(){ AuditAction(49,"已交易") } }<?php   }   ?>
									<?php if(  $endPurchase ) { ?>,{label:"终止采购",clazz:"btn-danger",action:function(){ ForceAuditAction(80,"终止采购") } }<?php } ?>
        				]
	        		},
	        		{status:49,label:"待收货",memo:true //48->49
	        			,actions:[
	        			          {}
									<?php if(  $ppp_callback ){ ?>,{label:"回退",action:function(){ ForceAuditAction(48,"回退") }}<?php  }?>
									<?php if( $isOwner || $ppp_assign_executor) { ?>,{label:"保存",action:function(){ ForceAuditAction(49,"保存") }}<?php  } ?>
									<?php if( $isOwner || $ppp_receviced) { ?>,{label:"已到货",action:function(){ AuditAction(50,"已到货") } }<?php } ?>
									<?php if(  $endPurchase ) { ?>,{label:"终止采购",clazz:"btn-danger",action:function(){ ForceAuditAction(80,"终止采购") } }<?php } ?>
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
									<?php if(  $endPurchase ) { ?>,{label:"终止采购",clazz:"btn-danger",action:function(){ ForceAuditAction(80,"终止采购") } }<?php } ?>
        				]
	        			
	        		},{status:60,label:"货品入库",memo:true
	        			
	        			,actions:[{}
								<?php if( $ppp_inwarehouse) { ?>
									<?php if( $ppp_callback ){ ?>
									,{label:"回退",action:function(){ ForceAuditAction(50,"回退") }},
									<?php }?>
									,{label:"保存",action:function(){ ForceAuditAction(60,"保存") }},
		      	        			{label:"入库确认",action:function(){ WarehouseInAction(70,"入库确认") } }
								<?php };?>
								<?php if(  $endPurchase ) { ?>,{label:"终止采购",clazz:"btn-danger",action:function(){ ForceAuditAction(80,"终止采购") } }<?php } ?>
        				]
	        			
	        		},
	        		{status:70,label:"采购审计",memo:true
	        			
	        			,actions:[{}
							<?php if( $ppp_confirm) { ?>
									,{label:"保存",action:function(){ ForceAuditAction(70,"保存") }},
		      	        			{label:"采购审计",action:function(){ AuditAction(80,"采购审计") } }
									<?php };?>
									<?php if(  $endPurchase ) { ?>,{label:"终止采购",clazz:"btn-danger",action:function(){ ForceAuditAction(80,"终止采购") } }<?php } ?>
		      	        			]
	        			
	        		},
	        		{status:80,label:"结束"}
	        	] ;
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
		        	<input id="productId" type="hidden" value='<?php echo $product['ID'] ;?>' />
		        	<input id="taskId" type="hidden" value='<?php echo $taskProduct['TASK_ID'] ;?>' />
					<!-- panel 头部内容  此场景下是隐藏的-->
					<div class="panel apply-panel">
						<!-- panel 中间内容-->
						<div class="panel-content">
							<!-- 数据列表样式 -->
							<table class="form-table" >
								<caption>基本信息</caption>
								<tbody>										   
									<tr>
										<th>编号：</th><td style="width:30%;"><?php echo  $product['ID'] ;?></td>
										<th>执行人：</th><td>
											<?php echo $executorName;?>
										</td>
									</tr>
									<tr>
										<th>货品：</th><td><a class="product-realsku"  sku="<?php echo $product['REAL_PRODUCT_SKU'] ;?>"  href="#">
										<?php echo  $product['REAL_PRODUCT_SKU']  ;?></a>（<?php echo  $product['TITLE']  ;?>）
										<input type="hidden"  name="sku" value="<?php echo  $product['REAL_PRODUCT_SKU']  ;?>"/>
										</td>
										<th>
										<button class="btn change_plan no-disabled">更改</button>
										采购计划：</th><td>
											<span class="plan-name"><?php echo $plan['NAME'] ;?> </span>
											<span  class="plan-container">
											</span>
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
										<td colspan=3><?php echo $product['PLAN_START_TIME'];?>&nbsp;&nbsp;&nbsp;到&nbsp;&nbsp;&nbsp;<?php echo $product['PLAN_END_TIME'];?></td>
									</tr>
									<tr>
										<th>计划采购数量：</th>
										<td><?php echo  $product['PLAN_NUM']  ;?></td>
										<th>采购限价：</th>
										<td><?php echo $product['LIMIT_PRICE'];?></td>
									</tr>
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
					                             	  $selected = $taskProduct['WAREHOUSE_ID'] == $w['ID'] ?"selected":"" ;
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
													if( $taskProduct['WAREHOUSE_TIME'] == "0000-00-00 00:00:00"){
														echo "";
													} else{
														echo $taskProduct['WAREHOUSE_TIME'];
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
										<select id="providor"   class="45-input   input" 
											<?php echo $status>=45?"data-validator='required'":"" ?>
										>
											<option value="">--</option>
										<?php
										
											foreach($suppliers as $suppli){
												$suppli = $SqlUtils->formatObject($suppli) ;
												$temp = "" ;
												if( $suppli['SUP_ID'] == $taskProduct['PROVIDOR'] ){
													$temp = "selected" ;
												}
												echo "<option $temp value='".$suppli['SUP_ID']."'>".$suppli['NAME']."</option>" ;
											}
										?>
										</select> 
										<?php if( !empty($taskProduct['PROVIDOR']) ){ ?>
										<a href="#" supplier-id="<?php echo $taskProduct['PROVIDOR'] ;?>">查看</a>
										<?php } ?>
										<button sku="<?php echo $sku ;?>" class="btn edit_supplier 45-input  input">编辑</button>
										</td>
										<th>供应商报价：</th>
										<td><input id="quotePrice"   class="45-input input"   type="text" 
													data-validator="double<?php echo $status>=45?",required":"" ?>"
													 value='<?php echo $taskProduct['QUOTE_PRICE'] ;?>' /></td>
										</td>
									</tr>
									<tr>
										
										<th>运费支付：</th>
										<td>
											<select id="shipFeeType"  class="45-input input  ship-fee"   <?php echo $status>=45?"data-validator='required'":"" ?> >
												<option value="">选择</option>
												<option value="by" <?php if( $taskProduct['SHIP_FEE_TYPE'] == 'by' ) echo 'selected' ;?>>卖家承担</option>
												<option value="hdfk" <?php if($taskProduct['SHIP_FEE_TYPE']  == 'hdfk' ) echo 'selected' ;?> >到付</option>
												<option value="mjds" <?php if($taskProduct['SHIP_FEE_TYPE']  == 'mjds' ) echo 'selected' ;?> >卖家代收</option>
											</select>
										</td>
										<th>运费：</th>
										<td>
											<input id="shipFee"   class="45-input input"   type="text"   data-validator="double"
													value='<?php echo $taskProduct['SHIP_FEE'] ;?>' />
										</td>
									</tr>
									<tr>
										<th>支付方式：</th>
										<td>
											<select  id="payType" class="45-input input"  <?php echo $status>=45?"data-validator='required'":"" ?>>
												<option value="">--</option>
												<option value="dh"  <?php if( $taskProduct['PAY_TYPE'] == 'dh' ) echo 'selected' ;?>>电汇</option>
												<option value="zfb" <?php if( $taskProduct['PAY_TYPE'] == 'zfb' ) echo 'selected' ;?>>支付宝</option>
												<option value="df" <?php if( $taskProduct['PAY_TYPE'] == 'df' ) echo 'selected' ;?>>物流代收</option>
												<option value="zqzf" <?php if( $taskProduct['PAY_TYPE'] == 'zqzf' ) echo 'selected' ;?>>账期支付</option>
											</select>
										</td>
										<th>承诺交期：</th>
										<td>
											<select  id="promiseDeliveryDate" class="45-input input"  <?php echo $status>=45?"data-validator='required'":"" ?>>
												<option value="">--</option>
												<option value="1"  <?php if( $taskProduct['PROMISE_DELIVERY_DATE'] == '1' ) echo 'selected' ;?>>常备库存</option>
												<option value="2" <?php if( $taskProduct['PROMISE_DELIVERY_DATE'] == '2' ) echo 'selected' ;?>>少量库存</option>
												<option value="3" <?php if( $taskProduct['PROMISE_DELIVERY_DATE'] == '3' ) echo 'selected' ;?>>3天内</option>
												<option value="7" <?php if( $taskProduct['PROMISE_DELIVERY_DATE'] == '7' ) echo 'selected' ;?>>7天内</option>
												<option value="15" <?php if( $taskProduct['PROMISE_DELIVERY_DATE'] == '15' ) echo 'selected' ;?>>15天以内</option>
												<option value="30" <?php if( $taskProduct['PROMISE_DELIVERY_DATE'] == '30' ) echo 'selected' ;?>>30天以内</option>
												<option value="31" <?php if( $taskProduct['PROMISE_DELIVERY_DATE'] == '31' ) echo 'selected' ;?>>30天以上</option>
											</select>
										</td>
									</tr>
									<tr>
										<th>实际供应商：</th>
										<td>
										<select id="realProvidor"   class=" 70-input  input" 
											<?php echo $status>=70?"data-validator='required'":"" ?>
										>
											<option value="">--</option>
										<?php
											foreach($suppliers as $suppli){
												$suppli = $SqlUtils->formatObject($suppli) ;
												$temp = "" ;
												if( $suppli['SUP_ID'] == $taskProduct['REAL_PROVIDOR'] ){
													$temp = "selected" ;
												}
												echo "<option $temp value='".$suppli['SUP_ID']."'>".$suppli['NAME']."</option>" ;
											}
										?>
										</select> 
										<?php if( !empty($taskProduct['REAL_PROVIDOR']) ){ ?>
										<a href="#" supplier-id="<?php echo $taskProduct['REAL_PROVIDOR'] ;?>">查看</a>
										<?php } ?>
										<button sku="<?php echo $sku ;?>" class="btn edit_supplier  70-input  input">编辑</button>
										</td>
										
									</tr>
									<tr class="check-purchase-tr">
										<th>实际采购时间：</th>
										<td><input id="realPurchaseDate"  data-widget="calendar" 
											data-options="{'isShowWeek':'true','dateFmt':'yyyy-MM-dd HH:mm:ss'}"
											 type="text"   class="70-input input" 
											<?php echo $status>=70?"data-validator='required'":"" ?>
											value='<?php echo $taskProduct['REAL_PURCHASE_DATE'] ;?>' /></td>
										<th>实际采购价：</th>
										<td><input id="realQuotePrice"   type="text"   class="70-input input" 
													data-validator="double<?php echo $status>=70?",required":"" ?>"
											value='<?php echo $taskProduct['REAL_QUOTE_PRICE'] ;?>' /></td>
									</tr>
									<tr class="check-purchase-tr">
										<th>实际运费支付：</th>
										<td>
											<select id="realShipFeeType"  class="70-input input ship-fee"   <?php echo $status>=70?"data-validator='required'":"" ?> >
												<option value="">选择</option>
												<option value="by" <?php if( $taskProduct['REAL_SHIP_FEE_TYPE'] == 'by' ) echo 'selected' ;?>>卖家承担</option>
												<option value="hdfk" <?php if($taskProduct['REAL_SHIP_FEE_TYPE']  == 'hdfk' ) echo 'selected' ;?> >到付</option>
												<option value="mjds" <?php if($taskProduct['REAL_SHIP_FEE_TYPE']  == 'mjds' ) echo 'selected' ;?> >卖家代收</option>
											</select>
										</td>
										<th>实际运费：</th>
										<td>
											<input id="realShipFee" class="70-input input"   type="text"   data-validator="double"
													value='<?php echo $taskProduct['REAL_SHIP_FEE'] ;?>' />
										</td>
									</tr>
									<tr class="check-purchase-tr">
										<th>合格数量：</th>
										<td><input id="qualifiedProductsNum" class="50-input input"   type="text" 
													<?php echo $status>=50?"data-validator='required'":"" ?>
													value='<?php echo $taskProduct['QUALIFIED_PRODUCTS_NUM'] ;?>' /></td>
										<th>不合格数量：</th>
										<td><input id="badProductsNum" class="50-input input"  type="text"
													<?php echo $status>=50?"data-validator='required'":"" ?>
													value='<?php echo $taskProduct['BAD_PRODUCTS_NUM'] ;?>' /></td>
									</tr>
									<tr>
										<th>采购地区：</th><td colspan=3>
										<select id="area"  class="45-input input" >
											<option value="china" <?php if( $taskProduct['AREA'] == 'china' ) echo 'selected' ;?>>大陆</option>
											<option value="taiwan" <?php if($taskProduct['AREA']  == 'taiwan' ) echo 'selected' ;?> >台湾</option>
											<option value="american" <?php if($taskProduct['AREA']  == 'american' ) echo 'selected' ;?>>美国</option>
										</select>
										</td>
									</tr>
									
									<tr>
										<th>备注：</th><td colspan=3>
										<textarea class="10-input input" style="width:500px;height:80px;" id="memo"><?php echo $product['MEMO'] ;?></textarea>
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
			<div id="ref-asins" style="width:880px;padding:10px;">
				<div class="grid-content-details" style="width:858px;"></div>
			</div>
			<?php } ?>
			<div id="tracks" style="width:880px;padding:10px;">
				<div class="grid-track" style="width:858px;"></div>
			</div>
		</div>
	</div>
</body>

</html>
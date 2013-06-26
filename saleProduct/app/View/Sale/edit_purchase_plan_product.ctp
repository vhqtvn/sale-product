<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
   <?php echo $this->Html->charset(); ?>
    <title>采购计划产品</title>
    <meta http-equiv="pragma" content="no-cache"/>
	<meta http-equiv="cache-control" content="no-cache"/>

   <?php
   		include_once ('config/config.php');
   
		echo $this->Html->meta('icon');
		echo $this->Html->css('../js/grid/jquery.llygrid');
		echo $this->Html->css('../js/validator/jquery.validation');
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
		echo $this->Html->script('modules/sale/edit_purchase_plan_product');
		echo $this->Html->script('calendar/WdatePicker');
		
		$planProductId = $params['arg1'] ;
		
		$Sale  = ClassRegistry::init("Sale") ;
		$security  = ClassRegistry::init("Security") ;
		$SqlUtils  = ClassRegistry::init("SqlUtils") ;
		$Supplier  = ClassRegistry::init("Supplier") ;
		
		$product = $Sale->getProductPlanProduct($planProductId) ;
		
		$sku = $product["SKU"]  ;
		$suppliers = $Supplier->getProductSuppliersBySku( $sku  ) ;
		
		$loginId = $user['LOGIN_ID'] ;
		
		//////////////////////////////////////
		
		//获取计划信息
		$plan = $SqlUtils->getObject("select * from sc_purchase_plan where id = '{@#planId#}'",array("planId"=> $product["PLAN_ID"] )) ;
		//debug($plan) ;
		
		$pp_edit 								= $security->hasPermission($loginId , 'ppp_edit') ;
		$ppp_callback                      = $security->hasPermission($loginId , 'ppp_callback') ;//回退
		$ppp_add_product				= $security->hasPermission($loginId , 'ppp_add_product') ;
		$ppp_export							= $security->hasPermission($loginId , 'ppp_export') ;
		$ppp_audit							= $security->hasPermission($loginId , 'ppp_audit') ;
		$ppp_setlimitprice				= $security->hasPermission($loginId , 'ppp_setlimitprice') ;
		$ppp_assign_executor			= $loginId == $plan['EXECUTOR'] || $security->hasPermission($loginId , 'ppp_assign_executor') ;//分配执行人，计划负责人等于当前用户
		$ppp_qc								= $security->hasPermission($loginId , 'ppp_qc') ;
		$ppp_inwarehouse				= $security->hasPermission($loginId , 'ppp_inwarehouse') ;
		$ppp_confirm 						= $security->hasPermission($loginId , 'ppp_confirm') ;
		$reedit_pp_product				= $security->hasPermission($loginId , 'reedit_pp_product') ;//在编辑功能
		$hasSetSupplierPermission = $security->hasPermission($loginId, 'SET_PRODUCT_SUPPLIER_FLAG') ;
		$hasViewRelListing =  $security->hasPermission($loginId , 'view_rp_rel_listing') ;
		//成本利润查看权限
		//成本权限
		//成本权限
		$COST_EDIT_PROFIT   						= $security->hasPermission($loginId , 'COST_EDIT_PROFIT') ;
		$COST_VIEW_TOTAL  						= $security->hasPermission($loginId , 'COST_VIEW_TOTAL') ;//cen
		$COST_VIEW_PROFIT  						= $security->hasPermission($loginId , 'COST_VIEW_PROFIT') ||$COST_EDIT_PROFIT  ;
		
		//获取货品供应商询价
		$suppliers = $SqlUtils->exeSql("sql_purchase_plan_product_inquiry",array('planId'=>$product["PLAN_ID"] ,'sku'=>$product["SKU"])) ;
		
		$status = $product['STATUS'] ;
		$executor = $product['EXECUTOR'] ;
		$executorName = $product['EXECUTOR_NAME'] ;
		if( empty($executor) ){
			$executor = $product['PURCHASE_CHARGER'] ;
			$executorName = $product['PURCHASE_CHARGER_NAME'] ;
		}
		
		$isOwner = $loginId == $product['EXECUTOR'] ;
	
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
    var sku = '<?php echo $product['SKU'];?>' ;
    var planId =  '<?php echo $product['PLAN_ID'];?>' ;
   
    var $pp_edit = <?php echo $pp_edit?"true":"false" ;?> ;
	var $ppp_add_product = <?php echo $ppp_add_product?"true":"false" ;?> ;
	var $ppp_export= <?php echo $ppp_export?"true":"false" ;?> ;
	var $ppp_audit = <?php echo $ppp_audit?"true":"false" ;?> ;
	var $ppp_setlimitprice = <?php echo $ppp_setlimitprice?"true":"false" ;?> ;
	var $ppp_assign_executor = <?php echo $ppp_assign_executor?"true":"false" ;?> ;
	var $ppp_qc = <?php echo $ppp_qc?"true":"false" ;?> ;
	var $ppp_inwarehouse = <?php echo $ppp_inwarehouse?"true":"false" ;?> ;
	var $ppp_confirm = <?php echo $ppp_confirm?"true":"false" ;?> ;
	var $reedit_pp_product = <?php echo $reedit_pp_product?"true":"false" ;?> ;

	var id = '<?php echo $product['ID'] ;?>' ;
	var currentStatus = "<?php echo $product['STATUS'];?>" ;

	 var flowData = [
	        		{status:10,label:"编辑中",memo:true
	        			<?php if( $pp_edit ) { ?>
	        			,actions:[
									{label:"保存",action:function(){ ForceAuditAction(10,"保存") }},
		      	        			{label:"保存提交审批",action:function(){ AuditAction(20,"保存并提交审批") }}
		      	        	]
	        			<?php };?>
	        		},
	        		{status:20,label:"审批确认",memo:true,format:function(node){
								if( currentStatus == 25 ){//审批不通过，中止采购
									node.label = "审批不通过，结束采购" ;
									node.statusClass = "termination" ;
									node.isbreak = true ;
							   }
						}
	        			<?php if( $ppp_audit ) { ?>
	        			,actions:[{label:"审批通过",action:function(){ AuditAction(30,"审批通过") } },
	        				{label:"审批不通过，继续编辑",action:function(){ ForceAuditAction(10,"审批不通过，继续编辑") } },
	        				{label:"审批不通过，结束采购",action:function(){ ForceAuditAction(25,"审批不通过，结束采购") } }
        				]
	        			<?php };?>
	        		},
	        		{status:30,label:"限价确认",memo:true
	        			<?php if( $ppp_setlimitprice) { ?>
	        			,actions:[
		      	        			<?php if( $ppp_callback ){ ?>
		      	        			{label:"回退",action:function(){ ForceAuditAction(20,"回退") }},
		      	        			<?php }?>
									{label:"保存",action:function(){ ForceAuditAction(30,"保存") }},
		      	        			{label:"确认限价",action:function(){ AuditAction(40,"确认限价") } }
        				]
	        			<?php };?>
	        		},
	        		{status:40,label:"分配执行人",memo:true
	        			<?php if( $ppp_assign_executor ) { ?>
	        			,actions:[
									<?php if( $ppp_callback ){ ?>
									{label:"回退",action:function(){ ForceAuditAction(30,"回退") }},
									<?php }?>
									{label:"保存",action:function(){ ForceAuditAction(40,"保存") }},
		      	        			{label:"分配采购执行人",action:function(){ AuditAction(45,"分配采购执行人") } }
        				]
	        			<?php };?>
	        		}
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
		        	<input id="id" type="hidden" value='<?php echo $product['ID'] ;?>' />
					<!-- panel 头部内容  此场景下是隐藏的-->
					<div class="panel apply-panel">
						<!-- panel 中间内容-->
						<div class="panel-content">
							<!-- 数据列表样式 -->
							<table class="form-table" >
								<caption>基本信息</caption>
								<tbody>										   
									<tr>
										<th>编号：</th><td><?php echo  $product['ID'] ;?></td>
										<th>执行人：</th><td>
											<input type="hidden"   id="executor"  class="40-input input"
											value="<?php echo $executor;?>"/>
											<input type="text"  class="40-input input span2"  id="executorName"  readonly
													<?php echo $status>=40?"data-validator='required'":"" ?>
													value="<?php echo $executorName;?>"/>
											<button class="40-input input btn btn-charger">选择</button>
										</td>
									</tr>
									<tr>
										<th>货品：</th><td colspan=3><a class="product-realsku"  sku="<?php echo $product['SKU'] ;?>"  href="#">
										<?php echo  $product['SKU']  ;?></a>（<?php echo  $product['TITLE']  ;?>）</td>
									</tr>
									<tr>
										<th><button class="btn btn-tags 10-input  20-input 30-input  40-input 45-input input no-disabled">添加</button>标签：</th>
										<td colspan=3>
											<input id="tags"   type="hidden"   value="<?php echo $product['TAGS'];?>"/>
											<ul class="tag-container" style="list-style: none;">
											</ul>
										</td>
									</tr>
									<tr>
										<th>采购时限：</th>
										<td colspan=3>
										<input id="planStartTime" class="10-input input"  data-validator="required"  type="text"  
											value="<?php echo $product['PLAN_START_TIME'];?>" data-widget="calendar"/>到
										<input id="planEndTime"  class="10-input input"   data-validator="required"  type="text" 
											value="<?php echo $product['PLAN_END_TIME'];?>" data-widget="calendar"/></td>
									</tr>
									<tr>
										<th>计划采购数量：</th>
										<td><input id="plan_num"   class="10-input input"  data-validator="required"    type="text" value='<?php echo  $product['PLAN_NUM']  ;?>' /></td>
										<th>采购限价：</th>
										<td><input id="limit_price"   class="30-input input"   type="text" 
													 <?php echo $status>=30?"data-validator='required'":"" ?>
													 value='<?php echo $product['LIMIT_PRICE'];?>' /></td>
									</tr>
									<tr>
										<th>备注：</th><td colspan=3>
										<textarea class="10-input input" style="width:500px;height:80px;" id="memo"><?php echo $product['MEMO'] ;?></textarea>
										</td>
									</tr>
								</tbody>
						</table>
						<?php 
							$products = $SqlUtils->exeSqlWithFormat("SELECT sptp.*,
													      (SELECT NAME FROM sc_purchase_task spt WHERE spt.id = sptp.TASK_ID) AS TASK_NAME,
															 (SELECT NAME FROM sc_supplier spt WHERE spt.id = sptp.REAL_PROVIDOR) AS REAL_PROVIDOR_NAME
													 FROM sc_purchase_task_products sptp where sptp.product_id = '{@#productId#}' ",
									array('productId'=>$product['ID'] )) ;
							
						
							
						?>
						<table class="form-table" >
								<caption>采购任务</caption>
								<thead>
									<tr>
									<th>任务名称</th>
									<th>任务状态</th>
									<th>实际供应商</th>
									<th>采购价格</th>
									<th>采购时间</th>
									<th>合格数量</th>
									<th>不合格数量</th>
									</tr>
								</thead>
								<tbody>
								<?php  foreach ( $products as $p ){ 
									$message = "" ;
									switch ( $p['STATUS']  ){
										case '45':  $message = "待询价";break;
										case '46':  $message = "采购申请";break;
										case '47':  $message = "待交易";break;
										case '48':  $message = "待发货";break;
										case '50':  $message = "QC验货";break;
										case '60':  $message = "入库";break;
										case '70':  $message = "采购审计";break;
										case '80':  $message = "结束";break;
									}
									?>  
									<tr>
										<td><a target="_blank" href="<?php echo $contextPath;?>/page/forward/Sale.edit_purchase_task_product/<?php echo $p['PRODUCT_ID'];?>/<?php echo $p['TASK_ID'];?>"><?php echo  $p['TASK_NAME'] ;?></a></td>
										<td><?php echo  $message ;?></td>
										<td><?php echo  $p['REAL_PROVIDOR_NAME'] ;?></td>
										<td><?php echo  $p['REAL_QUOTE_PRICE'] ;?></td>
										<td><?php echo  $p['REAL_PURCHASE_DATE'] ;?></td>
										<td><?php echo  $p['QUALIFIED_PRODUCTS_NUM'] ;?></td>
										<td><?php echo  $p['BAD_PRODUCTS_NUM'] ;?></td>
									</tr>
								<?php 	} ?>	
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
				<div class="grid-content-details" style="width:958px;"></div>
			</div>
			<?php } ?>
			<div id="tracks" style="width:880px;padding:10px;">
				<div class="grid-track" style="width:858px;"></div>
			</div>
		</div>
	</div>
</body>

</html>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
   <?php echo $this->Html->charset(); ?>
    <title></title>
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
		
		$security  = ClassRegistry::init("Security") ;
		
		$loginId = $user['LOGIN_ID'] ;
		$create_pp 				= $security->hasPermission($loginId , 'create_pp') ;
		$delete_pp 				= $security->hasPermission($loginId , 'delete_pp') ;
		$add_pp_product 		= $security->hasPermission($loginId , 'add_pp_product') ;
		$add_pp_audit_product	= $security->hasPermission($loginId , 'add_pp_audit_product') ;
		$export_pp 				= $security->hasPermission($loginId , 'export_pp') ;
		$print_pp 				= $security->hasPermission($loginId , 'print_pp') ;
		$edit_pp_product 		= $security->hasPermission($loginId , 'edit_pp_product') ;
		$reedit_pp_product 		= $security->hasPermission($loginId , 'reedit_pp_product') ;
		$delete_pp_product 		= $security->hasPermission($loginId , 'delete_pp_product') ;
		$apply_purchase 		= $security->hasPermission($loginId , 'apply_purchase') ;
		$audit_purchase 	= $security->hasPermission($loginId , 'audit_purchase') ;
		$purchase_cost_view 	= $security->hasPermission($loginId , 'purchase_cost_view') ;
		$confirm_purchase 	= $security->hasPermission($loginId , 'confirm_purchase') ;
		
		$hasViewRelListing = $security->hasPermission($loginId , 'view_rp_rel_listing') ;
	?>
  
   <style>
   		*{
   			font:12px "微软雅黑";
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
			position:relative;
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
	</style>

   <script type="text/javascript">
    var $create_pp = <?php echo $create_pp?"true":"false" ;?> ;
	var $delete_pp = <?php echo $delete_pp?"true":"false" ;?> ;
	var $add_pp_product = <?php echo $add_pp_product?"true":"false" ;?> ;
	var $add_pp_audit_product = <?php echo $add_pp_audit_product?"true":"false" ;?> ;
	var $export_pp = <?php echo $export_pp?"true":"false" ;?> ;
	var $print_pp = <?php echo $print_pp?"true":"false" ;?> ;
	var $edit_pp_product = <?php echo $edit_pp_product?"true":"false" ;?> ;
	var $reedit_pp_product = <?php echo $reedit_pp_product?"true":"false" ;?> ;
	var $delete_pp_product = <?php echo $delete_pp_product?"true":"false" ;?> ;
	var $apply_purchase = <?php echo $apply_purchase?"true":"false" ;?> ;
	var $audit_purchase = <?php echo $audit_purchase?"true":"false" ;?> ;
	var $purchase_cost_view = <?php echo $purchase_cost_view?"true":"false" ;?> ;
	var $confirm_purchase = <?php echo $confirm_purchase?"true":"false" ;?> ;

	var id = '<?php echo $id ;?>' ;
	var currentStatus = "<?php echo $product['STATUS'];?>" ;

	 var flowData = [
	        		{status:1,label:"编辑中",memo:true
	        			<?php if( $security->hasPermission($loginId , 'add_pp_product') ) { ?>
	        			,actions:[
									{label:"保存暂不提交审批",action:function(){ AuditAction(1,"保存暂不提交审批") }},
		      	        			{label:"保存提交审批",action:function(){ AuditAction(2,"保存并提交审批") }}
		      	        	]
	        			<?php };?>
	        		},
	        		{status:2,label:"待审批",memo:true
	        			<?php if( $security->hasPermission($loginId , 'add_pp_audit_product')) { ?>
	        			,actions:[{label:"审批通过",action:function(){ AuditAction(3,"审批通过") } },
	        				{label:"审批不通过，继续编辑",action:function(){ AuditAction(1,"审批不通过，继续编辑") } },
	        				{label:"审批不通过，结束采购",action:function(){ AuditAction(4,"审批不通过，结束采购") } }
        				]
	        			<?php };?>
	        		},
	        		{status:3,label:"采购确认",memo:true
	        			<?php if( $security->hasPermission($loginId , 'confirm_purchase')) { ?>
	        			,actions:[{label:"确认采购",action:function(){ AuditAction(5,"采购确认") } }]
	        			<?php };?>
	        		,format:function(node){
						if( currentStatus == 4 ){//审批不通过，中止采购
								node.label = "审批不通过，中止采购" ;
								node.statusClass = "termination" ;
								node.isbreak = true ;
						}
		        	}},
	        		{status:5,label:"验收货品",memo:true
	        			<?php if( $security->hasPermission($loginId , 'purchase_qc_confirm')) { ?>
	        			,actions:[{label:"验收货品",action:function(){ AuditAction(6,"确认验收货品") } }]
	        			<?php };?>
	        		},
	        		{status:6,label:"结束"}
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
		        	<input id="id" type="hidden" value='<?php echo $id ;?>' />
					<!-- panel 头部内容  此场景下是隐藏的-->
					<div class="panel apply-panel">
						<!-- panel 中间内容-->
						<div class="panel-content">
							<!-- 数据列表样式 -->
							<table class="form-table" >
								<caption>采购产品信息</caption>
								<tbody>										   
									<tr>
										<th>编号：</th><td><?php echo $id ;?></td>
										<th>执行人：</th><td>
											<input type="hidden" data-validator="required" id="executor" 
											value="<?php echo $product['EXECUTOR'];?>"/>
											<input type="text" data-validator="required" id="executorName" class="span2" readonly
													value="<?php echo $product['EXECUTOR_NAME'];?>"/>
											<button class="btn btn-charger">选择</button>
										</td>
									</tr>
									<tr>
										<th>货品：</th><td colspan=3><a class="product-realsku"  sku="<?php echo $sku ;?>"  href="#"><?php echo $sku ;?></a>（<?php echo $title ;?>）</td>
									</tr>
									<tr>
										<th>采购时限：</th>
										<td colspan=3><input id="planStartTime"  data-validator="required"  type="text"  value="<?php echo $product['PLAN_START_TIME'];?>" data-widget="calendar"/>到
										<input id="planEndTime"  data-validator="required"  type="text" value="<?php echo $product['PLAN_END_TIME'];?>" data-widget="calendar"/></td>
									</tr>
									<tr>
										<th>计划采购数量：</th>
										<td><input id="plan_num"  data-validator="required"    type="text" value='<?php echo $plan_num ;?>' /></td>
										<th>计划采购价：</th>
										<td><input id="quote_price"    type="text" value='<?php echo $quote_price ;?>' /></td>
									</tr>
									<?php  if( $product['STATUS'] >= 3 ){ ?>
									<tr class="real-purchase-tr">
										<th>实际采购数量：</th>
										<td><input id="realNum"  data-validator="required"  type="text" value='<?php echo $product['REAL_NUM'] ;?>' /></td>
										<th>实际采购价：</th>
										<td><input id="realQuotePrice" data-validator="required"  type="text" value='<?php echo $product['REAL_QUOTE_PRICE'] ;?>' /></td>
									</tr>
									<?php } ?>
									<tr class="real-purchase-tr">
										<th>供应商：</th>
										<td <?php echo $product['STATUS'] >= 3?"":"colspan=3" ?>   >
										<select id="providor"  <?php echo $product['STATUS'] >= 3?"data-validator='required'":"" ?>>
											<option value="">--</option>
										<?php
											$SqlUtils  = ClassRegistry::init("SqlUtils") ;
											
											foreach($supplier as $suppli){
												$suppli = $SqlUtils->formatObject($suppli) ;
												$temp = "" ;
												if( $suppli['ID'] == $providor ){
													$temp = "selected" ;
												}
												echo "<option $temp value='".$suppli['ID']."'>".$suppli['NAME']."</option>" ;
											}
										?>
										</select> 
										<button sku="<?php echo $sku ;?>" class="btn edit_supplier">编辑</button>
										</td>
										<?php  if( $product['STATUS'] >= 3 ){ ?>
										<th>实际采购日期：</th>
										<td><input id="realPurchaseDate"  data-widget="calendar" data-validator="required" type="text" value='<?php echo $product['REAL_PURCHASE_DATE'] ;?>' /></td>
										<?php } ?>
									</tr>
									<?php  if( $product['STATUS'] >= 5 ){ ?>
									<tr class="check-purchase-tr">
										<th>合格货品数量：</th>
										<td colspan=3><input id="qualifiedProductsNum"  data-validator="required"  type="text" value='<?php echo $product['QUALIFIED_PRODUCTS_NUM'] ;?>' /></td>
									</tr>
									<tr class="check-purchase-tr">
										<th>验收说明：</th>
										<td colspan=3>
										<textarea style="width:500px;height:80px;" id="checkMemo"><?php echo $product['CHECK_MEMO'] ;?></textarea>
									</tr>
									<?php } ?>
									<tr>
										<th>样品：</th><td>
										<select id="sample">
											<option value="0" <?php if($sample == 0 ) echo 'selected' ;?>>无</option>
											<option value="1" <?php if($sample == 1 ) echo 'selected' ;?> >准备中</option>
											<option value="2" <?php if($sample == 2 ) echo 'selected' ;?>>有</option>
										</select>
										</td>
										<th>样品编码：</th><td>
										<input type="text" id="sample_code" value='<?php echo $sample_code ;?>' placeHolder="位置码+产品码组成，中间以下划线连接" />
										</td>
									</tr>
									<tr>
										<th>采购地区：</th><td colspan=3>
										<select id="area">
											<option value="china" <?php if($area == 'china' ) echo 'selected' ;?>>大陆</option>
											<option value="taiwan" <?php if($area == 'taiwan' ) echo 'selected' ;?> >台湾</option>
											<option value="american" <?php if($area == 'american' ) echo 'selected' ;?>>美国</option>
										</select>
										</td>
									</tr>
									<tr>
										<th>备注：</th><td colspan=3>
										<textarea style="width:500px;height:80px;" id="memo"><?php echo $product['MEMO'] ;?></textarea>
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
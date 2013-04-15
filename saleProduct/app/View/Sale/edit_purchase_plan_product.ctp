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
		
		$loginId = $user['LOGIN_ID'] ;
		
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
		$SqlUtils  = ClassRegistry::init("SqlUtils") ;
		//获取计划信息
		$plan = $SqlUtils->getObject("select * from sc_purchase_plan where id = '{@#planId#}'",array("planId"=>$planId)) ;
		//debug($plan) ; 
		
		$loginId 								= $user['LOGIN_ID'] ;
		$pp_edit 								= $security->hasPermission($loginId , 'pp_edit') ;
		$ppp_add_product				= $security->hasPermission($loginId , 'ppp_add_product') ;
		$ppp_export							= $security->hasPermission($loginId , 'ppp_export') ;
		$ppp_audit							= $security->hasPermission($loginId , 'ppp_audit') ;
		$ppp_setlimitprice				= $security->hasPermission($loginId , 'ppp_setlimitprice') ;
		//分配执行人，计划负责人等于当前用户
		$ppp_assign_executor			= $loginId == $plan['EXECUTOR'] || $security->hasPermission($loginId , 'ppp_assign_executor') ;
		$ppp_qc								= $security->hasPermission($loginId , 'ppp_qc') ;
		$ppp_inwarehouse				= $security->hasPermission($loginId , 'ppp_inwarehouse') ;
		$ppp_confirm 						= $security->hasPermission($loginId , 'ppp_confirm') ;
		//在编辑功能
		$reedit_pp_product				= $security->hasPermission($loginId , 'reedit_pp_product') ;
		
		$hasSetSupplierPermission = $security->hasPermission($loginId, 'SET_PRODUCT_SUPPLIER_FLAG') ;
		
		$status = $product['STATUS']; ;
		
		$hasViewRelListing =  $security->hasPermission($loginId , 'view_rp_rel_listing') ;
		
		//获取货品供应商询价
		$suppliers = $SqlUtils->exeSql("sql_purchase_plan_product_inquiry",array('planId'=>$planId,'sku'=>$sku)) ;
		
		$executor = $product['EXECUTOR'] ;
		$executorName = $product['EXECUTOR_NAME'] ;
		if( empty($executor) ){
			$executor = $product['PURCHASE_CHARGER'] ;
			$executorName = $product['PURCHASE_CHARGER_NAME'] ;
		}
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
		
		.tag-container li{
			float:left;
			list-style: none;
			margin:2px 5px;
		 	padding:2px;
		}
	</style>

   <script type="text/javascript">
    var sku = '<?php echo $sku;?>' ;
    var planId =  '<?php echo $planId;?>' ;
   
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

	var id = '<?php echo $id ;?>' ;
	var currentStatus = "<?php echo $product['STATUS'];?>" ;

	 var flowData = [
	        		{status:10,label:"编辑中",memo:true
	        			<?php if( $pp_edit ) { ?>
	        			,actions:[
									{label:"保存",action:function(){ AuditAction(10,"保存") }},
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
	        				{label:"审批不通过，继续编辑",action:function(){ AuditAction(10,"审批不通过，继续编辑") } },
	        				{label:"审批不通过，结束采购",action:function(){ AuditAction(25,"审批不通过，结束采购") } }
        				]
	        			<?php };?>
	        		},
	        		{status:30,label:"限价确认",memo:true
	        			<?php if( $ppp_setlimitprice) { ?>
	        			,actions:[
									{label:"保存",action:function(){ AuditAction(30,"保存") }},
		      	        			{label:"确认限价",action:function(){ AuditAction(40,"确认限价") } }
        				]
	        			<?php };?>
	        		},
	        		{status:40,label:"分配执行人",memo:true
	        			<?php if( $ppp_assign_executor ) { ?>
	        			,actions:[
									{label:"保存",action:function(){ AuditAction(40,"保存") }},
		      	        			{label:"分配采购执行人",action:function(){ AuditAction(45,"分配采购执行人") } }
        				]
	        			<?php };?>
	        		},
	        		{status:45,label:"采购执行",memo:true
	        			<?php if( $loginId == $product['EXECUTOR'] ) { ?>
	        			,actions:[
									{label:"保存",action:function(){ AuditAction(45,"保存") }},
		      	        			{label:"采购执行",action:function(){ AuditAction(50,"采购执行") } }
        				]
	        			<?php };?>
	        		},{status:50,label:"QC验货",memo:true
	        			<?php if( $ppp_qc) { ?>
	        			,actions:[
									{label:"保存",action:function(){ AuditAction(50,"保存") }},
		      	        			{label:"验货完成",action:function(){ AuditAction(60,"验货完成") } }
        				]
	        			<?php };?>
	        		},{status:60,label:"货品入库",memo:true
	        			<?php if( $ppp_inwarehouse) { ?>
	        			,actions:[
									{label:"保存",action:function(){ AuditAction(60,"保存") }},
		      	        			{label:"入库确认",action:function(){ AuditAction(70,"入库确认") } }
        				]
	        			<?php };?>
	        		},
	        		{status:70,label:"采购确认",memo:true
	        			<?php if( $ppp_confirm) { ?>
	        			,actions:[
									{label:"保存",action:function(){ AuditAction(70,"保存") }},
		      	        			{label:"确认采购",action:function(){ AuditAction(80,"采购确认") } }]
	        			<?php };?>
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
											<input type="hidden"   id="executor"  class="40-input input"
											value="<?php echo $executor;?>"/>
											<input type="text"  class="40-input input span2"  id="executorName"  readonly
													<?php echo $status>=40?"data-validator='required'":"" ?>
													value="<?php echo $executorName;?>"/>
											<button class="40-input input btn btn-charger">选择</button>
										</td>
									</tr>
									<tr>
										<th>货品：</th><td colspan=3><a class="product-realsku"  sku="<?php echo $sku ;?>"  href="#"><?php echo $sku ;?></a>（<?php echo $title ;?>）</td>
									</tr>
									<tr>
										<th><button class="btn btn-tags 10-input  input no-disabled">添加</button>标签：</th>
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
										<td><input id="plan_num"   class="10-input input"  data-validator="required"    type="text" value='<?php echo $plan_num ;?>' /></td>
										<th>采购限价：</th>
										<td><input id="limit_price"   class="30-input input"   type="text" 
													 <?php echo $status>=30?"data-validator='required'":"" ?>
													 value='<?php echo $product['LIMIT_PRICE'];?>' /></td>
									</tr>
									<tr class="check-purchase-tr">
										<th>合格数量：</th>
										<td><input id="qualifiedProductsNum" class="50-input input"   type="text" 
													<?php echo $status>=50?"data-validator='required'":"" ?>
													value='<?php echo $product['QUALIFIED_PRODUCTS_NUM'] ;?>' /></td>
										<th>不合格数量：</th>
										<td><input id="badProductsNum" class="50-input input"  type="text"
													<?php echo $status>=50?"data-validator='required'":"" ?>
													value='<?php echo $product['BAD_PRODUCTS_NUM'] ;?>' /></td>
									</tr>
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
												if( $suppli['SUP_ID'] == $providor ){
													$temp = "selected" ;
												}
												echo "<option $temp value='".$suppli['SUP_ID']."'>".$suppli['NAME']."</option>" ;
											}
										?>
										</select> 
										<button sku="<?php echo $sku ;?>" class="btn edit_supplier 45-input  input">编辑</button>
										</td>
										<th>供应商报价：</th>
										<td><input id="quote_price"   class="45-input input"   type="text" 
													 <?php echo $status>=45?"data-validator='required'":"" ?>
													 value='<?php echo $quote_price ;?>' /></td>
										</td>
									</tr>
									<tr>
										<th>支付方式：</th>
										<td colspan="3" >
											<input id="payType" class="45-input input"  type="text"
													<?php echo $status>=45?"data-validator='required'":"" ?>
													value='<?php echo $product['PAY_TYPE'] ;?>' />
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
												if( $suppli['SUP_ID'] == $product['REAL_PROVIDOR'] ){
													$temp = "selected" ;
												}
												echo "<option $temp value='".$suppli['SUP_ID']."'>".$suppli['NAME']."</option>" ;
											}
										?>
										</select> 
										<button sku="<?php echo $sku ;?>" class="btn edit_supplier  70-input  input">编辑</button>
										</td>
										<th>入库时间：</th>
										<td><input id="warehouseTime" class="60-input input"   type="text"  data-widget="calendar"
													<?php echo $status>=60?"data-validator='required'":"" ?>
													value='<?php echo $product['WAREHOUSE_TIME'] ;?>' /></td>
									</tr>
									<tr class="check-purchase-tr">	
										<th>实际采购时间：</th>
										<td><input id="realPurchaseDate"  data-widget="calendar"  type="text"   class="70-input input" 
											<?php echo $status>=70?"data-validator='required'":"" ?>
											value='<?php echo $product['REAL_PURCHASE_DATE'] ;?>' /></td>
										<th>实际采购价：</th>
										<td><input id="realQuotePrice"   type="text"   class="70-input input" 
											<?php echo $status>=70?"data-validator='required'":"" ?>
											value='<?php echo $product['REAL_QUOTE_PRICE'] ;?>' /></td>
									</tr>
									<!-- 
									<tr class="check-purchase-tr">
										<th>验收说明：</th>
										<td colspan=3>
										<textarea class="50-input input"  style="width:500px;height:80px;" id="checkMemo"><?php echo $product['CHECK_MEMO'] ;?></textarea>
										</td>
									</tr>
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
									 -->
									<tr>
										<th>采购地区：</th><td colspan=3>
										<select id="area"  class="45-input input" >
											<option value="china" <?php if($area == 'china' ) echo 'selected' ;?>>大陆</option>
											<option value="taiwan" <?php if($area == 'taiwan' ) echo 'selected' ;?> >台湾</option>
											<option value="american" <?php if($area == 'american' ) echo 'selected' ;?>>美国</option>
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
			
			<?php  if( $status>=45  ){ ?>
			<div id="supplier-tab" class="ui-tabs-panel" style="height: 100px; display: block; margin:5px;">
				<?php if( $status ==45  ){  ?>
				<button class="supplier btn">添加产品供应商</button>
				<button class="supplier-select btn">选择产品供应商</button>
				<?php } ?>
				<table class="table table-bordered">
					<tr>
						<th>操作</th>
						<th>名称</th>
						<th>产品重量</th>
						<th>生产周期</th>
						<th>包装方式</th>
						<th>付款方式</th>
						<th>产品尺寸</th>
						<th>包装尺寸</th>
						<th>报价1</th>
						<th>报价2</th>
						<th>报价3</th>
						<th></th>
					</tr>
					<?php foreach($suppliers as $supplier){
						$supplier = $SqlUtils->formatObject($supplier) ;
						//debug($supplier) ;
						$urls = "" ;
						if( $supplier['URL'] != '' ){
							if( $supplier['IMAGE'] != "" ){
								$urls = "	<a href='".$supplier['URL']."' target='_blank'>
									<img src='/".$fileContextPath."/".$supplier['IMAGE']."' style='width:80px;height:50px;'>
								</a>" ;
							}else{
								$urls = "	<a href='".$supplier['URL']."' target='_blank'>产品网址</a>" ;
							}
						}else if($supplier['IMAGE'] != ""){
							$urls = "<img src='/".$fileContextPath."/".$supplier['IMAGE']."' style='width:80px;height:50px;'>" ;
						}
						
						$isUsed = "" ;
						if( $hasSetSupplierPermission && !empty($supplier['ID']) ){
							$isUsed = $supplier['IS_USED'] == 1?"(已采用)":"<button class='btn used' supplierId='".$supplier['SUP_ID']."'>采用</button>" ;
						}
						
						$usedClz = $supplier['IS_USED'] == 1?"used-clz":"" ;
						echo "<tr class='$usedClz'>
							<td rowspan=2 ><a href='#' class='update-supplier' supplierId='".$supplier['SUP_ID']."' >修改询价</a></td>
							<td>
							<a href='#' supplier-id='".$supplier['SUP_ID']."'>".$supplier['NAME']."</a>
							$isUsed
							</td>
							<td>".$supplier['WEIGHT']."</td>
							<td>".$supplier['CYCLE']."</td>
							<td>".$supplier['PACKAGE']."</td>
							<td>".$supplier['PAYMENT']."</td>
							<td>".$supplier['PRODUCT_SIZE']."</td>
							<td>".$supplier['PACKAGE_SIZE']."</td>
							<td>".$supplier['NUM1']."/".$supplier['OFFER1']."</td>
							<td>".$supplier['NUM2']."/".$supplier['OFFER2']."</td>
							<td>".$supplier['NUM3']."/".$supplier['OFFER3']."</td>
							<td rowspan=2>
							  $urls
							</td>
						</tr><tr class='$usedClz'>
							<td colspan=10>".$supplier['MEMO']."
							</td>
							
						</tr> " ;
					}?>
					
				</table>
			</div>
			<?php } ?>
		</div>
	</div>
</body>

</html>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
   <?php echo $this->Html->charset(); ?>
    <title>入库单计划编辑</title>
    <meta http-equiv="pragma" content="no-cache"/>
	<meta http-equiv="cache-control" content="no-cache"/>

   <?php
   include_once ('config/config.php');
   
		echo $this->Html->meta('icon');
		echo $this->Html->css('../js/validator/jquery.validation');
		echo $this->Html->css('../js/listselectdialog/jquery.listselectdialog');
		echo $this->Html->css('default/style');
		echo $this->Html->css('../js/grid/jquery.llygrid');

		echo $this->Html->script('jquery');
		echo $this->Html->script('common');
		echo $this->Html->script('jquery.json');
		echo $this->Html->script('grid/jquery.llygrid');
		echo $this->Html->script('dialog/jquery.dialog');
		echo $this->Html->script('grid/query');
		echo $this->Html->script('validator/jquery.validation');	
		echo $this->Html->script('listselectdialog/jquery.listselectdialog');
		echo $this->Html->script('modules/warehouse/ram/addRma');
		echo $this->Html->script('calendar/WdatePicker');
	
		$result = null ;
		
		$orderId = $params['arg1'] ;
		$ramId   = $params['arg2'] ;
		$SqlUtils  = ClassRegistry::init("SqlUtils") ;
		$orders = null ;
		if(!empty($orderId)){
			$orders = $SqlUtils->exeSql("sql_order_list",array('orderId'=>$orderId) ) ;
		}
		
		$rma = null ;
		if(!empty($ramId)){
			$rma = $SqlUtils->getObject("sql_ram_event_getById",array('id'=>$ramId) ) ;
		}
		
		if($orderId == 'bad'){//残品入库
			
		}
		
		
	?>
	
</head>

<body class="container-popup">
	<!-- apply 主场景 -->
	<div class="apply-page">
		<!-- 页面标题 -->
		<div class="page-title">
			<h2>入库单信息</h2>
		</div>
		<div class="container-fluid">
	        <form id="personForm" action="<?php echo $contextPath;?>/page/model/Warehouse.Ram.doSaveRam"
	          								method="post" data-widget="validator" target="form-target"
	         								enctype="multipart/form-data" class="form-horizontal" >
	         	
	         	<button type="button" class="btn btn-primary btn-save" style="position:absolute;top:5px;right:20px;">保存</button>							
	         								
	        	<input type="hidden" name="id" value="<?php echo $result['ID'];?>"/>
	        	<input type="hidden" name="ramId" value="<?php echo $ramId;?>"/>
				<!-- panel 头部内容  此场景下是隐藏的-->
				<div class="panel apply-panel">
					<!-- panel 中间内容-->
					<div class="panel-content">
						<!-- 数据列表样式 -->
						<table class="form-table " >
							<tbody>										   
								<tr>
									<th> 货品：<br/>
									<?php if( !empty($orders) ){ ?>
									<button class="btn btn-other">其他货品</button>
									<?php } ?>
									</th>
									<td colspan=3>
									<?php
										if( !empty($orders) ){
										?>
										<table class="select-product-table">
										<tr style="padding:0px;margin:0px;">
											<th style="padding:0px;text-align:center;">选择</th>
											<th style="padding:0px;text-align:center;">货品SKU</th>
											<th style="padding:0px;text-align:center;">货品名称</th>
											<th style="padding:0px;text-align:center;">货品图片</th>
											<th style="padding:0px;text-align:center;">购买数量</th>
										</tr>
										<?php
										if( !empty($orders) ){
											foreach( $orders as $order ){
												$order = $SqlUtils->formatObject($order) ;
												$imageUrl = $order['IMAGE_URL'] ;
												$imageUrl = str_replace("%" , "%25",$imageUrl) ;
												?>
												<tr style="padding:0px;margin:0px;">
													<td style="text-align:center;">
													<input type="radio" data-validator="required" name="realProductId" value="<?php echo $order['REAL_ID']?>"></td>
													<td key="realSku"><?php echo $order['REAL_SKU']?></td>
													<td ><?php echo $order['REAL_NAME']?></td>
													<td ><?php echo "<img style='width:25px;height:25px;' src='/".$fileContextPath."/".$imageUrl."'>"?></td>
													<td ><?php echo $order['QUANTITY_TO_SHIP']?></td>
												</tr>
												<?php
												//debug($order);
											}
										}
										?>
									</table>
										<?php  }?>
										<span class="select-product hide">
											<input type="hidden" id="realProductId"  name="realProductId" 
											value=""/>
											<input type="text" id="realProductName" name="realProductName" 
											value=""/>
											<button class="btn btn-select-product">选择</button>
										</span>
									</td>
								</tr>
								<tr>
									<th>RMA编码：</th>
									<td colspan=3>
										<input type="text" class="alert" data-validator="required" name="rmaCode" value="<?php echo $rma['CODE'];?>"/>
									</td>
								</tr>
								<tr>
									<th>质量：</th>
									<td colspan=3>
										良品&nbsp;<input type="radio" 
										<?php echo $orderId=='good'?"checked":"" ?>
										<?php echo $orderId=='bad'?"disabled":"" ?>
										data-validator="required" name="quality" value="good"/>
										&nbsp;&nbsp;&nbsp;&nbsp;
										残品&nbsp;<input type="radio"
										<?php echo $orderId=='bad'?"checked":"" ?>
										<?php echo $orderId=='good'?"disabled":"" ?>
										 data-validator="required" name="quality" value="bad"/>
									</td>
								</tr>
								<tr>
									<th>数量：</th>
									<td colspan=3>
										<input type="text" class="alert-danger" data-validator="required" name="quantity" value=""/>
									</td>
								</tr>
								<tr>
									<th>货品图片：</th>
									<td colspan=3>
										<input type="file"  name="image" value=""/>
									</td>
								</tr>
								<tr>
									<th>目标仓库：</th>
									<td colspan=3>
									<input data-validator="required" type="hidden" id="warehouseId"  name="warehouseId"
										value=""/>
									<input type="text" data-validator="required" id="warehouseName" name="warehouseName" readonly
										value=""/>
									<button class="btn btn-warehouse">选择</button>
									</td>
								</tr>
								<tr>
									<th>备注：</th><td  colspan=3>
										<textarea name="memo"  style="width:90%;height:50px;"><?php echo $result['MEMO'];?></textarea>
									</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			</form>
		</div>
	</div>
	<div class="grid-content-rma" id="tab-rma" style="margin-top:5px;zoom:1;"></div>
	<iframe style="width:0; height:0; border:0;display:none;" name="form-target"></iframe>
</body>
</html>
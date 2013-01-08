<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
   <?php echo $this->Html->charset(); ?>
    <title>入库单计划编辑</title>
    <meta http-equiv="pragma" content="no-cache"/>
	<meta http-equiv="cache-control" content="no-cache"/>

   <?php
		echo $this->Html->meta('icon');
		echo $this->Html->css('../js/validator/jquery.validation');
		echo $this->Html->css('../js/listselectdialog/jquery.listselectdialog');
		echo $this->Html->css('default/style');

		echo $this->Html->script('jquery');
		echo $this->Html->script('common');
		echo $this->Html->script('jquery.json');
		echo $this->Html->script('grid/jquery.llygrid');
		echo $this->Html->script('dialog/jquery.dialog');
		echo $this->Html->script('grid/query');
		echo $this->Html->script('validator/jquery.validation');	
		echo $this->Html->script('listselectdialog/jquery.listselectdialog');
		echo $this->Html->script('modules/warehouse/disk/edit');
		echo $this->Html->script('calendar/WdatePicker');
		
		$SqlUtils  = ClassRegistry::init("SqlUtils") ;
		$security  = ClassRegistry::init("Security") ;
		
		
		
		$loginId   = $user['LOGIN_ID'] ;
		
		$result = null ;
		$diskId = $params['arg1'] ;
		if(!empty($diskId)){
			
			$result = $SqlUtils->getObject("sql_warehouse_disk_lists",array('id'=>$diskId) ) ;
		
		}
	?>
	
	<script>
		var diskId = '<?php echo $diskId ; ?>';
   </script>
</head>

<body class="container-popup">
	<!-- apply 主场景 -->
	<div class="apply-page">
		<div class="container-fluid">

	        <form id="personForm" action="#" data-widget="validator" class="form-horizontal" >
	        <input type="hidden" id="id" value="<?php echo $result['ID'];?>"/> 
				<!-- panel 头部内容  此场景下是隐藏的-->
				<div class="panel apply-panel">
				<?php
					$status = $result['STATUS'] ;
					if( $status == 2 ){//结束盘点
						//nothing
					}else{
						
						if( $security->hasPermission($loginId , 'WAREHOUSE$DODISK') 
							&& ( $status == '' || $status == 0 || $status==3 ) ){//盘点库存权限
				 		?>		
		                    <div class="panel-foot">
								<div class="form-actions" style="padding:5px;">
									<button type="button" class="btn btn-primary btn-save">保&nbsp;存</button>
									<?php
										if( empty($result) ){
											//nothing
										}else{
									 ?>	
									<button type="button" class="btn btn-primary btn-select-product">选择盘点货品</button>
									&nbsp;&nbsp;&nbsp;&nbsp;
									<button type="button" class="btn btn-danger btn-end">结束盘点提交审批</button>
									<?php } ?>
								</div>
							</div>
						<?php 
						}
						
						if( $security->hasPermission($loginId , 'WAREHOUSE$DOAPPLY')
							&& $status == 1  ){//审批库存权限
						?>
							<div class="panel-foot">
								<div class="form-actions" style="padding:5px;">
									<button type="button" class="btn btn-primary btn-pass">审批通过</button>
									<button type="button" class="btn btn-primary btn-nopass">审批不通过</button>
									
								</div>
							</div>	
						<?php
						}
					} ?>
					<!-- panel 中间内容-->
					<div class="panel-content">
						<!-- 数据列表样式 -->
						<table class="form-table " >
							<caption>基本信息</caption>
							<tbody>										   
								<tr>
									<th>盘点日期：</th>
									<td>
									<?php
										if(!empty($result['DISK_TIME'])){
											echo $result['DISK_TIME'] ;
										}else{
										?>
									<input data-validator="required" type="text" id="diskTime"
										value="<?php echo $result['DISK_TIME'];?>" data-widget="calendar"/>
									<?php	
										}
									?>
									</td>
									<th>目标仓库：</th>
									<td>
									<?php
										if(!empty($result['WAREHOUSE_NAME'])){
											echo $result['WAREHOUSE_NAME'] ;
										}else{
										?>
									<input data-validator="required" type="hidden" id="warehouseId" 
										value="<?php echo $result['WAREHOUSE_ID'];?>"/>
									<input type="text" data-validator="required" id="warehouseName" readonly
										value="<?php echo $result['WAREHOUSE_NAME'];?>" class="span2"/>
									<button class="btn btn-warehouse">选择</button>
									<?php	
										}
									?>
									</td>
								</tr>
								<tr>
									<th>盘点单号：</th><td><input data-validator="required" type="text" id="diskNo"
										value="<?php echo $result['DISK_NO'];?>"/></td>
								
									<th>经办人：</th><td><input data-validator="required" type="text" id="processor"
										value="<?php echo $result['PROCESSOR'];?>"/></td>
								</tr>
								
								<tr>
									<th>备注：</th><td  colspan=3>
										<textarea name="memo"  style="width:90%;height:40px;"><?php echo $result['MEMO'];?></textarea>
									</td>
								</tr>
							</tbody>
						</table>
					</div>
			<?php	if(!empty($diskId)){	?>
					<table class="table table-bordered table-stripped edit-table">
						<tr style="background:#CCC;">
							<th style="width:5%;">图片</th>
							<th style="width:12%;">货品SKU</th>
							<th style="width:18%;">货品名称</th>
							<th style="width:8%;">账面库存</th>
							<th style="width:7%;">实际库存</th>
							<th style="width:7%;">盈</th>
							<th style="width:7%;">亏</th>
							<th>备注</th>
						</tr>
						<?php
						$deskDetails = $SqlUtils->exeSql("sql_warehouse_disk_details",array('id'=>$diskId) ) ;
						
						if( !empty($deskDetails) ){
							foreach($deskDetails as $detail){
								$product = $SqlUtils->formatObject($detail) ;
								$imgUrl = '/saleProduct/'.$product['IMAGE_URL'] ;
								?>
								<tr class="data-row">
									<th style="display:none;">
										<input type="hidden" name="id" value="<?php echo $product['ID'] ?>"/>
										<input type="hidden" name="paperNum" value="<?php echo $product['PAPER_NUM'] ?>"/>
									</th>
									<th><img style="width:45px;height:45px;" src="<?php echo $imgUrl;?>"/></th>
									<th><?php echo $product['REAL_SKU'] ?></th>
									<th><?php echo $product['NAME'] ?></th>
									<th><?php echo $product['PAPER_NUM'] ?></th>
									<th><input type='text' name="realNum" class="span1" value="<?php echo $product['REAL_NUM'] ?>"/></th>
									<th key="gainNum"><?php echo $product['GAIN_NUM'] ?></th>
									<th key="lossNum"><?php echo $product['LOSS_NUM'] ?></th>
									<th><textarea name="memo"><?php echo $product['MEMO'] ?></textarea></th>
								</tr>
								<?php
							}
						}
						?>
					</table>
			<?php	}	?>
				</div>
			</form>
		</div>
	</div>
</body>
</html>
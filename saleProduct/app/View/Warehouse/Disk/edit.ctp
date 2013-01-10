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
		$planId = null ;
		if(!empty($diskId)){
			$result = $SqlUtils->getObject("sql_warehouse_disk_lists",array('id'=>$diskId) ) ;
			$planId = $result['PLAN_ID'] ;
		}
		
		$defaultCode = "DA-".date("Ymd") ;
	?>
	
	<style type="text/css">
		.audit-same{
			
		}
		
		.audit-nosame{
			background:pink;
		}
		
		.audit-agree{
			background:#B5F4B5;
		}
	</style>
	
	<script>
		var diskId = '<?php echo $diskId ; ?>';
		var planId = '<?php echo $planId ; ?>'||window.opener.currentPlan.ID;
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
					$count = $result['COUNT'] ;
					$passCount = $result['PASS_COUNT'] ;
					
					if( $status == 2 ){//结束盘点
						//nothing
					}else{
						
						if( $security->hasPermission($loginId , 'WAREHOUSE$DODISK') 
							&& ( $status == '' || $status == 0 || $status==3 )
							&& !($passCount == $count && $count > 0 )
							 ){//盘点库存权限
							
				 		?>		
		                    <div class="panel-foot">
								<div class="form-actions" style="padding:5px;">
									<button type="button" class="btn btn-primary btn-save">保&nbsp;存</button>
									<?php
										if( empty($result) ){
											//nothing 新建
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
						$hasAudit = $security->hasPermission($loginId , 'WAREHOUSE$DOAPPLY') ;
						if( $hasAudit && $status == 1  ){//审批库存权限
						?>
							<div class="panel-foot">
								<div class="form-actions" style="padding:5px;">
									<button type="button" class="btn btn-primary btn-audit-save">保存审批结果</button>
									<button type="button" class="btn btn-primary btn-audit-complete">审批完成</button>
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
									<th>计划名称：</th>
									<td>
									<script type="text/javascript">
										document.write( window.opener.currentPlan.NAME );
									</script>
									</td>
								</tr>
								<tr>
									<th>活动代码：</th><td><input data-validator="required" type="text" id="diskNo"
										value="<?php if( empty($result['DISK_NO']) ){
												echo $defaultCode ;
											   }else{
											   		echo $result['DISK_NO'];
											   }?>"/></td>
								
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
							<th style="width:30px;">&nbsp;</th>
						</tr>
						<?php
						$deskDetails = $SqlUtils->exeSql("sql_warehouse_disk_details",array('id'=>$diskId) ) ;
						
						if( !empty($deskDetails) ){
							foreach($deskDetails as $detail){
								$product = $SqlUtils->formatObject($detail) ;
								$imgUrl = '/saleProduct/'.$product['IMAGE_URL'] ;
								$id = $product['ID'] ;
								$paperNum = $product['PAPER_NUM']  ;//账面库存
								$realNum  = $product['REAL_NUM'] ;//实际库存
								$pstatus   = $product['STATUS'] ;
								
								$rowClass= $paperNum == $realNum ?"audit-same":"audit-nosame" ;
								$isChecked = $paperNum == $realNum ?"checked=checked":"" ;
								
								if($pstatus == 1){
									$rowClass = "audit-agree" ;
								}
								
								?>
								<tr class="data-row <?php echo $rowClass;?>">
									<th style="display:none;">
										<input type="hidden" name="id" value="<?php echo $product['ID'] ?>"/>
										<input type="hidden" name="paperNum" value="<?php echo $product['PAPER_NUM'] ?>"/>
									</th>
									<th><img style="width:25px;height:25px;" src="<?php echo $imgUrl;?>"/></th>
									<th><?php echo $product['REAL_SKU'] ?></th>
									<th><?php echo $product['NAME'] ?></th>
									<th><?php echo $product['PAPER_NUM'] ?></th>
									<th>
									    <?php if( ($status == '' || $status == 0 || $status==3) && $pstatus!=1 ){
									    ?>
									    	<input type="text" name="realNum" style="width:50px;" value="<?php echo $product['REAL_NUM'] ?>"/>
									    <?php
									    }else{
									    	echo $product['REAL_NUM'] ;
									    }?>
										
									</th>
									<th key="gainNum"><?php echo $product['GAIN_NUM'] ?></th>
									<th key="lossNum"><?php echo $product['LOSS_NUM'] ?></th>
									<th style="padding:2px;">
										<?php if($pstatus == 1){?>
											<?php echo $product['MEMO'] ?>
										<?php }else{?>
										<textarea name="memo" style="height:35px;margin:0px;"><?php echo $product['MEMO'] ?></textarea>
										<?php } ?>
									</th>
									<th>
										<?php if($pstatus == 1){?>
											通过
										<?php }else if($hasAudit && $status == 1 ){?>
										<input type="checkbox" name="isPass" <?php echo $isChecked;?> value="<?php echo $id;?>"/>
										<?php } ?>
									</th>
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
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
   <?php echo $this->Html->charset(); ?>
    <title>RAM事件编辑</title>
    <meta http-equiv="pragma" content="no-cache"/>
	<meta http-equiv="cache-control" content="no-cache"/>

   <?php
		echo $this->Html->meta('icon');
		echo $this->Html->css('../js/validator/jquery.validation');
		echo $this->Html->css('../js/listselectdialog/jquery.listselectdialog');
		echo $this->Html->css('default/style');
		echo $this->Html->css('../js/tab/jquery.ui.tabs');
		echo $this->Html->css('../js/grid/jquery.llygrid');

		echo $this->Html->script('jquery');
		echo $this->Html->script('common');
		echo $this->Html->script('jquery.json');
		echo $this->Html->script('jquery.ui');
		echo $this->Html->script('grid/jquery.llygrid');
		echo $this->Html->script('dialog/jquery.dialog');
		echo $this->Html->script('grid/query');
		echo $this->Html->script('validator/jquery.validation');	
		echo $this->Html->script('tab/jquery.ui.tabs');
		echo $this->Html->script('listselectdialog/jquery.listselectdialog');
		echo $this->Html->script('modules/warehouse/ram/editEvent');
		echo $this->Html->script('calendar/WdatePicker');
		
		$SqlUtils  = ClassRegistry::init("SqlUtils") ;
		$security  = ClassRegistry::init("Security") ;
		
		$loginId   = $user['LOGIN_ID'] ;
		
		$result = null ;
		$eventId = $params['arg1'] ;
		if(!empty($eventId)){
			$result = $SqlUtils->getObject("sql_ram_event_getById",array('id'=>$eventId) ) ;
		}
		
		$orderId = $result['ORDER_ID'] ;
		if(empty($result)){
			$orderId = $eventId ;
			$result = $SqlUtils->getObject("sql_ram_event_getByOrderId",array('orderId'=>$eventId) ) ;
		}
		
		//1、提交审批 2、审批通过  3、重新编辑 
		$defaultCode = "RMA-".date("Ymd")."-".date("His") ;
		
		$status = $result["STATUS"] ;
		
		//解决策略 ,确认收货完成，触动订单重发
		$policyCode = $result['POLICY_CODE'];
		if(!empty($policyCode)){
			$policy = $SqlUtils->getObject("sql_ram_options_getByCode",array('code'=>$policyCode) ) ;
		}
		
		$isInit  = $status == '' || $status == '0' ;
		$isAudit = $status == '1' ;
		$isAuditPass = $status== '2' ;
		$isComplete = $status == '3' ;
		
		
		$orders = null ;
		$order = null ;
		if(!empty($orderId)){
			$orders = $SqlUtils->exeSql("sql_order_list",array('orderId'=>$orderId) ) ;
			$order = $SqlUtils->formatObject($orders[0]) ;
		}
	?>
</head>

<body class="container-popup">
	<!-- apply 主场景 -->
	<div class="apply-page">
		<div class="container-fluid">

	        <form id="personForm" action="#" data-widget="validator" class="form-horizontal" >
	        	<input type="hidden" id="id" value="<?php echo $result['ID'];?>"/> 
	        	<input type="hidden" id="status" value="<?php echo $result['STATUS'];?>"/> 
				<!-- panel 头部内容  此场景下是隐藏的-->
				<div class="panel apply-panel">
					
					<!-- panel 中间内容-->
					<div class="panel-content">
						<!-- 数据列表样式 -->
						<table class="form-table " >
							<caption>
							RMA事件编辑
							<div style="text-align:right;float:right;margin-top:1px;">
							<?php
								if( $isInit ){
								?>
								<button type="button" class="btn btn-primary btn-save">保&nbsp;存</button>
								<button type="button" class="btn btn-primary btn-save-audit">保存并提交审批</button>
								<?php
								}else if($isAudit){//审批
								?>
								<button type="button" class="btn btn-primary btn-aduitPass">审批通过</button>
								<button type="button" class="btn btn-primary btn-aduitNotPass">审批不通过</button>
								<?php
								}else if($isAuditPass){//审批
								?>
								<button type="button" class="btn btn-primary btn-save-track">保存轨迹</button>
								<button type="button" class="btn btn-primary btn-finish">完成处理</button>
								<?php
								}else if($isComplete){//审批
								}
							?>
							</div>
							
							</caption>
							<tbody>	
								<tr>
									<th>RMA编码：</th><td  colspan=3><input data-validator="required"
										<?php echo !$isInit?"readOnly":"" ;?>
										 type="text" id="code"
										value="<?php  
										if(empty($result['CODE'])){
											echo $defaultCode ;
										}else echo $result['CODE'];?>"/></td>
								</tr>
								<tr>
									<th>订单ID：</th><td><input data-validator="required" 
										type="text" id="orderId"
										<?php echo !$isInit?"readOnly":"" ;?>
										value="<?php if(empty($result['ORDER_ID'])){
											echo $order['ORDER_ID'] ;
										}else{
											echo $result['ORDER_ID'] ;
										};?>"/>
										<button class="btn btn-order"
										<?php echo !$isInit?"style='display:none;'":"" ;?>
										>选择</button>
									</td>
									<th>订单系统货号：</th><td><input data-validator="required" type="text" id="orderNo"
										<?php echo !$isInit?"readOnly":"" ;?>
										value="<?php if(empty($result['ORDER_NO'])){
											echo $order['ORDER_NUMBER'] ;
										}else{
											echo $result['ORDER_NO'] ;
										};?>"/></td>
								</tr>
								
								<tr>
									<th>RAM原因：</th>
									<td>
										<select name="causeCode" data-validator="required" <?php echo !$isInit?"disabled":"" ;?>>
											<option value="">请选择</option>
										<?php
											$causeCode = $result['CAUSE_CODE'];
											$causes = $SqlUtils->exeSql("sql_ram_options_getByType",array('type'=>'cause')) ;
											
											foreach( $causes as $cause ){
												$cause = $SqlUtils->formatObject($cause) ;
												$selected = $cause['CODE'] == $causeCode?"selected":"" ;
												echo "<option $selected  value='".$cause['CODE']."'>".$cause['NAME']."</option>" ;
											}
										?>
											<option value="other">其他原因</option>
										</select>
									</td>
									<th>RAM决策：</th>
									<td>
										<select name="policyCode" data-validator="required" <?php echo !$isInit?"disabled":"" ;?>>
											<option value="">请选择</option>
										<?php
											
											$causes = $SqlUtils->exeSql("sql_ram_options_getByType",array('type'=>'policy')) ;
											$selectedPolicy = null ;
											foreach( $causes as $cause ){
												$cause = $SqlUtils->formatObject($cause) ;
												$selected = $cause['CODE'] == $policyCode?"selected":"" ;
												
												if( $cause['CODE'] == $policyCode ){
													$selectedPolicy = $cause ;
												}
												
												echo "<option $selected  value='".$cause['CODE']."'>".$cause['NAME']."</option>" ;
											}
										?>
										</select>
									</td>
								</tr>
								
								<tr>
									<th>备注：</th><td  colspan=3>
										<textarea name="memo"  
										<?php echo !$isInit?"readOnly":"" ;?>
										style="width:90%;height:40px;"><?php echo $result['MEMO'];?></textarea>
									</td>
								</tr>
								<?php if($isAudit){?>
								<tr>
									<th>审批意见：</th><td  colspan=3>
										<textarea name="trackMemo" 
										style="width:90%;height:40px;"></textarea>
									</td>
								</tr>
								<?php }?>
								<?php if($isAuditPass){?>
								<tr>
									<th>跟踪意见：</th><td  colspan=3>
										<textarea name="trackMemo" 
										style="width:90%;height:40px;"></textarea>
									</td>
								</tr>
								<?php if( $selectedPolicy['IS_REFUND'] == 1 ){?>
								<tr>
									<th>是否已经退款：</th><td  colspan=3>
										是<input type="radio" name="refundStatus"
										<?php  echo $result['REFUND_STATUS'] == 1?'checked':"" ?>
										<?php  echo $result['REFUND_STATUS'] == 1?' disabled':"" ?>
										 value="1" />&nbsp;&nbsp;&nbsp;&nbsp;
										否<input type="radio" name="refundStatus"
											<?php  echo $result['REFUND_STATUS'] == 0?'checked':"" ?>
											<?php  echo $result['REFUND_STATUS'] == 1?' disabled':"" ?>
										 value="0" />&nbsp;&nbsp;&nbsp;&nbsp;
										 
										 <?php  echo $result['REFUND_STATUS'] == 1?'<span class="alert alert-info">退款金额:'.$result['REFUND_VALUE'].'</span>' :"" ?>
										 
										 <span class="refund-action" style="display:none;">
										 <input type="text" name="refundValue" placeHolder="请输入退款金额"/>
										 <button class="btn btn-danger refundConfirm">确认</button>
										 </span>
									</td>
								</tr>
								<?php }?>
								<tr>
									<th>是否收到退货：</th><td  colspan=3>
										是<input type="radio" name="isReceive"
										<?php  echo $result['IS_RECEIVE'] == 1?'checked':"" ?>
										<?php  echo $result['IS_RECEIVE'] == 1?' disabled':"" ?>
										 value="1" />&nbsp;&nbsp;&nbsp;&nbsp;
										否<input type="radio" name="isReceive"
											<?php  echo $result['IS_RECEIVE'] == 0?'checked':"" ?>
											<?php  echo $result['IS_RECEIVE'] == 1?' disabled':"" ?>
										 value="0" />&nbsp;&nbsp;&nbsp;&nbsp;
										<?php if($result['IN_STATUS'] != 1 ){?>
										<button class="btn disabled ram-in" disabled>RMA入库</button>
										<button class="btn btn-success disabled ram-in complete" disabled>RMA完成入库</button>
										<?php }else{
											echo "<span class='alert alert-success'>入库完成</span>" ;
 										}?>
									</td>
								</tr>
								
								<?php }?>
								<?php if( !empty($orders) ){?>
								<tr>
								<td colspan=4 style="text-align:left;">
									<div class="row-fluid">
										<div class="span5">
											<table style="width:100%;">
											<tr style="padding:0px;margin:0px;">
												<?php 
												
												$email =  $order['BUYER_EMAIL'] ;
												$Customer = $SqlUtils->getObject("sql_saleuser_findByEmail",array("email"=>$email)) ;
												$clz = $Customer['STATUS'] == 'danger'?"alert-danger":"" ;
											?>
												<th style="padding:0px;text-align:center;">
														<div class="<?php echo $clz;?>" style="height:100%;font-weight:bold;">客户:
														<?php echo $email;?>
														</div>
												</th>
											</tr>
											<?php if($Customer['STATUS'] != 'danger'){?>
											<tr style="padding:0px;margin:0px;">
												<td style="padding-top:0px;padding-bottom:0px;">
													<button email="<?php echo $order['BUYER_EMAIL']?>" class="btn btn-danger btn-dangerUser">加入风险客户</button>
												</td>
											</tr>
											<?php }?>
											</table>
										</div>
										<div class="span7">
											<table style="width:100%;">
												<tr style="padding:0px;margin:0px;">
													<th style="padding:0px;text-align:center;">货品SKU</th>
													<th style="padding:0px;text-align:center;">货品名称</th>
													<th style="padding:0px;text-align:center;">货品图片</th>
													<th style="padding:0px;text-align:center;">购买数量</th>
												</tr>
												<?php
													foreach( $orders as $order ){
														$order = $SqlUtils->formatObject($order) ;
														$imageUrl = $order['IMAGE_URL'] ;
														$imageUrl = str_replace("%" , "%25",$imageUrl) ;
														?>
														<tr style="padding:0px;margin:0px;">
															<td style="padding-top:0px;padding-bottom:0px;"><?php echo $order['REAL_SKU']?></td>
															<td style="padding-top:0px;padding-bottom:0px;"><?php echo $order['REAL_NAME']?></td>
															<td style="padding-top:0px;padding-bottom:0px;"><?php echo "<img style='width:25px;height:25px;' src='/saleProduct/".$imageUrl."'>"?></td>
															<td style="padding-top:0px;padding-bottom:0px;"><?php echo $order['QUANTITY_TO_SHIP']?></td>
														</tr>
														<?php
														//debug($order);
													}
												?>
											</table>
										</div>
									</div>	
								</td>
								</tr>
								<?php } ?>
							</tbody>
						</table>
					</div>
			
				</div>
			</form>
		</div>
		<!--tab-->
		<div id="tabs-default"></div>
	
		<div class="grid-content-track" id="tab-track" style="margin-top:5px;"></div>
		
		<div class="grid-content-rma" id="tab-rma" style="margin-top:5px;zoom:1;"></div>
	
	</div>
</body>
</html>
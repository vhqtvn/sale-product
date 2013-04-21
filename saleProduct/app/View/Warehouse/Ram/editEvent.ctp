<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
   <?php echo $this->Html->charset(); ?>
    <title>RAM事件编辑</title>
    <meta http-equiv="pragma" content="no-cache"/>
	<meta http-equiv="cache-control" content="no-cache"/>

   <?php
   		include_once ('config/config.php');
   
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
		$orderId =$params['arg1'] ;
		
		$rmaEdit 	= $security->hasPermission($loginId , 'RMA_EDIT') ;
		$rmaAudit 	= $security->hasPermission($loginId , 'RMA_AUDIT') ;
		$rmaTagConfirm 	= $security->hasPermission($loginId , 'RMA_TAG_CONFIRM') ;
		$rmaBackConfirm 	= $security->hasPermission($loginId , 'RMA_BACK_CONFIRM') ;
		$rmaWhIn	= $security->hasPermission($loginId , 'RMA_WH_IN') ;
		$rmaRefund 	= $security->hasPermission($loginId , 'RMA_REFUND') ;
		$rmaResendConfig 	= $security->hasPermission($loginId , 'RMA_RESEND_CONFIG') ;
		$rmaResendConfirm 	= $security->hasPermission($loginId , 'RMA_RESEND_CONFIRM') ;
		$rmaRiskCustomer	= $security->hasPermission($loginId , 'RMA_RISK_CUSTOMER') ;
		$rmaForceFinish =  $security->hasPermission($loginId , 'RMA_FORCE_FINISH') ;
		
		$result  = null ;
		
		if( !empty($orderId) ){
			$result = $SqlUtils->getObject("sql_ram_event_getById",array('id'=>$orderId) ) ;
			if(empty($result)){
				$result = $SqlUtils->getObject("sql_ram_event_getByOrderId",array('orderId'=>$orderId) ) ;
			}else{
				$orderId = $result['ORDER_ID'] ;
			}
				
		}
		
		//1、提交审批 2、审批通过  3、重新编辑 
		$defaultCode = null ;
		if( empty($result['CODE']) ){
			$index = $SqlUtils->getMaxValue("rma" , null , 1) ;
			if( strlen($index) < 5 ){
				$len = 5-strlen($index) ;
				for($i=0 ;$i < $len ;$i++){
					$index = '0'.$index ;
				}
			}
			$defaultCode = "RMA-".date("ymd").'-'.$index ;
		}
		
		
		$status = $result["STATUS"] ;
		
		//解决策略 ,确认收货完成，触动订单重发
		$policyCode = $result['POLICY_CODE'];
		if(!empty($policyCode)){
			$policy = $SqlUtils->getObject("sql_ram_options_getByCode",array('code'=>$policyCode) ) ;
		}
		
		$isInit  = $status == '' || $status == '10' ;
		
		$order = null ;
		$orderItems = null ;
		if(!empty($orderId)){
			$order = $SqlUtils->getObject("sql_sc_order_list",array('orderId'=>$orderId) ) ;
			$orderItems = $SqlUtils->exeSql("sql_sc_order_item_list",array('orderId'=>$orderId) ) ;
		}
		
		//决策原因
		$policys = $SqlUtils->exeSql("sql_ram_options_getByType",array('type'=>'policy')) ;
		$selectedPolicy = null ;
		foreach( $policys as $cause ){
			$cause = $SqlUtils->formatObject($cause) ;
			if( $cause['CODE'] == $policyCode ){
				$selectedPolicy = $cause ;
			}
		}
		//'IS_RESEND' => '1',  重发
		//'IS_REFUND' => '1',  退款
		//'IS_BACK' => '1'  退货
		
	?>
	
	<script type="text/javascript">
		var currentStatus = '<?php echo $status;?>';
		var AuditAction = function(status ,statusLabel,fixParams){
			if( !$.validation.validate('#personForm').errorInfo ) {
				if(window.confirm("确认【"+statusLabel+"】吗？")){
					var json = $("#personForm").toJson() ;
					json = $.extend({},json,fixParams) ;
					var memo = "("+statusLabel+")" + ($(".memo").val()||"") ;
					json.trackMemo = memo ;
					json.status =status ;
					//return ;
					//保存基本信息
					$.dataservice("model:Warehouse.Ram.doFlow",json,function(result){
						<?php if( !empty($result['ID']) ){ ?>
							window.location.reload();
						<?php }else{
							echo 'window.close();' ;
						}?>
					});
				}
			}
		}

		function ResendConfig(status ,statusLabel,fixParams){
			if(window.confirm("确认【"+statusLabel+"】吗？")){
				//保存重发货设置
				var result = [] ;
				$("[name='rmaReship']").each(function(){
					var me = $(this) ;
					var rmaReship = $(this).val() ;
					var orderId   = $(this).attr("orderId") ;
					var orderItemId = $(this).attr("orderItemId") ;
					var params = {rmaReship:rmaReship,orderId:orderId,orderItemId:orderItemId} ;
					result.push(params) ;
				}) ;
				$.dataservice("model:Warehouse.Ram.saveReship",{
					result:$.json.encode(result),
					resendStatus:1,
					id:$("#id").val(),
					orderId:$("#orderId").val()
					},function(result){
						var json = $("#personForm").toJson() ;
						json = $.extend({},json,fixParams) ;
						var memo = "("+statusLabel+")" + ($(".memo").val()||"") ;
						json.trackMemo = memo ;
						json.status =status ;
						//return ;
						//保存基本信息
						$.dataservice("model:Warehouse.Ram.doFlow",json,function(result){
								window.location.reload();
						});
				});	
			}
		}
<?php if( !empty($result['ID']) ){ ?>
		var flowData = [] ;
		flowData.push( {status:10,label:"编辑中",memo:true ,actions:[ 
			  <?php if( $rmaForceFinish ){ ?>
			          {label:"强制结束",action:function(){ AuditAction('80',"强制结束") } },
			<?php }?>             
		<?php if( $rmaEdit ){ ?>
		        {label:"保存",action:function(){ AuditAction(10,"保存") } },
		        {label:"提交审批",action:function(){ AuditAction(20,"提交审批") } } 
		<?php }?>
		]} ) ;

		<?php 
			$nextStatus = $selectedPolicy['IS_REFUND'] == 1?60:($selectedPolicy['IS_RESEND'] == 1?70:80 ) ; 
			if( $selectedPolicy['IS_BACK'] == 1   ){
				$nextStatus = 30 ;
			}
		?>
		
		flowData.push( {status:20,label:"审批确认",memo:true ,actions:[ 
		<?php if( $rmaForceFinish ){ ?>
			     	{label:"强制结束",action:function(){ AuditAction('80',"强制结束") } },
		<?php }?>                                    
		<?php if( $rmaAudit ){ ?>
		 		  {label:"保存轨迹",action:function(){ AuditAction(20,"保存轨迹") } },
		          {label:"审批确认",action:function(){ AuditAction('<?php echo $nextStatus;?>',"审批确认") } },
		          {label:"审批不通过，继续编辑",action:function(){ AuditAction(10,"审批不通过，继续编辑") } }
		 <?php }?>
		]} ) ;
		
		<?php if( $selectedPolicy['IS_BACK'] == 1 ){//退货 ?>
			flowData.push( {status:30,label:"退货标签确认",memo:true ,actions:[ 
			<?php if( $rmaForceFinish ){ ?>
				 {label:"强制结束",action:function(){ AuditAction('80',"强制结束") } },
			<?php }?>   
			<?php if( $rmaTagConfirm ){ ?>
					   {label:"保存轨迹",action:function(){ AuditAction(30,"保存轨迹") } },
                       {label:"确认退货标签发送",action:function(){ AuditAction(40,"确认退货标签发送，等待收到退货") } } 
		 <?php }?>
              ]} ) ;
			flowData.push( {status:40,label:"退货确认",memo:true,actions:[
				                                              			<?php if( $rmaForceFinish ){ ?>
				                                              				 {label:"强制结束",action:function(){ AuditAction('80',"强制结束") } },
				                                              			<?php }?> 
			<?php if( $rmaBackConfirm ){ ?>
			   {label:"保存轨迹",action:function(){ AuditAction(40,"保存轨迹") } },
					{label:"确认收退货",action:function(){ AuditAction(50,"确认收到退货，等待入库",{isReceive:1}) } }
		 <?php }?>
			] } ) ;

			//下一步状态
			var nextStatus1 = <?php echo $selectedPolicy['IS_REFUND'] == 1?60:($selectedPolicy['IS_RESEND'] == 1?70:80 ) ; ?> ;
			var nextStatusText1 = "<?php echo $selectedPolicy['IS_REFUND'] == 1?'入库完成，等待退款！':($selectedPolicy['IS_RESEND'] == 1?'入库完成，等待重发！':'入库完成，结束！' ) ; ?>";
			
			flowData.push( {status:50,label:"退货入库",memo:true,actions:[ 
				                                              			<?php if( $rmaForceFinish ){ ?>
					                                       				 {label:"强制结束",action:function(){ AuditAction('80',"强制结束") } },
					                                       			<?php }?>
			<?php if( $rmaWhIn ){ ?>
			   {label:"保存轨迹",action:function(){ AuditAction(50,"保存轨迹") } },
				    {label:"确认入库完成",action:function(){ AuditAction( nextStatus1,nextStatusText1,{inStatus:1}) } }
		 <?php }?>
			] } ) ;
		<?php }; ?>
	
		<?php if( $selectedPolicy['IS_REFUND'] == 1 ){//退款 ?>
			var nextStatus = <?php echo  $selectedPolicy['IS_RESEND'] == 1?70:80   ; ?> ;
			var nextStatusText = "<?php echo  $selectedPolicy['IS_RESEND'] == 1?'退款完成，等待重发！':'退款完成，结束！'   ; ?>";
			
			flowData.push( {status:60 ,label:"退款",memo:true,actions:[ 
				                                             			<?php if( $rmaForceFinish ){ ?>
					                                       				 {label:"强制结束",action:function(){ AuditAction('80',"强制结束") } },
					                                       			<?php }?>
			<?php if( $rmaRefund ){ ?>
			   {label:"保存轨迹",action:function(){ AuditAction(60,"保存轨迹") } },
			            {label:"确认退款完成",action:function(){ AuditAction( nextStatus,nextStatusText,{refundStatus:1}) } }
		 <?php }?>
			]  } ) ;
		<?php }; ?>
	
		<?php if( $selectedPolicy['IS_RESEND'] == 1 ){//重发 ?>
			flowData.push( {status:70,label:"重发配置",memo:true,actions:[ 
				                                              			<?php if( $rmaForceFinish ){ ?>
					                                       				 {label:"强制结束",action:function(){ AuditAction('80',"强制结束") } },
					                                       			<?php }?>
			<?php if( $rmaResendConfig ){ ?>
			   {label:"保存轨迹",action:function(){ AuditAction(70,"保存轨迹") } },
				      {label:"确认重发配置完成",action:function(){ ResendConfig(75 ,"重发配置完成",{resendStatus:1}) } }
		 <?php }?>
			]  } ) ;
			flowData.push( {status:75,label:"确认重发",memo:true,actions:[ 
				                                              			<?php if( $rmaForceFinish ){ ?>
					                                       				 {label:"强制结束",action:function(){ AuditAction('80',"强制结束") } },
					                                       			<?php }?>
			<?php if( $rmaResendConfirm ){ ?>
			   {label:"保存轨迹",action:function(){ AuditAction(75,"保存轨迹") } },
			         {label:"确认重发完成",action:function(){ AuditAction(80 ,"确认重发完成，结束！") } }
		 <?php }?>
			]  } ) ;
		<?php }; ?>
	
		flowData.push({status:80,label:'结束'}) ;
	
		$(function(){
			var flow = new Flow() ;
			flow.init(".flow-bar center",flowData) ;
			flow.draw('<?php echo $status;?>') ;
		}) ;
<?php }?>			
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
	<?php if( empty($result['ID']) ){ ?>
	<center>
		<button class="btn btn-primary" onclick='AuditAction(10,"保存") '>保存</button>
		<button class="btn btn-primary" onclick='AuditAction(20,"保存并提交审批") '>保存并提交审批</button>
	</center>
	<?php } ?>
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
									<th>订单内部订单号：</th><td><input data-validator="required" type="text" id="orderNo"
										<?php echo !$isInit?"readOnly":"" ;?>
										value="<?php if(empty($result['ORDER_NO'])){
											echo $order['ORDER_NUMBER'] ;
										}else{
											echo $result['ORDER_NO'] ;
										};?>"/></td>
								</tr>
								
								<tr>
									<th>RMA原因：</th>
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
									<th>RMA决策：</th>
									<td>
										<select name="policyCode" data-validator="required" <?php echo !$isInit?"disabled":"" ;?>>
											<option value="">请选择</option>
										<?php
											
											$selectedPolicy = null ;
											foreach( $policys as $cause ){
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
								<?php if( !empty($result['ID']) ){ ?>
								<tr>
											<?php 
												$email =  $order['BUYER_EMAIL'] ;
												$Customer = $SqlUtils->getObject("sql_saleuser_findByEmail",array("email"=>$email)) ;
												$clz = $Customer['STATUS'] == 'danger'?"alert-danger":"" ;
											?>
												<th>客户：</th>
												<td colspan="3" >
													<?php echo $email;?>
											<?php if($Customer['STATUS'] != 'danger'  && $rmaRiskCustomer ){?>
													<button email="<?php echo $order['BUYER_EMAIL']?>" class="btn btn-danger btn-dangerUser">加入风险客户</button>
											<?php }?>
												</td>
								</tr>
								<?php }?>
								<tr>
									<th>备注：</th><td  colspan=3>
										<textarea name="memo"  
										<?php echo !$isInit?"readOnly":"" ;?>
										style="width:90%;height:40px;"><?php echo $result['MEMO'];?></textarea>
									</td>
								</tr>
							</table>
							<?php if( $selectedPolicy['IS_BACK'] == 1 && $status >=30  ){?>
								<table class="form-table " >	
									<caption>退货信息</caption>
									<tr>
										<th>是否收到退货：</th><td  colspan=3>
											<?php  if( $result['IS_RECEIVE'] == 1 ){ ?>
												<?php if($result['IN_STATUS'] != 1 ){?>
													<button class="btn  ram-in">RMA入库</button>
												<?php }else {
													echo "<span class='alert alert-success' style='padding:5px 10px;'>入库完成</span>" ;
												}?>
											<?php  }else{
												echo "<span class='alert alert-danger' style='padding:5px 10px;'>未收到退货</span>" ;
											}?>
										</td>
									</tr>
								</table>
							<?php }?>
							
							<?php if( $selectedPolicy['IS_REFUND'] == 1  && $status >=60 ){?>
							 <table class="form-table " >
								<caption>退款信息</caption>
									<tr>
										<th>是否已经退款：</th><td  colspan=3>
											<?php if( $result['REFUND_STATUS'] != 1 ){ ?>
											是<input type="radio" name="refundStatus"
											<?php if(!$rmaRefund) echo 'disabled';?>
											<?php  echo $result['REFUND_STATUS'] == 1?'checked':"" ?>
											<?php  echo $result['REFUND_STATUS'] == 1?' disabled':"" ?>
											 value="1" />&nbsp;&nbsp;&nbsp;&nbsp;
											否<input type="radio" name="refundStatus"
												<?php if(!$rmaRefund) echo 'disabled';?>
												<?php  echo $result['REFUND_STATUS'] == 0?'checked':"" ?>
												<?php  echo $result['REFUND_STATUS'] == 1?' disabled':"" ?>
											 value="0" />&nbsp;&nbsp;&nbsp;&nbsp;
											 <span class="refund-action" style="display:none;">
											 <input type="text" name="refundValue" <?php if(!$rmaRefund) echo 'disabled';?> placeHolder="请输入退款金额"/>
											 </span>
											 
											<?php }?> 
											 <?php  echo $result['REFUND_STATUS'] == 1?'<span class="alert alert-info"  style="padding:5px 10px;">退款金额:'.$result['REFUND_VALUE'].'</span>' :"" ?>
										</td>
									</tr>
								</table>
							<?php }?>
								
							<?php if( !empty($orderItems) ){?>
							<table class="form-table " >
							<?php if( $selectedPolicy['IS_RESEND'] == 1  && $status >=70 ){?>
								<caption>重发货配置 
									<?php if( $result['RESEND_STATUS'] < 1 ){ ?><button class="btn save-reship">保存重发货设置</button><?php }?>
								</caption>
							<?php }?>	
								<tr>
								<td colspan=4 style="text-align:left;">
									<div class="row-fluid">
										<div class="span12">
											<?php if( $selectedPolicy['IS_RESEND'] == 1 ){//需要重发货
												echo '<input type="hidden"  id="_reSend" value="'.$result['RESEND_STATUS'].'"/>' ;
											}?>

											<table style="width:100%;">
												<tr style="padding:0px;margin:0px;">
													<th style="padding:0px;text-align:center;">货品SKU</th>
													<th style="padding:0px;text-align:center;">货品名称</th>
													<th style="padding:0px;text-align:center;">货品图片</th>
													<th style="padding:0px;text-align:center;">购买数量</th>
													<?php if(  $selectedPolicy['IS_RESEND'] == 1 && $status >=70 ){?>
													<th style="padding:0px;text-align:center;width:100px;">重发货数量</th>
													<?php }?>
												</tr>
												<?php
													foreach( $orderItems as $order ){
														$order = $SqlUtils->formatObject($order) ;
														$imageUrl = $order['IMAGE_URL'] ;
														$imageUrl = str_replace("%" , "%25",$imageUrl) ;
														?>
														<tr style="padding:0px;margin:0px;">
															<td style="padding-top:0px;padding-bottom:0px;"><?php echo $order['REAL_SKU']?></td>
															<td style="padding-top:0px;padding-bottom:0px;"><?php echo $order['NAME']?></td>
															<td style="padding-top:0px;padding-bottom:0px;"><?php echo "<img style='width:25px;height:25px;' src='/".$fileContextPath."/".$imageUrl."'>"?></td>
															<td style="padding-top:0px;padding-bottom:0px;"><?php echo $order['Quantity_Ordered']?></td>
															<?php if( $selectedPolicy['IS_RESEND'] == 1  && $status >=70 ){?>
															<td style="padding-top:0px;padding-bottom:0px;">
																<input type="text" class="alert alert-danger" style="width:85px;" 
																	<?php if( !$rmaResendConfig ) echo 'disabled';?>
																	<?php echo $result['RESEND_STATUS'] == 1?"disabled":"";?>
																	orderId="<?php echo $order['Order_ID']?>"
																	orderItemId="<?php echo $order['Order_Item_Id']?>"
																	name="rmaReship" value="<?php echo $order['RMA_RESHIP']?>"/>
															</td>
															<?php }?>
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
		
		<div class="grid-content-rma-rel" id="tab-rma-rel" style="margin-top:5px;zoom:1;"></div>
	
	</div>
</body>
</html>
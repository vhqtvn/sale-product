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
		echo $this->Html->script('modules/warehouse/ram/editPurchaseRma');
		echo $this->Html->script('calendar/WdatePicker');
		
		$SqlUtils  = ClassRegistry::init("SqlUtils") ;
		$security  = ClassRegistry::init("Security") ;
		
		$loginId   = $user['LOGIN_ID'] ;
		$ramId =$params['arg1'] ;
		
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
		$rmaCommitScore = $security->hasPermission($loginId , 'RMA_COMMIT_SCORE') ;
		
		$result  = null ;
		
		if( !empty($ramId) ){
			$result = $SqlUtils->getObject("sql_ram_event_getById",array('id'=>$ramId) ) ;
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
		
		$isInit  =  $status == '' || $status == '10' ;
		
		//决策原因
		$policys = $SqlUtils->exeSql("sql_ram_options_getByType",array('type'=>'policy',"rmaType"=>"P")) ;
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
		//获取最近的轨迹
		$tracks = $SqlUtils->exeSql("sql_ram_track_list",array('id'=>$result['ID'])) ;
		$lastTrack = '' ;
		if( !empty($tracks) ){
			$track = $tracks[0] ;
			$track = $SqlUtils->formatObject($track) ;
			$lastTrack = $track['MEMO'] ;
		}
	?>
	
	<script type="text/javascript">
		var lastTrack = '' ;
		var currentStatus = '<?php echo $status;?>';
		var rmaId = '<?php echo $ramId ;?>' ;
		var policyCode = '<?php echo $policyCode;?>' ;
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
		var flowData = [] ;
		flowData.push( {status:10,label:"RMA决策",memo:true ,actions:[ 
			    {label:"强制结束",action:function(){ AuditAction('80',"强制结束") } },
		        {label:"编辑保存",action:function(){ AuditAction(10,"编辑保存") } }
		        <?php if( !empty( $selectedPolicy )){ 
		        		$nextStatus = 10 ;
		        		$nextText    = "" ;
		        		if( $selectedPolicy['IS_BACK'] == 1 ){
		        			$nextStatus = 40 ;
		        			$nextText = "" ;
		        		}else if( $selectedPolicy['IS_REFUND'] == 1 ){
		        			$nextStatus = 60 ;
		        		}else if( $selectedPolicy['IS_RESEND'] == 1 ){
		        			$nextStatus = 75 ;
		        		}
		        	?>
		        ,{label:"提交",action:function(){ AuditAction('<?php echo $nextStatus;?>',"决策完成，提交下一步") } } 
		        <?php } ?>
		]} ) ;

		<?php if( $selectedPolicy['IS_BACK'] == 1 ){//退货 ?>
		    /*退货*/
			flowData.push( {status:40,label:"退货发货",memo:true,actions:[
			   {label:"强制结束",action:function(){ AuditAction('80',"强制结束") } },
			   {label:"保存",action:function(){ AuditAction(40,"保存") } }
			] } ) ;

			flowData.push( {status:45,label:"供应商确认退货",memo:true,actions:[
			   {label:"强制结束",action:function(){ AuditAction('80',"强制结束") } },
			   {label:"保存轨迹",action:function(){ AuditAction(45,"保存轨迹") } }
			] } ) ;
		<?php } ?>

		<?php if( $selectedPolicy['IS_REFUND'] == 1 ){//退款 ?>
			/*退款*/
			flowData.push( {status:60 ,label:"退款",memo:true,actions:[ 
			   {label:"强制结束",action:function(){ AuditAction('80',"强制结束") } },
			   {label:"保存轨迹",action:function(){ AuditAction(60,"保存轨迹") } }
			]  } ) ;
		<?php } ?>

		<?php if( $selectedPolicy['IS_RESEND'] == 1 ){//重发 ?>
			/*重发补货*/
			flowData.push( {status:75,label:"确认重发",memo:true,actions:[ 
				     {label:"强制结束",action:function(){ AuditAction('80',"强制结束") } },
			   		 {label:"保存轨迹",action:function(){ AuditAction(75,"保存轨迹") } },
			         {label:"确认重发完成",action:function(){ AuditAction(78 ,"确认重发完成！") }
			    }
			]  } ) ;

			flowData.push( {status:78,label:"确认客户收货",memo:true,actions:[ 
                     {label:"强制结束",action:function(){ AuditAction('80',"强制结束") } },
                     {label:"保存轨迹",action:function(){ AuditAction(78,"保存轨迹") } },
                     {label:"确认客户收货",action:function(){ AuditAction(79 ,"确认客户收货，填写Feedback！") }
            	}
            ]  } ) ;
		<?php } ?>
			
		flowData.push({status:80,label:'结束'}) ;
	
		$(function(){
			var flow = new Flow() ;
			flow.init(".flow-bar center",flowData) ;
			flow.draw('<?php echo $status;?>') ;

			var ts = (lastTrack+"").split(")") ;
			var t = "" ;
			if(ts.length >=2){
				t = ts[1] ;
			}
			document.title = ( $(".flow-node.active").text()+(t?"|":"")+t );
		}) ; 		
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
						<table class="form-table "  style="margin-top:10px;">
							<caption>
								RMA基本信息
							</caption>
								<tr>
									<th>RMA编码：</th><td  colspan=3><input data-validator="required"
										<?php echo !empty( $result['PURCHASE_ID'] )?"readOnly":"" ;?>
										 type="text" id="code"
										value="<?php  
										if(empty($result['CODE'])){
											echo $defaultCode ;
										}else echo $result['CODE'];?>"/></td>
								</tr>
								<tr>
								<th>提出时间：</th>
									 <td colspan="<?php echo $status == 80?"1":"3" ;?>">
									 	<input data-validator="required" 
										<?php echo !empty( $result['PURCHASE_ID'] )?"readOnly":"" ;?>
										 type="text" id="proposedTime"
										value="<?php echo   $result['PROPOSED_TIME'];?>"/>
									 </td>
									 <?php  if( $status == 80 ){ ?>
									 		<th>结束时间：</th>
											 <td colspan="3"><?php echo   $result['END_TIME'];?>
											 </td>
									<?php 	}  ?>
								</tr>
								<tr>
									<th>采购单：</th><td colspan="3"><input data-validator="required"  
										type="hidden" id="purchaseId"
										<?php echo !$isInit?"readOnly":"" ;?>
										value="<?php  echo $result['PURCHASE_ID'] ; ?>"/>
										<a href="#"  purchase-product="<?php echo $result['PURCHASE_ID'] ;?>"><?php echo $result['PURCHASE_CODE'] ;?></a>
									</td>
								</tr>
								<tr>
									<th>货品：</th>
									<td > <a href="#"  product-realsku="<?php echo $result['REAL_SKU'] ;?>"><?php echo $result['REAL_NAME'] ;?></a></td>
									<th>数量：</th>
									<td > <?php echo  $result['RMA_NUM'] ;?></td>
								</tr>
								<tr>
									<th>RMA原因：</th>
									<td>
										<select name="causeCode" data-validator="required" <?php echo empty( $result['PURCHASE_ID'] )?"":"disabled" ;?>>
											<option value="">请选择</option>
										<?php
											$causeCode = $result['CAUSE_CODE'];
											$causes = $SqlUtils->exeSql("sql_ram_options_getByType",array('type'=>'cause',"rmaType"=>'P')) ;
											
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
								<tr>
									<th>备注：</th><td  colspan=3>
										<textarea name="memo"  
										<?php echo !$isInit?"readOnly":"" ;?>
										style="width:90%;height:40px;"><?php echo $result['MEMO'];?></textarea>
									</td>
								</tr>
							</table>
					<?php 
					/**
					 * 退货操作区域
					 */
					if( $selectedPolicy['IS_BACK'] == 1 && !$isInit ){//退货 ?>
								<table class="form-table " >	
									<caption>退货信息</caption>
									<?php /*
									<tr>
										<th>退货物流商：</th>
										<td>
											<input type="text"  id="backLogisticsProvider"  value="" />
										</td>
										<th>物流跟踪码：</th>
										<td>
											<input type="text"  id="backLogisticsTrackCode"  value="" />
										</td>
									</tr>
									*/?>
									<tr>
										<th>退货时间：</th>
										<td>
											<?php 
												if( !empty($result['BACK_DATE']) ){
													echo  $result['BACK_DATE'] ;
												}else{?>
												<button class="btn btn-primary  confirm-back">确认退货</button>	
										<?php  } ?>
										</td>
										<th>供应商收货时间：</th>
										<td style="width:30%;">
										<?php 
												if( !empty($result['BACK_CUSTOM_RECEVICE_DATE']) ){
													echo  $result['BACK_CUSTOM_RECEVICE_DATE'] ;
												}else if( !empty($result['BACK_DATE']) ){?>
												<button class="btn btn-primary  custom-receive-back">客户确认收货</button>
										<?php  } ?>
										</td>
									</tr>
									<tr>
										<th>退货备注：</th>
										<td colspan="3">
										<?php 
												if( !empty($result['BACK_CUSTOM_RECEVICE_DATE']) ){
													echo $result['BACK_MEMO'];
												}else{?>
												<textarea style="width:80%;height:50px;" name="backMemo"><?php echo $result['BACK_MEMO'];?></textarea>
										<?php  } ?>
										</td>
									</tr>
								</table>
					<?php } ?>		
					
					<?php
					/**
					 * 退款操作区域
					 */
					 if( $selectedPolicy['IS_REFUND'] == 1 && $status >=60  ){ ?>
							 <table class="form-table " >
								<caption>退款信息</caption>
									<tr>
										<th>是否收到退款：</th><td  colspan=3>
										<?php  if( empty( $result['REFUND_DATE'] ) ){ ?>
												是<input type="radio" name="refundStatus" > 
											 value="1" />&nbsp;&nbsp;&nbsp;&nbsp;
											否<input type="radio" name="refundStatus" 
											 value="0" />&nbsp;&nbsp;&nbsp;&nbsp;
											 <span class="refund-action" style="display:none;">
											 	<input type="text" name="refundValue"  style="width:100px;"  <?php if(!$rmaRefund) echo 'disabled';?> placeHolder="请输入退款金额"/>
											 	<input type="text" placeHolder="退款备注"  name="refundMemo" style="width:50%;;"  ></input>
											 	<button class="btn btn-primary refundConfirm">确认退款</button>
											 </span>
										<?php  }else{  ?>
											<span class="alert alert-info"  style="padding:5px 10px;">退款金额:<?php echo $result['REFUND_VALUE']; ?></span>
											<?php echo $result['REFUND_MEMO']; ?>
											&nbsp;&nbsp;
											<?php echo $result['REFUND_DATE']; ?>
										<?php } ?>
										</td>
									</tr>
								</table>
							<?php } ?>
							
						<?php
						/**
						 * 重发补货操作区域
						 */
						 if( $selectedPolicy['IS_RESEND'] == 1 && !$isInit  ){//退货 ?>	
							<table class="form-table " >
								<caption>重发补货信息</caption>
								<thead>
									<tr>
										<th>标签</th>
										<th  style="font-weight:bold;">账号</th>
										<th style="font-weight:bold;">Listing SKU</th>
										<th style="font-weight:bold;">FNSKU</th>
										<th style="font-weight:bold;">渠道</th>
										<th style="font-weight:bold;">重发数量</th>
										<th style="font-weight:bold;">当前库存</th>
										<th style="font-weight:bold;">最近14天销量</th>
									</tr>
								</thead>
								<tbody>
									<?php 
										$sqlId = "sql_supplychain_requirement_plan_product_details_list_ALL" ;
										$reqList = $SqlUtils->exeSqlWithFormat($sqlId,array("realId"=>$result['REAL_ID'],"reqProductId"=>$result['REQ_PRODUCT_ID'])) ;
										$count  = count( $reqList ) ;
										$planNum = 0 ;
										$rmaNum = $result['RMA_NUM'] ;
										
										foreach( $reqList as $req){
											$IS_ANALYSIS = $req['IS_ANALYSIS'] ;
											if( $IS_ANALYSIS == 0 ) continue ;
											$purchaseQuantity = $req['PURCHASE_QUANTITY'] ;
											$fixQuantity = 0 ;
											if( $purchaseQuantity > $rmaNum  ){
												$fixQuantity = $rmaNum ;
											}else{
												$fixQuantity = $purchaseQuantity ;
											}
											$rmaNum = $rmaNum -  $fixQuantity;
										?>
									  <tr   class="edit-data-row">
										<td>
											<?php  if( $fixQuantity >0  ) { ?>
										  <input type='text'  class="print-num no-disabled" style='width:35px;height:20px;margin-top:2px;padding:0px;' value='<?php echo $fixQuantity+5 ;?>'  title='输入打印数量'>
										  &nbsp;<button class='btn print-btn  no-disabled'>打印</button>
										  <?php  }?>
										</td>
										<td><?php echo $req['ACCOUNT_NAME'] ;?></td>
										<td><?php echo $req['LISTING_SKU'] ;?></td>
										<td><?php echo $req['FC_SKU'] ;?></td>
										<td><?php echo $req['FULFILLMENT_CHANNEL'] ;?></td>
										<td>
											<input type="hidden" class="fulfillment"   value='<?php echo $req['FULFILLMENT_CHANNEL'] ;?>'/>
											<input type="hidden" class="accountId"   value='<?php echo $req['ACCOUNT_ID'] ;?>'/>
											<input type="hidden" class="supplyQuantity"   value='<?php echo $req['TOTAL_SUPPLY_QUANTITY'] ;?>'/>
											<input type="hidden" class="listingSku"   value='<?php echo $req['LISTING_SKU'] ;?>'/>
											<input type="text" 	   class="purchaseQuantity input 45-input"  value='<?php echo $fixQuantity ;?>' style="width:50px;"/>
										</td>
										<td><?php echo $req['TOTAL_SUPPLY_QUANTITY'] ;?></td>
										<td><?php echo $req['SALES_FOR_THELAST14DAYS'] ;?></td>
									</tr>
									<?php  } ?>
								</tbody>
						</table>
						<?php } ?>
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
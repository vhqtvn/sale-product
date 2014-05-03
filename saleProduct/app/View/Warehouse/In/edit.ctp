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

		echo $this->Html->script('jquery');
		echo $this->Html->script('common');
		echo $this->Html->script('jquery.json');
		echo $this->Html->script('grid/jquery.llygrid');
		echo $this->Html->script('grid/query');
		echo $this->Html->script('validator/jquery.validation');	
		echo $this->Html->script('listselectdialog/jquery.listselectdialog');
		echo $this->Html->script('calendar/WdatePicker');
		
		echo $this->Html->script('modules/warehouse/in/edit');
		echo $this->Html->script('modules/warehouse/in-flow');
	
		$SqlUtils  = ClassRegistry::init("SqlUtils") ;
		$security  = ClassRegistry::init("Security") ;
		$loginId   = $user['LOGIN_ID'] ;
		$inId = $params['arg1'];
		
		//获取
		$warehoseIn = $SqlUtils->getObject("sql_warehouse_in_getById",array("id"=>$inId)) ;
		$status = $warehoseIn['STATUS'];
		
		$hasEditPermission = $security->hasPermission($loginId , 'IN_STATUS0') ;
		$isRead = $hasEditPermission?($status >= 10 ?true:false):true ;
		
		$hasReEditPermission = $security->hasPermission($loginId , 'IN_PLAN_REEDIT') ;
		
		$sendPermission = 
			$security->hasPermission($loginId , 'IN_STATUS20')||$security->hasPermission($loginId , 'IN_STATUS30') ;
		$isSended = $sendPermission?($status >30?true:false):true ; 
		
		$defaultCode = null ;
		if( empty($result['IN_NUMBER']) ){
			$index = $SqlUtils->getMaxValue("in" , null , 1) ;
			if( strlen($index) < 5 ){
				$len = 5-strlen($index) ;
				for($i=0 ;$i < $len ;$i++){
					$index = '0'.$index ;
				}
			}
			$defaultCode = "IN-".date("ymd").'-'.$index ;
		}
	?>
	<?php if($hasReEditPermission){?>
	<script>
			var inSourceType = '<?php echo $warehoseIn['IN_SOURCE_TYPE'];?>' ;
			$(function(){
				$(".reedit").css("cursor","pointer").click(function(){
						$(this).parents("table:first").find(":input").removeAttr("disabled").removeAttr("readOnly") ;
						$(".panel-foot").show();
					}) ;
			}) ;
   </script>
   <?php }?>
</head>

<body class="container-popup">
	<!-- apply 主场景 -->
	<div class="apply-page">
		<!-- 页面标题 -->
		<div class="container-fluid">

	        <form id="personForm" action="#" data-widget="validator" class="form-horizontal" >
	             <input type="hidden"  id="id"      value="<?php echo $result['ID'];?>"/>
	             <input type="hidden"  id="type"  value="in"/>
				 <!-- panel 头部内容  此场景下是隐藏的-->
				 <div class="panel apply-panel">
					<!-- panel 中间内容-->
					<div class="panel-content">
						<!-- 数据列表样式 -->
						<table class="form-table " >
							<caption>基本信息 </caption>
							<tbody>										   
								<tr>
									<th>入库号：</th><td><input type="text" 
										<?php echo $isRead?"readOnly":"" ;?>
										data-validator="required" id="inNumber" value="<?php echo empty($result['IN_NUMBER'])?$defaultCode:$result['IN_NUMBER'];?>"/></td>
								
									<th>负责人：</th>
									<td>
									<input type="hidden" data-validator="required" id="charger" 
											value="<?php echo $result['CHARGER'];?>"/>
									<input type="text" data-validator="required" id="chargerName" class="span2" readonly
											value="<?php echo $result['CHARGER_NAME'];?>"/>
										<?php if( !$isRead ){
											echo '<button class="btn btn-charger">选择</button>' ;
										}?>
									
									</td>
								</tr>
								<tr>
									<th>入库类型：</th>
									<td  colspan="3">
									<?php /*
										采购入库&nbsp;<input type="radio"  name="inSourceType"  disabled value="out" <?php echo $isRead?"disabled":"" ;?>
											<?php echo $result['IN_SOURCE_TYPE']=='out'?"checked":"";?>   
											data-validator="required" style="vertical-align: top;"/>&nbsp;&nbsp;&nbsp; 
										转仓&nbsp;<input type="radio"  name="inSourceType"  value="warehouse"  <?php echo $isRead?"disabled":"" ;?>
											<?php echo $result['IN_SOURCE_TYPE']=='warehouse'?"checked":"";?>   
											data-validator="required"  style="vertical-align: top;"/>*/?>
										 
										FBA入库&nbsp;<input type="radio"  name="inSourceType"  value="fba"  <?php echo $isRead?"disabled":"" ;?>
											<?php echo $result['IN_SOURCE_TYPE']=='fba'?"checked":"";?>   
											data-validator="required"  style="vertical-align: top;"/>
									 
									</td>
								</tr>
								<tr>
									<th>入库流程：</th>
									<td colspan="3">
										<select  data-validator="required"   id="flowType"  <?php echo $isRead?"disabled":"" ;?>>
											<option value="">-选择入库流程-</option>
											<option value="international"  <?php echo $result['FLOW_TYPE']=='international'?"selected":"";?>>国际物流流程</option>
											<option value="internal" <?php echo $result['FLOW_TYPE']=='internal'?"selected":"";?>>国内物流流程</option>
										</select>
									</td>
								</tr>
								<tr>
									<th class="trans_ trans-wh hide" style="display:none;">出库仓库：</th>
									<td class="trans_ trans-wh hide" style="display:none;">
											<select  id="sourceWarehouseId"  <?php echo $isRead?"disabled":"" ;?>>
										    	<option value="">--选择--</option>
											   <?php 
											     // sql_warehouse_lists
											     $warehouses = $SqlUtils->exeSql("sql_warehouse_lists",array()) ;
					                             foreach($warehouses as $w){
					                             	  $w = $SqlUtils->formatObject( $w ) ;
					                             	  $selected = $result['SOURCE_WAREHOUSE_ID'] == $w['ID'] ?"selected":"" ;
					                             	  echo "<option $selected value='".$w['ID']."'>".$w['NAME']."</option>" ;
					                             }
											   ?>
											</select>
									</td>
									<th class="trans_ trans-rkaccount hide">入库账号：</th>
									<td class="trans_ trans-rkaccount hide">
										<select name="accountId" class="span2"  <?php echo $isRead?"disabled":"" ;?>>
							     		<option value="">--选择--</option>
								     	<?php
								     		 $amazonAccount  = ClassRegistry::init("Amazonaccount") ;
							   				 $accounts = $amazonAccount->getAllAccounts(); 
								     		foreach($accounts as $account ){
								     			$account = $account['sc_amazon_account'] ;
								     			$checked = $account['ID'] == $result['ACCOUNT_ID']?"selected":"" ;
								     			echo "<option value='".$account['ID']."'  $checked>".$account['NAME']."</option>" ;
								     		} ;
								     	?>
										</select>
									</td>
									<th class="trans_ trans-rk hide">入库仓库：</th>
									<td class="trans_ trans-rk hide">
										<select  id="warehouseId"  <?php echo $isRead?"disabled":"" ;?>>
										    	<option value="">--选择--</option>
											   <?php 
											     // sql_warehouse_lists
											     $warehouses = $SqlUtils->exeSql("sql_warehouse_lists",array()) ;
					                             foreach($warehouses as $w){
					                             	  $w = $SqlUtils->formatObject( $w ) ;
					                             	  $selected = $result['WAREHOUSE_ID'] == $w['ID'] ?"selected":"" ;
					                             	  echo "<option $selected value='".$w['ID']."'>".$w['NAME']."</option>" ;
					                             }
											   ?>
											</select>
									</td>
								</tr>
								<tr>
									<th>备注：</th><td  colspan=3>
										<textarea name="memo"  
										<?php echo $isRead?"readOnly":"" ;?>
										style="width:90%;height:50px;"><?php echo $result['MEMO'];?></textarea>
									</td>
								</tr>
							</tbody>
						</table>
						<table class="form-table " >
							<caption>物流信息<?php if($hasReEditPermission){?><img class="reedit"  title="在编辑" src="/<?php echo $fileContextPath?>/app/webroot/img/edit.png"/><?php }?></caption>
							<tbody class="logistics-tbody" >
								<tr>
									<th>运输公司：</th>
									<td colspan="3"><input data-validator="required" class="span9" type="text" id="shipCompany"
										<?php echo $isRead?"readOnly":"" ;?>
										value="<?php echo $result['SHIP_COMPANY'];?>"/></td>
								</tr>
								<tr>
									<th>运输方式：</th>
									<td><input data-validator="required" type="text" id="shipType"
										<?php echo $isRead?"readOnly":"" ;?>
										value="<?php echo $result['SHIP_TYPE'];?>"/></td>
									<th>达到港口：</th><td colspan=3><input type="text" id="arrivalPort" data-validator="required"
										<?php echo $isRead?"readOnly":"" ;?>
										value="<?php echo $result['ARRIVAL_PORT'];?>"/></td>
								</tr>
								<tr>
									<th>发货时间：</th><td>
									<input type="text" id="shipDate" 
										data-widget="calendar"  data-options="{dateFmt:'yyyy-MM-dd HH:mm:ss'}"
										<?php echo $isSended?"readonly":"" ;?>
										value="<?php echo $result['SHIP_DATE'];?>"/></td>
									<th>预计到达时间：</th><td><input type="text" id="planArrivalDate" data-widget="calendar" 
									  data-options="{dateFmt:'yyyy-MM-dd HH:mm:ss'}"
										<?php echo $isSended?"readonly":"" ;?>
										value="<?php echo $result['PLAN_ARRIVAL_DATE'];?>"/></td>
								</tr>
								<tr>
									<th>运单号：</th><td><input type="text" id="shipNo"
										<?php echo $isSended?"readonly":"" ;?>
										value="<?php echo $result['SHIP_NO'];?>"/></td>
			
									<th>物流跟踪号：</th><td><input type="text" id="shipTracknumber"
										<?php echo $isSended?"readonly":"" ;?>
										value="<?php echo $result['SHIP_TRACKNUMBER'];?>"/></td>
								</tr>
								
							</tbody>
						</table>
						
						<table class="form-table " >
							<caption>发货人信息<?php if($hasReEditPermission){?><img class="reedit"  title="在编辑" src="/<?php echo $fileContextPath?>/app/webroot/img/edit.png"/><?php }?></caption>
							<tbody>	
								<tr>
									<th>公司名称：</th><td><input type="text" id="sendCompany"
										<?php echo $isRead?"readonly":"" ;?> 
										value="<?php echo $result['SEND_COMPANY'];?>"/></td>
								<th>Email：</th><td colspan=3><input type="text" id="sendCompanyEmail"
										<?php echo $isRead?"readonly":"" ;?>
										value="<?php echo $result['SEND_COMPANY_EMAIL'];?>"/></td>
								</tr>
								<tr>
									<th>邮编：</th><td colspan="3"><input type="text" id="sendCompanyPost"
										<?php echo $isRead?"readonly":"" ;?>
										value="<?php echo $result['SEND_COMPANY_POST'];?>"/></td>
									<!-- 
									<th>国家：</th><td><input type="text" id="sendCompanyCountry"
										<?php echo $isRead?"readonly":"" ;?>
										value="<?php echo $result['SEND_COMPANY_COUNTRY'];?>"/></td>
									 -->
								</tr><tr>
									<th>联系人：</th><td><input type="text" id="sendCompanyContactor" 
										<?php echo $isRead?"readonly":"" ;?>
										value="<?php echo $result['SEND_COMPANY_CONTACTOR'];?>"/></td>
									<th>联系电话：</th><td><input type="text" id="sendCompanyPhone" 
										<?php echo $isRead?"readonly":"" ;?>
										value="<?php echo $result['SEND_COMPANY_PHONE'];?>"/></td>
								</tr>
								<tr>
										<th>公司地址：</th><td colspan=3>
										<textarea style="width:90%;height:50px;" id="sendCompanyAddress"
										<?php echo $isRead?"readonly":"" ;?>
										><?php echo $result['SEND_COMPANY_ADDRESS'];?></textarea>
										</td>
								</tr>
							</tbody>
						</table>
						
						<table class="form-table " >
							<caption>收货人信息<?php if($hasReEditPermission){?><img class="reedit"  title="在编辑" src="/<?php echo $fileContextPath?>/app/webroot/img/edit.png"/><?php }?></caption>
							<tbody>
								<tr>
									<th>公司名称：</th><td><input type="text" id="receiveCompany"
										<?php echo $isRead?"readonly":"" ;?> 
										value="<?php echo $result['RECEIVE_COMPANY'];?>"/></td>
										<th>Email：</th><td><input type="text" id="receiveCompanyEmail"
										<?php echo $isRead?"readonly":"" ;?>
										value="<?php echo $result['RECEIVE_COMPANY_EMAIL'];?>"/></td>
								</tr>
								<tr>
									<th>邮编：</th><td colspan="3"><input type="text" id="receiveCompanyPost"
										<?php echo $isRead?"readonly":"" ;?>
										value="<?php echo $result['RECEIVE_COMPANY_POST'];?>"/></td>
									<!-- 
									<th>国家：</th><td><input type="text" id="receiveCompanyCountry"
										<?php echo $isRead?"readonly":"" ;?>
										value="<?php echo $result['RECEIVE_COMPANY_COUNTRY'];?>"/></td>
								 -->
								</tr><tr>
									<th>联系人：</th><td><input type="text" id="receiveCompanyContactor" 
										<?php echo $isRead?"readonly":"" ;?>
										value="<?php echo $result['RECEIVE_COMPANY_CONTACTOR'];?>"/></td>
									<th>联系电话：</th><td><input type="text" id="receiveCompanyPhone" 
										<?php echo $isRead?"readonly":"" ;?>
										value="<?php echo $result['RECEIVE_COMPANY_PHONE'];?>"/></td>
								</tr>
								<tr>
									<th>公司地址：</th><td colspan=3>
										<textarea style="width:90%;height:50px;" id="receiveCompanyAddress"
										<?php echo $isRead?"readonly":"" ;?>
										><?php echo $result['RECEIVE_COMPANY_ADDRESS'];?></textarea>
										</td>
								</tr>
							</tbody>
						</table>
					</div>
					
					<div style="height:40px;">&nbsp;</div>
					
					<!-- panel脚部内容-->
					
                    <div class="panel-foot" style="<?php echo ( !$isSended || !$isRead ) ?'display:block;':"display:none;" ?>position:fixed;bottom:0px;right:0px;left:0px;z-index:1;background-color:#FFF;">
						<div class="form-actions  ">
							<button type="button" class="btn btn-primary btn-save">提&nbsp;交</button>
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>
</body>
</html>
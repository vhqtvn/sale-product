<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title></title>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
    <meta http-equiv="pragma" content="no-cache"/>
	<meta http-equiv="cache-control" content="no-cache"/>

   <?php
  		include_once ('config/config.php');
   
		echo $this->Html->meta('icon');
		echo $this->Html->css('../js/validator/jquery.validation');
		echo $this->Html->css('../js/grid/jquery.llygrid');
		echo $this->Html->css('default/style');

		echo $this->Html->script('jquery');
		echo $this->Html->script('common');
		echo $this->Html->script('jquery.json');
		echo $this->Html->script('validator/jquery.validation');	
		echo $this->Html->script('grid/jquery.llygrid');
		echo $this->Html->script('calendar/WdatePicker');

		echo $this->Html->script('modules/cost/cost');
		echo $this->Html->script('calendar/WdatePicker');
		
		$user = $this->Session->read("product.sale.user") ;
		$groupCode = $user["GROUP_CODE"] ;
		$loginId = $user['LOGIN_ID'] ;
		
		/**
		 *  create_pp 添加计划产品操作
			add_pp_product 添加审批产品操作
			add_pp_audit_product 导出操作
			export_pp 打印操作
			print_pp 编辑采购产品操作
			edit_pp_product 删除采购产品操作
			delete_pp_product 申请采购操作
			apply_purchase 审批通过操作
			audit_pass_purchase 审批不通过操作
			audit_nopass_purchase
		*/
		
		$SqlUtils  = ClassRegistry::init("SqlUtils") ;
		$security  = ClassRegistry::init("Security") ;
		ini_set('date.timezone','Asia/Shanghai');
		$startTime = $params['arg1'] ;
		$endTime  = $params['arg2'] ;
		
		if( empty($startTime) ){
			$startTime = date('Y-m-d');
		}
		if( empty($endTime) ){
			$endTime = date('Y-m-d');
		}
	?>
	<script type="text/javascript">
		function loadCount(){
			var startTime = $(".start-time").val() ;
			var endTime = $(".end-time").val() ;
			window.location.href = "<?php echo $contextPath?>/page/forward/Report.purchaseDetailsReport/"+startTime+"/"+endTime ;
		}
	</script>

	 <style type="text/css">
		img{
			cursor:pointer;
		}
		
		.report-table td{
			text-align:right;
			padding-right:10px;
		}
	</style>
</head>
<body>
	<div class="toolbar toolbar-auto query-bar">
					<table  class="query-table">	
						<tr>
							<th>开始时间:</th>
							<td>
								<input  type="text" class="Wdate  start-time"  value="<?php echo $startTime;?>" onclick="WdatePicker({dateFmt:'yyyy-MM-dd'})" readonly="readonly" />
							</td>
							<th>结束时间:</th>
							<td>
								<input  type="text" class="Wdate  end-time"  value="" onclick="WdatePicker({dateFmt:'yyyy-MM-dd'})" readonly="readonly" />
							</td>
							<td>
								<button class="btn  reload-daychart no-disabled"  onclick="loadCount()">确定</button>
							</td>
						</tr>						
					</table>
	</div>	
	<center>
		<div>
				<h3>采购绩效统计【<?php echo $startTime;?>&nbsp;<?php echo $endTime;?>】</h3>
		</div>
	</center>
	<div class="panel apply-panel">
	<table  class="form-table  report-table">
		<thead>
			<tr>
				<th rowspan="2"></th>
				<th rowspan="2">总量</th>
				<th rowspan="2">完成下单</th>
				<th rowspan="2">中止采购</th>
				<th colspan="3">成本</th>
				<th colspan="2">交期</th>
			</tr>
			<tr>
				<th>下降</th>
				<th>持平</th>
				<th>上升</th>
				<th>正常</th>
				<th>逾期</th>
			</tr>
		</thead>
		<tbody>
			<?php 
				$SqlUtils  = ClassRegistry::init("SqlUtils") ;
				//获取总计
				$total = $SqlUtils->getObject("sql_rp_loadTotal",array("startTime"=>$startTime,"endTime"=>$endTime)) ;
				$userTotal = $SqlUtils->exeSqlWithFormat("sql_rp_loadUser",array("startTime"=>$startTime,"endTime"=>$endTime)) ;
				$groupMap = array() ;
				foreach(  $userTotal as  $r ){
					$groupMap[ $r['NAME'] ] = true ;
				}
				$userTotalMap = array() ;
				foreach(  $groupMap as $g=>$v ){
					$userTotalMap[$g] = '-' ;
					foreach($userTotal as $r){
						if( $r['NAME'] ==$g  ){
							$userTotalMap[ $g ] = $r['C'] ;
							break ;
						}
					}
				}
				
				//{45:'新增采购单',51:'下单完成',49:'交易完成',50:'完成收货',60:'完成验货',80:'完成入库'} ;
			    // 获取总采购相关统计数据
				
				$result = $SqlUtils->exeSqlWithFormat("sql_rp_load51Total",array("startTime"=>$startTime,"endTime"=>$endTime)) ;
				$totalMap = array('45'=>'-','51'=>'-','49'=>'-','50'=>'-','60'=>'-','80'=>'-') ;
				foreach($result as $r){
					$totalMap[ $r['PD'] ] = $r['C'] ;
				} 
				
				$result = $SqlUtils->exeSqlWithFormat("sql_rp_load51User",array("startTime"=>$startTime,"endTime"=>$endTime)) ;
	
				
				foreach(  $result as  $r ){
					$groupMap[ $r['NAME'] ] = true ;
				}
			
				$userMap = array() ;
				foreach(  $groupMap as $g=>$v ){
					$_Map = array('45'=>'-','51'=>'-','49'=>'-','50'=>'-','60'=>'-','80'=>'-') ;
					foreach($result as $r){
						if( $r['NAME'] ==$g  ){
							$_Map[ $r['PD'] ] = $r['C'] ;
						}
					}
					$userMap[$g] = $_Map ;
				}
			
				//获取成本控制统计  1=  2小于上次成本  3大于上次成本
				$result = $SqlUtils->exeSqlWithFormat("sql_rp_loadCostTotal",array("startTime"=>$startTime,"endTime"=>$endTime)) ;
				$costMap = array('1'=>'-','2'=>'-','3'=>'-') ;
				foreach($result as $r){
					$costMap[ $r['PD'] ] = $r['C'] ;
				}
				
				$result = $SqlUtils->exeSqlWithFormat("sql_rp_loadCostUser",array("startTime"=>$startTime,"endTime"=>$endTime)) ;
				foreach(  $result as  $r ){
					$groupMap[ $r['NAME'] ] = true ;
				}
				
				$userCostMap = array() ;
				foreach(  $groupMap as $g=>$v ){
					$_Map = array('1'=>'-','2'=>'-','3'=>'-') ;
					foreach($result as $r){
						if( $r['NAME'] ==$g  ){
							$_Map[ $r['PD'] ] = $r['C'] ;
						}
					}
					$userCostMap[$g] = $_Map ;
				}
				
				//获取中止交易数据
				$terminalMap = $SqlUtils->getObject("sql_rp_loadTerminalDeal",array("startTime"=>$startTime,"endTime"=>$endTime)) ;
				
				$userTerminalResult = $SqlUtils->exeSqlWithFormat("sql_rp_loadTerminalDealUser",array("startTime"=>$startTime,"endTime"=>$endTime)) ;
				foreach(  $userTerminalResult as  $r ){
					$groupMap[ $r['NAME'] ] = true ;
				}
				$userTerminalMap = array() ;
				foreach(  $groupMap as $g=>$v ){
                    $userTerminalMap[$g] = '-' ;
					foreach($result as $r){
						if( $r['NAME'] ==$g  ){
							$userTerminalMap[ $g ] = $r['C'] ;
							break ;
						}
					}
				}
				
				
				//交期  2：正常 1：逾期
				$result = $SqlUtils->exeSqlWithFormat("sql_rp_loadDelivery",array("startTime"=>$startTime,"endTime"=>$endTime)) ;
				$DeliveryMap = array('1'=>'-','2'=>'-') ;
				foreach($result as $r){
					$DeliveryMap[ $r['PD'] ] = $r['C'] ;
				}
				
				$result = $SqlUtils->exeSqlWithFormat("sql_rp_loadDeliveryUser",array("startTime"=>$startTime,"endTime"=>$endTime)) ;
				foreach(  $result as  $r ){
					$groupMap[ $r['NAME'] ] = true ;
				}
				
				$userDeliveryMap = array() ;
				foreach(  $groupMap as $g=>$v ){
					$_Map = array('1'=>'-','2'=>'-') ;
					foreach($result as $r){
						if( $r['NAME'] ==$g  ){
							$_Map[ $r['PD'] ] = $r['C'] ;
						}
					}
					$userDeliveryMap[$g] = $_Map ;
				}
			?>
			<tr>
				<td>总计</td>
				<td><?php echo $total['C'] ;?></td>
				<!-- 统计总量 -->
				<td><?php  echo $totalMap['51']?></td>
				<!-- 中止交易 -->
				<td><?php echo $terminalMap['C'];?></td>
				<!-- 成本控制 -->
				<td><?php  echo $costMap['2']?></td>
				<td><?php  echo $costMap['1']?></td>
				<td><?php  echo $costMap['3']?></td>
				<!-- 交期控制 -->
				<td><?php  echo $DeliveryMap['2']?></td>
				<td><?php  echo $DeliveryMap['1']?></td>
			</tr>
			<?php foreach( $userMap as $userName=>$valueMap ){
							$_userCostMap =  array('1'=>'-','2'=>'-','3'=>'-') ;
							if( isset($userCostMap[$userName]) ){
								$_userCostMap = $userCostMap[$userName] ;
							}
							
							$_userDeliveryMap =array('1'=>'-','2'=>'-') ;
							if( isset($userDeliveryMap[$userName]) ){
								$_userDeliveryMap = $userDeliveryMap[$userName] ;
							}
							
							$_userTotal = '-';
							if( isset($userTotalMap[$userName]) ){
								$_userTotal = $userTotalMap[$userName] ;
							}
				?>
			<tr>
				<td><?php echo $userName;?></td>
				<td><?php  echo $_userTotal?></td>
				<!-- 统计总量 -->
				<td><?php  echo $valueMap['51']?></td>
				<!-- 中止交易 -->
				<td><?php echo $userTerminalMap[$userName];?></td>
				<!-- 成本控制 -->
				<td><?php  echo $_userCostMap['2']?></td>
				<td><?php  echo $_userCostMap['1']?></td>
				<td><?php  echo $_userCostMap['3']?></td>
				<!-- 交期控制 -->
				<td><?php  echo $_userDeliveryMap['2']?></td>
				<td><?php  echo $_userDeliveryMap['1']?></td>
			</tr>
			<?php }?>
		</tbody>
	</table>
	</div>
</body>
</html>

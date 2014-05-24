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
				<h3><?php echo $startTime;?>&nbsp;<?php echo $endTime;?>采购统计报表</h3>
		</div>
	</center>
	<div class="panel apply-panel">
	<table  class="form-table  report-table">
		<thead>
			<tr>
				<th rowspan="2"></th>
				<th rowspan="2">新增采购单</th>
				<th rowspan="2">完成下单</th>
				<th rowspan="2">完成交易</th>
				<th rowspan="2">完成收货</th>
				<th rowspan="2">完成验货</th>
				<th rowspan="2">完成入库</th>
				<th rowspan="2">中止采购</th>
				<th colspan="3">成本</th>
				<th colspan="2">交期</th>
			</tr>
			<tr>
				<th>A</th>
				<th>B</th>
				<th>C</th>
				<th>正常</th>
				<th>逾期</th>
			</tr>
		</thead>
		<tbody>
			<?php 
				//{45:'新增采购单',51:'下单完成',49:'交易完成',50:'完成收货',60:'完成验货',80:'完成入库'} ;
			    // 获取总采购相关统计数据
				$SqlUtils  = ClassRegistry::init("SqlUtils") ;
				$result = $SqlUtils->exeSqlWithFormat("sql_rp_loadTotal",array("startTime"=>$startTime)) ;
				$totalMap = array('45'=>0,'51'=>0,'49'=>0,'50'=>0,'60'=>0,'80'=>0) ;
				foreach($result as $r){
					$totalMap[ $r['PD'] ] = $r['C'] ;
				} 
				
				//获取成本控制统计  1=  2小于上次成本  3大于上次成本
				$result = $SqlUtils->exeSqlWithFormat("sql_rp_loadCostTotal",array("startTime"=>$startTime)) ;
				$costMap = array('1'=>0,'2'=>0,'3'=>0) ;
				foreach($result as $r){
					$costMap[ $r['PD'] ] = $r['C'] ;
				}
				//获取中止交易数据
				$terminalMap = $SqlUtils->getObject("sql_rp_loadTerminalDeal",array("startTime"=>$startTime)) ;
				
				//交期  2：正常 1：逾期
				$result = $SqlUtils->exeSqlWithFormat("sql_rp_loadDelivery",array("startTime"=>$startTime)) ;
				$DeliveryMap = array('1'=>0,'2'=>0) ;
				foreach($result as $r){
					$DeliveryMap[ $r['PD'] ] = $r['C'] ;
				}
			?>
			<tr>
				<td>总计</td>
				<!-- 统计总量 -->
				<td><?php  echo $totalMap['45']?></td>
				<td><?php  echo $totalMap['51']?></td>
				<td><?php  echo $totalMap['49']?></td>
				<td><?php  echo $totalMap['50']?></td>
				<td><?php  echo $totalMap['60']?></td>
				<td><?php  echo $totalMap['80']?></td>
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
		</tbody>
	</table>
	</div>
</body>
</html>

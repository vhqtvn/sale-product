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
			window.location.href = "<?php echo $contextPath?>/page/forward/Report.productDevDetailsReport/"+startTime+"/"+endTime ;
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
				<th></th>
				<th>总量</th>
				<th>新增产品</th>
				<th>完成分析</th>
				<th>询价完成</th>
				<th>审批完成</th>
				<th>制作Listing完成</th>
			</tr>
		</thead>
		<tbody>
			<?php 
				//var formatJson = {10:'新增开发产品',20:'产品分析完成',30:'询价完成',50:'审批完成',
				//60:'货品录入完成',70:'制作Listing完成',72:'Listing审批完成',80:'已采购'} ;

				$groupMap = array() ;
				$SqlUtils  = ClassRegistry::init("SqlUtils") ;
				//获取总计
				$totalRecords = $SqlUtils->exeSqlWithFormat("sql_rpd_loadTotal",array("startTime"=>$startTime,"endTime"=>$endTime)) ;
				$totalMap = array('10'=>'-','20'=>'-','30'=>'-','50'=>'-','60'=>'-','70'=>'-','72'=>'-','80'=>'-') ;
				foreach ( $totalRecords as $total ){
					$totalMap['STATUS'] = $total['C'] ;
				}
				
				$userTotal = $SqlUtils->exeSqlWithFormat("sql_rpd_loadUser",array("startTime"=>$startTime,"endTime"=>$endTime)) ;
				foreach(  $userTotal as  $r ){
					$groupMap[ $r['NAME'] ] = true ;
				}
				$userTotalMap = array() ;
				foreach(  $groupMap as $g=>$v ){
					$_Map = array('10'=>'-','20'=>'-','30'=>'-','50'=>'-','60'=>'-','70'=>'-','72'=>'-','80'=>'-') ;
					foreach($userTotal as $r){
						if( $r['NAME'] ==$g  ){
							$userTotalMap[ $g ] = $r['C'] ;
							$_Map[ $r['STATUS'] ] = $r['C'] ;
						}
					}
					$userTotalMap[$g]  = $_Map ;
				}
			?>
			<tr>
				<td>总计</td>
				<td></td>
				<td><?php  echo $totalMap['10']?></td>
				<td><?php  echo $totalMap['20'];?></td>
				<td><?php  echo $totalMap['30']?></td>
				<td><?php  echo $totalMap['50']?></td>
				<td><?php  echo $totalMap['70']?></td>
			</tr>
			<?php  foreach( $groupMap as $userName=>$_ ){
							$userTotal = $userTotalMap[$userName] ;
				?>
			<tr>
				<td><?php echo $userName;?></td>
				<td></td>
				<!-- 统计总量 -->
				<td><?php  echo $userTotal['10']?></td>
				<td><?php  echo $userTotal['20'];?></td>
				<td><?php  echo $userTotal['30']?></td>
				<td><?php  echo $userTotal['50']?></td>
				<td><?php  echo $userTotal['70']?></td>
			</tr>
			<?php }  ?>
		</tbody>
	</table>
	</div>
</body>
</html>

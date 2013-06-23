<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>采购任务单</title>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
    <meta http-equiv="pragma" content="no-cache"/>
	<meta http-equiv="cache-control" content="no-cache"/>

   <?php
  		include_once ('config/config.php');
   
		echo $this->Html->meta('icon');
		echo $this->Html->css('../js/grid/jquery.llygrid');
		
		echo $this->Html->css('default/style');

		echo $this->Html->script('jquery');
		echo $this->Html->script('common');
		echo $this->Html->script('jquery.json');
		echo $this->Html->script('grid/jquery.llygrid');
		
		//$user = $this->Session->read("product.sale.user") ;
		$groupCode = $user["GROUP_CODE"] ;
		$taskId = $params['arg1'] ;
		
		$SqlUtils  = ClassRegistry::init("SqlUtils") ;
		$security  = ClassRegistry::init("Security") ;
		$task = $SqlUtils->getObject("sql_purchase_task_getById",array('taskId'=>$taskId)) ;
		
		$SqlUtils->exeSql("sql_purchase_task_updateStatus",array( 'taskId'=>$taskId,'status'=>'2')) ;
	
		
		$Grid  = ClassRegistry::init("Grid") ;
		$recordSql = $SqlUtils->getRecordSql( array('taskId'=>$taskId,'sqlId'=>'sql_purchase_task_productsForPrint','start'=>0,'limit'=>1000)) ; 
    	$products = $Grid->query( $recordSql ) ;
		
	?>

   <script type="text/javascript">
   	 var type = '4' ;//查询已经审批通过
   </script>
 	<style>
   		*{
   			font:12px "微软雅黑";
   		}
   		
   		.cell-div {
   			white-space:normal;
   			padding:2px;
   			vertical-align:top;
   		}
   		
   		.product-content {
   			padding:2px;
   		}
 
   		.product-image img{
   			width:140px;
   			height:140px;
   			margin:5px;
   		}
   		
   		.product-asin , .product-title{
   			text-align:left;
   			font-weight:bold;
   		}
   		
   		.product-gg{
   			text-align:left;
   		}
   		
   		label{
   			margin-top:5px;
   			display:block;
   			text-align:left;
   		}
   		
   		.lly-grid-body-column{
   			vertical-align:top;
   		}
   		
   		.label-content{
   			font-weight:bold;
   			text-align:left;
   			text-indent:5px;
   			margin-bottom:7px;
   		}
   		
   		input{
   			border:none!important;
   			border-bottom:1px solid #CCC!important;;
   			filter:none!important;
   			width:100px;
   			-webkit-border-radius: 0px!important;;
			-moz-border-radius: 0px!important;;
			border-radius:0px!important;;
   			background:none!important;;
   			-webkit-box-shadow: none!important;;
			-moz-box-shadow:none!important;;
			box-shadow: none!important;;
			-webkit-transition: none!important;;
			-moz-transition: none!important;;
			-ms-transition: none!important;;
			-o-transition:none!important;;
			transition:none!important;;
   		}
   		
   		div.lly-grid .product-gg span{
   			font-size:11px;
   		}
   		
   		tr,td,th,.lly-grid-head{
			background:none!important;
   		}
   		.lly-grid-pager{
			display:none!important;
   		}
   		
   		table,tr,td,th,div{
			border-color: #000!important;
   		}
   		
   		.lly-grid-2-body{
			overflow:hidden!important;
   		}
   		
   		.title-bar div{
			font-weight:bold!important;	
   		}
   </style>

</head>
<body>
	<center>
		<h1>采购确认单</h1>
		<hr style="margin:2px;margin-bottom:5px;"/>
	</center>
	<div style="padding:5px 10px;font-size:13px;font-weight:bold;">
	   <div class="row-fluid title-bar">
	   <div class="span3">
		采购编号：<?php echo $task['TASK_CODE']?>
		</div>
		<div class="span3">采购单位：深圳双橙科技有限公司</div>
		<div class="span3">
		申请人：<?php echo $task['EXECUTOR_NAME']?>
		</div>
		<div class="span3">
		申请时间：<?php echo $task['CREATED_TIME']?>
		</div>
		</div>
	</div>
	<center>
	<div style="width:98%;">
	<div class="grid-content-details" style="margin-top:5px;overflow:hidden;">
	<table class="table table-bordered">
		<tr>
			<th style="text-align:center;width:20px;"></th>
			<th style="text-align:center;">产品详细信息</th>
			<th style="text-align:center;width:150px;">供应商</th>
			<th style="text-align:center;width:120px;">采购信息</th>
			<th style="text-align:center;width:100px;">承诺交期</th>
			<th style="text-align:center;width:100px;">备注</th>
		</tr>
	
	<?php 
	
	$index = 0 ;
	foreach( $products as $pd ){
		$index += 1 ;
		$pd = $SqlUtils->formatObject($pd) ; ?>
		<tr>
			<!-- 序号 -->
			<th style="text-algin:center;"><?php echo $index ;?></th>
			<!-- 产品基本信息 -->
			<td style="text-algin:center;">
				<?php
				$localUrl = $pd['IMAGE_URL'] ;
				$sku	=	$pd['SKU'] ;
				$title	=	$pd['TITLE'] ;
				$tags = $pd['TAGS'] ;
				
				$providor = $pd['PROVIDOR_CODE'] ;
				if( !empty($providor) ){
					$providor = str_replace("SUP-", "", $providor) ;
				}else{
					$providor = $pd['PROVIDOR'] ;
				}
				
				$qtc = $providor.'-'.$task['ID'].'-'.$sku ;
				
				if( !empty($localUrl) ){
					$localUrl = str_replace("%" , "%25",$localUrl) ;
					$localUrl = "<img src='/".$fileContextPath."/".$localUrl."' style='height:112px;'>" ;
				}
				
				$html = '
				<div>
				<div class="product-image" style="width:152px;height:122px;float:left;">'.$localUrl.'</div>
				<div class="product-base" style="width:202px;float:left;">
				<div class="product-content product-asin">SKU: '.$sku.'</div>
				<div class="product-content product-asin">QTC: '.$qtc.'</div>
				<hr style="margin:2px;height:1px;"/>
				<div class="product-content product-title">'.$title.'</div>
				<div class="product-content product-gg"></div>
				<hr style="margin:2px;"/><div class="product-content product-gg">标签：'.$tags.'</div>
				</div>
				</div>
				' ;
				
				echo $html ;
				?>
			</td>
			<!-- 供应商信息 -->
			<td style="text-algin:center;">
			<?php 
			echo "<div>" ;
			echo "<div class='product-provider'>" ;
			echo "<label>供应商名称：</label>" ;
			echo "<div class='label-content'>".$pd['PROVIDOR_NAME']."</div>" ;
			echo "<label>联系人：</label>" ;
			echo "<div class='label-content'>".$pd['PROVIDOR_CONTACTOR']."</div>" ;
			echo "<label>联系电话：</label>" ;
			echo "<div class='label-content'>".$pd['PROVIDOR_PHONE']."</div>" ;
			echo "</div>" ;
			echo "</div>" ;
			?>
			</td>
			<!-- 采购信息 -->
			<td style="text-algin:center;">
			<?php 
					$totalPrice = $pd['QUOTE_PRICE'] * $pd['SUPPIERABLE_NUM'] ;
		           	if($totalPrice) $totalPrice = $totalPrice ;// .toFixed(2) ;

		           	$html = '
		           		<div>
								<div class="product-purchase">
									<div class="row-fluid">
										<div class="span4">单价：</div>
										<div class="label-content span8">'.$pd['QUOTE_PRICE'].'</div>
									</div>
									<div class="row-fluid">
										<div class="span4">数量：</div>
										<div class="label-content span8">'.$pd['SUPPIERABLE_NUM'].'</div>
									</div>
									<div class="row-fluid">
										<div class="span4">总价：</div>
										<div class="label-content span8">'.$totalPrice.'</div>
									</div><hr style="margin:2px;"/>
									<div style="text-align:left;">支付方式:</div>
									<div class="label-content">'.$pd['PAY_TYPE'].'</div>
								</div>
						</div>
		           		' ;
		           	echo $html ;
			?>
			</td>
			<td style="text-align:center;vertical-align: middle;font-weight:bold;"><?php 
				$data =  $pd['PROMISE_DELIVERY_DATE']  ;
				echo str_replace( "00:00:00", "",$data) ;
			?></td>
			<td>
				<?php 
					echo $pd['MEMO'] ;
				?>
			</td>
		</tr>
	<?php } ;?>
		<tr>
			<th>备注</th>
			<td colspan="5" style="height:80px;"></td>
		</tr>
	</table>
	</div>
	</div>
	</center>
	<div style="padding:5px 10px;font-size:13px;font-weight:bold;">
		<table style="width:100%;">
			<tr>
				<td style="font-weight:bold;">营销主管：<input type="text" /></td>
				<td style="font-weight:bold;">采购主管：<input type="text" /></td>
				<td style="font-weight:bold;">会计：<input type="text" /></td>
				<td style="font-weight:bold;">总监：<input type="text" /></td>
			</tr>
		</table>
		<hr style="margin:3px;"/>
		<table style="width:100%;">
		
		<tr>
				<td style="font-weight:bold;width:50%;">供应商负责人签字：<input type="text" /></td>
				<td style="font-weight:bold;text-align:left;width:25%;">盖章</td>
				<td style="font-weight:bold;">日期：<input type="text" /></td>
			</tr>
			<tr>
			<td colspan="3" style="text-align:right;font-size:10px;padding-top:10px;padding-right:30px;"><h4>请供应商收到后签字盖章并传真发回我司确认</h4></td>
		</tr>
		</table>
		
		
	</div>
	
</body>
</html>

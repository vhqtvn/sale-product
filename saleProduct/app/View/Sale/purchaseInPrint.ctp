<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>入库单</title>
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
		$wpt= "" ;
		if( isset( $params['printTime']) ){
			$wpt = $params['printTime'] ;
		}
		
		$SqlUtils  = ClassRegistry::init("SqlUtils") ;
		$security  = ClassRegistry::init("Security") ;
		$task = $SqlUtils->getObject("sql_purchase_task_getById",array('taskId'=>$taskId)) ;
		
		$SqlUtils->exeSql("sql_purchase_task_updateStatus",array( 'taskId'=>$taskId,'status'=>'2')) ;
		
		
		$Grid  = ClassRegistry::init("Grid") ;
		
		$params1 = array('taskId'=>$taskId,'sqlId'=>'sql_purchase_task_productInedForPrint','start'=>0,'limit'=>1000) ;
		if( empty($wpt) ){
			$params1['printTimeNull'] = 1 ;
		}else{
			$params1['printTime'] =$wpt ;
		}
		
		$recordSql = $SqlUtils->getRecordSql($params1) ; 
    	$products = $Grid->query( $recordSql ) ;
    	
    	//获取打印批次
    	$batchs = $SqlUtils->exeSqlWithFormat("sql_purchase_task_groupByPrintTime",array( 'taskId'=>$taskId)) ;
	
    	$currentTime =  date("Y-m-d H:i:s") ;
    	$printTime = empty($wpt)? $currentTime:"$wpt" ;
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
   
   <script>
	$( function(){
			$(".print-btn").click(function(){
					if( window.confirm("确认打印采购入库单？") ){
						$.dataservice("model:Sale.savePrintTime",{'printTime':'<?php echo $printTime;?>',taskId:'<?php echo $taskId;?>'}, function(){
							$(".print").hide() ;
							window.print() ;
							});
					}
				}) ;

			$(".print-batch").click(function(){
				var wpt = $(this).val() ;
				window.location.href = contextPath+"/page/forward/Sale.purchaseInPrint/<?php echo $taskId;?>?printTime="+wpt ;
			}) ;
		})
   </script>

</head>
<body>
	<center>
		<h1>采购入库单</h1>
		<hr style="margin:2px;margin-bottom:5px;"/>
		<div class="print"  style="position:absolute;right:5px;top:5px;" >
			<select class="span3 print-batch">
				<option value="">未打印采购入库产品</option>
				<?php if(!empty($batchs)){
					foreach($batchs as $btach){
						$text = $btach['PRINT_TIME'] ;
						$total = $btach['TOTAL'] ;
						echo $text.'  ---    '.$wpt ;
						$s = $text == $wpt ?"selected":"" ;
						echo "<option $s value='$text'>$text($total)</option>" ;
					}
				}?>
			</select>
			<a  class=" print-btn btn btn-primary" target="_self">打印</a>
		</div>
		
	</center>
	<div style="padding:5px 10px;font-size:13px;font-weight:bold;">
	   <div class="row-fluid title-bar">
	   <div class="span5">
			采购任务编号：<?php echo $task['TASK_CODE']?>
		</div>
		<div class="span3">
			采购负责人：<?php echo $task['EXECUTOR_NAME']?>
		</div>
		<div class="span4">
			打印时间：<?php echo $printTime;?>
			<?php if( !empty($wpt) ){//非第一次打印
				echo "($currentTime)" ;
			}?>
		</div>
		</div>
	</div>
	<center>
	<div style="width:98%;">
	<div class="grid-content-details" style="margin-top:5px;overflow:hidden;">
	<table class="table table-bordered">
		<tr>
			<th style="text-align:center;width:20px;"></th>
			<th style="text-align:center;">图片</th>
			<th style="text-align:center;">SKU</th>
			<th style="text-align:center;">名称</th>
			<th style="text-align:center;">合格产品数量</th>
			<th style="text-align:center;">不合格产品数量</th>
			<th style="text-align:center;">入库时间</th>
			<th style="text-align:center;">入库操作人</th>
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
			<?php
				$localUrl = $pd['IMAGE_URL'] ;
				$sku	=	$pd['SKU'] ;
				$title	=	$pd['TITLE'] ;
				$tags = $pd['TAGS'] ;
				
				if( !empty($localUrl) ){
					$localUrl = str_replace("%" , "%25",$localUrl) ;
					$localUrl = "<img src='/".$fileContextPath."/".$localUrl."' style='width:30px;height:30px;;'>" ;
				}
				?>
			<th style="text-algin:center;"><?php echo $localUrl ;?></th>
			<th style="text-algin:center;"><?php echo $sku ;?></th>
			<th style="text-algin:center;"><?php echo $title ;?></th>
			<th style="text-algin:center;"><?php echo $pd['QUALIFIED_PRODUCTS_NUM'] ;?></th>
			<th style="text-algin:center;"><?php echo $pd['BAD_PRODUCTS_NUM'] ;?></th>
			<th style="text-algin:center;"><?php if( $pd['WAREHOUSE_TIME'] == "0000-00-00 00:00:00"){
														echo "";
													} else{
														echo $pd['WAREHOUSE_TIME'];
													};?></th>
			<th style="text-algin:center;"><?php echo $pd['WARHOUSE_USERNAME'] ;?></th>
		</tr>
	<?php } ;?>
	</table>
	</div>
	</div>
	</center>
	<div style="padding:5px 10px;font-size:13px;font-weight:bold;">
		<table style="width:100%;">
			<tr>
				<td style="font-weight:bold;">采购负责人：<input type="text" /></td>
				<td style="font-weight:bold;">入库操作人：<input type="text" /></td>
				<td style="font-weight:bold;">总监：<input type="text" /></td>
			</tr>
		</table>
	</div>
</body>
</html>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
   <?php echo $this->Html->charset(); ?>
    <title>产品采集操作</title>
    <meta http-equiv="pragma" content="no-cache"/>
	<meta http-equiv="cache-control" content="no-cache"/>

   <?php
		echo $this->Html->meta('icon');
		echo $this->Html->css('../grid/jquery.llygrid');
		echo $this->Html->css('../js/validator/jquery.validation');
		echo $this->Html->css('../js/tree/jquery.tree');
		echo $this->Html->css('style-all');

		echo $this->Html->script('jquery');
		echo $this->Html->script('common');
		echo $this->Html->script('../grid/query');
		echo $this->Html->script('jquery.json');
		echo $this->Html->script('../grid/jquery.llygrid');
		echo $this->Html->script('validator/jquery.validation');
		echo $this->Html->script('tree/jquery.tree');
	?>
	
   <style>
   		*{
   			font:12px "微软雅黑";
   		}

		.rule-content-item{
			clear:both;
		}

		.item-label,.item-relation,.item-value,.item-value{
			float:left;
		}
		
		input{
			width:300px;
		}
   </style>

   <script>
    function formatGridData(data){
		var records = data.record ;
 		var count   = data.count ;
 		
 		count = count[0][0]["count(*)"] ;
 		
		var array = [] ;
		$(records).each(function(){
			var row = {} ;
			for(var o in this){
				var _ = this[o] ;
				for(var o1 in _){
					row[o1] = _[o1] ;
				}
			}
			array.push(row) ;
		}) ;
	
		var ret = {records: array,totalRecord:count } ;
			
		return ret ;
	   }
   
   		var accountId = '<?php echo $id;?>' ;
		$(function(){
			//$(".step1,.step2,.step3").attr("disabled",true).hide() ;
			
			//getStatus() ;
			
			$(".step1").click(function(){//发
				$(this).html("正在采集中......").attr("disabled",true) ;
				$.ajax({
					type:"post",
					url:"/saleProduct/index.php/task/amazonAsin/"+accountId ,
					data:{},
					cache:false,
					dataType:"text",
					success:function(result,status,xhr){
						window.location.reload() ;
					}
				}); 
			}) ;
			
			$(".step2").click(function(){//发
				$(this).html("正在采集中......").attr("disabled",true) ;
				$.ajax({
					type:"post",
					url:"/saleProduct/index.php/task/gatherAmazonCompetitions/"+accountId,
					data:{},
					cache:false,
					dataType:"text",
					success:function(result,status,xhr){
						window.location.reload() ;
					}
				}); 
			}) ;
			
			$(".step3").click(function(){//发
				$(this).html("正在采集中......").attr("disabled",true) ;
				$.ajax({
					type:"post",
					url:"/saleProduct/index.php/task/gatherAmazonFba/"+accountId,
					data:{},
					cache:false,
					dataType:"text",
					success:function(result,status,xhr){
						window.location.reload() ;
					}
				}); 
			}) ;
			
			$(".step4").click(function(){//发
				$(this).html("正在采集中......").attr("disabled",true) ;
				$.ajax({
					type:"post",
					url:"/saleProduct/index.php/task/amazonShippingAsin/"+accountId,
					data:{},
					cache:false,
					dataType:"text",
					success:function(result,status,xhr){
						//window.location.reload() ;
					}
				}); 
			}) ;
		
		}) ;
		
   </script>
	<?php
		$account = $account[0]['sc_amazon_account'] ;
	?>
</head>
<body>
<form id="personForm" action="#" data-widget="validator,ajaxform">
	<table class="table table-bordered">
		<caption>产品采集操作</caption>
		<tr>
			<td style="width:150px;">产品基本信息采集</td>
			<td><?php
				if( empty( $account['GATHER_STATUS_PRODUCT'] ) ){
					echo '<button class="step1 btn btn-primary">开始采集</button>' ;
				}else{
					echo '<button disabled=true class="step1 btn btn-primary">正在采集中......</button>' ;
				}
				
				if( !empty( $account['GATHER_STATUS_PRODUCT_TIME'] ) ){
					echo "上次采集时间:".$account['GATHER_STATUS_PRODUCT_TIME']  ;
				}
			?>
			
			 </td>
		</tr>
		<tr>
			<td>产品竞争信息采集</td>
			<td><?php
				if( empty( $account['GATHER_STATUS_COMPETE'] ) ){
					echo '<button class="step2 btn btn-primary">开始采集</button>' ;
				}else{
					echo '<button disabled=true class="step2 btn btn-primary">正在采集中......</button>' ;
				}
				if( !empty( $account['GATHER_STATUS_COMPETE_TIME'] ) ){
					echo "上次采集时间:".$account['GATHER_STATUS_COMPETE_TIME']  ;
				}
			?>
			
			 </td>
		</tr>
		<tr>
			<td>FBA信息采集</td>
			<td><?php
				if( empty( $account['GATHER_STATUS_FBA'] ) ){
					echo '<button class="step3 btn btn-primary">开始采集</button>' ;
				}else{
					echo '<button disabled=true class="step3 btn btn-primary">正在采集中......</button>' ;
				}
				if( !empty( $account['GATHER_STATUS_FBA_TIME'] ) ){
					echo "上次采集时间:".$account['GATHER_STATUS_FBA_TIME']  ;
				}
			?>
			 </td>
		</tr>
		<tr>
			<td>运费(FM)信息采集</td>
			<td><?php
				if( empty( $account['GATHER_STATUS_PRODUCT_SHIPPING'] ) ){
					echo '<button class="step4 btn btn-primary">开始采集</button>' ;
				}else{
					echo '<button disabled=true class="step4 btn btn-primary">正在采集中......</button>' ;
				}
				if( !empty( $account['GATHER_STATUS_PRODUCT_SHIPPING_TIME'] ) ){
					echo "上次采集时间:".$account['GATHER_STATUS_PRODUCT_SHIPPING_TIME']  ;
				}
			?>
			 </td>
		</tr>
	</table>
	
	<div class="grid-content" style="width:98%;">
	
	</div>
</form>
</html>
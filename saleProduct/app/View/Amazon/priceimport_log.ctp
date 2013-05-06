<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
   <?php echo $this->Html->charset(); ?>
    <title>产品获取操作</title>
    <meta http-equiv="pragma" content="no-cache"/>
	<meta http-equiv="cache-control" content="no-cache"/>

   <?php
   include_once ('config/config.php');
   
		echo $this->Html->meta('icon');
		echo $this->Html->css('../grid/jquery.llygrid');
		echo $this->Html->css('../js/validator/jquery.validation');
		echo $this->Html->css('../js/tree/jquery.tree');
		echo $this->Html->css('default/style');

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
   
   		var accountId = '<?php echo $accountId;?>' ;
		$(function(){
			$(".grid-content").llygrid({
				columns:[
					{align:"center",key:"ID",label:"编号", width:"5%"},
					{align:"left",key:"CREATE_TIME",label:"同步时间",width:"20%",forzen:false},
		           	{align:"left",key:"USERNAME",label:"操作用户",width:"10%",forzen:false},
		           	{align:"left",key:"FEEDSUBMISSION_ID",label:"请求标志",width:"15%"},
		           	{align:"left",key:"STATUS",label:"状态",width:"18%"},
		           	{align:"left",key:"SUCCESS_NUM",label:"成功",width:"10%"},
		           	{align:"left",key:"FAIL_NUM",label:"失败",width:"10%"},
		           	{align:"center",key:"STATUS",label:"操作",width:"10%",format:function(val,record){
		           		if(val == 'Complete') return "" ;
		           		return "<a href='#' class='update-state' feedSubmissionId='"+record.FEEDSUBMISSION_ID+"'>更新<a>" ;
		           	}}
		         ],
		         ds:{type:"url",content:contextPath+"/amazongrid/productFeedHistory/"+accountId},
				 limit:10,
				 pageSizes:[10,20,30,40],
				 height:250,
				 title:"价格更新历史",
				 indexColumn:false,
				 querys:{accountId:accountId,sqlId:"sql_price_import_log"},
				 loadMsg:"数据加载中，请稍候......"
			}) ;
		}) ;
		

   </script>

</head>
<body>
	
	<div class="grid-content" style="width:98%;">
	
	</div>
</html>
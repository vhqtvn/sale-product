<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>View Track</title>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
    <meta http-equiv="pragma" content="no-cache"/>
	<meta http-equiv="cache-control" content="no-cache"/>

   <?php
		echo $this->Html->meta('icon');
		echo $this->Html->css('../js/grid/jquery.llygrid');
		echo $this->Html->css('default/style');
		echo $this->Html->css('../js/tab/jquery.ui.tabs');

		echo $this->Html->script('jquery');
		echo $this->Html->script('common');
		echo $this->Html->script('jquery-ui');
		echo $this->Html->script('jquery.json');
		echo $this->Html->script('grid/jquery.llygrid');
		echo $this->Html->script('grid/query');
		echo $this->Html->script('tab/jquery.ui.tabs');
	?>
  
   <script type="text/javascript">
		
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

		$(function(){
			$(".grid-content").llygrid({
				columns:[
		           	{align:"center",key:"DETAIL_TYPE",label:"售后类型",width:"20%",forzen:false,align:"left",format:{type:"json",
		           		content:{1:"评价",2:"品质",3:"物流",4:"库存",5:"促销"}}},
		           	{align:"center",key:"MEMO",label:"备注",width:"30%",forzen:false,align:"left"},
		           	{align:"center",key:"RESOLVER",label:"解决方案",width:"30%"},
		           	{align:"center",key:"ACT_TIME",label:"时间",width:"30%"},
		           	{align:"center",key:"USERNAME",label:"操作用户",width:"20%"}
		         ],
		         ds:{type:"url",content:"/saleProduct/index.php/grid/query"},
				 limit:20,
				 pageSizes:[10,20,30,40],
				 height:350,
				 autoWidth:true,
				 title:"",
				 querys:{sqlId:"sql_order_aftermarket_list",orderId:'<?php echo $orderId?>',orderItemId:'<?php echo $orderItemId?>'},
				 loadMsg:"数据加载中，请稍候......"
			}) ;
	
			
			$(".grid-content2").llygrid({
				columns:[
		           	{align:"center",key:"STATUS",label:"变更状态",width:"20%",forzen:false,align:"left"},
		           	{align:"center",key:"MESSAGE",label:"说明",width:"30%",forzen:false,align:"left"},
		           	{align:"center",key:"ACT_TIME",label:"时间",width:"30%"},
		           	{align:"center",key:"USERNAME",label:"操作用户",width:"20%"}
		         ],
		         ds:{type:"url",content:"/saleProduct/index.php/grid/query"},
				 limit:20,
				 pageSizes:[10,20,30,40],
				 height:350,
				 title:"",
				 querys:{sqlId:"sql_order_track_list",orderId:'<?php echo $orderId?>',orderItemId:'<?php echo $orderItemId?>'},
				 loadMsg:"数据加载中，请稍候......"
			}) ;
		}) ;
		
		$(function(){
			var tab = $('#details_tab').tabs( {
				tabs:[
					{label:'售后管理列表',content:"tab-content"},
					{label:'操作轨迹列表',content:"tab-content2"}
				] ,
				//height:'500px',
				select:function(event,ui){
					var index = ui.index ;
					renderAction(index);
					
				}
			} ) ;
		}) ;
				
	function renderAction(index){
		if(index == 0){
			$(".grid-content").llygrid("reload") ;
		}else if(index == 1){
			$(".grid-content2").llygrid("reload") ;
		}
	}
   </script>

</head>
<body>
	<div id="details_tab">
	</div>
	<div class="grid-content" id="tab-content">
	</div>
	
	<div class="grid-content2" id="tab-content2">
	</div>
</body>
</html>

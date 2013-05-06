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
		echo $this->Html->css('default/style');

		echo $this->Html->script('jquery');
		echo $this->Html->script('common');
		echo $this->Html->script('jquery.json');
		echo $this->Html->script('grid/jquery.llygrid');
		echo $this->Html->css('../js/grid/jquery.llygrid');
		
		$currentDate = date("Y-m-d H:i:s");
	?>

   <script>
   
   
   		var accountId = '<?php echo $accountId;?>' ;
   		var categoryId = '<?php echo $categoryId;?>' ;
   		if(!categoryId || categoryId == 'null')
   			categoryId = "" ;
   		var isRunning = false ;
   			
		$(function(){
			$(".current-cateogry").html( window.parent.currentCategoryName ).css("font-weight","bold") ;
			
			$(".stepall").click(function(){//发
				$(this).html("正在处理中......").attr("disabled",true) ;
				$.ajax({
					type:"post",
					url:contextPath+"/gatherCategory/doGather/"+accountId+"/"+ categoryId,
					data:{},
					cache:false,
					dataType:"text",
					success:function(result,status,xhr){
						//window.location.reload() ;
					}
				});
			}) ;
			
			var gridConfig = {
					columns:[
			           	{align:"center",key:"START_TIME",label:"开始时间",width:"8%"},
			           	{align:"center",key:"END_TIME",label:"结束时间",width:"8%"},
			           	{align:"center",key:"END_TIME",label:"耗时",width:"6%",format:function(val,record){
			           		if(!record.END_TIME){
			           			isRunning = true ;
			           		}
			           		
			           		var startTime   = record.START_TIME ;
			           		var currentDate = record.END_TIME||'<?php echo $currentDate;?>' ;
			           		var cd=Date.parse(currentDate.replace(/-/g,"/"));
			            	var st=Date.parse(startTime.replace(/-/g,"/"));
			            	var waste = (cd - st)/(3600*1000)  ;
			            	
			            	var hours = Math.round(waste*100)/100 ;
			            	return "<strong>"+hours+"小时</strong>" ;
			           	}},
			           	{align:"center",key:"MESSAGE",label:'进度',width:"6%"},
			            {align:"center",key:"FORCE_STOP",label:"状态",width:"6%",format:function(val , record){
			            	if(record.END_TIME) return "执行结束" ;
			            	if(val == 1) return "等待中止..." ;
			            	return "运行中..." ;
			            }},
			            {align:"center",key:"FEED_PRICE",label:'操作',width:"6%",format:function(val , record){
			            	if(record.END_TIME) return "" ;
			            	
			            	var currentDate = '<?php echo $currentDate;?>' ;
			            	var startTime   = record.START_TIME ;
			            	
			            	var cd=Date.parse(currentDate.replace(/-/g,"/"));
			            	var st=Date.parse(startTime.replace(/-/g,"/"));
			            	var waste = (cd - st)/(3600*1000)  ;
			            	
			            	if(waste >= 2){
			            		waste = Math.round(waste) ;
			            		return "<div style='white-space:normal;margin:0px;padding:2px;;' class='alert alert-error'>该任务已经执行大约"+waste+"个小时，任务可能已经执行结束或发生异常</div><button taskingId='"+record.ID+"' class='btn btn-mini btn-danger'>删除</button>" ;
			            	}
			            	
			            	if(record.FORCE_STOP == 1){
			            		return "" ;
			            	}
			            	
			            	return "<button taskingId='"+record.ID+"' class='btn btn-mini'>中止</button>" ;
			            }},
			            {align:"center",key:"ID",label:"详细日志",width:"3%",format:function(val,record){
			            	return "<a href='#' taskId='"+val+"'>查看</a>" ;
			            }}
			         ],
			         ds:{type:"url",content:contextPath+"/grid/query/"},
					 limit:15,
					 pageSizes:[15,20,30,40],
					 height:240,
					 title:"",
					 autoWidth:true,
					 indexColumn:false,
					 querys:{accountId:accountId,categoryId:categoryId,sqlId:"sql_gather_category_task"},
					 loadMsg:"数据加载中，请稍候......",
					 loadAfter:function(){
						if(isRunning){
							$(".gather-btn").attr("disabled","disabled").addClass("disabled").html("获取进行中") ;
						}else{
							$(".gather-btn").removeAttr("disabled").removeClass("disabled").html("获取&营销策略执行") ;
						}
					 }
				} ;
	     
			$(".grid-content").llygrid(gridConfig) ;
			
			$("[taskingId]").live('click',function(){
				$taskId = $(this).attr("taskingId") ;
				var url = contextPath+"/tasking/stop/"+ $taskId ;
				if($(this).hasClass("btn-danger")){
					url = contextPath+"/tasking/stop/"+ $taskId+"/1" ;
				}
				
	 			$.ajax({
					type:"post",
					url:url,
					data:{},
					cache:false,
					dataType:"text",
					success:function(result,status,xhr){
						window.location.reload() ;
					}
				}); 
	 		}) ;	
	 		
	 		$("[taskId]").live("click",function(){
				var taskId = $(this).attr("taskId") ;
				openCenterWindow(contextPath+"/log/taskLog/"+taskId,600,480) ;
				return false ;
			}) ;
			
			window.setInterval(function(){
				$(".grid-content").llygrid("reload") ;
			},20000) ;
		}) ;
		
   </script>
	<?php
		$account = $account[0]['sc_amazon_account'] ;
	?>
</head>
<body>
<form id="personForm" action="#" data-widget="validator,ajaxform">
	<div style="margin:5px 3px;padding:10px;" class="alert alert-info">
		当前获取分类为：<span class="current-cateogry"></span>
		
		&nbsp;&nbsp;&nbsp;&nbsp;  当前时间：<?php echo  date("Y-m-d H:i:s");?>
	</div>
	<table class="table table-bordered">
		<caption>获取&营销策略执行（集中操作）</caption>
		<tr>
			<td style="width:150px;">获取&营销策略执行</td>
			<td style="height:auto;">
			<div>
				<button disabled class="stepall btn btn-primary gather-btn">获取进行中</button> 
			</div>
			</td>
		</tr>
	</table>
	<div class="grid-content" style="width:99%;"></div>
</form>
</html>
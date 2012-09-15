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
		         ds:{type:"url",content:"/saleProduct/index.php/amazongrid/productFeedHistory/"+accountId},
				 limit:10,
				 pageSizes:[10,20,30,40],
				 height:250,
				 title:"价格更新历史",
				 indexColumn:false,
				 querys:{name:"hello",name2:"world"},
				 loadMsg:"数据加载中，请稍候......"
			}) ;
		}) ;
		
		$(".update-state").live("click",function(){
			$(this).addClass("disabled").html("状态获取中.....") ;
			var feedId = $(this).attr("feedSubmissionId") ;
			$.ajax({
				type:"post",
				url:"/saleProduct/index.php/amazon/getFeedSubmissionResult/"+accountId+"/"+feedId,
				data:{},
				cache:false,
				dataType:"text",
				success:function(result,status,xhr){
					window.location.reload() ;
				}
			}); 
			return false ;
		});
		
		function uploadSuccess(){
			window.location.reload() ;
		}
   </script>

</head>
<body>
	<form action="/saleProduct/index.php/amazonaccount/doUploadAmazonPrice" method="post" target="form-target" enctype="multipart/form-data">
	<table class="table table-bordered">
		<caption>价格更新</caption>
		<tr>
			
			  	<input name="accountId" value='<?php echo $accountId ;?>' type="hidden"/>
			    <td style="width:150px;">价格文件导入</td>
				<td>
					<input name="priceFile" type="file"/>
					<input type="submit" class="btn btn-primary" value="导入">
			 	</td>
		</tr>
		<tr>
			    <td colspan=2 class="alert-info">
			    	文件格式：sku,price作为一行，如: 20998,10.12 ,不需要文件头
			 	</td>
		</tr>
	</table>
	 </form>
	 <iframe style="width:0; height:0; border:0;display:none;" name="form-target"></iframe>
	<div class="grid-content" style="width:98%;">
	
	</div>
</html>
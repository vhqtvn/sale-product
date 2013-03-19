<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
   <?php echo $this->Html->charset(); ?>
    <title>产品同步操作</title>
    <meta http-equiv="pragma" content="no-cache"/>
	<meta http-equiv="cache-control" content="no-cache"/>

   <?php
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
   var type = '<?php echo $type;?>' ;
   var flag = "" ;
   if(type == 'active'){
   	 flag = 'Active' ;
   }
   
   		var accountId = '<?php echo $id;?>' ;
		$(function(){
			$(".step1,.step2,.step3").attr("disabled",true).hide() ;
			
			var sqlId = "sql_product_asyn_history" ;
			if(type == 'active'){
		   	 sqlId = "sql_product_active_asyn_history" ;
		   }
			
			$(".grid-content").llygrid({
				columns:[
					{align:"center",key:"ID",label:"编号", width:"5%"},
					{align:"left",key:"CREATE_TIME",label:"同步时间",width:"30%",forzen:false},
		           	{align:"left",key:"USERNAME",label:"操作用户",width:"30%",forzen:false},
		           	{align:"left",key:"REPORT_ID",label:"REPORT_ID",width:"15%"}
		          //{align:"center",key:"TYPE",label:"类型",width:"10%"}
					
		         ],
		         ds:{type:"url",content:"/saleProduct/index.php/grid/query"},
				 limit:10,
				 pageSizes:[10,20,30,40],
				 height:250,
				 title:"同步历史列表",
				 indexColumn:false,
				 querys:{sqlId:sqlId,accountId:accountId},
				 loadMsg:"数据加载中，请稍候......"
			}) ;
			
			getStatus() ;
			
			$(".step1").click(function(){//发
				$(this).html("请求处理中.....").attr("diasbled",true) ;
				$.ajax({
					type:"post",
					url:"/saleProduct/index.php/amazon/getProduct"+flag+"Report1/"+accountId,
					data:{},
					cache:false,
					dataType:"text",
					success:function(result,status,xhr){
						window.location.reload() ;
					}
				}); 
			}) ;
			
			$(".step2").click(function(){//发
				$(this).html("请求处理中.....").attr("diasbled",true) ;
				$.ajax({
					type:"post",
					url:"/saleProduct/index.php/amazon/getProduct"+flag+"Report2/"+accountId,
					data:{},
					cache:false,
					dataType:"text",
					success:function(result,status,xhr){
						window.location.reload() ;
					}
				}); 
			}) ;
			
			$(".step3").click(function(){//发
				$(this).html("请求处理中.....").attr("diasbled",true) ;
				$.ajax({
					type:"post",
					url:"/saleProduct/index.php/amazon/getProduct"+flag+"Report3/"+accountId,
					data:{},
					cache:false,
					dataType:"text",
					success:function(result,status,xhr){
						window.location.reload() ;
					}
				}); 
			}) ;
			
			
		}) ;
		
		function getStatus(){
			$.ajax({
				type:"post",
				url:"/saleProduct/index.php/amazon/getProduct"+flag+"Asyns/"+accountId,
				data:{},
				cache:false,
				dataType:"text",
				success:function(result,status,xhr){
					//alert(result);
					$(".step1,.step2,.step3").attr("disabled",true).hide() ;
					
					eval("var result = "+result) ;
					if( result.length <= 0 ){//未开始采集
						$(".step1").removeAttr("disabled").show() ;
					}else{
						var record = result[0]['sc_amazon_account_asyn'] ;
						if( !record.REPORT_REQUEST_ID ){
							$(".step1").removeAttr("disabled").show() ;
						}else if( !record.REPORT_ID ){
							$(".step2").removeAttr("disabled").show() ;
						}else if( record.REPORT_ID && !record.STATUS ){
							$(".step3").removeAttr("disabled").show() ;
						}else if( record.STATUS ){
							$(".step1").removeAttr("disabled").show() ;
						}
					}
				}
			}); 
		}
   </script>

</head>
<body>
<form id="personForm" action="#" data-widget="validator,ajaxform">
	<table class="table table-bordered">
		<caption>产品同步操作</caption>
		<tr>
			<td style="width:150px;">1、发送产品同步请求 </td><td><button disabled=true class="step1 btn btn-primary">发送产品同步请求</button> </td>
		</tr>
		<tr>
			<td>2、获取同步状态</td><td><button disabled=true  class="step2 btn btn-primary">获取同步状态</button></td>
		</tr>
		<tr>
			<td>3、获取产品数据</td><td><button disabled=true  class="step3 btn btn-primary">获取产品数据</button></td>
		</tr>
	</table>
	
	<div class="grid-content" style="width:98%;">
	
	</div>
</form>
</html>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>供应商列表</title>
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
		
		$user = $this->Session->read("product.sale.user") ;
		$group=  $user["GROUP_CODE"] ;
		
		$sku = $params['arg1'] ;
	?>
  
   <script type="text/javascript">
   
   var taskId = '' ;
   

	$(function(){
			$(".grid-content").llygrid({
				columns:[
					{align:"center",key:"ID",label:"操作",forzen:true, width:"10%",format:function(val,record){
							var html = [] ;
							html.push("<a href='#' class='action-update' val='"+val+"'>修改</a>&nbsp;") ;
							<?php if($group == 'purchasing_manager' || $group == 'general_manager' ){
							?>
								html.push("<a href='#' class='action-del' val='"+val+"'>删除</a>&nbsp;") ;
							<?php	
							} ?>
							
							html.push("<a href='#' class='action-view' val='"+val+"'>查看</a>&nbsp;") ;
							return html.join("") ;
					}},
		           	{align:"center",key:"CODE",label:"代码", width:"8%"},
		           	{align:"center",key:"NAME",label:"名称", width:"15%"},
		        	{align:"left",key:"EVALUATE",label:"评价",width:"6%",format:{type:'json',content:{1:'不推荐',2:'备选',3:'推荐',4:'优先推荐'}}},
		        	{align:"center",key:"REAL_QUOTE_PRICE",label:"最近交易价格",width:"10%"},
		        	{align:"center",key:"REAL_SHIP_FEE",label:"运费支付",width:"10%",format:{type:'json',content:{'by':"卖家承担",'hdfk':'到付','mjds':"卖家代收"}}},
			        {align:"center",key:"REAL_SHIP_FEE",label:"运费",width:"10%"},
		           	{align:"center",key:"REAL_PURCHASE_DATE",label:"最近交易时间",width:"13%"},
		           	{align:"center",key:"CONTACTOR",label:"联系人",width:"6%"},
		           	{align:"center",key:"PHONE",label:"联系电话",width:"8%"},
		           	{align:"center",key:"MOBILE",label:"手机",width:"8%"},
		           	{align:"center",key:"FAX",label:"传真",width:"8%"},
		           	{align:"center",key:"EMAIL",label:"EMAIL",width:"8%"},
		           	{align:"center",key:"ZIP_CODE",label:"邮编",width:"6%"},
		           	{align:"center",key:"QQ",label:"QQ/MSN/Skype",width:"6%"},
		           	{align:"center",key:"URL",label:"网址",width:"10%",format:function(val){
		           		if(!val) return "" ;
		           		return "<a href='"+val+"' target='_blank'>"+val+"</a>" ;
		           	}},
		           	{align:"center",key:"USERNAME",label:"创建人",width:"6%"},
		           	{align:"center",key:"CREATE_TIME",label:"创建时间",width:"10%"}
		         ],
		         ds:{type:"url",content:contextPath+"/grid/query/"},
				 limit:30,
				 pageSizes:[10,20,30,40],
				 height:function(){
						return $(window).height() - 130 ;
				 },
				 title:"产品列表",
				 indexColumn:true,
				 querys:{sqlId:"sql_supplier_list",realSku:'<?php echo $sku;?>'},
				 loadMsg:"数据加载中，请稍候......"
			}) ;
			
			$(".action-update").live("click",function(){
				var id = $(this).attr("val") ;
				openCenterWindow(contextPath+"/supplier/add/"+id,800,600) ;
			});
			
			$(".action-del").live("click",function(){
				var id = $(this).attr("val") ;
				if(window.confirm("确认删除吗？")){
					$.ajax({
						type:"post",
						url:contextPath+"/supplier/del/"+id,
						data:{},
						cache:false,
						dataType:"text",
						success:function(result,status,xhr){
							window.location.reload();
						}
					}); 
				}
			});
			
			$(".action-view").live("click",function(){
				var id = $(this).attr("val") ;
				viewSupplier(id) ;
			})
			
			
			$(".query-btn").click(function(){
				var name = $("[name='name']").val() ;
				var querys = {} ;
				if(name){
					querys.name = name ;
				}
				$(".grid-content").llygrid("reload",querys) ;	
			}) ;
			
			$(".add-btn").click(function(){
				openCenterWindow(contextPath+"/supplier/add/sku/<?php echo $sku;?>",800,600,function(){
					window.location.reload() ;
				}) ;
			}) ;

			$(".supplier-select").click(function(){
				openCenterWindow(contextPath+"/supplier/listsSelectBySku/<?php echo $sku;?>",800,600,function(){
					window.location.reload() ;
				}) ;
			}) ;

			
   	 });
   </script>
   
   <style>
   		*{
   			font:12px "微软雅黑";
   		}
   </style>

</head>
<body>
<div class="toolbar toolbar-auto">
		<table>
			<tr>
				<th>
					供应商名称:
				</th>
				<td>
					<input type="text" name="name"/>
				</td>								
				<td class="toolbar-btns">
					<button class="query-btn btn btn-primary">查询</button>
					<button class="add-btn btn">添加供应商</button>
					<button class="supplier-select btn">选择产品供应商</button>
				</td>
			</tr>						
		</table>					

	</div>
	<div class="grid-content"></div>
</body>
</html>

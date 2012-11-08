<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>供应商列表</title>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
    <meta http-equiv="pragma" content="no-cache"/>
	<meta http-equiv="cache-control" content="no-cache"/>

   <?php
		echo $this->Html->meta('icon');
		echo $this->Html->css('../js/grid/jquery.llygrid');
		echo $this->Html->css('default/style');

		echo $this->Html->script('jquery');
		echo $this->Html->script('common');
		echo $this->Html->script('jquery.json');
		echo $this->Html->script('grid/jquery.llygrid');
		
		$user = $this->Session->read("product.sale.user") ;
		$group=  $user["GROUP_CODE"] ;
	?>
  
   <script type="text/javascript">
   
   var taskId = '' ;
   
   //result.records , result.totalRecord
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
		           	{align:"center",key:"NAME",label:"名称", width:"15%"},
		           	{align:"center",key:"ADDRESS",label:"地址",width:"15%"},
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
		         ds:{type:"url",content:"/saleProduct/index.php/grid/query/"},
				 limit:30,
				 pageSizes:[10,20,30,40],
				 height:350,
				 title:"",
				 indexColumn:true,
				 querys:{sqlId:"sql_saleproduct_supplier_list",realSku:'<?php echo $sku;?>'},
				 loadMsg:"数据加载中，请稍候......"
			}) ;
			
			$(".action-update").live("click",function(){
				var id = $(this).attr("val") ;
				openCenterWindow("/saleProduct/index.php/supplier/add/"+id,800,600) ;
			});
			
			$(".action-del").live("click",function(){
				var id = $(this).attr("val") ;
				if(window.confirm("确认删除吗？")){
					$.ajax({
						type:"post",
						url:"/saleProduct/index.php/supplier/del/"+id,
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
			
			$(".add-btn").click(function(){
				openCenterWindow("/saleProduct/index.php/saleProduct/forward/select_supplier/<?php echo $sku;?>",800,600) ;
			}) ;
   	 });
   	 
   	 function doSelectedValue(suppliers){
   	 	$(suppliers).each(function(index,item){
   	 		var params = {} ;
   	 		params.realSku = '<?php echo $sku;?>' ;
   	 		params.supplierId = item ;
   	 		params.sqlId = "sql_saleproduct_supplier_save" ;
   	 		$.ajax({
				type:"post",
				url:"/saleProduct/index.php/form/ajaxSave",
				data:params,
				cache:false,
				dataType:"text",
				success:function(result,status,xhr){
					$(".grid-content").llygrid("reload") ;
				}
			}); 
   	 	}) ;
   	 }
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
				<td class="toolbar-btns">
					<button class="add-btn btn btn-primary">选择供应商</button>
				</td>
			</tr>						
		</table>					

	</div>
	<div class="grid-content"></div>
</body>
</html>

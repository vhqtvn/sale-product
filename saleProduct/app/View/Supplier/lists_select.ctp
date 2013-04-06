<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>商品供应商</title>
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
		
	?>
  
   <script type="text/javascript">
   
   var asin = '<?php echo $asin;?>' ;

	$(function(){
			$(".grid-content").llygrid({
				columns:[
					{align:"center",key:"ID",label:"名称", width:"5%",format:{type:'checkbox',callback:function(record){
						var checked = $(this).attr("checked");
						if(checked){
							$(".product-list ul").append("<li class='alert alert-success' supplier='"+record.ID+"'>"+record.NAME+"</li>") ;
						}else{
							$(".product-list ul").find("[supplier='"+record.ID+"']").remove() ;
						}
					}}},
		           	{align:"center",key:"NAME",label:"名称", width:"15%"},
		           	{align:"center",key:"ADDRESS",label:"地址",width:"15%"},
		           	{align:"center",key:"CONTACTOR",label:"联系人",width:"8%"},
		           	{align:"center",key:"PHOME",label:"联系电话",width:"12%"},
		           	{align:"center",key:"EMAIL",label:"EMAIL",width:"10%"},
		           	{align:"center",key:"ZIP_CODE",label:"邮编",width:"10%"},
		           	{align:"center",key:"URL",label:"网站地址",width:"15%",format:function(val){
		           		if(!val) return "" ;
		           		return "<a href='"+val+"' target='_blank'>"+val+"</a>" ;
		           	}}
		         ],
		         ds:{type:"url",content:contextPath+"/supplier/grid/"},
				 limit:30,
				 pageSizes:[10,20,30,40],
				 height:270,
				 title:"供应商列表",
				 indexColumn:true,
				 querys:{},
				 loadMsg:"数据加载中，请稍候......",
				 loadAfter:function(){
				 	$(".grid-checkbox").each(function(){
						var val = $(this).attr("value") ;
						if( $(".product-list ul li[supplier='"+val+"']").length ){
							$(this).attr("checked",true) ;
						}
					}) ;
				 }
			}) ;
			
			$(".action-update").live("click",function(){
				var id = $(this).attr("val") ;
				openCenterWindow(contextPath+"/supplier/add/"+id,400,300) ;
			})
			
			$(".query-btn").click(function(){
				var name = $("[name='name']").val() ;
				var querys = {} ;
				if(name){
					querys.name = name ;
				}
				$(".grid-content").llygrid("reload",querys) ;	
			}) ;
			
			$(".select-btn").click(function(){
				openCenterWindow(contextPath+"/supplier/add/asin/"+asin , 800,600) ;
			}) ;

			$(".add-btn").click(function(){
				openCenterWindow(contextPath+"/supplier/add/asin/"+asin , 800,600) ;
			}) ;
			
			$(".save-product-supplier").click(function(){
				var suppliers = [] ;
				$(".product-list li").each(function(){
					suppliers.push( $(this).attr("supplier") ) ;
				}) ;
				
				$.ajax({
					type:"post",
					url:contextPath+"/supplier/saveProductSupplier/"+asin,
					data:{
						asin:asin,
						suppliers:suppliers.join(",")
					},
					cache:false,
					dataType:"text",
					success:function(result,status,xhr){
						window.opener.location.reload() ;
						window.close() ;
					}
				}); 
			}) ;
   	 });
   </script>
   
    <style>
   		*{
   			font:12px "微软雅黑";
   		}
   		
   		.product-list ul{
   			list-style:none;
   			margin:3px;padding:0px;
   			display:block;
   			width:100%;
   		}
   		
   		.product-list ul li{
   			float:left;
   			margin:2px 5px;
   			padding:5px 10px;
   		}
   </style>

</head>
<body>
	<div class="toolbar toolbar-auto query-bar">
		<table>
			<tr>
				<th>
					供应商名称:
				</th>
				<td>
					<input type="text" name="name"/>
				</td>							
				<td class="toolbar-btns">
					<button class="query-btn btn">查询</button>
					<button class="add-btn btn btn-primary">添加供应商</button>
				</td>
			</tr>						
		</table>
	</div>
	
	<div class="grid-content">
	</div>
	<div class="product-list" style="border:1px solid #CCC;width:100%;height:100px;margin:10px 0px;">
     		<ul>
     			<?php
     				foreach($suppliers as $supplier){
     					echo "<li supplier='".$supplier['sc_supplier']['ID']."' class='alert alert-success'>".$supplier['sc_supplier']['NAME']."</li>" ;
     				} ;
     			?>
     		</ul>
     </div>
     
     <div class="panel-foot">
						<div class="form-actions col2">
							
							<button class="save-product-supplier btn-primary btn">保存产品供应商</button>
							
							<button onclick="window.close();" class="btn">关&nbsp;闭</button>
						</div>
					</div>
</body>
</html>

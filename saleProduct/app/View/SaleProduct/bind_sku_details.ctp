<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>营销产品列表</title>
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
	
   var accountId = '<?php echo $accountId ;?>' ;

	var currentAccountId = accountId ;
	var currentCategoryId = "" ;
	var currentCategoryText = "" ;
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
		$(".btn").click(function(){
			if(window.confirm("确认添加SKU吗？")){
				$.ajax({
						type:"post",
						url:"/saleProduct/index.php/saleProduct/saveSkuToRealProduct/" ,
						data:{accountId:accountId,skus:$("#skus").val(),realSku:'<?php echo $sku;?>'},
						cache:false,
						dataType:"text",
						success:function(result,status,xhr){
							window.location.reload() ;
						}
				});
			}
		}) ;
		
		var gridConfig = {
					columns:[
						{align:"center",key:"SKU",label:"SKU",width:"8%"}
			         ],
			         ds:{type:"url",content:"/saleProduct/index.php/grid/query"},
					 limit:15,
					 pageSizes:[15,20,30,40],
					 height:320,
					 autoWidth:true,
					 title:"",
					 indexColumn:false,
					 querys:{accountId:accountId,realSku:'<?php echo $sku;?>',sqlId:"sql_saleproduct_selelctsku_list"},
					 loadMsg:"数据加载中，请稍候......",
					 loadAfter:function(){
						//$(".country-area-flag").parents("tr").css("background","#EEE") ;
						//$(".country-area-flag").parents("tr").css("background","#EEE") ;
						//$(".country-area-flag").parents("tr").css("background","#EEE") ;
					 }
				} ;
	       
			setTimeout(function(){
				$(".grid-content").llygrid(gridConfig) ;
			},200) ;
   	 });
   </script>
   
</head>
<body style="magin:0px;padding:0px;">
	<table class="form-table col2" style="width:100%;">
			<caption>添加产品SKU(sku1,sku2,sku3..)</caption>
			<tbody>										   
				<tr>
					<th>产品SKU：</th><td><textarea id="skus" style="width:90%;height:100px"></textarea></td>
				</tr>
				
				<tr>
					<th colspan=2>
						<button class="btn btn-primary">保存</button>
					</th>
				</tr>
			</tbody>
		</table>
		
		<div class="grid-content" style="width:99%;">
		</div>	
	
</body>
</html>

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
		
		$(".del-action").live("click",function(){
			var record = $(this).parents("tr:first").data("record") ;
			if(window.confirm("确认删除引用吗？")){
				$.ajax({
						type:"post",
						url:"/saleProduct/index.php/saleProduct/deleteRelProduct/" ,
						data:{accountId:record.REL_ACCOUNT_ID,sku:record.REL_SKU,realSku:record.REL_REAL_SKU},
						cache:false,
						dataType:"text",
						success:function(result,status,xhr){
							$(".grid-content").llygrid("reload") ;
						}
				});
			}
			return false ;
		}) ;
		
		var gridConfig = {
					columns:[
						{align:"center",key:"ID",label:"操作",forzen:false,width:"30",format:function(val,record){
							var html = [] ;
							html.push('<a href="#" title="删除" class="del-action"  val="'+val+'"><?php echo $this->Html->image('delete.gif') ?></a>&nbsp;') ;
							return html.join("") ;
							
						}},
						{align:"center",key:"SKU",label:"SKU",width:"8%",format:function(val,record){
							return val||record.REL_SKU ;
						}},
			           	{align:"left",key:"ASIN",label:"ASIN", width:"90",format:function(val,record){
			           		var memo = record.MEMO||"" ;
			           		return "<a href='#' class='product-detail' title='"+memo+"' asin='"+val+"' sku='"+record.SKU+"'>"+(val||'')+"</a>" ;
			           	}},
			           	{align:"center",key:"LOCAL_URL",label:"Image",width:"6%",forzen:false,align:"left",format:function(val,record){
			           		if(val){
			           			val = val.replace(/%/g,'%25') ;
			           		}else{
			           			return "" ;
			           		}
			           		return "<img src='/saleProduct/"+val+"' onclick='showImg(this)' style='width:25px;height:25px;'>" ;
			           	}},
			           	{align:"center",key:"TITLE",label:"TITLE",width:"10%",forzen:false,align:"left",format:function(val,record){
			           		return "<a href='http://www.amazon.com/gp/offer-listing/"+record.ASIN+"' target='_blank'>"+(val||'')+"</a>" ;
			           	}}
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

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>营销产品列表</title>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
    <meta http-equiv="pragma" content="no-cache"/>
	<meta http-equiv="cache-control" content="no-cache"/>
    <?php
    include_once ('config/config.php');
    
		echo $this->Html->meta('icon');
		echo $this->Html->css('../js/grid/jquery.llygrid');
		echo $this->Html->css('../js/layout/jquery.layout');
		echo $this->Html->css('../js/tree/jquery.tree');
		
		echo $this->Html->css('default/style');

		echo $this->Html->script('jquery');
		echo $this->Html->script('common');
		echo $this->Html->script('jquery.json');
		echo $this->Html->script('grid/jquery.llygrid');
		echo $this->Html->script('layout/jquery.layout');
		echo $this->Html->script('tree/jquery.tree');
		
		$user = $this->Session->read("product.sale.user") ;
		$security  = ClassRegistry::init("Security") ;
		$group=  $user["GROUP_CODE"] ;
		$loginId = $user['LOGIN_ID'] ;
		$limitPricePermissin 		= $security->hasPermission($loginId , 'set_accountproduct_limit_price') ;//设置限价权限
	?>
	
    <script type="text/javascript">


	var currentAccountId = '' ;
	var currentCategoryId = "" ;
	var currentCategoryText = "" ;
	$(function(){
		var isView = window.parent.action == 'view' ;
		
		if( isView ){
			$(".query-bar").hide();
		}
	       var gridConfig = {
					columns:[
						/*{align:"center",key:"SKU",label:"操作",width:"6%",format:{type:"checkbox",render:function(record){
								if(record.checked >=1){
									$(this).attr("checked",true) ;
								}
						}}},*/
						{align:"center",key:"CHANNEL_NAME",label:"ACCONT",width:"8%"},
						{align:"center",key:"SKU",label:"SKU",width:"8%",format:function(val,record){
							return val||record.REL_SKU ;
						}},
			           	{align:"left",key:"ASIN",label:"ASIN", width:"90",format:function(val,record){
			           		return "<a href='#' offer-listing='"+val+"'>"+(val||'')+"</a>" ;
			           	}},
			           	{align:"center",key:"P_LOCAL_URL",label:"Image",width:"6%",forzen:false,align:"left",format:{type:'img'}},
			           	{align:"center",key:"P_TITLE",label:"名称",width:"10%",forzen:false,align:"left"},
			           	{align:"center",key:"FULFILLMENT_CHANNEL",label:"销售渠道",width:"8%"},
			           	{align:"center",key:"ITEM_CONDITION",label:"使用程度",width:"8%",format:function(val){
			           		if(val == 1) return "Used" ;
			           		if(val == 11) return 'New' ;
			           		return '' ;
			           	}},
			        	{align:"center",key:"SALES_FOR_THELAST14DAYS",label:'14天销量',width:"6%"},
			        	{align:"center",key:"SALES_FOR_THELAST30DAYS",label:'30天销量',width:"6%"},
			           	{align:"center",key:"LOWEST_FBA_PRICE",label:"FBA最低价",width:"8%"},
			           	{align:"center",key:"FBA_PRICE_ARRAY",label:"FBA卖家价格",width:"8%"},
			           	{align:"center",key:"LIMIT_PRICE",label:"最低限价",width:"8%",format:function(val,record){
                        	return "<input type='text' value='"+(val||"")+"' style='width:50px;'>"  ;           
				        }},
			           	{align:"center",key:"FBA_PRICE_LAST_UPDATE_TIME",label:"更新时间",width:"8%"},
			           	
			         ],
			         ds:{type:"url",content:contextPath+"/grid/query"},
					 limit:10,
					 pageSizes:[10,15,20,30,40],
					 height:350,
					 autoWidth:true,
					 title:"",
					 indexColumn:false,
					 querys:{id:'<?php echo $id;?>',sqlId:"sql_saleproduct_channel_list"},
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

			$(".query-btn").click(function(){
				$(".grid-content").llygrid("reload",getQueryCondition(),
					{ds:{type:"url",content:contextPath+"/grid/query/"}}) ;	
			}) ;
			
			function getQueryCondition(){
				var asin = $("[name='asin']").val() ;
				var title = $("[name='title']").val() ;
				var querys = {} ;
				querys.reply = 0 ;
				querys.asin = asin ;
				querys.title = title ;
				querys.sku = $("[name='sku']").val() ;
				return querys ;
			}

			$(".save-limit").click(function(){

				    var limitPrices = [] ;
					$(".lly-grid-content").find("tr").each(function(){
							var record = $(this).data("record") ;
							if(!record) return ;
							var limitPrice = $(this).find("input").val() ;
							if( limitPrice &&  limitPrice >0 ){
								limitPrices.push( { limitPrice: limitPrice, accountId:record.ACCOUNT_ID,sku:record.SKU,asin:record.ASIN }) ;
							}else{
								limitPrices.push( { limitPrice: 0, accountId:record.ACCOUNT_ID,sku:record.SKU,asin:record.ASIN }) ;
							}
					});
					
					$.dataservice("model:SaleProduct.saveLimitPrices",{limitPrices:limitPrices},function(result){
						//刷新树
						$(".grid-content").llygrid("reload",{},true) ;
					});
			}) ;

			$(".add-channel-product").click(function(){
				openCenterWindow(contextPath+"/saleProduct/bindProduct/<?php echo $id;?>/1",1000,640) ;
			}) ;
			
			$(".add-sku").click(function(){
				openCenterWindow(contextPath+"/saleProduct/bindProduct/<?php echo $id;?>/2",1000,640) ;
			}) ;
   	 });
   </script>
   
   <style style="text/css">
   		*{
   			font:12px "微软雅黑";
   		}
   	
   		.lly-grid-cell-input{
   		}
   		
   		.query-bar ul{
   			display:block;
   			margin_bottom:5px;
   			height:auto;
   			width:100%;
   		}
   		
   		.query-bar ul li{
   			list-style-type:none;
   			float:left;
   			padding:3px 0px;
   		}
   		
   		.query-bar ul li label{
   			float:left;
   			margin:0px 0px;
   			margin-left:15px;
   		}
   		
   		.query-bar{
   			clear:both;
   		}
   		
   		li select,li input{
   			width:auto;
   			padding:0px;
   		}
   </style>

</head>
<body style="magin:0px;padding:0px;">
			<div class="toolbar toolbar-auto">
				<table style="width:100%;" class="query-table  save-sale-price">
					<tr>
						<td  class=" query-bar" style="width:300px;">
								<button class="btn btn-primary btn-mini add-btn add-channel-product">选择渠道产品</button>
				 				<button class="btn btn-primary btn-mini add-btn add-sku">相关产品SKU</button>
						</td>
						<td>
				 				<?php if($limitPricePermissin){ ?>
				 				<button class="btn btn-danger btn-mini add-btn save-limit">保存限价</button>
				 				<?php  } ?>
						</td>
					</tr>							
				</table>	
			</div>
			<div style="clear:both;height:5px;"></div>
			<div class="grid-content" style="width:99%;">
			</div>
</body>
</html>

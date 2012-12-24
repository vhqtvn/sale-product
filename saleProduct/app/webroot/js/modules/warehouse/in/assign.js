

var currentId = '' ;
$(function(){
	var gridConfig = {
					columns:[
						/*{align:"center",key:"SKU",label:"操作",width:"6%",format:{type:"checkbox",render:function(record){
								if(record.checked >=1){
									$(this).attr("checked",true) ;
								}
						}}},*/
						{align:"center",key:"CHANNEL_NAME",label:"ACCONT",width:"15%"},
						{align:"left",key:"SKU",label:"SKU",width:"15%",format:function(val,record){
							return val||record.REL_SKU ;
						}},
			           	{align:"center",key:"ASSIGN_QUANTITY",label:"库存数量",width:"15%",format:{type:"editor"}},
			           	{align:"left",key:"ASIN",label:"ASIN", width:"12%",format:function(val,record){
			           		var memo = record.MEMO||"" ;
			           		return "<a href='#' class='product-detail' title='"+memo+"' asin='"+val+"' sku='"+record.SKU+"'>"+(val||'')+"</a>" ;
			           	}},
			           	{align:"center",key:"TITLE",label:"TITLE",width:"21%",forzen:false,align:"left",format:function(val,record){
			           		return "<a href='http://www.amazon.com/gp/offer-listing/"+record.ASIN+"' target='_blank'>"+(val||'')+"</a>" ;
			           	}},
			           	{align:"center",key:"ITEM_CONDITION",label:"使用程度",width:"16%",format:function(val,record){
			           		var _ = [] ;
			           		var fc = record['FULFILLMENT_CHANNEL'] ;
			           		fc&&_.push(fc) ;
			           		var isF = record['IS_FM'] ;
			           		isF&&_.push(isF) ;
			           		var ic = '' ;
			           		if(val == 1) ic = "Used" ;
			           		if(val == 11) ic = 'New' ;
			           		ic&&_.push(ic) ;
			           		return _.join(":");
			           	}}
			         ],
			         ds:{type:"url",content:"/saleProduct/index.php/grid/query"},
					 limit:100,
					 pageSizes:[100],
					 height:function(){
					 	return	$(window).height() - 220
					 },
					 autoWidth:false,
					 title:"",
					 indexColumn:false,
					 querys:{id:realProductId,sqlId:"sql_saleproduct_channel_list"},
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
var Page = {
	init: function(){
		$(function(){
			Page.loadDownloadGrid() ;
			Page.loadOrderGrid() ;
			
			$(".download").click(function(){
				Page.download()
			}) ;
			
			$(".toamazon").click(function(){
				Page.asynTn2Amazon() ;
			}) ;
			
			$(".redownload").live("click",function(){
				var row =  $(this).parents("tr:first").data("record") ;
				Page.reDownload(row['ID']) ;
			}) ;
		}) ;
	},
	download:function(){
		window.location.href = "/saleProduct/index.php/order/doDownloadOrder/"+accountId;
	},
	reDownload:function(id){ //重新下载
		window.location.href = "/saleProduct/index.php/order/doDownloadOrder/"+accountId+"/"+id;
		return false ;
	},
	asynTn2Amazon:function(){
	
	},
	loadDownloadGrid:function(){
		$("#picked-grid-content").llygrid({
				 columns:[
		           	{align:"center",key:"NAME",label:"名称",width:"20%",forzen:false,align:"left",format:function(val,record){
		           		if(record.STATUS == 1) return "&nbsp;&nbsp;&nbsp;"+val+"<strong></strong>" ;
		           		return '<a href="#" class="redownload">'+img+'</a>&nbsp;'+val+"<strong></strong>" ;
		           	}}
		         ],
		         ds:{type:"url",content:"/saleProduct/index.php/grid/query"},
				 limit:20,
				 pageSizes:[10,20,30,40],
				 height:function(){
				 	return $(window).height() - $(".toolbar-auto").height() -55 ;
				 },
				 autoWidth:true,
				 title:"",
				 pagerType:"simple",
				 querys:{sqlId:"sql_order_download_list",accountId:accountId},
				 loadMsg:"数据加载中，请稍候......",
				 rowClick:function(rowIndex , rowData){
				 	currentPickId = rowData.ID ;
				 	currentPickName = rowData.NAME ;
				 	$(".grid-content").llygrid("reload",{pickId:rowData.ID});
				 	renderBtn() ;
				 }

			}) ;
	},
	loadOrderGrid:function(){
		var sqlId = "sql_order_can_do_ship" ;
			//拣货单订单列表
			$(".grid-content").llygrid({
				columns:[
					{align:"left",key:"ASIN",label:"ASIN", width:"90",render:function(record){
							if(record.IS_PACKAGE || record.C > 1){
								$(this).find("td").css("background","#EEBBFF") ;
							}
						}
						,format:function(val,record){
			           		var memo = record.MEMO||"" ;
			           		return "<a href='#' class='product-detail' title='"+memo+"' asin='"+val+"' sku='"+record.SKU+"'>"+(val||'')+"</a>" ;
			        }},
			        {align:"left",key:"ORDER_NUMBER",label:"系统货号", width:"10%"},
		           	{align:"left",key:"REAL_SKU",label:"货品SKU", width:"10%"},
			        {align:"left",key:"NAME",label:"货品名称", width:"10%"},
		           	{align:"center",key:"IMAGE_URL",label:"货品图片",width:"4%",format:function(val,record){
		           		if(val){
		           			val = val.replace(/%/g,'%25') ;
		           		}else{
		           			return "" ;
		           		}
		           		return "<img src='/saleProduct/"+val+"' onclick='showImg(this)' style='width:25px;height:25px;'>" ;
		           	}},
		           	{align:"center",key:"ORDER_ID",label:"ORDER_ID", width:"15%"},
		           	{align:"center",key:"ORDER_ITEM_ID",label:"ORDER_ITEM_ID", width:"12%"},
		           	{align:"center",key:"SKU",label:"SKU",sort:true, width:"10%"},
		           	{align:"center",key:"PRODUCT_NAME",label:"PRODUCT_NAME", width:"20%"},
		           	{align:"center",key:"PURCHASE_DATE",label:"PURCHASE_DATE",sort:true, width:"20%"},
		           	{align:"center",key:"PAYMENTS_DATE",label:"PAYMENTS_DATE",sort:true, width:"20%"},
		           	{align:"center",key:"BUYER_EMAIL",label:"BUYER_EMAIL", width:"30%"},
		           	{align:"center",key:"BUYER_NAME",label:"BUYER_NAME", width:"10%"},
		           	{align:"center",key:"BUYER_PHONE_NUMBER",label:"BUYER_PHONE_NUMBER", width:"10%"},
		           	{align:"center",key:"QUANTITY_PURCHASED",label:"QUANTITY_PURCHASED", width:"10%"},
		           	{align:"center",key:"CURRENCY",label:"CURRENCY", width:"10%"},
		           	{align:"center",key:"ITEM_PRICE",label:"ITEM_PRICE", width:"10%"},
		           	{align:"center",key:"ITEM_TAX",label:"ITEM_TAX", width:"10%"}
		         ],
		         ds:{type:"url",content:"/saleProduct/index.php/grid/query/"+accountId},
				 limit:20,
				 pageSizes:[10,20,30,40],
				 height:function(){
				 	return $(window).height() - $(".toolbar-auto").height() -130 ;
				 },
				 title:"",
				 indexColumn:false,
				 querys:{sqlId:sqlId,accountId:accountId},
				 loadMsg:"数据加载中，请稍候......"
			}) ;
	}
} ;

Page.init() ;
$(function(){
	 function formatMoney(val){
		 	val = $.trim(val+"") ;
		 	val = val.replace("$","") ;
		 	return $.trim(val) ;
	 }

	
	//widget init
	var tab = $('#details_tab').tabs( {
		tabs:[
			{label:'基本信息',content:"base-info"},//9
			{label:'关联ASIN',content:"ref-asins"}//,
			//{label:'处理轨迹',content:"product-select-content"}
		] ,
		height:function(){
			return $(window).height() - 135 ;
		}
	} ) ;
	
	$(".grid-content-details").llygrid({
		columns:[
		    {align:"center",key:"SKU",label:"Listing SKU",width:"14%",forzen:false,align:"left"},
           	{align:"left",key:"ASIN",label:"ASIN", width:"11%",format:{type:'asin'}},
           	{align:"center",key:"LOCAL_URL",label:"Image",width:"4%",forzen:false,align:"left",format:{type:'img'}},
            {align:"left",key:"TITLE",label:"TITLE",width:"10%",forzen:false,align:"left",format:{type:'titleListing'}},
            {align:"center",key:"QUANTITY",label:"账号库存",width:"6%",forzen:false,align:"left"},
           	{align:"center",key:"XJ",label:"询价",width:"4%",format:function(val,record){
           		if(val >0 )return "Y" ;
           		return "N" ;
           	}} ,
           {align:"center",key:"FBM_COST",label:"其他成本",group:"FBM",width:"6%",format:function(val,record){
           		return "<a href='' class='cost' type='FBM' asin='"+record.ASIN+"'>"+(val||"")+"</a>" ;
           	},permission:function(){ return $purchase_cost_view; } },
           	{align:"center",key:"FBM_PRICE",label:"最低价",group:"FBM",width:"6%",permission:function(){ return $purchase_cost_view; }},
           	{align:"center",key:"FBM_PRICE",label:"利润额",group:"FBM",width:"6%",format:function(val,record){
           		var lye = parseFloat(formatMoney(record.FBM_PRICE)) 
           					- parseFloat(formatMoney(record.QUOTE_PRICE||0)) -   parseFloat(formatMoney(record.FBM_COST||0)) ;
           		
           		if( !record.QUOTE_PRICE  || record.QUOTE_PRICE == '0' ){
           			return "-" ;
           		}
           		
           		if( !record.FBM_PRICE || record.FBM_PRICE == '0'){
           			return "-" ;
           		}
           		
           		if( !record.FBM_COST || record.FBM_COST == '0'){
           			return "-" ;
           		}
           		
           		if( parseFloat(formatMoney(record.QUOTE_PRICE||0)) +  parseFloat(formatMoney(record.FBM_COST||0)) <= 0 ){
           			return "-" ;
           		}
           		lye = lye.toFixed(2) ;
           		if( lye < 0 ){
           			return "<font color='red'>"+lye+"</font>"
           		}else{
           			return lye ;
           		}
           	},permission:function(){ return $purchase_cost_view; }},
           	{align:"center",key:"FBM_PRICE",label:"利润率",group:"FBM",width:"6%",format:function(val,record){
           		var lye = parseFloat(formatMoney(record.FBM_PRICE)) 
           					- parseFloat(formatMoney(record.QUOTE_PRICE||0)) -   parseFloat(formatMoney(record.FBM_COST||0)) ;
           		
           		if( !record.QUOTE_PRICE  || record.QUOTE_PRICE == '0' ){
           			return "-" ;
           		}
           		
           		if( !record.FBM_PRICE || record.FBM_PRICE == '0'){
           			return "-" ;
           		}
           		
           		
           		if( !record.FBM_COST || record.FBM_COST == '0'){
           			return "-" ;
           		}
           		
           		if( parseFloat(formatMoney(record.QUOTE_PRICE||0)) +  parseFloat(formatMoney(record.FBM_COST||0)) <= 0 ){
           			return "-" ;
           		}
           		
           		var lyl = (lye / ( parseFloat(formatMoney(record.QUOTE_PRICE||0)) +  parseFloat(formatMoney(record.FBM_COST||0)) ))*100 ;
           		lyl = lyl.toFixed(2) ;
           		if( lyl < 0 ){
           			return "<font color='red'>"+lyl+"%</font>"
           		}else{
           			return lyl+"%" ;
           		}
           	},permission:function(){ return $purchase_cost_view; }},
           	{align:"center",key:"FBA_COST",label:"其他成本",group:"FBA",width:"6%",format:function(val,record){
           		return "<a href='' class='cost' type='FBA' asin='"+record.ASIN+"'>"+(val||"")+"</a>" ;
           	},permission:function(){ return $purchase_cost_view; }},
           	{align:"center",key:"FBA_PRICE",label:"最低价",group:"FBA",width:"6%",permission:function(){ return $purchase_cost_view; }},
           	{align:"center",key:"FBA_PRICE",label:"利润额",group:"FBA",width:"6%",format:function(val,record){
           		var lye = parseFloat(formatMoney(record.FBA_PRICE)) 
           					- parseFloat(formatMoney(record.QUOTE_PRICE||0)) -   parseFloat(formatMoney(record.FBA_COST||0)) ;
           		
           		if( !record.QUOTE_PRICE  || record.QUOTE_PRICE == '0' ){
           			return "-" ;
           		}
           		
           		if( !record.FBA_PRICE || record.FBA_PRICE == '0'){
           			return "-" ;
           		}
           		
           		
           		if( !record.FBA_COST || record.FBA_COST == '0'){
           			return "-" ;
           		}
           		
           		if( parseFloat(formatMoney(record.QUOTE_PRICE||0)) +  parseFloat(formatMoney(record.FBA_COST||0)) <= 0 ){
           			return "-" ;
           		}
           		lye = lye.toFixed(2) ;
           		if( lye < 0 ){
           			return "<font color='red'>"+lye+"</font>"
           		}else{
           			return lye ;
           		}
           	},permission:function(){ return $purchase_cost_view; }},
           	{align:"center",key:"FBA_PRICE",label:"利润率",group:"FBA",width:"6%",format:function(val,record){
           		var lye = parseFloat(formatMoney(record.FBA_PRICE)) 
           					- parseFloat(formatMoney(record.QUOTE_PRICE||0)) -   parseFloat(formatMoney(record.FBA_COST||0)) ;
           		
           		if( !record.QUOTE_PRICE  || record.QUOTE_PRICE == '0' ){ return "-" ; } 
           		if( !record.FBA_PRICE || record.FBA_PRICE == '0'){ return "-" ; } 
           		if( !record.FBA_COST || record.FBA_COST == '0'){ return "-" ; }
           		
           		
           		if( parseFloat(formatMoney(record.QUOTE_PRICE||0)) +  parseFloat(formatMoney(record.FBA_COST||0)) <= 0 ){
           			return "-" ;
           		}
           		
           		var lyl = (lye / ( parseFloat(formatMoney(record.QUOTE_PRICE||0)) +  parseFloat(formatMoney(record.FBA_COST||0)) ))*100 ;
           		lyl = lyl.toFixed(2) ;
           		if( lyl < 0 ){
           			return "<font color='red'>"+lyl+"%</font>"
           		}else{
           			return lyl+"%" ;
           		}
           	},permission:function(){ return $purchase_cost_view; }}
           	
         ],
         ds:{type:"url",content:contextPath+"/grid/query"},
		 limit:30,
		 pageSizes:[10,20,30,40],
		 height:function(){
		 	return $(window).height() - 370 ;
		 },
		 title:"",
		 indexColumn:false,
		 querys:{id:id,sqlId:"sql_purchase_plan_getAsinFromSku"},//sql_purchase_plan_details_listForSKU sql_purchase_plan_details_list
		 loadMsg:"数据加载中，请稍候......",
		 loadAfter:function(){
		 	$(".grid-checkbox").each(function(){
				var val = $(this).attr("value") ;
				if( $(".product-list ul li[asin='"+val+"']").length ){
					$(this).attr("checked",true) ;
				}
			}) ;
		 }
	}) ;
	
	//dom bind events
	$(".btn-save").click(function(){
		var json = $("#personForm").toJson() ;
		$.dataservice("model:Sale.savePurchasePlanProduct",json,function(){
			window.close() ;
		}) ;
	});
	
	$(".edit_supplier").click(function(){
		openCenterWindow(contextPath+"/supplier/listsSelectBySku/<?php echo $sku ;?>",800,600) ;
		return false;
	}) ;
	
})
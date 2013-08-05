$(function(){
	$(".grid-content").llygrid({
		columns:[
           	{align:"center",key:"PUBLISH_TIME",label:"刊登时间",width:"18%",forzen:false,align:"left"},
           	//{align:"center",key:"ACCOUNT_NAME",label:"EBAY账号",width:"15%"},
           	{align:"center",key:"PUBLISHER_NAME",label:"刊登用户",width:"10%"},
        	{align:"left",key:"LISTINGTYPE",label:"刊登方式",width:"10%",format:function(val,record){
        		var record = $.parseJSON( record.PUBLISH_DETAIL ) ;
        		
           		var days = record.LISTINGDURATION ;
           		days = days.replace("Days_","")+"天" ;
           		if( val == 'Chinese' ){
           			return "拍卖("+days+")" ;
           		}
           		return "一口价("+days+")" ;
           	}},
           	{align:"center",key:"QUANTITY",label:"刊登数量",width:"10%",format:function(val,record){
           		var record = $.parseJSON( record.PUBLISH_DETAIL ) ;
           		return record.QUANTITY ;
           	}},
           	{align:"center",key:"BUYITNOWPRICE",label:"刊登价格",width:"20%",format:function(val,record){
           		var record = $.parseJSON( record.PUBLISH_DETAIL ) ;
           		return record.BUYITNOWPRICE ;
           	}},
        	{align:"center",key:"QUANTITY",label:"刊登结果",width:"10%",format:function(val,record){
           		var result = $.parseJSON( record.PUBLISH_RESULT ) ;
           		return result.isSuccess === "false"?"刊登失败":"刊登成功" ;
           	}},
           	{align:"center",key:"QUANTITY",label:"结果明细",width:"20%",format:function(val,record){
           		var result = $.parseJSON( record.PUBLISH_RESULT ) ;
           		return result.message ;
           	}}
         ],
         ds:{type:"url",content:contextPath+"/grid/query"},
		 limit:20,
		 pageSizes:[10,20,30,40],
		 height:function(){
		 	return $(window).height() - 90 ;
		 },
		 title:"Ebay刊登历史",
		 indexColumn:false,
		  querys:{sqlId:"sql_ebay_publish_history_list",templateId:templateId},
		 loadMsg:"数据加载中，请稍候......",
		 loadAfter:function(){
		 }
	}) ;
});

 
 function openCallback(){
 	$(".grid-content").llygrid("reload");
 }
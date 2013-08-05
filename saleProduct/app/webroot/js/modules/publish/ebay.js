$(function(){
	$(".new-template").click(function(){
		window.open( contextPath+"/page/forward/Publish.ebay_publish/"+accountId ) ;
	}) ;
	

	$(".grid-content").llygrid({
		columns:[
			{align:"center",key:"ID",label:"操作", width:"15%",format:function(val,record){
					var html = [] ;
					html.push("<a href='#' class='action update' val='"+val+"'>修改</a>&nbsp;") ;
					html.push("<a href='#' class='action del' val='"+val+"'>删除</a>&nbsp;") ;
					html.push("<a href='#' class='action publish' val='"+val+"'>刊登</a>&nbsp;") ;
					html.push("<a href='#' class='action publish-history' val='"+val+"'>刊登历史</a>") ;

					return html.join("") ;
			}},
           	//{align:"center",key:"ID",label:"ID", width:"5%" },
			{align:"center",key:"ITEMTITLE",label:"标题",width:"25%",forzen:false,align:"left"},
           	{align:"center",key:"NO",label:"模板编号",width:"5%",forzen:false,align:"left"},
           	//{align:"center",key:"ACCOUNT_NAME",label:"EBAY账号",width:"15%"},
           	{align:"center",key:"SITE",label:"发布国家",width:"10%",format:{type:"json",content:{'0':"美国"}}},
           	{align:"left",key:"LISTINGTYPE",label:"刊登方式",width:"10%",format:function(val,record){
           		var days = record.LISTINGDURATION ;
           		days = days.replace("Days_","")+"天" ;
           		if( val == 'Chinese' ){
           			return "拍卖("+days+")" ;
           		}
           		return "一口价("+days+")" ;
           	}},
           	{align:"center",key:"QUANTITY",label:"刊登数量",width:"10%"},
           	{align:"center",key:"BUYITNOWPRICE",label:"刊登价格",width:"20%"}
         ],
         ds:{type:"url",content:contextPath+"/grid/query"},
		 limit:20,
		 pageSizes:[10,20,30,40],
		 height:function(){
		 	return $(window).height() - 150 ;
		 },
		 title:"Ebay刊登模板列表",
		 indexColumn:false,
		  querys:{sqlId:"sql_ebay_template_listByAccount",accountId:accountId},
		 loadMsg:"数据加载中，请稍候......",
		 loadAfter:function(){
			 $(".update").click(function(){
				 var record = $(this).closest("tr").data("record") ;
				 window.open( contextPath+"/page/forward/Publish.ebay_publish/"+record.ACCOUNT_ID+"/"+record.ID ) ;
			 }) ;
			 
			 $(".publish").click(function(){
				 if(window.confirm("确认刊登吗？")){
					 var record = $(this).closest("tr").data("record") ;
					 $.request({
						 url:contextPath+"/publishEbay/doPublish/"+record.ID,
						 success:function(result){
							 if(result.isSuccess==='true'){
								 alert("刊登成功！") ;
							 }else{
								 alert("刊登失败："+result.message) ;
							 }
						 }
					 }) ;
				 }
			 }) ;
			 
			 $(".publish-history").click(function(){
				 var record = $(this).closest("tr").data("record") ;
				 openCenterWindow(contextPath+"/page/forward/Publish.ebay_publish_history/"+record.ID,800,650) ;
			 }) ;
		 }
	}) ;
});

 
 function openCallback(){
 	$(".grid-content").llygrid("reload");
 }
$(function(){
	
	$(".grid-content").llygrid({
		columns:[
			//{align:"center",key:"MessageID",label:"",width:"40",sort:false,format:{type:"checkbox"}},
		    {align:"center",key:"Subject",label:"标题",width:"20%",format:function(val,record){
		    	return "<a href='#' messageID='"+record.MessageID+"'>"+val+"</a>"
		    }},
			{align:"center",key:"Sender",label:"发送者",width:"15%",forzen:false,align:"left"},
           	{align:"center",key:"SendToName",label:"接收者",width:"15%",forzen:false,align:"left"},
           	{align:"center",key:"Flagged",label:"是否已标记",width:"7%",format:{type:"json",content:{'false':"否",'true':"是"}}},
           	{align:"center",key:"SRead",label:"是否已读",width:"7%",format:{type:"json",content:{'false':"否",'true':"是"}}},
           	{align:"center",key:"ResponseEnabled",label:"是否回复",width:"7%",format:function(val,record){
           		if( val == 'false' ) return "-" ;
           		var Replied = record.ResponseEnabled ;
           		if( Replied == 'false'  ) return "否" ;
           		return "是" ;
           	}},
           	{align:"center",key:"ReceiveDate",label:"接收时间",width:"15%"}
         ],
         ds:{type:"url",content:contextPath+"/grid/query"},
		 limit:50,
		 height:function(){
		 	return 100;
		 },
		 title:"",
		 indexColumn:false,
		 querys:{sqlId:"sql_ebay_message_list",messageIds:messageIds.join(",")},
		 loadMsg:"数据加载中，请稍候......",
		 loadAfter:function(){
			 $("[messageID]").click(function(){
				 var record = $(this).closest("tr").data("record") ;
				 openCenterWindow(contextPath+"/page/forward/Publish.ebayMesssageDetails/"+record.MessageID,690,650) ;
			 }) ;
		 }
	}) ;
	
	$(".grid-template").llygrid({
		columns:[
			{align:"center",key:"title",label:"标题",width:"38%",forzen:false,align:"left"},
		    {align:"left",key:"description",label:"描述",width:"60%"}
			
         ],
         ds:{type:"url",content:contextPath+"/grid/query"},
		 limit:50,
		 height:function(){
		 	return 100;
		 },
		 title:"",
		 indexColumn:false,
		 querys:{sqlId:"sql_ost_list"},
		 loadMsg:"数据加载中，请稍候......",
		 loadAfter:function(){
			 $("[messageID]").click(function(){
				 var record = $(this).closest("tr").data("record") ;
				 openCenterWindow(contextPath+"/page/forward/Publish.ebayMesssageDetails/"+record.MessageID,690,650) ;
			 }) ;
		 },
		 rowDblClick:function(row,rowData){
			 $("#subject").val( rowData.title||"" ) ;
			 $("#body").val( rowData.answer||"" ) ;
		 }
	}) ;
	
	$(".save-reply").click(function(){
		if( window.confirm("确认保存回复？") ){
			$.dataservice("model:PublishEbay.saveLocalResponse",{
				messageIds: messageIds.join(","),
				subject: $("#subject").val(),
				body:$("#body").val()
			},function(result){
				alert(result) ;
			}) ;
		}
	}) ;
}) ;	
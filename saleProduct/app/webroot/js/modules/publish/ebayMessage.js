$(function(){
	$(".new-template").click(function(){
		window.open( contextPath+"/page/forward/Publish.ebay_publish/"+accountId ) ;
	}) ;
	
	$(".tag-read").click(function(){
		var ids = $(".grid-content").llygrid("getSelectedValue",{key:"MessageID",checked:true}) ;
		if( ids && ids.length > 0 ){
			if( window.confirm("是否确定为已读?") ){
				$.dataservice("model:PublishEbay.tagMessageLocalStatus",{messageIds:ids.join(","),read:'true'},function(result){
					loadMessageStatus() ;
					$(".grid-content").llygrid("reload",{},true) ;
				}) ;
			}
		}
	}) ;
	
	$(".tag-flagged").click(function(){
		var ids = $(".grid-content").llygrid("getSelectedValue",{key:"MessageID",checked:true}) ;
		if( ids && ids.length > 0 ){
			if( window.confirm("是否确定为已标记?") ){
				$.dataservice("model:PublishEbay.tagMessageLocalStatus",{messageIds:ids.join(","),flagged:'true'},function(result){
					loadMessageStatus() ;
					$(".grid-content").llygrid("reload",{},true) ;
				}) ;
			}
		}
	}) ;
	
	$(".tag-all").click(function(){
		var ids = $(".grid-content").llygrid("getSelectedValue",{key:"MessageID",checked:true}) ;
		if( ids && ids.length > 0 ){
			if( window.confirm("是否确定为已读和已标记?") ){
				$.dataservice("model:PublishEbay.tagMessageLocalStatus",{messageIds:ids.join(","),flagged:'true',read:'true'},function(result){
					loadMessageStatus() ;
					$(".grid-content").llygrid("reload",{},true) ;
				}) ;
			}
		}
	}) ;
	
	$(".do-reply").click(function(){
		var ids = $(".grid-content").llygrid("getSelectedValue",{key:"MessageID",checked:true}) ;
		//if( ids && ids.length > 0 ){
			//if( window.confirm("是否确定为已读和已标记?") ){
				openCenterWindow(contextPath+"/page/forward/Publish.ebayMessageResponse",900,650,null,{messageIds:ids}) ;
			//}
		//}
	}) ;
	
	$(".grid-content").llygrid({
		columns:[
			{align:"center",key:"MessageID",label:"",width:"40",sort:false,format:{type:"checkbox"}},
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
		 limit:20,
		 pageSizes:[10,20,30,40],
		 height:function(){
		 	return $(window).height() - 170 ;
		 },
		 title:"",
		 indexColumn:false,
		  querys:{sqlId:"sql_ebay_message_list"},
		 loadMsg:"数据加载中，请稍候......",
		 loadAfter:function(){
			 $(".update").click(function(){
//				 var record = $(this).closest("tr").data("record") ;
				 window.open( contextPath+"/page/forward/Publish.ebay_publish/"+record.ACCOUNT_ID+"/"+record.ID ) ;
			 }) ;
			 
			 $("[messageID]").click(function(){
				 var record = $(this).closest("tr").data("record") ;
				 openCenterWindow(contextPath+"/page/forward/Publish.ebayMesssageDetails/"+record.MessageID,690,650) ;
			 }) ;
		 }
	}) ;
	
	
	var treeData = [
	          {id:"read",text:"是否已读",childNodes:[
	                    {id:"read_true",text:"已读",type:"sread",val:"true"},
	                    {id:"read_false",text:"未读(<span class=\"read_false\">0</span>)",type:"sread",val:"false"},
	                    {id:"read_localtrue",text:"已读未上传(<span class=\"local_read_false\">0</span>)",type:"sread",val:"localtrue"}
	          ]},
	          {id:"flagged",text:"是否标记",childNodes:[
	                  {id:"flagged_true",text:"已标记",type:"flagged",val:"true"},
	                  {id:"flagged_false",text:"未标记(<span class=\"flagged_false\">0</span>)",type:"flagged",val:"false"},
	                  {id:"flagged_localtrue",text:"已标记未上传(<span class=\"local_flagged_false\">0</span>)",type:"flagged",val:"localtrue"}
	         ]},
	         {id:"reply",text:"是否回复",childNodes:[
	                  {id:"reply_true",text:"已回复",type:"replied",val:"true"},
	                  {id:"reply_false",text:"未回复(<span class=\"replied_false\">0</span>)",type:"replied",val:"false"},
	                  {id:"reply_localtrue",text:"已回复未上传(<span class=\"local_replied_false\">0</span>)",type:"replied",val:"localtrue"}
	        ]},
            
	 ] ;
	//$(".b").attr("disabled","disabled") ;
	$('#default-tree').tree({//tree为容器ID
		source:'array',
		data:treeData ,
		isRootExpand:true,
		asyn:false,
		expandLevel:2,
		onNodeClick:function(id,text,record){
			var params = {} ;
			
			if( record.type == 'sread' && record.val== 'false' ){
				$(".b").attr("disabled","disabled") ;
				$(".br").removeAttr("disabled") ;
			}else if( record.type == 'flagged' && record.val== 'false'  ){
				$(".b").attr("disabled","disabled") ;
				$(".bf").removeAttr("disabled") ;
			}else if( record.type == 'replied'  && record.val== 'false' ){
				$(".b").attr("disabled","disabled") ;
				$(".bp").removeAttr("disabled") ;
			}else{
				$(".b").attr("disabled","disabled") ;
			}
			
			if(record.val == 'localtrue'){
				params["local_"+record.type] = "true" ;
			}else if(record.val == 'false'){
				params["f_"+record.type] = 'false' ;
			}else{
				params[record.type] = record.val ;
			}
			
			$(".grid-content").llygrid("reload",params) ;
		}
   }) ;
	
	loadMessageStatus() ;
	
});

function loadMessageStatus(){
	$.dataservice("model:PublishEbay.loadMessageCounts",{},function(result){
		$(result).each(function(){
			$("."+this.TYPE+"_false").text( this.C ) ;
		}) ;
	}) ;
}

 
 function openCallback(){
 	$(".grid-content").llygrid("reload");
 }
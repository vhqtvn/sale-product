$(function(){
	$(".action").live("click",function(){
		var id = $(this).attr("val") ;
	
		if( $(this).hasClass("del") ){
			var record = $.llygrid.getRecord(this) ;
			if(window.confirm("确认删除吗")){
				$.dataservice("model:ProductDev.deleteDoc",{docId:record.DOC_ID},function(result){
						$(".grid-content").llygrid("reload",{},true) ;
				});
			}
		}else if( $(this).hasClass("add") ){
			openCenterWindow(contextPath+"/page/forward/Product.developer.addDoc/"+devId,800,550) ;
		} 
		return false ;
	});

	$(".grid-content").llygrid({
		columns:[
			{align:"center",key:"ID",label:"操作", width:"10%",format:function(val,record){
					var html = [] ;
					var val = record["LOGIN_ID"] ;
					html.push("<a href='#' class='action del' val='"+val+"'>删除</a>") ;

					return html.join("") ;
			}},
           	{align:"center",key:"NAME",label:"资料名称",width:"20%",forzen:false,align:"left"},
           	{align:"center",key:"TEXT_CONTENT",label:"内容",width:"30%"},
           	{align:"center",key:"FILE_URL",label:"附件",width:"15%",format:function(val,record){
           		if( !record.FILE_URL )return "" ;
           		var fileName = val.split("/files/dev/")[1] ;
           		return "<a href='/"+fileContextPath+"/files/dev/"+fileName+"'>下载</a>" ;
           	}},
           	{align:"center",key:"CREATED_NAME",label:"创建人",width:"10%"},
           	{align:"left",key:"CREATED_DATE",label:"创建时间",width:"10%"}
         ],
         ds:{type:"url",content:contextPath+"/grid/query"},
		 limit:20,
		 pageSizes:[10,20,30,40],
		 height:function(){
		 	return $(window).height() - 160 ;
		 },
		 title:"产品资料列表",
		 indexColumn:false,
		  querys:{sqlId:"sql_pdev_new_doc_list",devId:devId},
		 loadMsg:"数据加载中，请稍候......"
	}) ;
});

$(function(){
	$(".query").click(function(){
				var json = $(".query-table").toJson() ;
				$(".grid-content").llygrid("reload",json,true) ;
	}) ;
	
	$(".action").live("click",function(){
		var id = $(this).attr("val") ;
	
		if( $(this).hasClass("update") ){
			
			var record = $.llygrid.getRecord(this) ;
			openCenterWindow("/saleProduct/index.php/page/forward/Suggest.editSuggest/"+record.ID,600,500) ;
			
		}else if( $(this).hasClass("del") ){
			
			var record = $.llygrid.getRecord(this) ;
			if(window.confirm("确认删除该用户吗")){
				$.dataservice("model:User.disableUser",{id:record.ID},function(result){
						$(".grid-content").llygrid("reload") ;
				});
			}
		}else if( $(this).hasClass("add") ){
			openCenterWindow("/saleProduct/index.php/page/forward/Suggest.editSuggest",600,500) ;
		} 
		return false ;
	})

	$(".grid-content").llygrid({
		columns:[
			{align:"center",key:"ID",label:"操作", width:"5%",format:function(val,record){
					var html = [] ;
					var val = record["LOGIN_ID"] ;
					html.push("<a href='#' class='action update' val='"+val+"'>修改</a>&nbsp;") ;
					//html.push("<a href='#' class='action del' val='"+val+"'>删除</a>") ;

					return html.join("") ;
			}},
           	//{align:"center",key:"ID",label:"ID", width:"5%" },
			{align:"left",key:"STATUS",label:"状态",width:"5%",format:{type:'json',content:{'0':'未处理','1':'已处理','2':'暂不处理'}}},
			{align:"left",key:"TYPE",label:"类型",width:"5%",format:{type:'json',content:{'1':'需求','2':'问题'}}},
			{align:"left",key:"IMPORTANT_LEVEL",label:"重要程度",width:"7%",format:{type:'json',content:{'1':'非常重要','2':'重要','3':'不重要'}}},
			{align:"left",key:"ENERY_LEVEL",label:"紧急程度",width:"7%",format:{type:'json',content:{'1':'非常紧急','2':'紧急','3':'不紧急'}}},
           	{align:"center",key:"TITLE",label:"标题",width:"20%",align:"left"},
           	{align:"center",key:"MEMO",label:"备注",width:"20%"},
           	{align:"center",key:"CREATOR",label:"创建人",width:"10%"},
           	{align:"left",key:"CREATE_TIME",label:"创建时间",width:"10%"}
         ],
         ds:{type:"url",content:"/saleProduct/index.php/grid/query"},
		 limit:20,
		 pageSizes:[10,20,30,40],
		 height:function(){
		 	return $(window).height() - 150 ;
		 },
		 title:"需求问题列表",
		 indexColumn:false,
		  querys:{sqlId:"sql_suggest_list",status:"0"},
		 loadMsg:"数据加载中，请稍候......"
	}) ;
});

 
 function openCallback(){
 	$(".grid-content").llygrid("reload");
 }
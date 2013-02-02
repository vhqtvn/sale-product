$(function(){
	$(".query").click(function(){
				var json = $(".query-table").toJson() ;
				$(".grid-content").llygrid("reload",json,true) ;
	}) ;
	
	$(".action").live("click",function(){
		var id = $(this).attr("val") ;
	
		if( $(this).hasClass("update") ){
			
			var record = $.llygrid.getRecord(this) ;
			openCenterWindow("/saleProduct/index.php/page/forward/SaleProduct.Postage.editSoftwareVendor/"+record.ID,650,500) ;
			
		}else if( $(this).hasClass("del") ){
			var record = $.llygrid.getRecord(this) ;
			if(window.confirm("确认删除该物流软件商吗，对应的服务也将会删除？")){
				$.dataservice("model:RealProduct.Postage.delPostageVendor",{id:record.ID},function(result){
						$(".grid-content").llygrid("reload") ;
				});
			}
		}else if( $(this).hasClass("add") ){
			openCenterWindow("/saleProduct/index.php/page/forward/SaleProduct.Postage.editSoftwareVendor",650,500) ;
		}
		return false ;
	})

	$(".grid-content").llygrid({
		columns:[
			{align:"center",key:"ID",label:"操作", width:"5%",format:function(val,record){
					var html = [] ;
					var val = record["LOGIN_ID"] ;
					html.push("<a href='#' class='btn action update' val='"+val+"'>修改</a>&nbsp;") ;

					return html.join("") ;
			}},
			{align:"left",key:"NAME",label:"名称",width:"10%"},
			{align:"left",key:"CODE",label:"代码",width:"10%"},
			{align:"left",key:"DATA_SQL",label:"获取数据sql",width:"20%"},
			{align:"left",key:"RESULT_TABLE",label:"返回数据表",width:"10%"},
			{align:"left",key:"DB_USER",label:"数据库用户名",width:"10%"},
			{align:"left",key:"DB_PASSWORD",label:"数据库密码",width:"10%"},
			{align:"left",key:"MEMO",label:"备注",width:"20%"}
         ],
         ds:{type:"url",content:"/saleProduct/index.php/grid/query"},
		 limit:20,
		 pageSizes:[10,20,30,40],
		 height:function(){
		 	return  $(window).height() - 130  ;
		 },
		 title:"物流软件商列表",
		 indexColumn:false,
		  querys:{sqlId:"sql_postage_software_vender_list",status:"0"},
		 loadMsg:"数据加载中，请稍候......",
		 rowClick:function(rowIndex , rowData){
		 	
		 }
	}) ;
	
});

 
 function openCallback(flag){
 		$(".grid-content").llygrid("reload");
 }
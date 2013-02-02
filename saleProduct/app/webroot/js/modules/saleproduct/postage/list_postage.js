$(function(){
	$(".query").click(function(){
				var json = $(".query-table").toJson() ;
				$(".grid-content").llygrid("reload",json,true) ;
	}) ;
	
	$(".action").live("click",function(){
		var id = $(this).attr("val") ;
	
		if( $(this).hasClass("update") ){
			
			var record = $.llygrid.getRecord(this) ;
			openCenterWindow("/saleProduct/index.php/page/forward/SaleProduct.Postage.editPostageVendor/"+record.ID,600,400) ;
			
		}else if( $(this).hasClass("del") ){
			var record = $.llygrid.getRecord(this) ;
			if(window.confirm("确认删除该物流商吗，对应的服务也将会删除？")){
				$.dataservice("model:RealProduct.Postage.delPostageVendor",{id:record.ID},function(result){
						$(".grid-content").llygrid("reload") ;
				});
			}
		}else if( $(this).hasClass("add") ){
			openCenterWindow("/saleProduct/index.php/page/forward/SaleProduct.Postage.editPostageVendor",600,400) ;
		}else if( $(this).hasClass("addServices") ){
			var record = $.llygrid.getRecord(this) ;
			openCenterWindow("/saleProduct/index.php/page/forward/SaleProduct.Postage.editPostageServices/"+record.ID,600,500) ;
		}else if( $(this).hasClass("updateServices") ){
			var record = $.llygrid.getRecord(this) ;
			openCenterWindow("/saleProduct/index.php/page/forward/SaleProduct.Postage.editPostageServices/"+currentVendorId+"/"+record.ID,600,500) ;
			
		}
		return false ;
	})

	$(".grid-content").llygrid({
		columns:[
			{align:"center",key:"ID",label:"操作", width:"18%",format:function(val,record){
					var html = [] ;
					var val = record["LOGIN_ID"] ;
					html.push("<a href='#' class='btn action update' val='"+val+"'>修改</a>&nbsp;") ;
					//html.push("<a href='#' class='btn action del' val='"+val+"'>删除</a>&nbsp;") ;
					html.push("<a href='#' class='btn action addServices' val='"+val+"'>添加服务</a>&nbsp;") ;

					return html.join("") ;
			}},
			{align:"left",key:"NAME",label:"名称",width:"15%"},
			{align:"left",key:"CODE",label:"代码",width:"15%"},
			{align:"left",key:"SOFTWARE_NAME",label:"物流软件商",width:"15%"},
			{align:"left",key:"MEMO",label:"备注",width:"27%"}
         ],
         ds:{type:"url",content:"/saleProduct/index.php/grid/query"},
		 limit:20,
		 pageSizes:[10,20,30,40],
		 height:function(){
		 	return  150 ;
		 },
		 title:"物流商列表",
		 indexColumn:false,
		  querys:{sqlId:"sql_postage_vender_list",status:"0"},
		 loadMsg:"数据加载中，请稍候......",
		 rowClick:function(rowIndex , rowData){
		 	var id = rowData.ID  ;
		 	currentVendorId = id ;
		 	$(".grid-content-services").llygrid("reload",{vendorId:id}) ;
		 }
	}) ;
	
	var currentVendorId = null ;
	
	$(".grid-content-services").llygrid({
		columns:[
			{align:"center",key:"ID",label:"操作", width:"11%",format:function(val,record){
					var html = [] ;
					var val = record["LOGIN_ID"] ;
					html.push("<a href='#' class='btn action updateServices' val='"+val+"'>修改</a>&nbsp;") ;
					//html.push("<a href='#' class='btn action delServices' val='"+val+"'>删除</a>&nbsp;") ;
					return html.join("") ;
			}},
			{align:"left",key:"NAME",label:"名称",width:"15%"},
			{align:"left",key:"CODE",label:"代码",width:"15%"},
			{align:"left",key:"TAG",label:"TAG",width:"15%"},
			{align:"left",key:"COUNTRY",label:"国家",width:"15%"},
			{align:"left",key:"MEMO",label:"备注",width:"27%"}
         ],
         ds:{type:"url",content:"/saleProduct/index.php/grid/query"},
		 limit:20,
		 pageSizes:[10,20,30,40],
		 height:function(){
		 	return $(window).height() - 370 ;
		 },
		 title:"供应商服务列表",
		 indexColumn:false,
		  querys:{sqlId:"sql_postage_services_list",status:"0"},
		 loadMsg:"数据加载中，请稍候......"
	}) ;
});

 
 function openCallback(flag){
 	if(flag == 'services')
 		$(".grid-content-services").llygrid("reload",{},true);
 	else
 		$(".grid-content").llygrid("reload");
 }
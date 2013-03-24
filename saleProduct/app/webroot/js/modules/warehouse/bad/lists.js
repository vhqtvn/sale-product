	$(function(){
			$(".bad-in").click(function(){
				openCenterWindow(contextPath+"/page/forward/Warehouse.Ram.addRma/bad",830,600) ;
			}) ;
		
			$(".action").live("click",function(){
				var id = $(this).attr("val") ;
					openCenterWindow(contextPath+"/page/forward/Warehouse.Ram.history/"+id,850,600) ;

				return false ;
			});
		
			$(".query").click(function(){
				var json = $(".query-table").toJson() ;
				$(".grid-content").llygrid("reload",json,true) ;
			}) ;

			$(".grid-content").llygrid({
				columns:[
					{align:"center",key:"ID",label:"操作", width:"10%",format:function(val,record){
						var html = [] ;
						html.push("<a href='#' class='action inout btn' val='"+val+"'>残品出入库</a>&nbsp;") ;
						return html.join("") ;
					}},
		           	{align:"center",key:"NAME",label:"名称",width:"20%",forzen:false,align:"left",format:function(val,reocrd){
		           		return "<a href='"+reocrd.URL+"' target='_blank'>"+val+"</a>" ;
		           	}},
		           	{align:"center",key:"REAL_SKU",label:"SKU",width:"10%"},
		           	{align:"center",key:"BAD_QUANTITY",label:"残品数量",width:"5%",render:function(record){
		           		$(this).find("td[key='BAD_QUANTITY']").css({"background":"red",'font-size':'15px',color:'#FFF'}) ;
		           		
		           	}},
		           	
		           	{align:"center",key:"TYPE",label:"货品类型",width:"10%",format:{type:"json",content:{'base':"基本类型",'package':"打包货品"}}},
		           	{align:"center",key:"IMAGE_URL",label:"图片",width:"5%",format:function(val,record){
		           		if(val){
		           			val = val.replace(/%/g,'%25') ;
		           			return "<img src='/"+fileContextPath+"/"+val+"' style='width:30px;height:30px;'>" ;
		           		}
		           		return "" ;
		           	}},
		           	{align:"center",key:"MEMO",label:"备注",width:"25%"}
		           	
		         ],
		         ds:{type:"url",content:contextPath+"/grid/query"},
				 limit:20,
				 pageSizes:[10,20,30,40],
				 height:function(){
				 	return $(window).height() - 220 ;
				 },
				 title:"",
				// autoWidth:true,
				 indexColumn:false,
				  querys:{sqlId:"sql_warehouse_bad_lists",type:"base",status:1,categoryId:''},
				 loadMsg:"数据加载中，请稍候......"
			}) ;
   	 });

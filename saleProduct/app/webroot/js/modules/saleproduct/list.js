
	 //result.records , result.totalRecord
	 function formatGridData(data){
		var records = data.record ;
 		var count   = data.count ;
 		
 		count = count[0][0]["count(*)"] ;
 		
		var array = [] ;
		$(records).each(function(){
			var row = {} ;
			for(var o in this){
				var _ = this[o] ;
				for(var o1 in _){
					row[o1] = _[o1] ;
				}
			}
			array.push(row) ;
		}) ;
	
		var ret = {records: array,totalRecord:count } ;
			
		return ret ;
	   }

	$(function(){
			$(".action").live("click",function(){
				var id = $(this).attr("val") ;
				if( $(this).hasClass("update") ){
					openCenterWindow("/saleProduct/index.php/saleProduct/details/"+id,900,600) ;
				}else if( $(this).hasClass("giveup") ){
					var type = $(this).attr("type");
					var message = type == 1?"确认将该货品作废吗":"确认恢复该货品吗？"
					if(window.confirm(message)){
						$.ajax({
							type:"post",
							url:"/saleProduct/index.php/saleProduct/giveup/"+id+"/"+type,
							data:{id:id},
							cache:false,
							dataType:"text",
							success:function(result,status,xhr){
								$(".grid-content").llygrid("reload",{},true) ;
							}
						}); 
					}
				}else if( $(this).hasClass("add") ){
					openCenterWindow("/saleProduct/index.php/saleProduct/forward/edit_product/",700,500) ;
				}
				return false ;
			})

			$(".grid-content").llygrid({
				columns:[
					{align:"center",key:"REAL_SKU",label:"Actions", width:"10%",format:function(val,record){
						if(tabIndex < 2){
							var html = [] ;
							html.push("<a href='#' class='action update btn' val='"+val+"'>编辑</a>&nbsp;") ;
							html.push("<a href='#' class='action giveup btn' val='"+val+"' type=1>作废</a>") ;
							return html.join("") ;
						}else{
							var html = [] ;
							html.push("<a href='#' class='action giveup btn' val='"+val+"' type=2>恢复</a>") ;
							return html.join("") ;
						}
							
					}},
		           	{align:"center",key:"NAME",label:"名称",width:"20%",forzen:false,align:"left",format:function(val,reocrd){
		           		return "<a href='"+reocrd.URL+"' target='_blank'>"+val+"</a>" ;
		           	}},
		           	{align:"center",key:"REAL_SKU",label:"SKU",width:"10%"},
		           	{align:"center",key:"TYPE",label:"货品类型",width:"10%",format:{type:"json",content:{'base':"基本类型",'package':"打包货品"}}},
		           	{align:"center",key:"IMAGE_URL",label:"图片",width:"8%",format:function(val,record){
		           		if(val){
		           			val = val.replace(/%/g,'%25') ;
		           			return "<img src='/saleProduct/"+val+"' style='width:50px;height:50px;'>" ;
		           		}
		           		return "" ;
		           	}},
		           	{align:"center",key:"MEMO",label:"备注",width:"40%"}
		         ],
		         ds:{type:"url",content:"/saleProduct/index.php/grid/query"},
				 limit:20,
				 pageSizes:[10,20,30,40],
				 height:function(){
				 	return $(window).height() - 200 ;
				 },
				 title:"货品列表",
				 //autoWidth:true,
				 indexColumn:false,
				  querys:{sqlId:"sql_saleproduct_list",type:"base",status:1},
				 loadMsg:"数据加载中，请稍候......"
			}) ;
   	 });
   	 
   	 $(function(){
			var tab = $('#details_tab').tabs( {
				tabs:[
					{label:'基本货品',content:"tab-content"},
					{label:'打包货品',content:"tab-content"},
					{label:'作废货品',content:"tab-content"}
				] ,
				//height:'500px',
				select:function(event,ui){
					var index = ui.index ;
					tabIndex = index ;
					renderAction(index);
				}
			} ) ;
		}) ;
var tabIndex = 0 ;   	   	 
function renderAction(index){
	$(".save-btn").show() ;
	if(index == 0){//未审核订单
		$(".grid-content").llygrid("reload",{type:"base",status:1},true) ;
	}else if(index == 1){//合格订单
		$(".grid-content").llygrid("reload",{type:"package",status:1},true) ;
	}else if(index == 2){//合格订单
		$(".grid-content").llygrid("reload",{type:"",status:'0'},true) ;
	}
}
	$(function(){
			$(".action").live("click",function(){
				var id = $(this).attr("val") ;
				var record = $(this).parents("tr:first").data("record") ;
				if( $(this).hasClass("view") ){
					openCenterWindow(contextPath+"/saleProduct/details/"+record.REAL_SKU+"/sku",900,650) ;
				}else if( $(this).hasClass("update") ){
					openCenterWindow(contextPath+"/saleProduct/details/"+id,900,650) ;
				}else if( $(this).hasClass("giveup") ){
					var type = $(this).attr("type");
					var message = type == 1?"确认将该货品作废吗":"确认恢复该货品吗？"
					message = type == 3?"确认删除该货品吗（相关货品数据都将删除，如与SKU关系，组合产品关系等）？":message;
					if(window.confirm(message)){
						$.ajax({
							type:"post",
							url:contextPath+"/saleProduct/giveup/"+id+"/"+type,
							data:{id:id},
							cache:false,
							dataType:"text",
							success:function(result,status,xhr){
								$(".grid-content").llygrid("reload",{},true) ;
							}
						}); 
					}
				}else if( $(this).hasClass("add") ){
					openCenterWindow(contextPath+"/saleProduct/forward/edit_product/",800,620) ;
				}else if( $(this).hasClass("inout") ){//货品出入库明细
					openCenterWindow(contextPath+"/page/forward/Warehouse.In.storageDetails/"+id,850,600) ;
				}else if( $(this).hasClass("assign") ){//货品出入库明细
					openCenterWindow(contextPath+"/page/forward/Warehouse.In.assign/"+id,850,600) ;
				}
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
						html.push(  getImage('icon-grid.gif','查看','action view ') +"&nbsp;") ;
						if(tabIndex < 2){
							if( $product_edit ){
								html.push(  getImage('edit.png','编辑','action update ') +"&nbsp;") ;
							}
						}
						if( $product_stock_quanity_assign  ){
							html.push(  getImage('retry.png','出入库','action inout ') +"&nbsp;") ;
							html.push(  getImage('pkg.gif','库存分配','action assign ') +"&nbsp;") ;
							
							//html.push("<a href='#' class='action inout btn' val='"+val+"'>出入库</a>&nbsp;") ;
							//html.push("<a href='#' class='action assign btn' val='"+val+"'>库存分配</a>&nbsp;") ;
						}
						return html.join("") ;
					},permission:function(){ return $product_edit||$product_stock_quanity_assign ; }},
				 	{align:"center",key:"IMAGE_URL",label:"图片",width:"5%",format:{type:'img'}},
		           	{align:"center",key:"NAME",label:"名称",width:"20%",forzen:false,align:"left"},
		           	{align:"center",key:"REAL_SKU",label:"SKU",width:"10%"},
		           	{align:"center",key:"LAST_IN_TIME",label:"",group:"库存",width:"3%",format:function(val,record){
		           		if( record.LAST_ASSIGN_TIME &&( val >= record.LAST_ASSIGN_TIME ) ){
							return '<img title="已经分配" src="/'+fileContextPath+'/app/webroot/img/success.gif">';
				
		           		}
		           		return "<img title='未分配' src='/"+fileContextPath+"/app/webroot/img/error.gif'>"  ;
		           	}},
		           	{align:"center",key:"QUANTITY",label:"总",group:"库存",width:"5%" },
		        	{align:"center",key:"COMMON_QUANTITY",label:"普通",group:"库存",width:"5%" },
		        	{align:"center",key:"FBA_QUANTITY",label:"FBA",group:"库存",width:"5%" },
		           	{align:"center",key:"SECURITY_QUANTITY",label:"安全",group:"库存",width:"5%" },
		           	{align:"center",key:"LOCK_QUANTITY",label:"锁定",group:"库存",width:"5%" },
		           	{align:"center",key:"ASSIGN_QUANTITY",label:"可分配",group:"库存",width:"5%",format:function(val, record){
		           		var quantity = record.QUANTITY - record.SECURITY_QUANTITY - record.LOCK_QUANTITY ;
		           		return quantity ;
		           	},render:function(record){
		           		var quantity = record.QUANTITY - record.SECURITY_QUANTITY - record.LOCK_QUANTITY ;
		           		if( parseInt(record.WARNING_QUANTITY||0) >= quantity ){
		           			var title = "当前可分配库存已经小于或等于预警库存【"+record.WARNING_QUANTITY+"】" ;
		           			$(this).find("td[key='ASSIGN_QUANTITY']").css({"background":"red",'font-size':'15px',color:'#FFF'}).find("span").attr("title",title);
		           		}
		           	}},
		           	
		           	{align:"center",key:"TYPE",label:"货品类型",width:"10%",format:{type:"json",content:{'base':"基本类型",'package':"打包货品"}}},
		          
		           	{align:"center",key:"MEMO",label:"备注",width:"25%"},
		           	{align:"center",key:"ID",label:"操作", width:"6%",format:function(val,record){
						if(tabIndex < 2){
							var html = [] ;
							html.push("<a href='#' class='action giveup btn' val='"+val+"' type=1>作废</a>") ;
							return html.join("") ;
						}else{
							var html = [] ;
							html.push(deleteHtml) ;
							html.push("<a href='#' class='action giveup btn'  val='"+val+"' type=2>恢复</a>") ;
							return html.join("") ;
						}
					},permission:function(){ return $product_giveup ; }}
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
				  querys:{sqlId:"sql_saleproduct_list",type:"base",status:1,categoryId:''},
				 loadMsg:"数据加载中，请稍候......"
			}) ;
   	 });
   	 
   	 $(function(){
   		 	var tabs = [
   						{label:'基本货品',content:"tab-content"},
   						{label:'打包货品',content:"tab-content"}
   					] ;
   		 	if( $view_giveup_product ){
   		 		tabs.push( {label:'作废货品',content:"tab-content"} ) ;
   		 	}
   		 
			var tab = $('#details_tab').tabs( {
				tabs: tabs,
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
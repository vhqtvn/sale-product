

	var currentId = '' ;
	$(function(){
			$(".grid-content-details").llygrid({
				columns:[
					{align:"center",key:"ID",label:"操作",width:"3%",permission:function(){
		           		return !$isRead ;
		           	},format:function(val,record){
		           		var html = [] ;
		           		html.push(  getImage('edit.png','编辑','edit-box-product ') +"&nbsp;") ;
		           		html.push( getImage("delete.gif","删除","delete-box-product") ) ;
						return html.join("") ;
					}},
				    {align:"center",key:"BOX_NUMBER",label:"包装箱",width:"5%"},
					{align:"center",key:"IMAGE_URL",label:"",width:"2%",format:{type:'img'}},
		           	{align:"center",key:"NAME",label:"货品名称",width:"5%"},
	           		{align:"center",key:"SKU",label:"货品SKU",width:"5%",format:function(val,reocrd){
	           			return "<a href='#' product-realsku='"+val+"'>"+val+"</a>" ;
	           		}},
	           		{align:"center",key:"LISTING_SKU",label:"Listing SKU",width:"5%"},
	           		{align:"center",key:"INVENTORY_TYPE",label:"库存类型",width:"5%",format:{type:'json',content:{1:'普通库存',2:'FBA库存'}}}, 
	           		{align:"center",key:"QUANTITY",label:"数量",width:"3%"},
	           		{align:"center",key:"DELIVERY_TIME",label:"供货时间",width:"6%"},
	           		{align:"center",key:"PRODUCT_TRACKCODE",label:"产品跟踪码",width:"6%"},
	           		{align:"center",key:"MEMO",label:"备注",width:"6%"}
		         ],
		         ds:{type:"url",content:contextPath+"/grid/query"},
				 limit:30,
				 pageSizes:[10,20,30,40],
				 height:function(){
					 return $(window).height() -280 ;
				 },
				 title:"货品列表",
				 autoWidth:true,
				 querys:{sqlId:"sql_warehouse_box_products",boxId:''},
				 loadMsg:"数据加载中，请稍候......",
				 loadAfter:function(){
					 $(".delete-box-product").bind("click",function(event){
							event.stopPropagation() ;
							if(window.confirm("确认删除？")){
								var bpId = $(this).parents("tr").data("record")['ID'] ;
								$.dataservice("model:Warehouse.In.deleteBoxProduct",{bpId:bpId},function(result){
									if(result){
										alert(result) ;
									}else{
										$(".grid-content-details").llygrid("reload",{},true) ;
									}
								});
							}
							
							return false ;
						}) ;
				 }
			}) ;
			
			$(".process-action").live("click",function(){
				var FILTER_ID = $(this).attr("val") ;
				var asin = $(this).attr("asin") ;
				var status = $(this).attr("status") ;
				openCenterWindow(contextPath+"/sale/details1/"+FILTER_ID+"/"+asin+"/"+type+"/"+status,950,650) ;
			}) ;
			
			$(".add-box").live("click",function(){
				openCenterWindow(contextPath+"/page/model/Warehouse.In.editBoxPage/"+inId,550,420,function(){
					$(".grid-content").llygrid("reload",{});
					$(".grid-content-details").llygrid("reload",{boxId:''});
				}) ;
			}) ;
			
			$(".add-box-product").live("click",function(){
				openCenterWindow(contextPath+"/page/model/Warehouse.In.editBoxProductPage/"+currentId+"/",850,650,function(){
					$(".grid-content-details").llygrid("reload",{},true);
				}) ;
			})
			
			$(".add-box-product-req").live("click",function(){
				if( warehouse.IN_SOURCE_TYPE == "fba" ){
					openCenterWindow(contextPath+"/page/forward/Warehouse.In.editFBAListing/"+currentId+"/",800,550,function(){
						$(".grid-content-details").llygrid("reload",{},true);
					}) ;
				}else{
					openCenterWindow(contextPath+"/page/forward/Warehouse.In.editBoxProductPageForReq/"+currentId+"/",850,650,function(){
						$(".grid-content-details").llygrid("reload",{},true);
					}) ;
				}
				
			})
			
			$(".edit-box-product").live("click",function(){
				//var boxPId = $(this).attr("val") ;
				var record = $(this).closest("tr").data("record") ;
				openCenterWindow(contextPath+"/page/model/Warehouse.In.editBoxProductPage/"+currentId+"/"+record.ID,850,650,function(){
					$(".grid-content-details").llygrid("reload",{},true);
				}) ;
			})
			
   	 });
   	 
   	 function openCallback(type){
   	 	if(type == 'box'){
   	 		$(".grid-content").llygrid("reload",{},true);
   	 	}else{
   	 		$(".grid-content-details").llygrid("reload",{},true);
   	 	}
   	 }
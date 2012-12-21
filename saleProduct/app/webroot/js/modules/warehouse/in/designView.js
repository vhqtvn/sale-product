$(function() {
	WDesign.initEvent() ;

	WDesign.render(designText) ;
	
	var tab = $('#tabs-default').tabs( {//$this->layout="index";
		tabs:[
			{label:'基本信息',content:'base-tab'},
			{label:'上架货品信息<button class="btn add-unitProduct" onclick="addUnitProduct();return false;">添加货品</button>',content:'product-tab'}
		] ,
		height:'230px',
		select:function(event,ui){
			if(ui.index == 1){
				$(".product-grid").llygrid("reload",{},true)
			}
		}
	} ) ;

	$(".design-view").click(function(){
		openCenterWindow("/saleProduct/index.php/page/model/Warehouse.In.loadDesign/"+warehouseId,1000,700) ;
	}) ;
	
	$(".product-grid").llygrid({
				columns:[
					{align:"center",key:"REAL_SKU",label:"货品SKU",sort:true, width:"10%"},
		           	{align:"center",key:"NAME",label:"货品名称", width:"20%"},
		           	{align:"center",key:"IMAGE_URL",label:"图片", width:"20%",format:function(val,record){
			           		if(val){
			           			val = val.replace(/%/g,'%25') ;
			           		}else{
			           			return "" ;
			           		}
			           		return "<img src='/saleProduct/"+val+"' onclick='showImg(this)' style='width:25px;height:25px;'>" ;
			           	}},
		           	{align:"center",key:"UNIT_QUANTITY",label:"数量",sort:true, width:"20%"}
		         ],
		         ds:{type:"url",content:"/saleProduct/index.php/grid/query/"},
				 limit:20,
				 pageSizes:[10,20,30,40],
				 height:function(){
				 	return 175 ;
				 },
				 title:"",
				 autoWidth:true,
				 indexColumn:false,
				 querys:{sqlId:"sql_warehouse_item_product",unitId:""},
				 loadMsg:"数据加载中，请稍候......"
			}) ;
});

	
	
function addUnitProduct(){
	openCenterWindow("/saleProduct/index.php/page/forward/Warehouse.Design.loadUnitProduct/"+warehouseId,800,600) ;
	return false ;
} ;
	

var blockIndex = 0 ;

function getBlockId(){
	blockIndex++ ;
	return warehouseId+"_"+blockIndex ;
}

var WDesign = {
	render : function(items) {
		//var items = [{"key":"rkm","text":"入库门","left":98.89999389648438,"top":58,"width":100,"height":18},{"key":"ckm","text":"出库门","left":98.89999389648438,"top":127,"width":100,"height":18},{"key":"td","text":"通道","left":99.89999389648438,"top":203,"width":100,"height":18},{"key":"hj","text":"货架","left":97.89999389648438,"top":267,"width":100,"height":18}]
		$(items).each(function(){
			var left =  $(".design-area").offset().left;
			var top =  $(".design-area").offset().top;
			var item = this ;
			var key = item.key ;
			var text = item.text ;
			var left = item.left +left ;
			var top = item.top -top - 3 ;
			var id = item.id||getBlockId() ;
			
			if(window.unitMap && window.unitMap[id]){
				text = window.unitMap[id]['CODE'] ;
			}
			
			var containment = '.design-area' ;
			
			$('<div class="block w-'+key+'" blockId="'+id+'"  key="'+key+'" style="position: absolute; left: '+left+'px; top:'+top+'px;">'+text+'</div>')
				.appendTo(".design-area").css({width:item.width,height:item.height});
		}) ;
	},
	format:function(){
		var result = [];
			$(".design-area").find("[key]").each(function() {
				var item = {};
				var pleft = $(".design-area").offset().left;
				var text = $(this).text();
				var key = $(this).attr("key");
				var offset = $(this).offset();
				item.key = key;
				item.text = text ;
				item.left = offset.left - pleft;
				item.top = offset.top;
				item.width = $(this).width();
				item.height = $(this).height();
				item.code = '' ;
	
				result.push(item);
			});
		return result ;
	},
	initEvent:function(){

		$(".design-area .block[key='hw']").live("click",function(){
			$(".active").removeClass("active");
			$(this).addClass("active");
			var blockId = $(this).attr("blockId");
			$.dataservice("sqlId:sql_warehouse_itemGetById",{id:blockId}
			,function(resp){
				var rowMap = {} ;
				for(var o in resp[0]){
					 rowMap = resp[0][o] ;
				}

				$("#blockId").val(blockId) ;
				$("#code").val( rowMap['CODE'] ) ;
				$("#memo").val( rowMap['MEMO'] ) ;
			});
		}) ;
		
		$(".save-config").click(function(){
			var code = $("#code").val() ;
			var memo = $("#memo").val() ;
			var blockId = $("#blockId").val() ;
			$.dataservice("model:Warehouse.Design.saveHw",{id:blockId,code:code,memo:memo,warehouseId:warehouseId}
			,function(){
				alert("保存成功！");	
			})
		}) ;
	
	}
}
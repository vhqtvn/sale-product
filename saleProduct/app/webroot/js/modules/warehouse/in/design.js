$(function() {
	WDesign.initEvent() ;

	$(".save").click(function() {
		WDesign.save(function(){
			window.location.reload();
		});
	});
	
	WDesign.render(designText) ;
});

var blockIndex = 0 ;

function getBlockId(){
	blockIndex++ ;
	return warehouseId+"_"+$blockIndex+'_'+blockIndex ;
}

var WDesign = {
	save:function(callback){
		var result = WDesign.format() ;
		$.dataservice("model:Warehouse.In.saveDesign",{text:$.json.encode(result),warehouseId:warehouseId},function(){
			callback(result) ;
		}) ;
	},
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
				.appendTo(".design-area").resizable({
					handles : "all"
				}).draggable({
					containment : containment
				}).css({width:item.width,height:item.height});
		}) ;
	},
	format:function(){
		var result = [];
			$(".design-area").find("[key]").each(function() {
				var item = {};
				var pleft = $(".design-area").offset().left;
				var blockId = $(this).attr("blockId");
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
				item.id = blockId ;
	
				result.push(item);
			});
		return result ;
	},
	initEvent:function(){
		$(".tool-area .block").draggable({
			helper : "clone",
			cursor : 'crosshair'
		});
		
		$(".design-area .block").live("click",function(){
			$(".active").removeClass("active");
			$(this).addClass("active");
			$(".btn-delete").removeAttr("disabled").removeClass("disabled");
		}) ;
		
		$(".design-area .block[key='hw']").live("click",function(){
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
		
		$(".btn-delete").click(function(){
			var blockId = $(".block.active").attr("blockId");
			if( window.confirm("确认删除吗？")){
				$(".block.active").remove() ;
				$(".btn-delete").attr("disabled","disabled").addClass("disabled");
			}
		}) ;
		
		$(".save-config").click(function(){
			var code = $("#code").val() ;
			var memo = $("#memo").val() ;
			var blockId = $("#blockId").val() ;
			$.dataservice("model:Warehouse.Design.saveHw",{id:blockId,code:code,memo:memo,warehouseId:warehouseId}
			,function(){
				alert("保存成功！");	
				window.location.reload();
			})
		}) ;
	
		$(".design-area").droppable({
			accept : ".tool-area .block",
			drop : function(event, ui) {
				var key = ui.helper.attr("key");
				var text = ui.helper.text();
				var outerHTML = ui.helper[0].outerHTML
				var html = outerHTML;
				
				var containment = '.design-area' ;
				
				$(html).appendTo($(".design-area")).resizable({
							handles : "all"
						}).draggable({
							containment : containment
						});
			}
		});
	}
}
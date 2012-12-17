$(function() {
	WDesign.initEvent() ;

	$(".save").click(function() {
		WDesign.save(function(){
			
		});
	});
	
	WDesign.render(designText) ;
});

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

			$('<div class="block w-'+key+'" key="'+key+'" style="position: absolute; left: '+left+'px; top:'+top+'px;">'+text+'</div>')
				.appendTo(".design-area").resizable({
					handles : "all"
				}).draggable({
					containment : ".design-area"
				}).css({width:item.width,height:item.height});
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
	
				result.push(item);
			});
		return result ;
	},
	initEvent:function(){
		$(".tool-area .block").draggable({
			helper : "clone",
			cursor : 'crosshair'
		});
	
		$(".design-area").droppable({
			accept : ".tool-area .block",
			drop : function(event, ui) {
				var key = ui.helper.attr("key");
				var text = ui.helper.text();
				var outerHTML = ui.helper[0].outerHTML
				var html = outerHTML;
				$(html).appendTo($(".design-area")).resizable({
							handles : "all"
						}).draggable({
							containment : ".design-area"
						});
			}
		});
	}
}
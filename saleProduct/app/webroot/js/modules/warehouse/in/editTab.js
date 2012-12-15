$(function(){
	var tab = $('#details_tab').tabs( {
		tabs:[
			{label:'基本信息',iframe:true,url:"/saleProduct/index.php/page/model/Warehouse.In.edit/"+inId,id:'t1'},//9
			{label:'物流货品',iframe:true,url:"/saleProduct/index.php/page/model/Warehouse.In.editBox/"+inId,id:'t2'},
			{label:'跟踪状态',iframe:true,url:"/saleProduct/index.php/page/model/Warehouse.In.editTrack/"+inId,id:'t3'}
		] ,
		height:'520px',
		select:function(event,ui){
			var index = ui.index ;
			//renderAction(index);
		}
	} ) ;
}) ;
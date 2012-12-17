 //result.records , result.totalRecord
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
		$(".message,.loading").hide() ;
		
			$(".grid-content").llygrid({
				columns:[
					{align:"center",key:"ID",label:"操作",width:"15%",format:function(val,record){
						var status = record.STATUS ;
						var html = [] ;
						html.push("<a href='#' class='edit' val='"+val+"'>编辑</a>&nbsp;&nbsp;") ;
						html.push("<a href='#' class='design' val='"+val+"'>视图</a>&nbsp;&nbsp;") ;
						return html.join("") ;
					}},
					{align:"center",key:"CODE",label:"代码",width:"8%",forzen:false,align:"left"},
		           	{align:"center",key:"NAME",label:"仓库名称",width:"20%",forzen:false,align:"left"},
		           	{align:"center",key:"ADDRESS",label:"仓库地址",width:"20%"},
		           	{align:"center",key:"ZIPCODE",label:"邮编",width:"10%"},
		           	{align:"center",key:"MEMO",label:"备注",width:"20%"}
		         ],
		         ds:{type:"url",content:"/saleProduct/index.php/grid/query"},
				 limit:10,
				 pageSizes:[10,20,30,40],
				 height:200,
				 title:"商家列表",
				 indexColumn:false,
				 querys:{sqlId:"sql_warehouse_lists"},
				 loadMsg:"数据加载中，请稍候......"
			}) ;
			
			$(".design").live("click",function(){
				var val = $(this).attr("val") ;
				openCenterWindow("/saleProduct/index.php/page/model/Warehouse.In.loadDesign/"+val,1000,630) ;
			}) ;
			

			$(".add").click(function(){
				openCenterWindow("/saleProduct/index.php/warehouse/addPage",650,530) ;
			}) ;
			
			$(".edit").live("click",function(){
				var val = $(this).attr("val") ;
				openCenterWindow("/saleProduct/index.php/warehouse/editPage/"+val,650,530) ;
				return false;
			}) ;
		
   	 });


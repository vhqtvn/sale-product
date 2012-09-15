<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>llygrid demo</title>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
    <meta http-equiv="pragma" content="no-cache"/>
	<meta http-equiv="cache-control" content="no-cache"/>

   <?php
		echo $this->Html->meta('icon');
		echo $this->Html->css('../grid/redmond/ui');
		echo $this->Html->css('../grid/grid');
		echo $this->Html->css('../grid/redmond/ui');
		echo $this->Html->css('../kissu/widgets/core/layout/layout');
		echo $this->Html->css('../kissu/widgets/core/tree/ui.tree');

		echo $this->Html->script('jquery');
		echo $this->Html->script('../kissu/scripts/jquery.utils');
		echo $this->Html->script('jquery.json');
		echo $this->Html->script('../grid/grid');
		echo $this->Html->script('../kissu/widgets/core/layout/jquery.layout');
		echo $this->Html->script('../kissu/widgets/core/tree/jquery.tree');
	?>
	
   <script type="text/javascript">
    var treeData = {id:"root",text:"产品分类",isExpand:true,childNodes:[]} ;
    var treeMap  = {} ;

    <?php
    	$index = 0 ;
		foreach( $categorys as $Record ){
			$sfs = $Record['sc_product_category']  ;
			$id   = $sfs['ID'] ;
			$name = $sfs['NAME']."(".$Record[0]['TOTAL'].")" ;
			$pid  = $sfs['PARENT_ID'] ;
			echo " var item$index = {id:'$id',text:'$name',isExpand:true} ;" ;
			
			echo " treeMap['id_$id'] = item$index  ;" ;
			if(empty($pid)){
				echo " item$index ['childNodes'] = item$index ['childNodes']||[] ;" ;
				echo "treeData.childNodes.push( item$index ) ;" ;
			}else{
				echo " treeMap['id_$pid'].childNodes.push( item$index ) ;" ;
			}
			$index++ ;
		} ;
	?>
   
   var taskId = '<?php echo $taskId ;?>' ;
   
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
			$('#default-tree').tree({//tree为容器ID
				source:'array',
				data:treeData ,
				onNodeClick:function(id,text,record){
					if( id == 'root' ){
						$(".grid-content").llygrid("reload",{categoryId:""}) ;
					}else{
						$(".grid-content").llygrid("reload",{categoryId:id}) ;
					}
				}
	       }) ;
			setTimeout(function(){
				$(".grid-content").llygrid({
					columns:[
			           	{align:"center",key:"ASIN",label:"ASIN", width:"90",format:function(val,record){
			           		return "<a href='#' class='product-detail' asin='"+val+"'>"+val+"</a>" ;
			           	}},
			           	{align:"center",key:"LOCAL_URL",label:"Image",width:"6%",forzen:false,align:"left",format:function(val,record){
			           		if(val){
			           			val = val.replace(/%/g,'%25') ;
			           		}else{
			           			return "" ;
			           		}
			           		return "<img src='/saleProduct/"+val+"' onclick='showImg(this)' style='width:50px;height:50px;'>" ;
			           	}},
			           	{align:"center",key:"TITLE",label:"TITLE",width:"20%",forzen:false,align:"left",format:function(val,record){
			           		return "<a href='http://www.amazon.com/gp/offer-listing/"+record.ASIN+"' target='_blank'>"+val+"</a>" ;
			           	}},
			           	{align:"center",key:"TARGET_PRICE",label:"目标价格",width:"7%"},
			           	{align:"center",key:"DAY_PAGEVIEWS",label:"每日PV",width:"7%"},
			           	{align:"center",key:"FM_NUM",label:"FM数量",width:"7%"},
			           	{align:"center",key:"NM_NUM",label:"NM数量",width:"7%"},
			           	{align:"center",key:"UM_NUM",label:"UM数量",width:"7%"},
			           	{align:"center",key:"FBA_NUM",label:"FBA数量",width:"7%"},
			           	{align:"center",key:"REVIEWS_NUM",label:"Reviews数量",width:"9%"},
			           	{align:"center",key:"QUALITY_POINTS",label:"质量分",width:"5%"}
			         ],
			         ds:{type:"url",content:"/saleProduct/index.php/grid/product/"+taskId},
					 limit:30,
					 pageSizes:[10,20,30,40],
					 height:400,
					 title:"",
					 indexColumn:false,
					 querys:{name:"hello",name2:"world"},
					 loadMsg:"数据加载中，请稍候......"
				}) ;
			},200) ;
				
			
			$(".product-detail").live("click",function(){
				var asin = $(this).attr("asin") ;
				openCenterWindow("/saleProduct/index.php/product/details/"+asin,950,650) ;
			})
			
			$(".query-btn").click(function(){
				var asin = $("[name='asin']").val() ;
				var title = $("[name='title']").val() ;
				var querys = {} ;
				//if(asin){
				querys.asin = asin ;
				//}
				//if(title){
				querys.title = title ;
				//}
				
				querys.test_status = $("[name='test_status']").val()||"" ;
				
				$(".grid-content").llygrid("reload",querys) ;	
			}) ;
   	 });
   </script>
   
   <style>
   		*{
   			font:12px "微软雅黑";
   		}
   </style>

</head>
<body style="magin:0px;padding:0px;">
	<div class="widget-class" widget="layout" style="width:100%;height:90%;">
		<div region="center" split="true" border="true" title="产品列表" style="padding:2px;">
			<div class="query-bar">
				<label>ASIN:</label><input type="text" name="asin"/>
				<label>名称:</label><input type="text" name="title"/>
				<label>状态:</label>
				<select name="test_status">
					<option value="">--状态--</option>
					<option value="testing">试销中</option>
					<option value="formal">正式销售</option>
					<option value="uninstall">下架</option>
					<option value="focus">异常关注</option>
				</select>
				
				<button class="query-btn">查询</button>
			</div>
			<div class="grid-content" style="width:98%;">
			
			</div>
		</div>
		<div region="west" icon="icon-edit" split="true" border="true" title="产品分类" style="width:180px;">
			<div id="default-tree" class="tree" style="padding: 5px; "></div>
		</div>
   </div>
	
</body>
</html>

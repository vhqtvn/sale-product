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
     //result.records , result.totalRecord
	var treeData = {id:"root",text:"上传任务组",isExpand:true,childNodes:[]} ;
    var treeMap  = {} ;

    <?php
    	$index = 0 ;
		foreach( $uploadGroup as $Record ){
			$sfs = $Record['sc_upload_group']  ;
			$id   = $sfs['ID'] ;
			$name = $sfs['NAME']."(".$Record[0]['TOTAL'].")" ;
			$pid  = $sfs['PARENT_ID'] ;
			echo " var item$index = {id:'$id',text:'$name',name:'".$sfs['NAME']."',isExpand:true} ;" ;
			
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
	
	var currentGroup = "" ;

	$(function(){
		//上传任务组列表
		$('#default-tree').tree({//tree为容器ID
			source:'array',
			data:treeData ,
			onNodeClick:function(id,text,record){
				if( id == 'root' ){
					currentGroup = "" ;
					$(".grid-content").llygrid("reload",{groupId:""}) ;
				}else{
					currentGroup = record ;
					$(".grid-content").llygrid("reload",{groupId:id}) ;
				}
			}
       }) ;
		
		
		//上传任务脚本
		
		$(".message,.loading").hide() ;
			$(".grid-content").llygrid({
				columns:[
		           	{align:"center",key:"ID",label:"编号", width:"15%"},
		           	{align:"center",key:"NAME",label:"上传文件名",width:"20%",forzen:false,align:"left",format:function(val,record){
		      
		           		return "<a href='#' class='show-products' val='"+record.ID+"'>"+val+"</a>" ;
		           	}},
		           	{align:"center",key:"UPLOAD_TIME",label:"上传时间",width:"20%"},
		           	{align:"center",key:"USERNAME",label:"上传者",width:"10%"},
		           		{align:"center",key:"TOTAL",label:"产品总数",width:"10%"},
					{align:"center",key:"ID",label:"采集操作",width:"25%",format:function(val,record){
						var html = [] ;
					html.push("<a href='#' class='gather-action' val='"+val+"'>信息采集</a>&nbsp;&nbsp;") ;
					return html.join("") ;
					}}
		         ],
		         ds:{type:"url",content:"/saleProduct/index.php/grid/upload"},
				 limit:20,
				 pageSizes:[10,20,30,40],
				 height:400,
				 title:"上传列表",
				 indexColumn:false,
				 querys:{name:"hello",name2:"world"},
				 loadMsg:"数据加载中，请稍候......"
			}) ;

			$(".upload-product").click(function(){
				var id = currentGroup.id ;
				var text = currentGroup.name ;
				if(id){
					openCenterWindow("/saleProduct/index.php/product/uploadPage/"+id+"/"+text,600,400) ;
				}else
					openCenterWindow("/saleProduct/index.php/product/uploadPage",600,400) ;
			}) ;
			
			
			var currentGather = null ;
			$(".gather-action").live("click",function(){
				var id = $(this).attr("val") ;
				$.ajax({
					type:"post",
					url:"/saleProduct/index.php/gatherUpload/taskAll/"+id,
					data:{},
					cache:false,
					dataType:"text",
					success:function(result,status,xhr){
						//alert(result);
					}
				}); 
			}) ;
			
			$(".show-products").live("click",function(){
				var val = $(this).attr("val") ;
				openCenterWindow("/saleProduct/index.php/product/index/"+val,900,600) ;
			}) ;
   	 });
   	 
   	 function uploadSuccess(id){
   	 	$.ajax({
			type:"post",
			url:"/saleProduct/index.php/gatherUpload/taskAll/"+taskId,
			data:{},
			cache:false,
			dataType:"text",
			success:function(result,status,xhr){
				//alert(result);
				//$(".message").html(result) ;
			}
		}); 
   	 }
   	 

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
   </script>
   
   <style>
   		*{
   			font:12px "微软雅黑";
   		}
   		
   		.message{
   			width:600px;
   			border:1px solid #CCC;
   			overflow:auto;
   			margin:5px;
   			height:200px;
   			background:#000;
   			color:#FFF;
   			margin-bottom:0px;
   		}
   		
   		.loading{
   			width:600px;
   			background:#000;
   			color:#FFF;
   			margin-top:-1px;
   			display:hidden;
   			margin-left:6px;
   		}
   </style>

</head>


<body style="magin:0px;padding:0px;">
	<div class="widget-class" widget="layout" style="width:100%;height:90%;">
		<div region="center" split="true" border="true" title="上传任务" style="padding:2px;">
			<button class="upload-product">添加产品</button>
			<div class="grid-content" style="width:99%;">
			
			</div>
			
			<div class="message">
			</div>
			<div class="loading">
				处理中......
			</div>
		</div>
		<div region="west" icon="icon-edit" split="true" border="true" title="上传任务组" style="width:180px;">
			<div id="default-tree" class="tree" style="padding: 5px; "></div>
		</div>
   </div>
	
</body>
</html>

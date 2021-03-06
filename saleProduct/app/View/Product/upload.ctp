<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>上传产品列表</title>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
    <meta http-equiv="pragma" content="no-cache"/>
	<meta http-equiv="cache-control" content="no-cache"/>

   <?php
  		 include_once ('config/config.php');
   
		echo $this->Html->meta('icon');
		echo $this->Html->css('../js/grid/jquery.llygrid');
		echo $this->Html->css('../js/layout/jquery.layout');
		echo $this->Html->css('../js/tree/jquery.tree');
		
		echo $this->Html->css('default/style');

		echo $this->Html->script('jquery');
		echo $this->Html->script('common');
		echo $this->Html->script('jquery.json');
		echo $this->Html->script('grid/jquery.llygrid');
		echo $this->Html->script('layout/jquery.layout');
		echo $this->Html->script('tree/jquery.tree');
		
		$SqlUtils  = ClassRegistry::init("SqlUtils") ;
		$security  = ClassRegistry::init("Security") ;
		
		$otherCount = $SqlUtils->getObject("sql_product_upload_count_fornocategory",array()) ;
		
		$count = $otherCount['c'] ;
	?>
  
   <script type="text/javascript">
     //result.records , result.totalRecord
	var treeData = {id:"root",text:"上传任务组",isExpand:true,childNodes:[]} ;
    var treeMap  = {} ;

    <?php
    $Utils  = ClassRegistry::init("Utils") ;

    $Utils->echoTreeScript( $uploadGroup ,null, function( $sfs, $index ,$ss ){
    	$id   = $sfs['ID'] ;
			$name = $sfs['NAME']."(".$sfs['TOTAL'].")" ;
			$pid  = $sfs['PARENT_ID'] ;
			echo " var item$index = {id:'$id',text:'$name',name:'".$sfs['NAME']."',isExpand:true} ;" ;
    } ) ;
	?>
	
	var currentGroup = "" ;

	$(function(){

		treeData.childNodes.push( {id:"more",text:"未分类任务组(<?php echo $count;?>)",isExpand:true,childNodes:[]}  ) ;
		
		//上传任务组列表
		$('#default-tree').tree({//tree为容器ID
			source:'array',
			data:treeData ,
			onNodeClick:function(id,text,record){
				if( id == 'root' ){
					currentGroup = "" ;
					$(".grid-content").llygrid("reload",{groupId:"",sqlId:"sql_product_upload"}) ;
				}else if( id == 'more' ){
					currentGroup = "" ;
					$(".grid-content").llygrid("reload",{groupId:"",sqlId:"sql_product_upload_more"}) ;
				}else{
					currentGroup = record ;
					$(".grid-content").llygrid("reload",{groupId:id,sqlId:"sql_product_upload"}) ;
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
		        	{align:"center",key:"PLATFORM_NAME",label:"平台",width:"15%"},
		           	{align:"center",key:"UPLOAD_TIME",label:"上传时间",width:"15%"},
		           	{align:"center",key:"USERNAME",label:"上传者",width:"10%"},
		           		{align:"center",key:"TOTAL",label:"产品总数",width:"10%"},
					{align:"center",key:"ID",label:"获取操作",width:"8%",format:function(val,record){
						var html = [] ;
					html.push("<a href='#' class='gather-action' val='"+val+"'>信息获取</a>&nbsp;&nbsp;") ;
					return html.join("") ;
					}}
		         ],
		         //ds:{type:"url",content:contextPath+"/grid/upload"},
		         ds:{type:"url",content:contextPath+"/grid/query"},
				 limit:20,
				 pageSizes:[10,20,30,40],
				 height:function(){
					return $(window).height() - 140;
				},
				 title:"上传列表",
				 indexColumn:false,
				 querys:{sqlId:"sql_product_upload"},
				 loadMsg:"数据加载中，请稍候......"
			}) ;

			$(".upload-product").click(function(){
				var id = currentGroup.id ;
				var text = currentGroup.name ;
				if(id){
					openCenterWindow(contextPath+"/product/uploadPage/"+id+"/"+text,650,450) ;
				}else
					openCenterWindow(contextPath+"/product/uploadPage",650,450) ;
			}) ;
			
			
			var currentGather = null ;
			$(".gather-action").live("click",function(){
				var id = $(this).attr("val") ;
				$.ajax({
					type:"post",
					url:contextPath+"/gatherUpload/taskAll/"+id,
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
				openCenterWindow(contextPath+"/product/index/"+val,900,600) ;
			}) ;
   	 });
   	 
   	 function uploadSuccess(id){
   	 	$.ajax({
			type:"post",
			url:contextPath+"/gatherUpload/taskAll/"+taskId,
			data:{},
			cache:false,
			dataType:"text",
			success:function(result,status,xhr){
				//alert(result);
				//$(".message").html(result) ;
			}
		}); 
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


<body style="magin:0px;padding:0px;"  data-widget="layout">
	<div data-widget="layout" style="width:100%;height:100%;">
		<div region="center" split="true" border="true"  title="上传任务"style="padding:2px;">
			<button class="upload-product btn btn-primary">添加产品</button>
			<div class="grid-content" style="width:99%;">
			
			</div>
			
			<div class="message">
			</div>
			<div class="loading">
				处理中......
			</div>
		</div>
		<div region="west" icon="icon-edit" split="true" border="true" title="营销产品分类" style="width:200px;">
			<div id="default-tree" class="tree" style="padding: 5px; "></div>
		</div>
   </div>

</body>
</html>

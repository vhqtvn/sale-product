<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>动态标签管理</title>
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
		$groups = $SqlUtils->exeSql("sql_package_group_list",array() ) ;
		
		
		$types = $SqlUtils->exeSqlWithFormat("select * from sc_tag_type",array()) ;

	?>
	
   <script type="text/javascript">

	$(function(){
		var currentType = "" ;
		       
			setTimeout(function(){
				$(".grid-content").llygrid({
					columns:[
			           	{align:"center",key:"NAME",label:"标签名称",width:"30%"},
			           	{align:"center",key:"DESCRIPTION",label:"描述",width:"60%"}
			         ],
			         ds:{type:"url",content:contextPath+"/grid/query/"},
					 limit:20,
					 pageSizes:[10,20,30,40],
					 height:function(){
						return $(window).height() - 150 ;
					},
					 title:"",
					 indexColumn:false,
					 querys:{sqlId:"sql_tag_list",typeId:'-'},
					 loadMsg:"数据加载中，请稍候......"
				}) ;
			},200) ;

			$(".tag-type-ul li").click(function(){
				$(".tag-type-ul li").removeClass("active") ;
				$(this).addClass("active") ;

				var typeId = $(this).attr("typeId") ;

				currentType = typeId ;
				
				$(".grid-content").llygrid("reload",{typeId:currentType}) ;
			}) ;

			$(".edit-tag").live("click",function(){
				if(!currentType){
					alert("先选择标签分类！") ;
					return ;
				}
				
				var tagId = $(this).attr("tagId")||"" ;
				openCenterWindow(contextPath+"/page/forward/Tag.editTag/"+currentType+"/"+tagId,500,320,function(){
					$(".grid-content").llygrid("reload",{typeId:currentType}) ;
				}) ;
			}) ;
				
			/*
			$(".product-detail").live("click",function(){
				var asin = $(this).attr("asin") ;
				openCenterWindow(contextPath+"/product/details/"+asin,950,650) ;
			})*/
			
			$(".query-btn").click(function(){
				var asin = $("[name='asin']").val() ;
				var title = $("[name='title']").val() ;
				var ts = $("[name='test_status']").val()||"" ;
				var querys = {} ;
				querys.asin = asin ;
				querys.title = title ;
				querys.categoryId = '' ;
				
				if(ts == 'focus'){
					querys.user_status = ts ;
				}else{
					querys.test_status = ts ;
				}
				
				$(".grid-content").llygrid("reload",querys,true) ;	
			}) ;
   	 });
   </script>
   
   <style>
   		*{
   			font:12px "微软雅黑";
   		}
   		
   		.tag-type-ul {
			margin:0px;
   		}
   		
   		.tag-type-ul li{
			display:block;
   			list-style: none;
   			padding:5px 10px;
   			cursor:pointer ;
   		}
   		
		.tag-type-ul li.active{
			 background:#CCC;
   		}
   		
		.tag-type-ul li:hover{
			 background:#EEE;
   		}
   </style>

</head>
<body style="magin:0px;padding:0px;">
	<div data-widget="layout" style="width:100%;height:100%;">
	
		<div region="center" split="true" border="true" title="标签列表" style="">
		
			<div class="toolbar toolbar-auto query-bar">
					<table style="width:100%;" class="query-table">	
						<tr>
							<td style="text-align:right;">
								<button class="btn btn-primary edit-tag">添加标签</button>
							</td>
						</tr>
					</table>
				</div>
					<div class="panel grid-panel">
						<div class="grid-content" style="width:100%;"></div>
					</div>
		</div>
		<div region="west" icon="icon-edit" split="true" border="true" title="标签分类列表"  style="width:180px;">
			<div class="toolbar toolbar-auto query-bar">
					<table style="width:100%;" class="query-table">	
						<tr>
							<td>
								<button class="btn btn-primary" disabled="disabled" >添加标签分类</button>
							</td>
						</tr>
					</table>
				</div>
			   <ul  class="tag-type-ul  ">
			   <?php 
			   	foreach( $types as $type ){
			   		echo "<li  typeId='".$type['ID']."'>".$type['NAME']."</li>" ;
			   	}
			   ?>
			   </ul>
		</div>
   </div>
	
</body>
</html>

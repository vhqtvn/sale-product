<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>产品列表</title>
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
	?>
	
   <script type="text/javascript">
    var treeData = {id:"root",text:"产品分类",isExpand:true,childNodes:[]} ;
    var treeMap  = {} ;

    <?php
    $Utils  = ClassRegistry::init("Utils") ;
    
    $Utils->echoTreeScript( $categorys ,null, function( $sfs, $index ,$ss ){
    	$id   = $sfs['ID'] ;
			$name = $sfs['NAME']."(".$sfs['TOTAL'].")" ;
			$pid  = $sfs['PARENT_ID'] ;
			echo " var item$index = {id:'$id',text:'$name',isExpand:true} ;" ;
    } ) ;
    
	?>
   
   var taskId = '<?php echo $taskId ;?>' ;


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
				var initParams = {sqlId:"sql_product_list",countSqlId:"sql_product_list_count"};
				if(taskId){
					initParams = {sqlId:"sql_task_product_list",countSqlId:"sql_task_product_list_count",taskId:taskId};
				}
				initParams.categoryId = "--" ;
				$(".grid-content").llygrid({
					columns:[
			           	{align:"center",key:"ASIN",label:"ASIN", width:"90",format:function(val,record){
			           		return "<a href='#' class='product-detail' asin='"+val+"'>"+val+"</a>" ;
			           	}},
			           	{align:"center",key:"LOCAL_URL",label:"Image",width:"6%",forzen:false,align:"left",format:{type:'img'}},
			           	{align:"center",key:"TITLE",label:"TITLE",width:"20%",forzen:false,align:"left",format:function(val,record){
			           		return "<a href='"+contextPath+"/page/forward/Platform.asin/"+record.ASIN+"' target='_blank'>"+val+"</a>" ;
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
			         ds:{type:"url",content:contextPath+"/grid/query/"},
					 limit:20,
					 pageSizes:[10,20,30,40],
					 height:function(){
						return $(window).height() - 150 ;
					},
					 title:"",
					 indexColumn:false,
					 querys:initParams,
					 loadMsg:"数据加载中，请稍候......"
				}) ;
			},200) ;
				
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
   </style>

</head>
<body style="magin:0px;padding:0px;">
	<div data-widget="layout" style="width:100%;height:100%;">
	
		<div region="center" split="true" border="true" title="产品列表" style="padding:2px;">
		
			<div class="toolbar toolbar-auto">
						<table>
							<tr>
								<th>ASIN：
								</th>
								<td>
									<input type="text" name="asin" class="input-medium"/>
								</td>
								<th>
									名称:
								</th>
								<td>
									<input type="text" name="title" class="input-medium"/>
								</td>
								<!-- 
								<th>
									状态:
								</th>
								<td>
									<select name="test_status" class="input-medium">
										<option value="">--状态--</option>
										<option value="testing">试销中</option>
										<option value="formal">正式销售</option>
										<option value="uninstall">下架</option>
										<option value="focus">异常关注</option>
									</select>
								</td>		
								 -->						
								<td class="toolbar-btns" rowspan="3">
									<button class="query-btn btn btn-primary">查询</button>
								</td>
							</tr>						
						</table>					

					</div>	
					<div class="panel grid-panel">
						<div class="grid-content" style="width:100%;"></div>
					</div>
		</div>
		<div region="west" icon="icon-edit" split="true" border="true" title="产品分类" style="width:180px;">
			<div id="default-tree" class="tree" style="padding: 5px; "></div>
		</div>
   </div>
	
</body>
</html>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
   <?php echo $this->Html->charset(); ?>
    <title>供应商</title>
    <meta http-equiv="pragma" content="no-cache"/>
	<meta http-equiv="cache-control" content="no-cache"/>
	
   <?php
   		include ('config/config.php');
	
		 $id = $params['arg1'] ;
		 $SqlUtils  = ClassRegistry::init("SqlUtils") ;
		 $categorys = $SqlUtils->exeSql("sql_saleproduct_categorytreeBySupplier",array('supplierId'=>$id) ) ;

	?>
	
	
	<link href="/<?php echo $fileContextPath?>/app/webroot/favicon.ico" type="image/x-icon" rel="icon" />
	<link href="/<?php echo  $fileContextPath;?>/app/webroot/favicon.ico" type="image/x-icon" rel="shortcut icon" />
	<link rel="stylesheet" type="text/css" href="/<?php echo  $fileContextPath;?>/app/webroot/css/../js/validator/jquery.validation.css" />
	<link rel="stylesheet" type="text/css" href="/<?php echo  $fileContextPath;?>/app/webroot/css/../js/tree/jquery.tree.css" />
	<link rel="stylesheet" type="text/css" href="/<?php echo  $fileContextPath;?>/app/webroot/css/default/style.css" />
	<link rel="stylesheet" type="text/css" href="/<?php echo  $fileContextPath;?>/app/webroot/css/../js/tab/jquery.ui.tabs.css" />
	<link rel="stylesheet" type="text/css" href="/<?php echo  $fileContextPath;?>/app/webroot/css/../js/grid/jquery.llygrid.css" />
	<script type="text/javascript" src="/<?php echo  $fileContextPath;?>/app/webroot/js/jquery.js"></script>
	<script type="text/javascript" src="/<?php echo  $fileContextPath;?>/app/webroot/js/common.js"></script>
	<script type="text/javascript" src="/<?php  echo $fileContextPath;?>/app/webroot/js/jquery-ui.js"></script>
	<script type="text/javascript" src="/<?php echo  $fileContextPath;?>/app/webroot/js/../grid/query.js"></script>
	<script type="text/javascript" src="/<?php echo  $fileContextPath;?>/app/webroot/js/jquery.json.js"></script>
	<script type="text/javascript" src="/<?php echo  $fileContextPath;?>/app/webroot/js/grid/jquery.llygrid.js"></script>
	<script type="text/javascript" src="/<?php echo  $fileContextPath;?>/app/webroot/js/validator/jquery.validation.js"></script>
	<script type="text/javascript" src="/<?php echo  $fileContextPath;?>/app/webroot/js/tree/jquery.tree.js"></script>
	<script type="text/javascript" src="/<?php echo  $fileContextPath;?>/app/webroot/js/tab/jquery.ui.tabs.js"></script>
	
   <style>
   		*{
   			font:12px "微软雅黑";
   		}

		.rule-content-item{
			clear:both;
		}

		.item-label,.item-relation,.item-value,.item-value{
			float:left;
		}
		
		.table-bordered tr td,.table-bordered tr th{
			padding:5px;
		}
		.WdateDiv, .ui-corner-all {
			border:none;
		}
		
		.eva-ul{
			list-style: none;
		}
		
		.eva-ul li{
			float:left;
			width:350px;
			padding:5px;
			margin:5px;
			border:1px solid #CCC;
		}
		
		.eva-ul li textarea{
			margin:5px 2px;
		}
		

   </style>

   <script>
		var supplierId = '<?php echo $id ;?>'
   
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
   

		   $(function(){
			   $('#default-tree').tree({//tree为容器ID
					source:'array',
					data:treeData ,
					//showCheck:true,
					cascadeCheck:false,
					onNodeClick:function(id,text,record){
						if( id == 'root' ){
							$(".grid-content").llygrid("reload",{categoryId:""}) ;
						}else{
							$(".grid-content").llygrid("reload",{categoryId:id}) ;
						}
					}
	           }) ;


				$(".action").live("click",function(){
					var record = $(this).parents("tr:first").data("record")||{} ;
					var id = record.ID;
					if( $(this).hasClass("view") ){
						openCenterWindow(contextPath+"/saleProduct/details/"+record.REAL_SKU+"/sku",900,650) ;
					}
					return false ;
				});
				
				

				$(".grid-content").llygrid({
					columns:[
					 	{align:"center",key:"IMAGE_URL",label:"图片",width:"5%",format:{type:'img'}},
					 	{align:"center",key:"IS_ONSALE",label:"销售状态",width:"5%",format:function(val,record){
					 		if(val == 1){
					 			return   getImage('checked.gif','在售中','onsale-status ');
					 		}
					 		
					 		return   getImage('unchecked.gif','未销售','unsale-status');
					 	}},
			           	{align:"center",key:"NAME",label:"名称",width:"20%",forzen:false,align:"left"},
			           	{align:"center",key:"REAL_SKU",label:"SKU",width:"10%",sort:true,format:function(val,record){
								return "<a href='#' class='action view'>"+val+"</a>" ;
				        }},
			           	{align:"center",key:"QUANTITY",label:"总",group:"库存",width:"5%",sort:true },
			        	{align:"center",key:"COMMON_QUANTITY",label:"普通",group:"库存",width:"5%" ,sort:true},
			        	{align:"center",key:"FBA_QUANTITY",label:"FBA",group:"库存",width:"5%" ,sort:true},
			           	{align:"center",key:"SECURITY_QUANTITY",label:"安全",group:"库存",width:"5%",sort:true },
			           	{align:"center",key:"TYPE",label:"货品类型",width:"10%",format:{type:"json",content:{'base':"基本类型",'package':"打包货品"}}},
			          
			           	{align:"center",key:"MEMO",label:"备注",width:"25%"}
			         ],
			         ds:{type:"url",content:contextPath+"/grid/query"},
					 limit:20,
					 pageSizes:[10,20,30,40],
					 height:function(){
					 	return $(window).height() - 100 ;
					 },
					 title:"",
					// autoWidth:true,
					 indexColumn:false,
					  querys:{sqlId:"sql_saleproduct_listBySupllierId",supplierId:supplierId,categoryId:''},
					 loadMsg:"数据加载中，请稍候......",
					 loadAfter:function(){}
						
				}) ;
			  }) ;
   </script>

</head>
<body style="overflow:hidden;">
		<div class="row-fluid">
			<div class="span3">
				<div id="default-tree" class="tree" style="padding: 5px;overflow-y:auto;overflow-x:hidden;height:460px; "></div>
			</div>
			<div class="span9" style="margin-left:1px;"> 
				<div class="grid-content"  ></div>
			</div>
		</div>
</body>
</html>
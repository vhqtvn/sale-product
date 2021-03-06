<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
   <?php echo $this->Html->charset(); ?>
    <title>llygrid demo</title>
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
   </style>

   <script>
    var treeData = {id:"root",text:"产品分类",isExpand:true,childNodes:[]} ;
    var treeMap  = {} ;
    var asin = '<?php echo $asin;?>' ;

    <?php
    $Utils  = ClassRegistry::init("Utils") ;
    
    $Utils->echoTreeScript( $categorys ,null, function( $sfs, $index ,$ss ){
    	$selected = $sfs['selected'] ;
    	$id   = $sfs['ID'] ;
    	$name = $sfs['NAME'] ;
    	$pid  = $sfs['PARENT_ID'] ;
    	$charger = $sfs['PURCHASE_CHARGER'] ;
    	$chargerName = $sfs['PURCHASE_CHARGER_NAME'] ;
    	echo " var item$index = {id:'$id',text:'$name',memo:'".$sfs['MEMO']."',isExpand:true} ;" ;
		if( !empty($selected) ){
				echo " item$index ['checkstate'] = 1 ;" ;
		}
    } ) ;
    
	?>
   
	$(function(){

		$('#default-tree').tree({//tree为容器ID
				source:'array',
				data:treeData ,
				showCheck:true,
				cascadeCheck:false
           }) ;
           
         $("button").click(function(){
        	var ids = $('#default-tree').tree().getSelectedIds() ;
        	$.ajax({
				type:"post",
				url:contextPath+"/product/saveProductCategory/"+asin+"/"+ids,
				data:{},
				cache:false,
				dataType:"text",
				success:function(result,status,xhr){
					window.location.reload() ;
				}
			}); 
        }) ;
	}) ;
   </script>

</head>
<body>
<div id='content-default' class='demo' style="padding:10px;">
	<div class="row-fluid">
		<div id="default-tree" class="tree span10" style="padding: 5px; "></div>
	</div>
	<button class="btn"> 保存产品分类 </button>
</div>

</html>
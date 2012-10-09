<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
   <?php echo $this->Html->charset(); ?>
    <title>llygrid demo</title>
    <meta http-equiv="pragma" content="no-cache"/>
	<meta http-equiv="cache-control" content="no-cache"/>

   <?php
		echo $this->Html->meta('icon');
		echo $this->Html->css('../grid/redmond/ui');
		echo $this->Html->css('../kissu/widgets/core/tree/ui.tree');
		echo $this->Html->css('../js/validator/jquery.validation');
		echo $this->Html->css('style-all');
		echo $this->Html->script('../kissu/scripts/jquery');
		echo $this->Html->script('../kissu/scripts/jquery.utils');
		echo $this->Html->script('jquery.json');
		echo $this->Html->script('../kissu/widgets/core/tree/jquery.tree');
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
    	$index = 0 ;
		foreach( $categorys as $Record ){
			$sfs = $Record['sc_amazon_product_category']  ;
			$selected =  $Record[0]['selected'] ;
			$id   = $sfs['ID'] ;
			$name = $sfs['NAME'] ;
			$pid  = $sfs['PARENT_ID'] ;
			echo " var item$index = {id:'$id',text:'$name',memo:'".$sfs['MEMO']."',isExpand:true} ;" ;
			if( !empty($selected) ){
				echo " item$index ['checkstate'] = 1 ;" ;
			}
			
			echo " treeMap['id_$id'] = item$index  ;" ;
			echo " item$index ['childNodes'] = item$index ['childNodes']||[] ;" ;
			$index++ ;
		} ;
		
		$index = 0 ;
		foreach( $categorys as $Record ){
			$sfs = $Record['sc_amazon_product_category']  ;
			$selected =  $Record[0]['selected'] ;
			$id   = $sfs['ID'] ;
			$name = $sfs['NAME'] ;
			$pid  = $sfs['PARENT_ID'] ;
			if(empty($pid)){
				echo "treeData.childNodes.push( item$index ) ;" ;
			}else{
				echo " treeMap['id_$pid'].childNodes = treeMap['id_$pid'].childNodes||[] ;" ;
				echo " treeMap['id_$pid'].childNodes.push( item$index ) ;" ;
			}
			$index++ ;
		} ;
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
				url:"/saleProduct/index.php/amazonaccount/saveProductCategory/"+asin+"/"+ids,
				data:{},
				cache:false,
				dataType:"text",
				success:function(result,status,xhr){
					window.location.reload() ;
				}
			}); 
        }) ;
		
	})
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
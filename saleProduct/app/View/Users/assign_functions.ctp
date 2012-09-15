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
    var treeData = {id:"root",text:"功能树",isExpand:true,childNodes:[]} ;
    var treeMap  = {} ;
    var code     = '<?php echo $GroupCode;?>'
    <?php
    	$index = 0 ;
		foreach( $Functions as $Record ){
			$sfs = $Record['sc_security_function']  ;
			$selected =  $Record[0]['selected'] ;
			
			$id   = $sfs['ID'] ;
			$code = $sfs['CODE'] ;
			$name = $sfs['NAME'] ;
			$pid  = $sfs['PARENT_ID'] ;
			echo " var item$index = {id:'$code',text:'$name'} ;" ;
			
			if( !empty($selected) ){
				echo " item$index ['checkstate'] = 1 ;" ;
			}
			
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
   
	$(function(){


		$('#default-tree').tree({//tree为容器ID
				source:'array',
				data:treeData,
				showCheck:true
           }) ;
           
        $("button").click(function(){
        	var ids = $('#default-tree').tree().getSelectedIds() ;
        	$.ajax({
				type:"post",
				url:"/saleProduct/index.php/users/saveAssignFunctions/"+code+"/"+ids,
				data:{},
				cache:false,
				dataType:"text",
				success:function(result,status,xhr){
					window.opener.location.reload() ;
					window.close() ;
				}
			}); 
        }) ;
		
	})
   </script>

</head>
<body>
<div id='content-default' class='demo'>
		<h1></h1>
	<div id="default-tree" class="tree" style="padding: 5px; "></div>
	
	<button>保存权限设置</button>
</div>

</html>
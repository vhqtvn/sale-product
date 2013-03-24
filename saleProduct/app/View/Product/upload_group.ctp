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
		echo $this->Html->css('../js/tree/jquery.tree');
		echo $this->Html->css('../js/validator/jquery.validation');
		echo $this->Html->css('default/style');
		echo $this->Html->script('jquery');
		echo $this->Html->script('common');
		echo $this->Html->script('jquery.json');
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
    var treeData = {id:"root",text:"上传任务组",isExpand:true,childNodes:[]} ;
    var treeMap  = {} ;

    <?php
    	$index = 0 ;
		foreach( $uploadGroup as $Record ){
			$sfs = $Record['sc_upload_group']  ;
			$id   = $sfs['ID'] ;
			$name = $sfs['NAME']  ;
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
   
	$(function(){

		$('#default-tree').tree({//tree为容器ID
				source:'array',
				data:treeData ,
				onNodeClick:function(id, text, record,node){
					if(id == 'root'){
						$(".parentName").val("") ;
						$(".parentId").val("") ;
					}else{
						$(".parentName").val(text) ;
						$(".parentId").val(id) ;
					}
					$("#up-category .id").val(id) ;
					$("#up-category .name").val(text) ;
					$("#up-category .memo").val(record.memo) ;
				}
           }) ;
           
        $(".save-category").click(function(){
        	var ids = $('#xj-category').toJson() ;
        	
        	if(!ids.name){
        		alert("上传任务组名称不能为空");
        		return ;
        	}
        	
        	$.ajax({
				type:"post",
				url:contextPath+"/product/saveUploadGroup/",
				data:ids,
				cache:false,
				dataType:"text",
				success:function(result,status,xhr){
					window.location.reload() ;
				}
			}); 
        }) ;
        
        $(".update-category").click(function(){
        	var ids = $('#up-category').toJson() ;
        	
        	if(!ids.name){
        		alert("上传任务组名称不能为空");
        		return ;
        	}
        	
        	$.ajax({
				type:"post",
				url:contextPath+"/product/saveUploadGroup/",
				data:ids,
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
		<div id="default-tree" class="tree span3" style="padding: 5px; "></div>
		<div class="span4">
			<fieldset id="xj-category">
				<legend>添加下级任务组</legend>
				
				<label>上级任务组:</label>
				<input type="text" readonly class="parentName" id="parentName"/>
				<input type="hidden" class="parentId" id="parentId"/>
			
				<label>名称:</label>
				<input type="text" class="name" id="name" class="span4"/>
				
				<label>备注:</label>
				<textarea id="memo" class="memo" style="height:50px;" class="span4"></textarea>
				<br/><br/>
				<button class="btn save-category">保存任务组</button>
				
			</fieldset>
		
				
		</div>
		<div class="span4">
			<fieldset id="up-category">
				<legend>修改当前任务组</legend>
				<input type="hidden" class="id" id="id"/>
			
				<label>名称:</label>
				<input type="text" class="name" id="name" class="span4"/>
				
				<label>备注:</label>
				<textarea id="memo" class="memo" style="height:50px;" class="span4"></textarea>
				<br/><br/>
				<button class="btn update-category">修改任务组</button>
				
			</fieldset>
		</div>
	</div>
	
</div>

</html>
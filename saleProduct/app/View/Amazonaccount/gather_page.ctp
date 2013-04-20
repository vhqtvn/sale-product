<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>营销产品列表</title>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
    <meta http-equiv="pragma" content="no-cache"/>
	<meta http-equiv="cache-control" content="no-cache"/>

   <?php
   include_once ('config/config.php');
   
		echo $this->Html->meta('icon');
		echo $this->Html->css('../js/tree/jquery.tree');
		echo $this->Html->css('../js/layout/jquery.layout');
		echo $this->Html->css('default/style');

		echo $this->Html->script('jquery');
		echo $this->Html->script('common');
		echo $this->Html->script('jquery-ui');
		echo $this->Html->script('jquery.json');
		echo $this->Html->script('tree/jquery.tree');
		echo $this->Html->script('layout/jquery.layout');
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
			echo " var item$index = {id:'$id',text:'$name',memo:'".$sfs['MEMO']."',isExpand:true} ;" ;
    } ) ;
    
		//echo " treeMap['id_-'] = {id:'-',text:'未分类产品',memo:'',isExpand:true} ;" ;
		//echo " treeData.childNodes.push( treeMap['id_-']  ) ;" ;
	?>
   var accountId = "<?php echo $accountId;?>" ;
   var currentCategoryName = "全部" ;
	$(function(){
			$('#default-tree').tree({//tree为容器ID
				source:'array',
				data:treeData ,
				onNodeClick:function(id,text,record){
					if( id == 'root' ){
						currentCategoryName = "全部" ;
						$("#gatherIfr").attr("src",contextPath+"/amazonaccount/gatherDoPage/<?php echo $accountId;?>") ;
					}else{
						currentCategoryName = text ;
						$("#gatherIfr").attr("src",contextPath+"/amazonaccount/gatherDoPage/<?php echo $accountId;?>/"+id) ;
					}
				}
	       }) ;
		});
   </script>
   
</head>
<body style="magin:0px;padding:0px;">
	<div data-widget="layout" style="width:100%;height:100%;">
		<div region="center" split="true" border="true" title="产品采集" style="padding:2px;">
			<iframe id="gatherIfr" name="gatherIfr" src="<?php echo $contextPath;?>/amazonaccount/gatherDoPage/<?php echo $accountId;?>" style="width:100%;height:500px;" frameborder=0></iframe>
		</div>
		<div region="west" icon="icon-edit" split="true" border="true" title="营销产品分类" style="width:200px;">
			<div id="default-tree" class="tree" style="padding: 5px; "></div>
		</div>
   </div>
	
</body>
</html>

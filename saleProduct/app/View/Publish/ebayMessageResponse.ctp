<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>消息回复</title>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
    <meta http-equiv="pragma" content="no-cache"/>
	<meta http-equiv="cache-control" content="no-cache"/>

   <?php
  		include_once ('config/config.php');
   
		echo $this->Html->meta('icon');
		echo $this->Html->css('../js/grid/jquery.llygrid');
		echo $this->Html->css('../js/layout/jquery.layout');
		
		echo $this->Html->css('default/style');
		echo $this->Html->css('../js/tree/jquery.tree');
		
		echo $this->Html->script('jquery');
		echo $this->Html->script('common');
		echo $this->Html->script('jquery.json');
		echo $this->Html->script('grid/jquery.llygrid');
		echo $this->Html->script('layout/jquery.layout');
		echo $this->Html->script('tree/jquery.tree');
		echo $this->Html->script('modules/publish/ebayMessageResponse');
		
		$groupCode = $user["GROUP_CODE"] ;
		$loginId = $user['LOGIN_ID'] ;
		
		$SqlUtils  = ClassRegistry::init("SqlUtils") ;
		$security  = ClassRegistry::init("Security") ;
		
		//$Utils  = ClassRegistry::init("Utils") ;
	   // $ReviseMyMessages = $Utils->buildUrlByAccountId($accountId, "eBay/reviseMyMessages") ;  

	?>

	 <style type="text/css">
		img{
			cursor:pointer;
		}
		
		.bbit-tree-selected{
			background:#DDFFAA;
		}
	</style>
	
	<script type="text/javascript">
	    var args = jQuery.dialogAraguments() ;
	    var messageIds = args.messageIds||[] ;


		if( !messageIds || messageIds.length <=0 ){
			alert("未选择消息") ;
			window.close();
		}
	</script>

</head>
<body style="magin:0px;padding:0px;">
<div data-widget="layout" style="width:100%;height:100%;">
	
		<div region="center" split="true" border="true" title="消息列表" style="padding:2px;">
			<div class="grid-content" style="margin-top:5px;">
			</div>
			<div class="row-fluid" style="padding-top:10px;height:200px;overflow:hidden;">
				<div class="span3" style="height:200px;overflow:auto;">
					<div id="default-tree" class="tree" style="padding: 5px; "></div>
				</div>
				<script type="text/javascript">
				var treeData = {id:"root",text:"模板分类",isExpand:true,childNodes:[]} ;
			    var treeMap  = {} ;

					<?php //WithFormat
					$ocs = $SqlUtils->exeSql("
										SELECT oc.category_id AS ID ,
										       oc.parent_id AS PARENT_ID,
										       oc.name AS NAME, 
										(SELECT COUNT(1) FROM ost_premade_category opc WHERE 
										opc.premade_id IN (
										  SELECT okp.premade_id FROM ost_kb_premade okp
										) AND
										opc.category_id = oc.category_id) AS TOTAL
										 FROM ost_category oc",array() ) ;

					$Utils  = ClassRegistry::init("Utils") ;

					$Utils->echoTreeScript( $ocs ,null, function( $sfs, $index ,$ss ){
						$id   = $sfs['ID'] ;
						$name = $sfs['NAME']."(".$sfs['TOTAL'].")" ;
						$pid  = $sfs['PARENT_ID'] ;
						echo " var item$index = {id:'$id',text:'$name',isExpand:true} ;" ;
					} ) ;
					?>

					$('#default-tree').tree({//tree为容器ID
						source:'array',
						data:treeData ,
						onNodeClick:function(id,text,record){
							if( id == 'root' ){
								$(".grid-template").llygrid("reload",{categoryId:""}) ;
							}else{
								$(".grid-template").llygrid("reload",{categoryId:id}) ;
							}
						}
			       }) ;

			</script>
					
					<div class="span9">
					<div class="toolbar toolbar-auto">
							<table>
								<tr>
									<td>
										<input type="text" name="searchKey"  placeHolder="输入模板标题、描述或内容查询" class="span4"/>
									</td>								
									<td class="toolbar-btns">
										<button class="query-btn btn btn-primary">查询</button>
									</td>
								</tr>						
							</table>
						</div>
						<div class="grid-template" style="margin-top:5px;width:98%;"></div>
					</div>
			</div>
			<div style="padding-top:10px;">
				<input type="text" id="subject"  style="width:98%" />
				<textarea id="body" style="width:98%;height:145px;margin-top:5px;" placeHolder="填写回复内容"></textarea>
			</div>
			<div class="panel-foot">
						<div class="form-actions">
							<button class="btn btn-primary  save-reply">提交回复</button>
							<button class="btn">关闭</button>
						</div>
					</div>
			
		</div>
   </div>
</body>
</html>

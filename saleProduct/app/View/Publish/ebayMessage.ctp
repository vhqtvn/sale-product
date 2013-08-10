<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title></title>
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
		echo $this->Html->script('modules/publish/ebayMessage');
		
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

</head>
<body style="magin:0px;padding:0px;">
<div data-widget="layout" style="width:100%;height:100%;">
	
		<div region="center" split="true" border="true" title="消息列表" style="padding:2px;">
	
			<div class="toolbar toolbar-auto toolbar1">
				<table>
					<tr>
						
						<th>
							Item Id
						</th>
						<td>
							<input type="text"  class="span2"/>
						</td>										
						<td class="toolbar-btns">
							<button class="btn btn-primary query-btn"  data-widget="grid-query"  data-options="{gc:'.grid-content',qc:'.toolbar1'}">查询</button>
						</td>
					</tr>
				</table>
				<table>
					<tr>
						<td colspan="3">
								<button class="btn b br btn-primary  tag-read" >设为已读(Read)</button>&nbsp;
								<button class="btn b bf btn-primary tag-flagged">设为已标记(Flagged)</button>&nbsp;
								<button class="btn b bf br btn-primary tag-all">设为已读&标记</button>&nbsp;
								<button class="btn b bp btn-primary do-reply">回复</button>&nbsp;
								<!-- 
								<button class="btn btn-danger do-delete">删除</button>&nbsp;
								 -->
						</td>
					</tr>						
				</table>
			</div>	
			<div class="grid-content" style="margin-top:5px;">
			</div>
		</div>
		<div region="west" icon="icon-edit" split="true" border="true" title="消息分类" style="width:180px;">
			<div id="default-tree" class="tree" style="padding: 5px; ">&nbsp;</div>
		</div>
   </div>
</body>
</html>

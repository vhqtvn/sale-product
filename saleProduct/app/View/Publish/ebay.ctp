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
		echo $this->Html->css('default/style');

		echo $this->Html->script('jquery');
		echo $this->Html->script('common');
		echo $this->Html->script('jquery.json');
		echo $this->Html->script('grid/jquery.llygrid');
		echo $this->Html->script('modules/publish/ebay');
		
		$groupCode = $user["GROUP_CODE"] ;
		$loginId = $user['LOGIN_ID'] ;
		
		$SqlUtils  = ClassRegistry::init("SqlUtils") ;
		$security  = ClassRegistry::init("Security") ;
		
		$accountId = $params['arg1'] ;

	?>
	<script type="text/javascript">
		var accountId = '<?php echo $accountId;?>'
	</script>

	 <style type="text/css">
		img{
			cursor:pointer;
		}
	</style>

</head>
<body>

	<div class="toolbar toolbar-auto toolbar1">
		<table>
			<tr>
				<th>
					关键字:
				</th>
				<td>
					<input type="text" id="searchKey" placeHolder="输入货品SKU、标题、执行人" style="width:400px;"/>
				</td>								
				<td class="toolbar-btns">
					<button class="btn btn-primary query-btn"  data-widget="grid-query"  data-options="{gc:'.grid-content',qc:'.toolbar1'}">查询</button>
					<button class="btn btn-primary  new-template">新增模板</button>
				</td>
			</tr>						
		</table>
	</div>	
	<div class="grid-content" style="margin-top:5px;">
	</div>
	<iframe src="" id="exportIframe" style="display:none"></iframe>
</body>
</html>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>FBA入库计划列表</title>
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
		echo $this->Html->script('grid/query');
		echo $this->Html->script('modules/supplychain/list_local_inbound');
	?>
  
   <style>
   		*{
   			font:12px "微软雅黑";
   		}
   		
   		.message{
   			width:600px;
   			border:1px solid #CCC;
   			overflow:auto;
   			margin:5px;
   			height:200px;
   			background:#000;
   			color:#FFF;
   			margin-bottom:0px;
   		}
   		
   		.loading{
   			width:600px;
   			background:#000;
   			color:#FFF;
   			margin-top:-1px;
   			display:hidden;
   			margin-left:6px;
   		}
   </style>

</head>
<body>
<div class="toolbar toolbar-auto toolbar1 query-container">
		<table>
			<tr>
				<th>账号：</th>
				<td>
					<select name="accountId" class="span2">
		     		<option value="">--选择--</option>
			     	<?php
			     		 $amazonAccount  = ClassRegistry::init("Amazonaccount") ;
		   				 $accounts = $amazonAccount->getAllAccounts(); 
			     		foreach($accounts as $account ){
			     			$account = $account['sc_amazon_account'] ;
			     			echo "<option value='".$account['ID']."'>".$account['NAME']."</option>" ;
			     		} ;
			     	?>
					</select>
				</td>							
				<td class="toolbar-btns">
					<button class="query-btn btn btn-primary" data-widget="grid-query"  data-options="{gc:'.grid-content',qc:'.toolbar1'}">查询</button>
				</td>
			</tr>						
		</table>
	</div>	
	<div class="grid-content"></div>
	
	<div class="grid-content-detials"></div>
</body>
</html>

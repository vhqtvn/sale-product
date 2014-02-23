<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>amazon测试接口</title>
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
		echo $this->Html->script('calendar/WdatePicker');
	?>
  
   <script type="text/javascript">

	$(function(){
			$(".exe-service").click(function(){
				var json = $(".mws-service").toJson() ;
				$.dataservice("model:Mws.exeService",json,function(result){
					alert(result) ;
				});
			}) ;
   	 });

   </script>
   
   <style>
   	
   </style>

</head>
<body>

   <div style="border:1px solid #CCC;margin:3px;"  class="mws-service">
	    <table border=0 cellPadding=3 cellSpacing=4 >
		    <tr>
		    	<td>账号：</td>
			    <td>
			     	<select name="accountId" class="span2"   >
								     		<option value="">--选择--</option>
									     	<?php
									     		 $amazonAccount  = ClassRegistry::init("Amazonaccount") ;
								   				 $accounts = $amazonAccount->getAllAccounts(); 
									     		foreach($accounts as $account ){
									     			$account = $account['sc_amazon_account'] ;
									     			$checked = $account['ID'] == $result['ACCOUNT_ID']?"selected":"" ;
									     			echo "<option value='".$account['ID']."'  $checked>".$account['NAME']."</option>" ;
									     		} ;
									     	?>
						</select>
			     </td>
			     <td>接口名称：</td>
			     <td><input name="serviceName" type="text" /></td>
		     	<td rowspan="5" align=center><input type="button" class="btn btn-primary exe-service" value="执行接口"></td> 
		  </tr>
		  <tr>
		     <td>参数名称：</td>
		     <td><input type="text" name="paramName"/></td> 
		     <td>参数值：</td>
		     <td><input type="text" name="paramValue"/></td>
		    </tr>
		    <tr>
		     <td>参数名称：</td>
		     <td><input type="text" name="paramName1"/></td> 
		     <td>参数值：</td>
		     <td><input type="text" name="paramValue1"/></td>
		    </tr>
		    <tr>
		     <td>参数名称：</td>
		     <td><input type="text" name="paramName2"/></td> 
		     <td>参数值：</td>
		     <td><input type="text" name="paramValue2"/></td>
		    </tr>
		    <tr>
		     <td>参数名称：</td>
		     <td><input type="text" name="paramName3"/></td> 
		     <td>参数值：</td>
		     <td><input type="text" name="paramValue3"/></td>
		    </tr>
		   </table>
	</div>  
</body>
</html>

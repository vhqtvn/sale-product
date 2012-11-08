<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
   <?php echo $this->Html->charset(); ?>
    <title>llygrid demo</title>
    <meta http-equiv="pragma" content="no-cache"/>
	<meta http-equiv="cache-control" content="no-cache"/>


   <?php
		echo $this->Html->meta('icon');
		echo $this->Html->css('default/style');
		echo $this->Html->css('../js/tab/jquery.ui.tabs');

		echo $this->Html->script('jquery');
		echo $this->Html->script('common');
		echo $this->Html->script('jquery-ui');
		echo $this->Html->script('grid/jquery.llygrid');
		echo $this->Html->script('grid/query');
		echo $this->Html->script('jquery.json');
		echo $this->Html->script('tab/jquery.ui.tabs');
		
		
	?>
   <script>
   var tabs = [];
   <?php
   		$amazonaccount  = ClassRegistry::init("Amazonaccount") ;
		$accounts = $amazonaccount->getAccounts(); 
   		
   		foreach($accounts as $account){
   			$account = $account['sc_amazon_account'] ;
			echo "tabs.push( {label:'".$account['NAME']."',url:'/saleProduct/index.php/saleProduct/bindProductDetails/".$account['ID']."/$sku',iframe:true}) ;" ;
		} ;
   ?>
  	$(function(){
  		var tab = $('#tabs-default').tabs( {
			tabs:tabs ,
			height:'580px'
		} ) ;
  	})
  </script>

</head>
<body style="overflow-y:auto;padding:2px;">
	<div id="tabs-default" class="view-source">
	</div>
</body>

</html>
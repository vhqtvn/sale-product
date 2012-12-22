<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
   <?php echo $this->Html->charset(); ?>
    <title>产品信息</title>
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
  	$(function(){
  		var realId ='<?php echo $id;?>' ;
  		var tab = $('#tabs-default').tabs( {//$this->layout="index";
			tabs:[
				{label:'基本信息',url:"/saleProduct/index.php/saleProduct/forward/edit_product/"+realId,iframe:true},
				{label:'渠道产品信息',url:"/saleProduct/index.php/saleProduct/forward/channel/"+realId,iframe:true}
				<?php if($item['TYPE'] == 'package'){?>
					,{label:'打包货品信息',url:"/saleProduct/index.php/saleProduct/forward/composition/"+realId,iframe:true}
				<?php } ?>
			] ,
			height:'535px'
		} ) ;
  	})
  </script>

</head>
<body style="overflow-y:auto;padding:2px;">
	<div id="tabs-default" class="view-source">
	</div>
</body>

</html>
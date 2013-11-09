<!DOCTYPE HTML>
<html>
	<head>
	<?php echo $this->Html->charset(); ?>
    <title>Business Report</title>
    <meta http-equiv="pragma" content="no-cache"/>
	<meta http-equiv="cache-control" content="no-cache"/>

   <?php
  		 include_once ('config/config.php');
   
		echo $this->Html->meta('icon');
		echo $this->Html->css('../js/validator/jquery.validation');
		echo $this->Html->css('default/style');

		echo $this->Html->script('jquery');
		echo $this->Html->script('common');
		echo $this->Html->script('jquery.json');
		echo $this->Html->script('highcharts/highcharts');
		echo $this->Html->script('calendar/WdatePicker');
		echo $this->Html->script('highcharts/modules/exporting.src');
		echo $this->Html->script('modules/chart/businessReport');
		
		$realId = $params['arg1'] ;
		
		
	?>
		<script type="text/javascript">
			var $realId = '<?php echo $realId;?>' ;
		</script>
	</head>
	<body>
			<div>
				<input type="radio"  value="1"  name="type" checked/>ASIN Business Report
				<input type="radio"  value="2"  name="type"  />SKU Business Report
			</div>

			<div  class="day-container">
				<div class="container" style="min-width: 400px; height: 400px; margin: 0 auto"></div>
				<ul class="offer-container" style="list-style: none;text-align:right;"></ul>
			</div>
			
			<div  class="month-container">
				<div class="container" style="min-width: 400px; height: 400px; margin: 0 auto"></div>
				<ul class="offer-container" style="list-style: none;text-align:right;"></ul>
			</div>
	</body>
</html>

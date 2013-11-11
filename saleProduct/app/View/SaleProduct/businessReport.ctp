<!DOCTYPE HTML>
<html>
	<head>
	<?php echo $this->Html->charset(); ?>
    <title>Business Report</title>
    <meta http-equiv="pragma" content="no-cache"/>
	<meta http-equiv="cache-control" content="no-cache"/>

   <?php
  		include_once ('config/config.php');
  		include_once ('config/header.php');
  		
		echo $this->Html->script('highcharts/highcharts');
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
				<label class="radio inline">
			  		<input type="radio" name="type"  value="1" checked>
			  		父ASIN
				</label>
				<label class="radio inline">
			  		<input type="radio" name="type"   value="2" >
			  		父ASIN/子ASIN
				</label>
				<label class="radio inline">
			  		<input type="radio" name="type" value="3" >
			  		父ASIN/子ASIN/SKU
				</label>
			</div>
			
			<div class="grid-content"></div>
			<div class="grid-history-content"></div>

			<div  class="type1-container tc hide">
				<div class="container" style="min-width: 400px; height: 400px; margin: 0 auto"></div>
				<ul class="offer-container" style="list-style: none;text-align:right;"></ul>
			</div>
			
			<div  class="type2-container tc hide">
				<div class="container" style="min-width: 400px; height: 400px; margin: 0 auto"></div>
				<ul class="offer-container" style="list-style: none;text-align:right;"></ul>
			</div>
			
			<div  class="type3-container tc hide">
				<div class="container" style="min-width: 400px; height: 400px; margin: 0 auto"></div>
				<ul class="offer-container" style="list-style: none;text-align:right;"></ul>
			</div>
	</body>
</html>

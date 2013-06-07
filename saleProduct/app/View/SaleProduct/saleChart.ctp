<!DOCTYPE HTML>
<html>
	<head>
	<?php echo $this->Html->charset(); ?>
    <title>货品统计图</title>
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
		echo $this->Html->script('highcharts/modules/exporting.src');
		
		$sku = $params['arg1'] ;
	?>
		<script type="text/javascript">
$(function () {
	var asins = {} ;
	
	$.dataservice("model:Chart.RealSkuChart.load",{sku:'<?php echo $sku;?>'},function(result){
		asins= {} ;
		//alert(11111111111);
		//console.log( result ) ;
		result = result.records ;
		
		var categories = [] ;
		//init category
		$(result).each(function(){
			if( this.TYPE == 'TOTAL' ){
				categories.push(this.P_DATE) ;
			} 
		}) ;

		//init series type
		var seriresName = {} ;
		$(result).each(function(){
			if(this.ASIN){
				asins[this.ASIN] = this.REAL_SKU ;
			}
			var rs =  this.REAL_SKU  ;
			seriresName[rs] = [] ;
		}) ;

		//init series
		var seriesData = {} ;
		for(var realSku in seriresName){
			var rs =  realSku  ;
				$(categories).each(function(index , pDate){
						var hasData = false ;
						$(result).each(function(){
							if( this.REAL_SKU == realSku && this.P_DATE == pDate ){
								hasData = true ;
								seriresName[rs].push( parseInt(this.QUANTITY) ) ;
								}
						}) ;
						if(!hasData){
							seriresName[rs].push( 0) ;
					   }
						
					}) ;
		}

		//format series
		var series = [] ;
		for(var o in seriresName){
			series.push({name:o,data:seriresName[o]}) ;
			}

		$('#container').highcharts({
            chart: {
                type: 'line'
                	//type: 'column'
            },
            title: {
                text: '<?php echo $sku;?>-销量统计图'
            },
            subtitle: {
                text: ''
            },
            credits: {
           	 	enabled: false
            },
            xAxis: {
                categories: categories
            },
            yAxis: {
                min:0,
                title: {
                    text: '销量（Sales）'
                }
            },
            tooltip: {
                enabled: false,
                formatter: function() {
                    return '<b>'+ this.series.name +'</b><br/>'+
                        this.x +': '+ this.y +'°C';
                }
            },
            plotOptions: {
                line: {
                    dataLabels: {
                        enabled: true
                    },
                    enableMouseTracking: false
                }
            },
            series:series
        });

        //渲染竞争信息链接
		//[offer-listing]
		for(var asin in asins){
			$(".offer-container").append("<li style='float:left;margin:2px 5px;' class='alert alert-info'><a offer-listing='"+asin+"'>"+asins[asin]+"("+asin+")"+"</a></li>") ;
		}
	}) ;
    });
    

		</script>
	</head>
	<body>


			<div id="container" style="min-width: 400px; height: 400px; margin: 0 auto"></div>
			<ul class="offer-container" style="list-style: none;text-align:right;">
			
			</ul>
			<div style="clear:both;"></div>
			
	</body>
</html>

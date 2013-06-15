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
	
	$.dataservice("model:Chart.RealSkuPriceChart.load",{sku:'<?php echo $sku;?>'},function(result){
		//alert( $.json.encode(result) ) ;
		asins= {} ;
		//alert(11111111111);
		//console.log( result ) ;
		result = result.records ;
		
		var categories = [] ;
	//	{"categories":"","records":[{"REAL_QUOTE_PRICE":"2424","REAL_PURCHASE_DATE":"2013-04-13 00:00:00"}]}
		//init category
		$(result).each(function(){
				categories.push( this.REAL_PURCHASE_DATE+"<br/>("+this.NAME+")" ) ;
		}) ;

		//init series type
		var seriresName = [] ;

		//init series
		var seriesData = {} ;
				$(categories).each(function(index , pDate){
						var hasData = false ;
						$(result).each(function(){
							var t = this.REAL_PURCHASE_DATE+"<br/>("+this.NAME+")"  ;
							if( this.REAL_QUOTE_PRICE   && t == pDate ){
								hasData = true ;
								seriresName.push( parseFloat(this.REAL_QUOTE_PRICE) ) ;
							}
						}) ;
						if(!hasData){
							seriresName.push( 0) ;
					   }
					}) ;
		//format series
		var series = [] ;
		series.push({name:'采购曲线',data:seriresName}) ;

		//alert( $.json.encode(categories) ) ;
		//alert( $.json.encode(seriresName) ) ;

		$('#container').highcharts({
            chart: {
                type: 'line'
                	//type: 'column'
            },
            title: {
                text: '<?php echo $sku;?>-历史采购价格曲线图'
            },
            subtitle: {
                text: ''
            },
            credits: {
           	 	enabled: false
            },
            xAxis: {
                id:6,
            	events:{
					click:function(){
						alert(123) ;
					}
                },
                categories: categories
            },
            yAxis: {
                min:0,
                title: {
                    text: '采购价格'
                }
            },
            tooltip: {
                enabled: true ,
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

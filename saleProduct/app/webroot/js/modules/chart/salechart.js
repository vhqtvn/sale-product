$(function () {
	
	function  loadDayChart(){
		$(".month-container").hide() ;
		$(".day-container").show() ;
		
		var day = $(".daychart-month").val() ;
		var _day = day ;
		day = day.split("-") ;
		var year = parseInt(day[0]) ;
		var month = parseInt(day[1]) ;
	
		var asins = {} ;			
		$.dataservice("model:Chart.RealSkuChart.loadDay",{sku:sku,'year':year,month:month},function(result){
			asins= {} ;
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
					seriresName[this.ASIN+"_"+this.REAL_SKU] = [] ;
				}else{
					seriresName[this.REAL_SKU] = [] ;
				}
			}) ;

			//init series
			var seriesData = {} ;
			for(var realSku in seriresName){
				var rs =  realSku  ;
					$(categories).each(function(index , pDate){
							var hasData = false ;
							$(result).each(function(){
								if( this.TYPE == 'TOTAL' ){
									if( this.REAL_SKU == realSku && this.P_DATE == pDate ){
										hasData = true ;
										seriresName[rs].push( parseInt(this.QUANTITY) ) ;
									}
								}else{
									if( this.ASIN+"_"+this.REAL_SKU == realSku && this.P_DATE == pDate ){
										hasData = true ;
										seriresName[rs].push( parseInt(this.QUANTITY) ) ;
									}
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

			$('.container',".day-container").highcharts({
	            chart: {
	                type: 'line'
	                	//type: 'column'
	            },
	            title: {
	                text: sku+'-日销量统计图('+_day+')'
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
	                    text: '日销量（Sales）'
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
	                    dataLabels: {   enabled: true  },
	                    enableMouseTracking: false
	                }
	            },
	            series:series
	        });

	        //渲染竞争信息链接
			//[offer-listing]

			$(".offer-container",".day-container").empty();
			for(var asin in asins){
				$(".offer-container",".day-container").append("<li style='float:left;margin:2px 5px;' class='alert alert-info'><a offer-listing='"+asin+"'>"+asins[asin]+"("+asin+")"+"</a></li>") ;
			}
		}) ;
	}
	
		function  loadMonthChart(){
			$(".day-container").hide() ;
			$(".month-container").show() ;
			var asins = {} ;			
			$.dataservice("model:Chart.RealSkuChart.load",{sku:sku},function(result){
				asins= {} ;
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
						seriresName[this.ASIN+"_"+this.REAL_SKU] = [] ;
					}else{
						seriresName[this.REAL_SKU] = [] ;
					}
				}) ;
	
				//init series
				var seriesData = {} ;
				for(var realSku in seriresName){
					var rs =  realSku  ;
						$(categories).each(function(index , pDate){
								var hasData = false ;
								$(result).each(function(){
									if( this.TYPE == 'TOTAL' ){
										if( this.REAL_SKU == realSku && this.P_DATE == pDate ){
											hasData = true ;
											seriresName[rs].push( parseInt(this.QUANTITY) ) ;
										}
									}else{
										if( this.ASIN+"_"+this.REAL_SKU == realSku && this.P_DATE == pDate ){
											hasData = true ;
											seriresName[rs].push( parseInt(this.QUANTITY) ) ;
										}
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
	
				$('.container',".month-container").highcharts({
		            chart: {
		                type: 'line'
		                	//type: 'column'
		            },
		            title: {
		                text: sku+'-月销量统计图'
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
		                    text: '月销量（Sales）'
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
		                    dataLabels: {   enabled: true  },
		                    enableMouseTracking: false
		                }
		            },
		            series:series
		        });
	
		        //渲染竞争信息链接
				//[offer-listing]
				$(".offer-container",".month-container").empty();
				for(var asin in asins){
					$(".offer-container",".month-container").append("<li style='float:left;margin:2px 5px;' class='alert alert-info'><a offer-listing='"+asin+"'>"+asins[asin]+"("+asin+")"+"</a></li>") ;
				}
			}) ;
		}
	
		function loadChart(){
			var type = $("[name='type']:checked").val() ;
			if(type == 1){
				loadDayChart() ;
			}else{
				loadMonthChart() ;
			}
		}
		loadChart() ;
		$("[name='type']").click(function(){
			loadChart() ;
		}) ;
		
		$(".reload-daychart").click(function(){
			loadDayChart() ;
		}) ;
    });
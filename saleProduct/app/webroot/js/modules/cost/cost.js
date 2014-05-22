/**
 *  @productCost 货品成本
 *  @salePrice (计算渠道佣金) 
 *  @渠道佣金比率
 *  @可变关闭费用
 *  @FBA费用
 *  @物流属性（重量、长宽高）
 *  @转仓库流单价
 */
var  Cost = function(){
	var _productCost = 0 ;
	var _exchangeRate = 6.04 ;//汇率
	
	var _sellPrice = 0 ;
	var _channelFeeRatio = 0 ;
	var _variableCloseFee = 0 ;
	var _fbaCost = 0 ;
	var _productProps = {} ;
	var _wlUnitPrice = 0 ;
	var _channel = null ;
	var  _calcCostAbale = 0 ; 
	var  _fbcOrderRate = 0 ;
	var  _fbmOrderRate = 0 ;
	var _CommissionLowlimit = 1 ;
	var _ProductCostOnly = 0 ;
	
	this.setFbcOrderRate = function(fbcOrderRate){
		_fbcOrderRate= fbcOrderRate ;
	};
	this.setFbmOrderRate = function(fbmOrderRate){
		_fbmOrderRate= fbmOrderRate ;
	};
	
	this.setChannel = function(channel){
		if(channel) channel = $.trim(channel) ;
		_channel= channel ;
	};
	
	this.setProductCostOnly = function(t){
		_ProductCostOnly = t ;
	};
	
	this.setProductCost = function(pc,er){
		_productCost = pc ;
		_exchangeRate = er ;
	};
	
	this.setSellPrice = function( sp ){
		_sellPrice = sp ;
	};
	
	this.setCommissionLowlimit = function( CommissionLowlimit ){
		if(CommissionLowlimit)_CommissionLowlimit = CommissionLowlimit ;
	};
	
	this.setChannelFeeRatio = function(cfr){
		_channelFeeRatio = cfr ;
	};
	
	this.setVariableCloseFee = function(vcf){
		_variableCloseFee = vcf ;
	};
	
	this.setTransferProperties = function(vcf){
		_productProps = vcf ;
	};
	
	this.setFbaCost = function(vcf){
		_fbaCost = vcf ;
	};
	
	this.getFbaCost = function(){
		if( _channel== 'AMAZON_NA' || _channel == 'FBA'){
			return (parseFloat( _fbaCost)).toFixed(2) ;
		}
		return "-" ;
	};
	
	this.setTransferUnitPrice = function(vcf){
		_wlUnitPrice = vcf ;
	};
	
	this.getInventoryCenterFee = function(){
		if( _channel == 'AMAZON_NA'|| _channel == 'FBA'){
			return 0 ;
		}
		return "-" ;
	};
	
	this.getChannelFeeRatioFormat = function(){
		return (_channelFeeRatio*100).toFixed(2) +"%" ;
	};
	
	
	this.getChannelFee = function(){
		var fee =  parseFloat( _sellPrice*_channelFeeRatio ) ; 
		/*if( _CommissionLowlimit >0 && fee < _CommissionLowlimit  ){
			return _CommissionLowlimit ;
		}*/
		fee = fee.toFixed(2) ;
		if( fee <= 1 ) return 1 ;
		return fee ;
	};
	
	this.getTransferCost = function(){
		if( _channel == 'AMAZON_NA'|| _channel == 'FBA'){
			return (parseFloat( _productProps.weight*_wlUnitPrice )).toFixed(2) ;
		}
		return "-" ;
	};
	
	this.getCalcCostAbale = function(){
		return (_calcCostAbale).toFixed(2) ;
	};
	
	this.getOrderTransferCost = function(){
		if( _channel == 'AMAZON_NA'|| _channel == 'FBA'){
			return "-" ;
		}
		return (parseFloat( _productProps.weight*_fbcOrderRate )).toFixed(2) ;
	};
	
	this.check = function(){
		if(  !_productCost ||( !_ProductCostOnly  && _productCost<=2  )  ){
			return {error:"采购成本缺失"} ;
		}
		
		if(  !_productProps.weight   ){
			return {error:"重量缺失"} ;
		}
		
		if(   !_sellPrice ){
			return {error:"售价缺失"} ;
		}
		if(   (_channel == 'AMAZON_NA') || _channel == 'FBA'){
			if( !_fbaCost ){
				return {error:"FBA成本缺失"} ;
			}
		}
		return null ;
	};
	
	this.evlate  = function(){
		var checkInfo= this.check() ;
		if( checkInfo ){
			return checkInfo ;
		}
		
		var _cost = 0 ;
		if(   (_channel == 'AMAZON_NA') || _channel == 'FBA'){//FBA
			_cost = parseFloat(_productCost/_exchangeRate) ;//采购成本
			_cost += parseFloat( this.getFbaCost() );//FBA费用
			_cost += parseFloat( _variableCloseFee ) ;//可变关闭费用
			_cost += parseFloat( this.getChannelFee() ) ;//渠道佣金
			var weight = _productProps.weight ;
			_cost += parseFloat( weight*_wlUnitPrice ) ;
			var totalProfile = _sellPrice - _cost ;
			var  calcCostAbale = _cost -  parseFloat( _fbaCost ) - parseFloat( _variableCloseFee )  - parseFloat( _sellPrice*_channelFeeRatio ) ;
			_calcCostAbale = calcCostAbale;
			var profileRatio = ((totalProfile/calcCostAbale)*100).toFixed(2)+"%" ;
			return { cost: _cost , profile: totalProfile,profileRatio:profileRatio,calcCostAbale:calcCostAbale } ;
		}
		//FBM
		_cost = parseFloat( _productCost/_exchangeRate ) ;//采购成本
		_cost += parseFloat( _variableCloseFee ) ;//可变关闭费用
		_cost += parseFloat( this.getChannelFee() ) ;//渠道佣金
		var weight = _productProps.packageWeight||999 ;
		_cost += parseFloat( weight*_fbcOrderRate ) ;
		var totalProfile = _sellPrice - _cost ;
		var  calcCostAbale = _cost - parseFloat( _variableCloseFee )  - parseFloat( _sellPrice*_channelFeeRatio ) ;
		_calcCostAbale = calcCostAbale ;
		var profileRatio = ((totalProfile/calcCostAbale)*100).toFixed(2)+"%" ;
		return { cost: _cost , profile: totalProfile ,profileRatio:profileRatio,calcCostAbale:calcCostAbale } ;
	};
};

Cost.getListing = function(listings , callback){
	$.dataservice("model:CostNew.readyListingCost" , {listings:listings} , function(result){
		 var returnCosts =[] ;
		$(result).each(function(index,item){
			var productCost = item.productCost ;
			var costTag = item.costTag ;
			var costLabor = item.costLabor ;
			var costTaxRate = item.costTaxRate ;
			var listingCost = item.listingCost ;
			var salesNum = item.salesNum ;
			
			if( !productCost ) return ;
			
			 var purchaseCost = productCost.PURCHASE_COST ||0  ;
			 var logisticsCost = productCost.LOGISTICS_COST||0 ;
	    	 var baseCost =parseFloat( purchaseCost )+ parseFloat(productCost.LOGISTICS_COST||0 )+parseFloat(productCost.OTHER_COST||0)
	    	 										 + parseFloat(costTag)+parseFloat(costLabor)  ;
			 var $costTaxRate = parseFloat(costTaxRate) ;
			 var purcharRate =parseFloat( (purchaseCost*$costTaxRate).toFixed(2)) ;
			 baseCost = baseCost + purcharRate ;
			
			 var cost = new Cost() ;
			 	cost.setProductCostOnly(purchaseCost) ;
				cost.setProductCost( baseCost , listingCost.EXCHANGE_RATE  ) ;
				cost.setChannel(  listingCost.FULFILLMENT_CHANNEL ) ;
				
				var lowerPrice = listingCost.LOWEST_FBA_PRICE?listingCost.LOWEST_FBA_PRICE:listingCost.LIMT_PRICE ;//LOWEST_FBA_PRICE
				cost.setSellPrice(lowerPrice ) ;
				
				cost.setChannelFeeRatio( listingCost.COMMISSION_RATIO ) ;
				cost.setVariableCloseFee( listingCost.VARIABLE_CLOSING_FEE ) ;
				cost.setFbaCost(  listingCost._FBA_COST ) ;
				cost.setTransferUnitPrice( listingCost.TRANSFER_WH_PRICE ) ;
				cost.setCommissionLowlimit( listingCost.COMMISSION_LOWLIMIT ) ;
				cost.setFbcOrderRate( listingCost.FBC_ORDER_RATE ) ;
				cost.setFbmOrderRate( listingCost.FBM_ORDER_RATE ) ;
				cost.setTransferProperties( {weight:listingCost.WEIGHT , length:listingCost.LENGTH , width:listingCost.WIDTH , height:listingCost.HEIGHT,packageWeight: listingCost.PACKAGE_WEIGHT } ) ;
		
				var costValue = cost.evlate() ;
				//console.log(costValue) ;
		
				var _cost = (parseFloat(costValue.cost)).toFixed(2);
				var  totalProfile =  (parseFloat(costValue.profile)).toFixed(2);
				var  profileRate =   costValue.profileRatio ;
		
				var salesNumMap = {} ;
				$(salesNum).each(function(index,item1){
					item1 = item1[0] ;
					salesNumMap[item1.TYPE] = item1.COUNT ;
				}) ;
				var saleString = (salesNumMap["7"]||'-')+"/"+(salesNumMap["14"]||'-')+"/"+(salesNumMap["30"]||'-') ;
				var  returnCost = {
						accountId:listingCost.ACCOUNT_ID,
						listingSku:listingCost.SKU, 
						costAvalibe:cost.getCalcCostAbale(),
						totalCost:_cost,
						purchaseCost:purchaseCost,
						totalProfile:totalProfile,
						profileRate:profileRate,
						logisticsCost:logisticsCost,
						saleString:saleString
					} ;
				
				returnCosts.push( returnCost ) ;
		}) ;
		callback( returnCosts ) ;
	},{noblock:true});
};

//return array("productCost"=>$productCost,"costTag"=>$costTag,"costLabor"=>$costLabor,"costTaxRate"=>$costTaxRate) ;
Cost.get = function(realId,callback){
	$.dataservice("model:CostNew.readyCost" , {realId:realId} , function(result){
		var productCost = result.productCost ;
		var costTag = result.costTag ;
		var costLabor = result.costLabor ;
		var costTaxRate = result.costTaxRate ;
		var listingCosts = result.listingCosts ;
		var providor = result.providor||{} ;
		var salesNum = result.salesNum ;
		
		 var purchaseCost = productCost.PURCHASE_COST ||0  ;
		 var logisticsCost = productCost.LOGISTICS_COST||0 ;
    	 var baseCost =parseFloat( purchaseCost )+ parseFloat(productCost.LOGISTICS_COST||0 )+parseFloat(productCost.OTHER_COST||0)
    	 										 + parseFloat(costTag)+parseFloat(costLabor)  ;
		 var $costTaxRate = parseFloat(costTaxRate) ;
		 var purcharRate =parseFloat( (purchaseCost*$costTaxRate).toFixed(2)) ;
		 baseCost = baseCost + purcharRate ;
		 
		 var returnCosts =[] ;
		$( listingCosts ).each(function(index,item){
			var cost = new Cost() ;
			cost.setProductCostOnly(purchaseCost) ;
			cost.setProductCost( baseCost , item.EXCHANGE_RATE  ) ;
			cost.setChannel(  item.FULFILLMENT_CHANNEL ) ;
			//LOWEST_PRICE  LOWEST_FBA_PRICE
			
			var lowerPrice = item.LOWEST_FBA_PRICE?item.LOWEST_FBA_PRICE:item.LIMT_PRICE ;
			cost.setSellPrice(lowerPrice ) ;
			
			cost.setChannelFeeRatio( item.COMMISSION_RATIO ) ;
			cost.setVariableCloseFee( item.VARIABLE_CLOSING_FEE ) ;
			cost.setFbaCost(  item._FBA_COST ) ;
			cost.setTransferUnitPrice( item.TRANSFER_WH_PRICE ) ;
			cost.setCommissionLowlimit( item.COMMISSION_LOWLIMIT ) ;
			cost.setFbcOrderRate( item.FBC_ORDER_RATE ) ;
			cost.setFbmOrderRate( item.FBM_ORDER_RATE ) ;
			cost.setTransferProperties( {weight:item.WEIGHT , length:item.LENGTH , width:item.WIDTH , height:item.HEIGHT,packageWeight: item.PACKAGE_WEIGHT } ) ;
	
			var costValue = cost.evlate() ;
			
			if(costValue.error){
				var  returnCost = {
						accountId:item.ACCOUNT_ID,
						listingSku:item.SKU, 
						error:costValue.error
				} ;
				returnCosts.push( returnCost ) ;
			}else{
				var _cost = (parseFloat(costValue.cost)).toFixed(2);
				var  totalProfile =  (parseFloat(costValue.profile)).toFixed(2);
				var  profileRate =   costValue.profileRatio ;

				var salesNumMap = {} ;
				$(salesNum).each(function(index,item1){
					item1 = item1[0] ;
						if( item.ACCOUNT_ID == item.ACCOUNT_ID && item1.LISTING_SKU == item.SKU ){
							salesNumMap[item1.TYPE] = item1.COUNT ;
						}
				}) ;
				var saleString = (salesNumMap["7"]||'-')+"/"+(salesNumMap["14"]||'-')+"/"+(salesNumMap["30"]||'-') ;
				var  returnCost = {
						accountId:item.ACCOUNT_ID,
						listingSku:item.SKU, 
						costAvalibe:cost.getCalcCostAbale(),
						totalCost:_cost,
						purchaseCost:purchaseCost,
						totalProfile:totalProfile,
						profileRate:profileRate,
						logisticsCost:logisticsCost,
						providorName:providor.NAME,
						providorId:providor.ID,
						saleString:saleString
					} ;
				
				
				returnCosts.push( returnCost ) ;
			}
		});
		
		var __ = {
				returnCosts: returnCosts,
				lastPurchase:result.lastPurchase
		}
		
		callback && callback(__) ;
	}) ;
};
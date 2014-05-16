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
	var _CommissionLowlimit = 0 ;
	
	this.setFbcOrderRate = function(fbcOrderRate){
		_fbcOrderRate= fbcOrderRate ;
	};
	this.setFbmOrderRate = function(fbmOrderRate){
		_fbmOrderRate= fbmOrderRate ;
	};
	
	this.setChannel = function(channel){
		_channel= channel ;
	};
	
	this.setProductCost = function(pc,er){
		_productCost = pc ;
		_exchangeRate = er ;
	};
	
	this.setSellPrice = function( sp ){
		_sellPrice = sp ;
	};
	
	this.setCommissionLowlimit = function( CommissionLowlimit ){
		_CommissionLowlimit = CommissionLowlimit ;
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
		if( _channel != 'Merchant'){
			return (parseFloat( _fbaCost)).toFixed(2) ;
		}
		return "-" ;
	};
	
	this.setTransferUnitPrice = function(vcf){
		_wlUnitPrice = vcf ;
	};
	
	this.getInventoryCenterFee = function(){
		if( _channel != 'Merchant'){
			return 0 ;
		}
		return "-" ;
	};
	
	this.getChannelFeeRatioFormat = function(){
		return (_channelFeeRatio*100).toFixed(2) +"%" ;
	};
	
	
	this.getChannelFee = function(){
		var fee =  parseFloat( _sellPrice*_channelFeeRatio ) ; 
		if( _CommissionLowlimit >0 && fee < _CommissionLowlimit  ){
			return _CommissionLowlimit ;
		}
		fee = fee.toFixed(2) ;
		return fee ;
	};
	
	this.getTransferCost = function(){
		if( _channel != 'Merchant'){
			return (parseFloat( _productProps.weight*_wlUnitPrice )).toFixed(2) ;
		}
		return "-" ;
	};
	
	this.getCalcCostAbale = function(){
		return (_calcCostAbale).toFixed(2) ;
	};
	
	this.getOrderTransferCost = function(){
		if( _channel != 'Merchant'){
			return "-" ;
		}
		return (parseFloat( _productProps.weight*_fbcOrderRate )).toFixed(2) ;
	};
	
	this.evlate  = function(){
		var _cost = 0 ;
		if( _channel != 'Merchant'){//FBA
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
//return array("productCost"=>$productCost,"costTag"=>$costTag,"costLabor"=>$costLabor,"costTaxRate"=>$costTaxRate) ;
Cost.get = function(realId,callback){
	$.dataservice("model:CostNew.readyCost" , {realId:realId} , function(result){
		var productCost = result.productCost ;
		var costTag = result.costTag ;
		var costLabor = result.costLabor ;
		var costTaxRate = result.costTaxRate ;
		var listingCosts = result.listingCosts ;
		var providor = result.providor||{} ;
		
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
			cost.setProductCost( baseCost , item.EXCHANGE_RATE  ) ;
			cost.setChannel(  item.FULFILLMENT_CHANNEL ) ;
			//LOWEST_PRICE  LOWEST_FBA_PRICE
			cost.setSellPrice( item.LOWEST_FBA_PRICE  ) ;
			cost.setChannelFeeRatio( item.COMMISSION_RATIO ) ;
			cost.setVariableCloseFee( item.VARIABLE_CLOSING_FEE ) ;
			cost.setFbaCost(  item._FBA_COST ) ;
			cost.setTransferUnitPrice( item.TRANSFER_WH_PRICE ) ;
			cost.setCommissionLowlimit( item.COMMISSION_LOWLIMIT ) ;
			cost.setFbcOrderRate( item.FBC_ORDER_RATE ) ;
			cost.setFbmOrderRate( item.FBM_ORDER_RATE ) ;
			cost.setTransferProperties( {weight:item.WEIGHT , length:item.LENGTH , width:item.WIDTH , height:item.HEIGHT,packageWeight: item.PACKAGE_WEIGHT } ) ;
	
			var costValue = cost.evlate() ;
	
			var _cost = (parseFloat(costValue.cost)).toFixed(2);
			var  totalProfile =  (parseFloat(costValue.profile)).toFixed(2);
			var  profileRate =   costValue.profileRatio ;
			/*
			$(".COMMISSION_FEE","#"+rowId).html( cost.getChannelFee() +"("+cost.getChannelFeeRatioFormat()+")") ;
			$(".transferCost","#"+rowId).html( cost.getTransferCost() ) ;
			$(".totalCost","#"+rowId).html( _cost ) ;
			$(".payCost","#"+rowId).html( cost.getCalcCostAbale() ) ;
			$(".fbaCost","#"+rowId).html( cost.getFbaCost() ) ;
			$(".inventoryCenterFee","#"+rowId).html( cost.getInventoryCenterFee() ) ;
			$(".orderTransferCost","#"+rowId).html( cost.getOrderTransferCost() ) ;
			
			$(".totalProfile","#"+rowId).html( totalProfile+"["+profileRate+"]" ) ;//profile profileRatio
			)*/
			returnCosts.push( {
				accountId:item.ACCOUNT_ID,
				listingSku:item.SKU, 
				costAvalibe:cost.getCalcCostAbale(),
				totalCost:_cost,
				purchaseCost:purchaseCost,
				totalProfile:totalProfile,
				profileRate:profileRate,
				logisticsCost:logisticsCost,
				providorName:providor.NAME
			} ) ;
		});
		
		callback && callback(returnCosts) ;
	}) ;
};
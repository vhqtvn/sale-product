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
			_cost += parseFloat( _sellPrice*_channelFeeRatio ) ;//渠道佣金
			
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
		_cost += parseFloat( _sellPrice*_channelFeeRatio ) ;//渠道佣金
		var weight = _productProps.packageWeight||999 ;
		_cost += parseFloat( weight*_fbcOrderRate ) ;
		var totalProfile = _sellPrice - _cost ;
		var  calcCostAbale = _cost - parseFloat( _variableCloseFee )  - parseFloat( _sellPrice*_channelFeeRatio ) ;
		_calcCostAbale = calcCostAbale;
		var profileRatio = ((totalProfile/calcCostAbale)*100).toFixed(2)+"%" ;
		
		return { cost: _cost , profile: totalProfile ,profileRatio:profileRatio,calcCostAbale:calcCostAbale } ;
	};
};
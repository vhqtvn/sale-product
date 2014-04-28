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
	
	this.setTransferUnitPrice = function(vcf){
		_wlUnitPrice = vcf ;
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
		return (parseFloat( _productProps.weight*_wlUnitPrice )).toFixed(2) ;
	};
	
	this.evlate  = function(){
		var _cost = parseFloat(_productCost/_exchangeRate) ;
		_cost += parseFloat( _fbaCost ) ;
		_cost += parseFloat( _variableCloseFee ) ;
		_cost += parseFloat( _sellPrice*_channelFeeRatio ) ;
		
		var weight = _productProps.weight ;
		_cost += parseFloat( weight*_wlUnitPrice ) ;
		var totalProfile = _sellPrice - _cost ;
		var profileRatio = ((totalProfile/_cost)*100).toFixed(2)+"%" ;
		return { cost: _cost , profile: totalProfile,profileRatio:profileRatio } ;
	};
};
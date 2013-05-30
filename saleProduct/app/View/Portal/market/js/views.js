var App = Em.Application.create({
	contextPath : "/portal/portal/layouts/webos/market/app",
	loadView : function(path, callback) {
		var selectedViewClass = null;
		var indexNum = path.indexOf("?");
		if (indexNum < 1) {
			indexNum = path.length;
		}
		var viewPath = path.substring(0, indexNum);
		$.each(App.BaseView.subclasses, function(i, item) {
					var itemViewPath = item.proto().viewPath;
					if (viewPath == itemViewPath) {
						selectedViewClass = item;
						return false;
					}
				});

		if (selectedViewClass == null)
			return;
		var tName = selectedViewClass.proto().templateName;
		var viewInstance = selectedViewClass.create({
					rawPath : path
				});
		if (tName == null || Ember.TEMPLATES[tName] != null) {
			viewInstance.initView(callback);
		} else {
			$.get(this.contextPath + "/" + path, function(templateText) {
				var re = /<script\s+type=\"(text\/x-handlebars|text\/x-raw-handlebars)\"[^>]*>[\s\S]*?<\/script>/ig;
				var $emTemplate = $("<div></div>")
				var matchResult = templateText.match(re);
				if (matchResult != null) {
					$.each(matchResult, function(i, val) {
								$emTemplate.append(val);
							});
					templateText = templateText.replace(re, "");
					Ember.Handlebars.bootstrap($emTemplate);
				}

				var compiledTemplate =Ember.Handlebars.compile(templateText);
				if (Ember.TEMPLATES[tName] == null) {
					Ember.TEMPLATES[tName] = compiledTemplate;
				}
				viewInstance.initView(callback);
			});
		}
	}
});

App.currentUser=Ember.Object.create({
	userId:"",
	name:"",
	isLogin:false
});

/*App.userController=Ember.Object.create({
	init:function(){
		this._super();
		//this.loadUserInfo(null);
	},
	loadUserInfo : function(callback) {
				var self = this;
				if(App.currentUser.get("isLogin")){
					if ($.isFunction(callback)) {
							callback(App.currentUser);
					}
				}
				prdtServices.getUserInfo(function(resp) {
						App.currentUser=$.extend(App.currentUser,resp);
						App.currentUser.set("isLogin",true);
						if ($.isFunction(callback)) {
							callback(App.currentUser);
						}
					});
			}
});*/

App.BaseView = Ember.View.extend({
			viewPath : "",
			rawPath : "",
			model : null,
			currentUser:Ember.computed(function(){
				return App.currentUser;
			}).property(),
			initView : function(callback) {
				var self = this;
				if ($.isFunction(callback)) {
					callback(self);
				}
			},
			queryString : function(key) {
				var svalue = this.get("rawPath").match(new RegExp("[\?\&]"
								+ key + "=([^\&]*)(\&?)", "i"));
				return svalue ? svalue[1] : svalue;
			}
		});

/**
 * 首页视图
 
App.IndexMainView = App.BaseView.extend({
			viewPath : "index_main.html",
			templateName : "index_main"
		});
*/
/**
 * 首页商品列表
 
App.PrdtListView = App.BaseView.extend({
			templateName : "app_list_tmpl",
			initView : function(callback) {
				var queryParams = {
					pager : {
						pageSize : 6,
						page : 1
					},
					whereExpr : " p.isAdditional=0 and p.isLatest=1 ",
					typeCode : "software"
				};
				var viewSelf = this;
				prdtServices.query(queryParams, function(resp) {
							viewSelf.set("model", Ember.Object.create(resp));
							if ($.isFunction(callback)) {
								callback(viewSelf);
							}
						});
			},
			initHotView:function(callback){
				var queryParams={
						pager:{
							pageSize: 6,
							page: 1
						},
						orderBy:" order by sales desc ",
						typeCode:"cooperation"
				};
				//queryParams.pager.page=ff;
				var viewSelf=this;
				prdtServices.query(queryParams,function(resp){
					viewSelf.set("model",Ember.Object.create(resp));
					if($.isFunction(callback)){
						callback(viewSelf);
					}
				});
			},

			didInsertElement : function() {
				this._super();
				var AppHover = $(this.get("element")).find("li");
				AppHover.mouseover(function() {
							if (!$(this).hasClass("applist_onApp")) {
								$(this).addClass("applist_HoverApp");
							}
						}).mouseout(function() {
							if (!$(this).hasClass("applist_onApp")) {
								$(this).removeClass("applist_HoverApp");
							}
						})
			},

			viewDetail : function(event) {
				var $curItem = $(event.currentTarget);
				var prdtId = $curItem.attr("id");
				if (!$curItem.hasClass("applist_onApp")) {
					$(".applist_onApp").removeClass("applist_onApp")
							.removeClass("applist_HoverApp");
					$curItem.addClass("applist_onApp");
				}
				App.loadView("appdetail.html?prdtId=" + prdtId, function(view) {
							$("#main_content_right").html("");
							view.appendTo("#main_content_right");
						});
			}
		});*/

/**
 * 商品详细
 */
App.PrdtDetailView = App.BaseView.extend({
	viewPath : "appdetail.html",
	templateName : "appdetail",
	prdtId : null, // 当前商品Id
	selectedSchemaId : null, // 当前计费方案
	selectedChargeItem : null, // 当前商品选中的费用项
	attributeBindings:"height:100%",
	selectedAppeChargeItems :[],
	init: function() {
	    this._super();
	    this.set('selectedAppeChargeItems', []);
  	},
	initView : function(callback) {
		var viewSelf = this;
		this.set("prdtId", this.queryString("prdtId"));
		prdtServices.getPrdtDetail(this.get("prdtId"), function(resp) {
					viewSelf.set("model", Ember.Object.create(resp));
					if ($.isFunction(callback)) {
						callback(viewSelf);
					}
				});
	},

	didInsertElement : function() {
		this._super();
		var viewSelf = this;
		$(".__price").click(function() {
			if ($(this).hasClass("__spec")) {
				$(this).parents(".row").find(".__price").removeClass("current");
				$(this).addClass("current");
			}
			if ($(this).hasClass("__schema")) {
				viewSelf.set("selectedSchemaId", $(this).val());
				// updateSelectedAppe();
			}
			viewSelf.calPrice();
		});
		viewSelf.calPrice();
	},

	/**
	 * 计算商品价格
	 */
	calPrice : function() {
		var viewSelf = this;
		var specValueArray = [];
		$(".__price").each(function() {
					if ($(this).hasClass("__schema")) {
						viewSelf.set("selectedSchemaId", $(this).val());
					} else if ($(this).hasClass("current")) {
						specValueArray.push($(this).attr("value"));
					}
				});
		viewSelf.setSelectedCharge(specValueArray);
		var goodPrice = 0; // 商品价格
		var appePrice = 0; // 附加功能价格
		if (viewSelf.get("selectedChargeItem") != null) {
			goodPrice = viewSelf.get("selectedChargeItem").price;
		}
		viewSelf.get("selectedAppeChargeItems").forEach(
				function(item, index, self) {
					appePrice += item.price;
				});
		var totalPrice = goodPrice + appePrice;

		$(".a_price").empty().html(totalPrice || "-");
		$(".a_schema").empty().html($("select.__price option[value='"
				+ viewSelf.get("selectedSchemaId") + "']").text());
	},

	/**
	 * 购买
	 *
	 * @param {}
	 *            event
	 */
	toBuy : function(event) {
		var selectedCharge = this.get("selectedChargeItem");
		var appeChargesItem = this.get("selectedAppeChargeItems");
		if (selectedCharge != null) {
			var allChargeItem = [];
			allChargeItem.pushObject(selectedCharge);
			appeChargesItem.forEach(function(item, index, se) {
						allChargeItem.pushObject(item);
					});
			this.addToShopCart(allChargeItem);
		} else {
			alert("请选择您要购买的商品");
		}
	},

	/**
	 * 将选中的商品添加到购物篮中
	 *
	 * @param {}
	 *            chargeItems
	 */
	addToShopCart : function(chargeItems) {
		var self = this;
		var selectedCharge = chargeItems.shiftObject();
		if (selectedCharge == null) {
			App.loadView("mymarket.html", function(view) {
						$("#all_apps").html("");
						view.appendTo("#all_apps");
					});
		} else {
			prdtServices.addToShopCart(selectedCharge.chargeId, 1,null, function(order) {
						self.addToShopCart(chargeItems);
					});
		}
	},

	/**
	 * 更新已选中的附加功能
	 *
	 * @param {}
	 *            event
	 */
	updateSelectedAppe : function(event) {
		var self = this;
		var schameId = this.get("selectedSchemaId");
		var appeChargeItems = this.get("selectedAppeChargeItems");
		var appeId = event.currentTarget.id;
		var isAdd = event.currentTarget.checked;

		$.each(this.get("model").appeProducts, function(i, appePrdt) {
			if (appeId != appePrdt.id) {
				return;
			}
			var appeCharge = null;
			$.each(appePrdt.productCharges, function(j, prdtCharge) {
						appeCharge = null;
						if (prdtCharge.schemaId == schameId) {
							appeCharge = prdtCharge;
							return false;
						}
					});
			if (appeCharge != null) {
				if (isAdd) {
					appeChargeItems.pushObject(Ember.Object.create(appeCharge));
				} else {
					var selectedItem = null;
					appeChargeItems.forEach(function(item, index, self) {
								if (item.id == appeCharge.id) {
									selectedItem = item;
									return false;
								}
							});
					if (selectedItem != null) {
						appeChargeItems.removeObject(selectedItem);
					}
				}
			}
		});
		this.calPrice();
	},

	/**
	 * schemaId:计费方案id specValueArray:所选择的规格值id数组
	 */
	setSelectedCharge : function(specValueArray) {
		var self = this;
		var schemaId = this.get("selectedSchemaId");
		var productCharges = this.get("model").productCharges||[];
		if (!specValueArray || specValueArray.length < 1) {
			return this.setSchemaCharge(schemaId, productCharges);
		}
		return this.setGoodsCharge(specValueArray);
	},

	/**
	 * 设置按方案计费的费用项
	 */
	setSchemaCharge : function() {
		var self = this;
		var schemaId = this.get("selectedSchemaId");
		var productCharges = this.get("model").productCharges||[];
		for (var i = 0; i < productCharges.length; ++i) {
			if (schemaId == productCharges[i].schemaId
					&& (!productCharges[i].goodsId)) {
				this.set("selectedChargeItem", productCharges[i]);
				return;
			}
		}
	},

	/**
	 * 设置按规格计费的费用项
	 *
	 * @param {}
	 *            specValueArray
	 */
	setGoodsCharge : function(specValueArray) {
		var self = this;
		var schemaId = this.get("selectedSchemaId");
		var productCharges = this.get("model").productCharges||[];
		for (var i = 0; i < productCharges.length; ++i) {
			if (schemaId == productCharges[i].schemaId
					&& productCharges[i].goodsId
					&& productCharges[i].goodsId != "") {
				var goodsSpecs = productCharges[i].goodsSpecs;
				var num = 0;
				for (var k = 0; k < specValueArray.length; ++k) {
					var specValueId = specValueArray[k];
					for (var j = 0; j < goodsSpecs.length; ++j) {
						if (specValueId == goodsSpecs[j].specValueId) {
							num++;
							break;
						}
					}
				}
				if (num == specValueArray.length) {
					this.set("selectedChargeItem", productCharges[i]);
				}
			}
		}
	}
});

/**
 * 购物车
 */
App.ShoppingCartView = App.BaseView.extend({
			viewPath : "mymarket.html",
			templateName : "mymarket",
			totalPric:function(){
				var total=0;
				this.get("model").orderItems.forEach(function(item,index,self){
					total+=item.price*item.quantity;
				});
				return total;
			}.property(),
			initView : function(callback) {
				var viewSelf = this;
				prdtServices.getShopCart(function(resp) {
							viewSelf.set("model", Ember.Object.create(resp));
							if ($.isFunction(callback)) {
								callback(viewSelf);
							}
						});
			},
			createOrder:function(event){
				var selfView=this;
				if(this.get("model").orderItems.length<1){
					alert("请选择商品后，再下订单!");
					return ;
				}
				//App.userController.loadUserInfo(null);
				//if(this.get("currentUser").get("isLogin")){
					prdtServices.createOrderFromShopCart(function(resp){
						App.loadView("personcenter.html",function(view){
							selfView.remove();
							view.appendTo("#all_apps");
						});
					});
				//}else{
					/*App.loadView("poplogin.html",function(view){
						view.popView();
					});*/
				//}
			},
			didInsertElement:function(){
				this._super();
				$.ajaxRegisterOauth(".AppDetailMaxBox .market_account","click");
			}
		});

/**
 * 登录
 */
App.LoginView = App.BaseView.extend({
	viewPath:"poplogin.html",
	templateName:"login",
	returnUrl:"index.html",
	initView:function(callback){
		var viewSelf=this;
		var mainUrl=window.location.href;
		if(mainUrl.indexOf("#")>0){
			mainUrl=mainUrl.substring(0,mainUrl.indexOf("#"));
		}
		var url=escape("index.jsp?returnUrl="+escape(mainUrl+"#mymarket.html"));
		this.set("returnUrl",url);
		viewSelf.append();
		if ($.isFunction(callback)) {
			callback(viewSelf);
		}
	},
	popView:function(){
		var elementId=this.get("elementId");
		var popLink=$("<a id='poplink' href='#"+elementId+"' style='display:none'>login</a>")
			.nyroModal()
			.appendTo(".indent_operate_account");

			//$.nmData($("#"+elementId).html());
			setTimeout(function(){
				popLink.nmCall();
				popLink.remove();
			},500);

	}
});

/**
 * 用户详细
 */
App.UserDetailView=App.BaseView.extend({
	templateName:"userDetail-template",
	viewPath:"userdetail.html",
	initView:function(callback){
		var viewSelf=this;
		if ($.isFunction(callback)) {
			callback(viewSelf);
		}
	},
	didInsertElement : function() {
		this._super();
	}
});

/**
 * 个人用户中心
 */
App.PersonCenterView=App.BaseView.extend({
	templateName:"personCenter-template",
	viewPath:"personcenter.html",
	initView:function(callback){
		var viewSelf=this;
		prdtServices.getMyOrder(10,1,function(resp) {
			viewSelf.set("model", Ember.Object.create(resp));
			if ($.isFunction(callback)) {
					callback(viewSelf);
			}
		});
	}
});
<?php
class PublishUtils extends AppModel {
	public function getShippingServiceCost( $tag){
		return "var ".$tag."_settimeoutid_showupview=0;
	var ".$tag."_upinput=$('#".$tag."_ucurrencyinput').children('.upinput');
	var ".$tag."_upview=$('#".$tag."_ucurrencyinput').children('.upview');
	var ".$tag."_pinput=$('#".$tag."_ucurrencyinput').children('.pinput');
		".$tag."_upinput.hide();
		".$tag."_upview.show();
	".$tag."_pinput.keyup(function(){
		var from_money= ".$tag."_pinput.val();
		var from_currency= 'USD';
		var to_currency= 'CNY';
		$.post('/index.php/ajax/exchange',{'from_money':from_money,'from_currency':from_currency,'to_currency':to_currency},function(data){
			data=eval('('+data+')');
			".$tag."_upinput.children('input').val(data.to_money);
			".$tag."_upview.children('span').html(data.to_money);
		});
		clearTimeout(".$tag."_settimeoutid_showupview);
		".$tag."_upinput.show();
		".$tag."_upview.hide();
	});
	".$tag."_upinput.children('input').keyup(function(){
		var from_money= ".$tag."_upinput.children('input').val();
		var from_currency=  'CNY';
		var to_currency='USD';
		$.post('/index.php/ajax/exchange',{'from_money':from_money,'from_currency':from_currency,'to_currency':to_currency},function(data){
			data=eval('('+data+')');
			".$tag."_pinput.val(data.to_money);
		});
		".$tag."_upview.children('span').html(from_money);
		clearTimeout(".$tag."_settimeoutid_showupview);
		".$tag."_upinput.show();
		".$tag."_upview.hide();
	});
	".$tag."_pinput.click(function(){
		clearTimeout(".$tag."_settimeoutid_showupview);
		".$tag."_upinput.show();
		".$tag."_upview.hide();
	});
	".$tag."_upinput.children('input').click(function(){
		clearTimeout(".$tag."_settimeoutid_showupview);
		".$tag."_upinput.show();
		".$tag."_upview.hide();
	});
	".$tag."_upview.dblclick(function(){
		clearTimeout(".$tag."_settimeoutid_showupview);
		".$tag."_upinput.show();
		".$tag."_upview.hide();
		".$tag."_upinput.children('input').focus();
	});
	".$tag."_upinput.children('input').blur(function(){
		".$tag."_settimeoutid_showupview=window.setTimeout(function(){
				".$tag."_upinput.hide();
				".$tag."_upview.show();
			},200
		);
	});
	".$tag."_pinput.blur(function(){
		".$tag."_settimeoutid_showupview=window.setTimeout(function(){
				".$tag."_upinput.hide();
				".$tag."_upview.show();
			},200
		);
	});
	function ".$tag."_setzero(v){
		".$tag."_pinput.val(v);
		".$tag."_upinput.children('input').val(v);
		".$tag."_upview.children('span').html(v);
	}" ;
	}
	
	public function getCanShipCountry(){}
	
	public function getShippingService1(){
		return '<option value="USPSPriority">USPS Priority
												Mail(2-3days)</option>
											<option value="USPSPriorityFlatRateEnvelope">USPS
												Priority Mail Flat Rate Envelope(2-3days)</option>
											<option value="USPSPriorityMailSmallFlatRateBox">USPS
												Priority Mail Small Flat Rate Box(2-3days)</option>
											<option value="USPSPriorityFlatRateBox">USPS
												Priority Mail Medium Flat Rate Box(2-3days)</option>
											<option value="USPSPriorityMailLargeFlatRateBox">USPS
												Priority Mail Large Flat Rate Box(2-3days)</option>
											<option value="USPSExpressMail">USPS Express Mail</option>
											<option value="USPSExpressFlatRateEnvelope">USPS
												Express Mail Flat Rate Envelope</option>
											<option value="USPSParcel">USPS Parcel
												Select(2-9days)</option>
											<option value="USPSMedia">USPS Media Mail(2-8days)</option>
											<option value="USPSFirstClass">USPS First Class
												Package(2-5days)</option>
											<option value="UPSGround">UPS Ground(1-5days)</option>
											<option value="UPS3rdDay">UPS 3 Day Select</option>
											<option value="UPS2ndDay">UPS 2nd Day Air</option>
											<option value="UPSNextDay">UPS Next Day Air Saver</option>
											<option value="UPSNextDayAir">UPS Next Day Air</option>
											<option value="ShippingMethodStandard">Standard
												Shipping(1-5days)</option>
											<option value="ShippingMethodExpress">Expedited
												Shipping(1-3days)</option>
											<option value="ShippingMethodOvernight">One-day
												Shipping</option>
											<option value="Other">Economy Shipping(1-10days)</option>
											<option value="Pickup">Local Pickup</option>
											<option value="EconomyShippingFromOutsideUS">Economy
												Shipping from outside US(11-23days)</option>
											<option value="StandardShippingFromOutsideUS">Standard
												Shipping from outside US(5-10days)</option>
											<option value="ExpeditedShippingFromOutsideUS">Expedited
												Shipping from outside US(1-4days)</option>
											<option value="USPSPriorityMailPaddedFlatRateEnvelope">USPS
												Priority Mail Padded Flat Rate Envelope(2-3days)</option>
											<option value="USPSPriorityMailLegalFlatRateEnvelope">USPS
												Priority Mail Legal Flat Rate Envelope(2-3days)</option>
											<option value="USPSExpressMailLegalFlatRateEnvelope">USPS
												Express Mail Legal Flat Rate Envelope</option>
											<option value="FedExHomeDelivery">FedEx Ground or
												FedEx Home Delivery(1-5days)</option>
											<option value="FedExExpressSaver">FedEx Express
												Saver(1-3days)</option>
											<option value="FedEx2Day">FedEx 2Day(1-2days)</option>
											<option value="FedExPriorityOvernight">FedEx
												Priority Overnight</option>
											<option value="FedExStandardOvernight">FedEx
												Standard Overnight</option>
											<option value="ePacketHongKong">ePacket delivery
												from Hong Kong(7-12days)</option>
											<option value="ePacketChina">ePacket delivery from
												China(7-12days)</option>
											<option value="US_FedExIntlEconomy">FedEx
												International Economy(2-4days)</option>' ;
	}
	
	public function getShippingService2(){
		return '<option value="USPSFirstClassMailInternational">USPS
												First Class Mail Intl / First Class Package Intl Service</option>
											<option value="USPSPriorityMailInternational">USPS
												Priority Mail International(6-10days)</option>
											<option value="USPSPriorityMailInternationalFlatRateEnvelope">USPS
												Priority Mail International Flat Rate Envelope(6-10days)</option>
											<option value="USPSPriorityMailInternationalSmallFlatRateBox">USPS
												Priority Mail International Small Flat Rate Box(6-10days)</option>
											<option value="USPSPriorityMailInternationalFlatRateBox">USPS
												Priority Mail International Medium Flat Rate Box(6-10days)</option>
											<option value="USPSPriorityMailInternationalLargeFlatRateBox">USPS
												Priority Mail International Large Flat Rate Box(6-10days)</option>
											<option value="USPSExpressMailInternational">USPS
												Express Mail International(3-5days)</option>
											<option value="USPSExpressMailInternationalFlatRateEnvelope">USPS
												Express Mail International Flat Rate Envelope(3-5days)</option>
											<option value="UPSWorldWideExpressPlus">UPS
												Worldwide Express Plus(1-2days)</option>
											<option value="UPSWorldWideExpress">UPS Worldwide
												Express(1-2days)</option>
											<option value="UPSWorldWideExpedited">UPS Worldwide
												Expedited(2-5days)</option>
											<option value="UPSWorldwideSaver">UPS Worldwide
												Saver(1-3days)</option>
											<option value="UPSStandardToCanada">UPS Standard To
												Canada</option>
											<option value="StandardInternational">Standard Int‘l
												Shipping</option>
											<option value="ExpeditedInternational">Expedited
												Int‘l Shipping</option>
											<option value="OtherInternational">Economy Int‘l
												Shipping</option>
											<option
												value="USPSPriorityMailInternationalPaddedFlatRateEnvelope">USPS
												Priority Mail International Padded Flat Rate
												Envelope(6-10days)</option>
											<option
												value="USPSPriorityMailInternationalLegalFlatRateEnvelope">USPS
												Priority Mail International Legal Flat Rate
												Envelope(6-10days)</option>
											<option
												value="USPSExpressMailInternationalLegalFlatRateEnvelope">USPS
												Express Mail International Legal Flat Rate Envelope(3-5days)</option>
											<option value="FedExInternationalEconomy">FedEx
												International Economy</option>
											<option value="FedExInternationalPriority">FedEx
												International Priority</option>
											<option value="FedExGroundInternationalToCanada">FedEx
												Ground International for Canada</option>' ;
	}
	
	public function getTaxStates(){
		return '<option value=""></option>
											<option value="PW">Palau</option>
											<option value="AS">American Samoa</option>
											<option value="GU">Guam</option>
											<option value="MP">Northern Mariana Islands</option>
											<option value="VI">Virgin Islands</option>
											<option value="PR">Puerto Rico</option>
											<option value="WY">Wyoming</option>
											<option value="WI">Wisconsin</option>
											<option value="WV">West Virginia</option>
											<option value="WA">Washington</option>
											<option value="VA">Virginia</option>
											<option value="VT">Vermont</option>
											<option value="UT">Utah</option>
											<option value="TX">Texas</option>
											<option value="TN">Tennessee</option>
											<option value="SD">South Dakota</option>
											<option value="SC">South Carolina</option>
											<option value="RI">Rhode Island</option>
											<option value="PA">Pennsylvania</option>
											<option value="OR">Oregon</option>
											<option value="OK">Oklahoma</option>
											<option value="OH">Ohio</option>
											<option value="ND">North Dakota</option>
											<option value="NC">North Carolina</option>
											<option value="NY">New York</option>
											<option value="NM">New Mexico</option>
											<option value="NJ">New Jersey</option>
											<option value="NH">New Hampshire</option>
											<option value="NV">Nevada</option>
											<option value="NE">Nebraska</option>
											<option value="MT">Montana</option>
											<option value="MO">Missouri</option>
											<option value="MS">Mississippi</option>
											<option value="MN">Minnesota</option>
											<option value="MI">Michigan</option>
											<option value="MA">Massachusetts</option>
											<option value="MD">Maryland</option>
											<option value="ME">Maine</option>
											<option value="LA">Louisiana</option>
											<option value="KY">Kentucky</option>
											<option value="KS">Kansas</option>
											<option value="IA">Iowa</option>
											<option value="IN">Indiana</option>
											<option value="IL">Illinois</option>
											<option value="ID">Idaho</option>
											<option value="HI">Hawaii</option>
											<option value="GA">Georgia</option>
											<option value="FL">Florida</option>
											<option value="DC">District of Columbia</option>
											<option value="DE">Delaware</option>
											<option value="CT">Connecticut</option>
											<option value="CO">Colorado</option>
											<option value="CA">California</option>
											<option value="AR">Arkansas</option>
											<option value="AZ">Arizona</option>
											<option value="AK">Alaska</option>
											<option value="AL">Alabama</option>' ;
	}
	
	public function getPublishCountrys(){
		return '<option value="AA">AA-APO/FPO</option>
										<option value="AD">AD-Andorra</option>
										<option value="AE">AE-United Arab Emirates</option>
										<option value="AF">AF-Afghanistan</option>
										<option value="AG">AG-Antigua and Barbuda</option>
										<option value="AI">AI-Anguilla</option>
										<option value="AL">AL-Albania</option>
										<option value="AM">AM-Armenia</option>
										<option value="AN">AN-Netherlands Antilles</option>
										<option value="AO">AO-Angola</option>
										<option value="AR">AR-Argentina</option>
										<option value="AS">AS-American Samoa</option>
										<option value="AT">AT-Austria</option>
										<option value="AU">AU-Australia</option>
										<option value="AW">AW-Aruba</option>
										<option value="AZ">AZ-Azerbaijan Republic</option>
										<option value="BA">BA-Bosnia and Herzegovina</option>
										<option value="BB">BB-Barbados</option>
										<option value="BD">BD-Bangladesh</option>
										<option value="BE">BE-Belgium</option>
										<option value="BF">BF-Burkina Faso</option>
										<option value="BG">BG-Bulgaria</option>
										<option value="BH">BH-Bahrain</option>
										<option value="BI">BI-Burundi</option>
										<option value="BJ">BJ-Benin</option>
										<option value="BM">BM-Bermuda</option>
										<option value="BN">BN-Brunei Darussalam</option>
										<option value="BO">BO-Bolivia</option>
										<option value="BR">BR-Brazil</option>
										<option value="BS">BS-Bahamas</option>
										<option value="BT">BT-Bhutan</option>
										<option value="BW">BW-Botswana</option>
										<option value="BY">BY-Belarus</option>
										<option value="BZ">BZ-Belize</option>
										<option value="CA">CA-Canada</option>
										<option value="CD">CD-Congo, Democratic Republic of
											the</option>
										<option value="CF">CF-Central African Republic</option>
										<option value="CG">CG-Congo, Republic of the</option>
										<option value="CH">CH-Switzerland</option>
										<option value="CI">CI-Cote d Ivoire (Ivory Coast)</option>
										<option value="CK">CK-Cook Islands</option>
										<option value="CL">CL-Chile</option>
										<option value="CM">CM-Cameroon</option>
										<option value="CN">CN-China</option>
										<option value="CO">CO-Colombia</option>
										<option value="CR">CR-Costa Rica</option>
										<option value="CU">CU-Cuba</option>
										<option value="CV">CV-Cape Verde Islands</option>
										<option value="CY">CY-Cyprus</option>
										<option value="CZ">CZ-Czech Republic</option>
										<option value="DE">DE-Germany</option>
										<option value="DJ">DJ-Djibouti</option>
										<option value="DK">DK-Denmark</option>
										<option value="DM">DM-Dominica</option>
										<option value="DO">DO-Dominican Republic</option>
										<option value="DZ">DZ-Algeria</option>
										<option value="EC">EC-Ecuador</option>
										<option value="EE">EE-Estonia</option>
										<option value="EG">EG-Egypt</option>
										<option value="EH">EH-Western Sahara</option>
										<option value="ER">ER-Eritrea</option>
										<option value="ES">ES-Spain</option>
										<option value="ET">ET-Ethiopia</option>
										<option value="FI">FI-Finland</option>
										<option value="FJ">FJ-Fiji</option>
										<option value="FK">FK-Falkland Islands (Islas
											Makvinas)</option>
										<option value="FM">FM-Micronesia</option>
										<option value="FR">FR-France</option>
										<option value="GA">GA-Gabon Republic</option>
										<option value="GB">GB-Great Britain</option>
										<option value="GD">GD-Grenada</option>
										<option value="GE">GE-Georgia</option>
										<option value="GF">GF-French Guiana</option>
										<option value="GH">GH-Ghana</option>
										<option value="GI">GI-Gibraltar</option>
										<option value="GL">GL-Greenland</option>
										<option value="GM">GM-Gambia</option>
										<option value="GN">GN-Guinea</option>
										<option value="GP">GP-Guadeloupe</option>
										<option value="GQ">GQ-Equatorial Guinea</option>
										<option value="GR">GR-Greece</option>
										<option value="GT">GT-Guatemala</option>
										<option value="GU">GU-Guam</option>
										<option value="GW">GW-Guinea-Bissau</option>
										<option value="GY">GY-Guyana</option>
										<option value="HK" selected="selected">HK-Hong Kong</option>
										<option value="HN">HN-Honduras</option>
										<option value="HR">HR-Croatia, Democratic Republic of
											the</option>
										<option value="HT">HT-Haiti</option>
										<option value="HU">HU-Hungary</option>
										<option value="ID">ID-Indonesia</option>
										<option value="IE">IE-Ireland</option>
										<option value="IL">IL-Israel</option>
										<option value="IN">IN-India</option>
										<option value="IQ">IQ-Iraq</option>
										<option value="IR">IR-Iran</option>
										<option value="IS">IS-Iceland</option>
										<option value="IT">IT-Italy</option>
										<option value="JM">JM-Jamaica</option>
										<option value="JO">JO-Jordan</option>
										<option value="JP">JP-Japan</option>
										<option value="KE">KE-Kenya Coast Republic</option>
										<option value="KG">KG-Kyrgyzstan</option>
										<option value="KH">KH-Cambodia</option>
										<option value="KI">KI-Kiribati</option>
										<option value="KM">KM-Comoros</option>
										<option value="KN">KN-Saint Kitts-Nevis</option>
										<option value="KP">KP-Korea, North</option>
										<option value="KR">KR-Korea, South</option>
										<option value="KW">KW-Kuwait</option>
										<option value="KY">KY-Cayman Islands</option>
										<option value="KZ">KZ-Kazakhstan</option>
										<option value="LA">LA-Laos</option>
										<option value="LB">LB-Lebanon, South</option>
										<option value="LC">LC-Saint Lucia</option>
										<option value="LI">LI-Liechtenstein</option>
										<option value="LK">LK-Sri Lanka</option>
										<option value="LR">LR-Liberia</option>
										<option value="LS">LS-Lesotho</option>
										<option value="LT">LT-Lithuania</option>
										<option value="LU">LU-Luxembourg</option>
										<option value="LV">LV-Latvia</option>
										<option value="LY">LY-Libya</option>
										<option value="MA">MA-Morocco</option>
										<option value="MC">MC-Monaco</option>
										<option value="MD">MD-Moldova</option>
										<option value="ME">ME-Montenegro</option>
										<option value="MG">MG-Madagascar</option>
										<option value="MH">MH-Marshall Islands</option>
										<option value="MK">MK-Macedonia</option>
										<option value="ML">ML-Mali</option>
										<option value="MM">MM-Burma</option>
										<option value="MN">MN-Mongolia</option>
										<option value="MO">MO-Macau</option>
										<option value="MQ">MQ-Martinique</option>
										<option value="MR">MR-Mauritania</option>
										<option value="MS">MS-Montserrat</option>
										<option value="MT">MT-Malta</option>
										<option value="MU">MU-Mauritius</option>
										<option value="MV">MV-Maldives</option>
										<option value="MW">MW-Malawi</option>
										<option value="MX">MX-Mexico</option>
										<option value="MY">MY-Malaysia</option>
										<option value="MZ">MZ-Mozambique</option>
										<option value="NA">NA-Namibia</option>
										<option value="NC">NC-New Caledonia</option>
										<option value="NE">NE-Niger</option>
										<option value="NG">NG-Nigeria</option>
										<option value="NI">NI-Nicaragua</option>
										<option value="NL">NL-Netherlands</option>
										<option value="NO">NO-Norway</option>
										<option value="NP">NP-Nepal</option>
										<option value="NR">NR-Nauru</option>
										<option value="NU">NU-Niue</option>
										<option value="NZ">NZ-New Zealand</option>
										<option value="OM">OM-Oman</option>
										<option value="PA">PA-Panama</option>
										<option value="PE">PE-Peru</option>
										<option value="PF">PF-French Polynesia</option>
										<option value="PG">PG-Papua New Guinea</option>
										<option value="PH">PH-Philippines</option>
										<option value="PK">PK-Pakistan</option>
										<option value="PL">PL-Poland</option>
										<option value="PM">PM-Saint Pierre and Miquelon</option>
										<option value="PR">PR-Puerto Rico</option>
										<option value="PT">PT-Portugal</option>
										<option value="PW">PW-Palau</option>
										<option value="PY">PY-Paraguay</option>
										<option value="QA">QA-Qatar</option>
										<option value="RE">RE-Réunion</option>
										<option value="RO">RO-Romania</option>
										<option value="RS">RS-Serbia</option>
										<option value="RU">RU-Russian Federation</option>
										<option value="RW">RW-Rwanda</option>
										<option value="SA">SA-Saudi Arabia</option>
										<option value="SB">SB-Solomon Islands</option>
										<option value="SC">SC-Seychelles</option>
										<option value="SD">SD-Sudan</option>
										<option value="SE">SE-Sweden</option>
										<option value="SG">SG-Singapore</option>
										<option value="SH">SH-Saint Helena</option>
										<option value="SI">SI-Slovenia</option>
										<option value="SJ">SJ-Svalbard</option>
										<option value="SK">SK-Slovakia</option>
										<option value="SL">SL-Sierra Leone</option>
										<option value="SM">SM-San Marino</option>
										<option value="SN">SN-Senegal</option>
										<option value="SO">SO-Somalia</option>
										<option value="SR">SR-Suriname</option>
										<option value="SV">SV-El Salvador</option>
										<option value="SY">SY-Syria</option>
										<option value="SZ">SZ-Swaziland</option>
										<option value="TC">TC-Turks and Caicos Islands</option>
										<option value="TD">TD-Chad</option>
										<option value="TG">TG-Togo</option>
										<option value="TH">TH-Thailand</option>
										<option value="TJ">TJ-Tajikistan</option>
										<option value="TM">TM-Turkmenistan</option>
										<option value="TN">TN-Tunisia</option>
										<option value="TO">TO-Tonga</option>
										<option value="TR">TR-Turkey</option>
										<option value="TT">TT-Trinidad and Tobago</option>
										<option value="TV">TV-Tuvalu</option>
										<option value="TW">TW-Taiwan</option>
										<option value="TZ">TZ-Tanzania</option>
										<option value="UA">UA-Ukraine</option>
										<option value="UG">UG-Uganda</option>
										<option value="US">US-United States</option>
										<option value="UY">UY-Uruguay</option>
										<option value="UZ">UZ-Uzbekistan</option>
										<option value="VA">VA-Vatican City State</option>
										<option value="VC">VC-Saint Vincent and the
											Grenadines</option>
										<option value="VE">VE-Venezuela</option>
										<option value="VG">VG-British Virgin Islands</option>
										<option value="VI">VI-Virgin Islands (U.S.)</option>
										<option value="VN">VN-Vietnam</option>
										<option value="VU">VU-Vanuatu</option>
										<option value="WF">WF-Wallis and Futuna</option>
										<option value="WS">WS-Western Samoa</option>
										<option value="YE">YE-Yemen</option>
										<option value="YT">YT-Mayotte</option>
										<option value="ZA">ZA-South Africa</option>
										<option value="ZM">ZM-Zambia</option>
										<option value="ZW">ZW-Zimbabwe</option>' ;
	}
}
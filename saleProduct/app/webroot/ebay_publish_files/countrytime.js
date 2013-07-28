function countryTime(){
	
	//utc当前时间小时
	var now = new Date();
	var utc_hours = now.getUTCHours();
	var utc_minutes = now.getUTCMinutes();
    if(utc_minutes<10){
        utc_minutes='0'+utc_minutes;
    }
    //中国时间
    var h_ch=utc_hours+8; 

	//美国时间
	var h_us=utc_hours-5;

	//德国时间
	var h_deu=utc_hours+1;

	//英国时间
	var h_uk=utc_hours;

	//法国时间
	var h_fra=utc_hours+1;

	//澳大利亚时间
	var h_au=utc_hours+10;
	
	h_ch=checkTime(h_ch);

	h_us=checkTime(h_us);

	h_deu=checkTime(h_deu);
	
	h_uk=checkTime(h_uk);
	
	h_fra=checkTime(h_fra);

	h_au=checkTime(h_au);
	
	document.getElementById('div_countrytime').innerHTML="&nbsp; <img src='/link/img/img2/guo_1.png'/> 中国"+h_ch+":"+utc_minutes+"&nbsp"
	document.getElementById('div_countrytime').innerHTML+="|&nbsp; <img src='/link/img/img2/guo_us.jpg' width='20' height='14'/> 美国 "+h_us+":"+utc_minutes+"&nbsp;"
	document.getElementById('div_countrytime').innerHTML+="|&nbsp; <img src='/link/img/img2/guo_3.png'/> 德国 "+h_deu+":"+utc_minutes+"&nbsp"
	document.getElementById('div_countrytime').innerHTML+="|&nbsp; <img src='/link/img/img2/guo_2.png'/> 英国 "+h_uk+":"+utc_minutes+"&nbsp"
	document.getElementById('div_countrytime').innerHTML+="|&nbsp; <img src='/link/img/img2/guo_4.png'/> 法国  "+h_fra+":"+utc_minutes+"&nbsp"
	document.getElementById('div_countrytime').innerHTML+="|&nbsp; <img src='/link/img/img2/guo_au.jpg' width='20' height='14'/> 澳大利亚   "+h_au+":"+utc_minutes+"&nbsp"
	t=setTimeout('countryTime()',30000)
}

function checkTime(i){
	if(i<0){
		i+=24
	}else if(i>=24)	{
		i-=24
	}
	if (i<10) 
	  {i="0" + i}
	  return i
}
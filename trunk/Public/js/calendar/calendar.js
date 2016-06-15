document.write('<iframe id=CalFrame name=CalFrame frameborder=0 scrolling=no src="'+webpath+'js/calendar/calendar.html" style="display:none;position:absolute;z-index:1001;background-color:#ffffff;"></iframe>');
document.onclick=hideCalendar;
var flay = 0;
function showCalendar(sImg,bOpenBound,sFld1,sNextP,sNextD,sStartD,sEndD,sVD,sOE,sVDE,sOT,s3F,sFld2,sCallback,sNextVDE)
{
	var fld1,fld2;
	var cf=document.getElementById("CalFrame");
	var wcf=window.frames.CalFrame;
	var oImg=document.getElementById(sImg);
	if(!oImg){alert("ƶԏ㲻䦔ڣᢩ;return;}
	if(!sFld1){alert("ʤȫָ样ᢩ;return;}
	fld1=document.getElementById(sFld1);
	if(!fld1){alert("ʤȫ䦔ڣᢩ;return;}
	if(fld1.tagName!="INPUT"||fld1.type!="text"){alert("ʤȫЍ䭎㣡");return;}
	if(sFld2)
	{
		fld2=document.getElementById(sFld2);
		if(!fld2){alert("⎿쿘쾲봦Ԛ㡢);return;}
		if(fld2.tagName!="INPUT"||(fld2.type!="text"&&fld2.type!="hidden")){alert("⎿쿘쾀Ѝ䭎㣡");return;}
	}
	if(!wcf.bCalLoaded){alert("ȕ:δ㉹旰Ԙ㡇닢Ђҳa㡢);return;}
	wcf.n_position=sNextP;
	wcf.n_textdate=sNextD;
	wcf.startdate=sStartD;
	wcf.enddate=sEndD;
	wcf.vailidday=sVD;
	wcf.oddeven=sOE;
	wcf.vailiddate=sVDE;
	wcf.nextvailiddate = sNextVDE;
	wcf.objecttype=sOT;
	wcf.thirdfocus=s3F;
	if(cf.style.display=="block"){cf.style.display="none";return;}
	var eT=0,eL=0,p=oImg;
	var sT=(document.body.scrollTop > document.documentElement.scrollTop)? document.body.scrollTop:document.documentElement.scrollTop;
	var sL=(document.body.scrollLeft > document.documentElement.scrollLeft )? document.body.scrollLeft:document.documentElement.scrollLeft;
	var h1 = document.body.clientHeight;
	var h2 = document.documentElement.clientHeight;
	var isXhtml = (h2<=h1&&h2!=0)?true:false;
	var myClient = getClient();
	var myScroll = getScroll();
	var eH=oImg.height,eW=oImg.width;
	while(p&&p.tagName.toLowerCase() != "body"){eT+=p.offsetTop;eL+=p.offsetLeft;p=p.offsetParent;}
	var bottomSpace = myClient.clientHeight - eT - myScroll.sTop;
	eH=5;
	if(sOT=="text")
	{
		cf.style.top= (eT+eH+20).toString() + "px";
	}
	else
	{
		cf.style.top= (eT+eH+20).toString() + "px";
	}
	cf.style.left= ((isXhtml?document.documentElement.clientWidth:document.body.clientWidth-(eL-sL)>=cf.width)?eL:eL+eW-cf.width).toString() + "px";
	cf.style.display="block";
	wcf.openbound=bOpenBound;
	wcf.fld1=fld1;
	wcf.fld2=fld2;
	wcf.callback=sCallback;
	wcf.initCalendar();
	ShowCalendarBottomTel();
}
function hideCalendar()
{
	var cf=document.getElementById("CalFrame");
	cf.style.display="none";
}
function getScroll()
{
		var sTop = 0, sLeft = 0, sWidth = 0, sHeight = 0;

		sTop = (document.body.scrollTop > document.documentElement.scrollTop)? document.body.scrollTop:document.documentElement.scrollTop;
		if( isNaN(sTop) || sTop <0 ){ sTop = 0 ;}

		sLeft = (document.body.scrollLeft > document.documentElement.scrollLeft )? document.body.scrollLeft:document.documentElement.scrollLeft;
		if( isNaN(sLeft) || sLeft <0 ){ sLeft = 0 ;}

		return { sTop:sTop, sLeft: sLeft, sWidth: sWidth, sHeight: sHeight };
}
function getClient()
{
    		var h1 = document.body.clientHeight;
			var h2 = document.documentElement.clientHeight;
			var isXhtml = (h2<=h1&&h2!=0)?true:false;

			this.clientHeight = isXhtml?document.documentElement.clientHeight:document.body.clientHeight;
			this.clientWidth  = isXhtml?document.documentElement.clientWidth:document.body.clientWidth;

    return {clientHeight:this.clientHeight,clientWidth:this.clientWidth};
}
function   addDate(NumDay,dtDate){   
    var date = new   Date(dtDate)   
    lIntval   =   parseInt(NumDay)//줸䠠   
    date.setDate(date.getDate()   +   lIntval)       
    return   date.getYear() +'-' + (date.getMonth()+1) + '-'   +date.getDate()  
}

function SelHotelCalMethod(event,type,comeid,outid,enddate){
	event = event || window.event;
    var comeDate = new Date(document.getElementById(comeid).value.replace(/-/g,"\/"));
    var begindate = comeDate.getFullYear() + "-" + (comeDate.getMonth()+1) + "-" + (comeDate.getDate()+1);
	if (type=="out"){
	    event.cancelBubble=true;showCalendar(outid,false,outid,'','',begindate,enddate,'','','','SWeek','');
	}
}

function HideCalendarBottomTel(){
    var doc;
    if (document.all){//IE
        doc = document.frames["CalFrame"].document;
    }else{//Firefox   
        doc = document.getElementById("CalFrame").contentDocument;
    }
    doc.getElementById("divCBottomTel").style.display = "none";
    document.getElementById("CalFrame").height="177";
}
function ShowCalendarBottomTel(){
    var doc;
    if (document.all){//IE
        doc = document.frames["CalFrame"].document;
    }else{//Firefox   
        doc = document.getElementById("CalFrame").contentDocument;
    }
    doc.getElementById("divCBottomTel").style.display = "block";
    document.getElementById("CalFrame").height="195";
}
function SWeek(v){
//	alert(ZNDate.getDateTip(ZNDate.format(ZNDate.plus(v,3))));
	
}
function exec(a){return a()}
var ZNDate=exec(function(){
var b={"2010-01-01":{holidayName:"元旦节",beforeTime:3,afterTime:3,dayindex:0},"2010-02-14":{holidayName:"春节",beforeTime:7,afterTime:0,dayindex:0},"2010-04-05":{holidayName:"清明节",beforeTime:3,afterTime:3,dayindex:0},"2010-05-01":{holidayName:"劳动节",beforeTime:3,afterTime:3,dayindex:0},"2010-06-16":{holidayName:"端午节",beforeTime:3,afterTime:3,dayindex:0},"2010-09-22":{holidayName:"中秋节",beforeTime:3,afterTime:3,dayindex:0},"2010-10-01":{holidayName:"国庆节",beforeTime:3,afterTime:0,dayindex:0}};
var e=["今天","明天","后天"];
var h=24*60*60*1000;
var g=["日","一","二","三","四","五","六"];
var c={week:"周",day:"天",before:"前",after:"后"};
var f={SECOND:"秒",MILLISECOND:"毫秒",MINUTE:"分钟",HOUR:"小时",DAY:"天",YEAR:"年"};
var a=null;
var d=null;
return{
isHoliday:function(i){return !!b[i]},
parseTimeToNL_et:function(i){if(i>=h){i=h}return this.parseTimeToNL(i)},
parseTimeToNL:function(n){var m=n%1000;var l=(n-m)%60000;var j=(n-l*1000-m)%3600000;var o=(n-j*60000-l*1000-m)%(24*3600000);var i=(n-o*3600000-j*60000-l*1000-m)%(24*3600000);var k="";if(n<1000){k=n+f.MILLISECOND}else{if(n<60000){k=parseInt(n/1000)+f.SECOND}else{if(n<3600000){k=parseInt(n/60000)+f.MINUTE
}else{if(n<(24*3600000)){k=parseInt(n/3600000)+f.HOUR}else{if(n<(365*24*3600000)){k=parseInt(n/(24*3600000))+f.DAY}else{k=parseInt(n/(365*24*3600000))+f.YEAR}}}}}return k},
plus:function(i,j){return new Date(i.getTime()+j*h)
},
today:function(){if(a){return a}var i=new Date();return a=new Date(i.getFullYear(),i.getMonth(),i.getDate())},
parse:function(j){var i=j.split("-");return new Date(i[0],i[1]-1,i[2])},
format:function(i){return i.getFullYear()+"-"+this.convert2digit(i.getMonth()+1)+"-"+this.convert2digit(i.getDate())},
convert2digit:function(i){return i<10?"0"+i:i},
compareDate:function(j,i){return j.getTime()-i.getTime()},
getFirstDaysOfMonth:function(i){return new Date(i.getFullYear(),i.getMonth(),1)},
getLastDaysOfMonth:function(i){return new Date(i.getFullYear(),i.getMonth()+1,0)},
getDateTip:function(i){//明天 || 周几
	var j=this.parse(i);
	var k=(j.getTime()-this.today().getTime())/1000/3600/24;
	var l="";
	if(k<3){
		l=e[k]
	}else{
		this.initDataTable();
		if(d[i]){l=d[i].holidayName}}
		if(l==""){l=c.week+g[j.getDay()]}
		return l
	},
initDataTable:function(){if(d!=null){return d}
d={};
for(var s in b)
	{var k=s;var o=b[s];d[s]=o;var n="";var p="";
		if(o.beforeTime>0){
			for(var l=1;l<=o.beforeTime;l++)
			{
				var q={};var t=new Date(this.parse(k).getTime()-l*24*3600*1000);var m=this.format(t);q.holidayName=o.holidayName+c.before+l+c.day;q.dayindex=o.dayindex;
				if(!d[m])
				{
					d[m]=q
				}else{
					if((o.dayindex>d[m].dayindex)&&d[m].beforeTime==null){d[m]=q}
				}
			}
		}
		if(o.afterTime>0){
			for(var l=1;l<=o.afterTime;l++)
			{
				var q={};var r=new Date(this.parse(k).getTime()+l*24*3600*1000);var j=this.format(r);q.holidayName=o.holidayName+c.after+l+c.day;q.dayindex=o.dayindex;
				if(!d[j]){d[j]=q}else{
					if((o.dayindex>d[j].dayindex)&&d[this.format(new Date(t))].afterTime==null){d[j]=q}
				}
			}
		}
	}
}
}//return's }
});
$(function(){
	$("#statistics_group a").removeClass("am-btn-primary");
	var str_type="";
	if(type=="order"){
		$("#a_order").addClass("am-btn-primary");
		str_type="成交订单";
	}
	if(type=="bill"){
		$("#a_bill").addClass("am-btn-primary");
		str_type="每日金额";
	}
	if(type=="collect"){
		$("#a_collect").addClass("am-btn-primary");
		str_type="每日收藏";
	}
	$(".f_type").html(str_type)
	
	var ary_date=[];
	var ary_data=[];
	var str_html="";
	for(var i=0;i<week_array.length;i++){
		ary_date.push(week_array[i].date);
		ary_data.push(week_array[i].total);
		str_html+="<tr><td>"+week_array[i].date+"</td><td class=\"am-text-right\">"+week_array[i].total+"</td></tr>";
	}
	$("#tbody_html").html(str_html);
//	var str="";
	var myChart = echarts.init(document.getElementById('echart_main'));
	var option = {
		tooltip: {
			trigger: 'axis'
		},
		grid: {
			width: '95%',
			x: 55,
			y: 10,
			y2: 30,
		},
		xAxis: [{
			type: 'category',
			boundaryGap: false,
			data:ary_date
		}],
		yAxis: [{
			type: 'value'
		}],
		series: [{
			name: str_type,
			type: 'line',
			stack: '总量',
			data:ary_data
		}]
	};
	myChart.setOption(option);
});

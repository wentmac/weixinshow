
$(function() {
	data_buider.select_refund_status();
	
	$("#refund").click(function() {
		data_buider.refund_click();
	});
	$("#btn_express").click(function() {
		data_buider.submit_express();
	});

	$("#cancle_request").click(function() {
		if (confirm("请确认是否要取消申请")) {
			data_buider.cancle_request();
		}
	});
	$("#customer_service").click(function(){
		if (confirm("请确认是否要客服介入")) {
			data_buider.customer_service();
		}
	});
	$("#edit_return_goods").click(function(){
		data_buider.refund_click();
	});
	
	$("#goods_customer").click(function(){//退货客服
		data_buider.goods_customer();
	});
});
var _is_modify=0;

var data_buider = {
	
	goods_customer:function(){//客服介入2
		$.ajax({
			type:"post",
			url:mobile_url+"member/refund.intervene_return",
			data:{
				order_refund_id:global_order_refund_id
			},
			cache:false,
			success:function(data){
				if(data.success==true){
					alert("已经成功申请客服介入");
					data_buider.select_refund_status();
				}else{
					alert(data.message);
				}
			},
			error:function(data){
				alert("系统正忙，请稍后再试...")
			}
		});
	},
	edit_return_goods:function(){
		_is_modify="1";
		data_buider.refund_click();
	},
	customer_service:function(){
		$.ajax({
			type:"post",
			url:mobile_url+"member/refund.intervene_refund",
			data:{
				order_refund_id:global_order_refund_id
			},
			cache:false,
			success:function(data){
				if(data.success==true){
					alert("已经成功申请客服介入");
					data_buider.select_refund_status();
				}
				else{
					alert(data.message);
				}
			},
			error:function(data){
				alert("系统正忙，请稍后再试...")
			}
		});
	},
	
	cancle_request: function() {
		$.ajax({
			type:"post",
			url:mobile_url+"member/refund.returned_cancel",
			data:{
				order_refund_id:global_order_refund_id
			},
			cache:false,
			success:function(data){
				if(data.success==true){
					
					alert("已经成功取消申请");
					data_buider.select_refund_status();
				}
				else{alert(data.message);}
			},error:function(data){
				alert("系统正忙请稍后再试");
			}
			
		});
	},
	submit_express: function() {//提交收获地址
		var express_id = $("#hid_express").attr("data_id");
		var express_name = $("#hid_express").attr("data_name");
		if ($("#fedexName").val() != "") {
			express_id = "0";
			express_name = $("#fedexName").val();
		}

		$.ajax({
			type: "post",
			url: mobile_url + "member/refund.returned_save",
			data: {
				order_refund_id: global_order_refund_id,
				express_id: express_id,
				express_name: express_name,
				express_no: $("#fedexNum").val(),
				is_modify:_is_modify
			},
			cache:false,
			success: function(data) {
				if(data.success){
					alert("提交成功");
					location.href="";
				}else{
					alert(data.message);
				}
			},
			error: function(data) {
				alert(data.message);
			}

		});
	},
	li_click: function() {//选择快递信息
		$("#fedex li").bind("click", function() {
			$("#fedex li").find("span").hide();
			$(this).find("span").show();
			$("#hid_express").attr("data_id", $(this).attr("data-express-id"));
			$("#hid_express").attr("data_name", $(this).attr("data-express-name"));
		});
	},
	refund_click: function() {//显示快递列表
		$.ajax({
			type: "get",
			url: mobile_url + "member/refund.get_express",
			data: {},
			cache:false,
			success: function(data) {
				if (data.success == true) {
					$("#fedex").html("");
					var express = "";
					var list = data.data;
					for (var i = 0; i < list.length; i++) {
						express += '<li  data-express-id="' + list[i].express_id + '" data-express-name="' + list[i].express_name + '">' + list[i].express_name + '<span class="hide"><em></em></span>'
					}
					$("#fedex").html($("#fedex").html() + express + '<li><input type="text" placeholder="自己填写快递公司" id="fedexName"><span class="hide"><em></em></span></li>');
					$("#logistics").show("slow");
					data_buider.li_click();
				} else {
					alert(data.message);
					return false;
				}
			},
			error: function(data) {
				alert(data.message);
			}

		});
	},
	select_refund_status: function() {
		if (global_refund_status == "1") {
			$("#progressInfo").show();
			$("#comment_ul").show();
		}
		if (global_refund_status == "2") {

		}
		if (global_refund_status == "3") {

		}
		
		if (global_service_status == 2 && global_refund_status == 2 && global_return_status == 1) {
			$("#refund").show();//    显示<botton>退货</botton>
		}
		if (global_service_status == 2 && global_refund_status == 3) {
			$("#cancle_request").show();//显示<botton>取消申请</botton><botton>客服介入_1</botton>
			$("#customer_service").show();
		} else if (global_service_status == 2 && global_refund_status == 2 && global_return_status  == 4) {
			
			$("#edit_return_goods").removeClass("hide");
			$("#goods_customer").removeClass("hide");
			
			//显示<botton>修改退货信息</botton><botton>客服介入_2</botton>
		}


	}
}
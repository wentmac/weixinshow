$(function(){
	$("#btnOk").click(function(){
		refund.save();
	});
	$("#hd_back").click(function(){
		location.href=mobile_url+"/member/order";
	});
});
var refund={
	
	save:function(){
		if($("#needProduct").val()=="0"){
			alert("请选择是否退货" );
			return false;
		}
		if($("#refundReason").val()=="0"){
			alert("请选择退款原因" );
			return false;
		}
		if($("#priceNeed").val()==""){
			alert("请输入金额" );
			return false;
		}

		$.ajax({
			type:"post",
			url:mobile_url+ "member/order.refund_save",
			data:{
				sn:global_order_sn,
				order_goods_id:global_order_goods_id,
				money:$("#priceNeed").val(),
				refund_service_status:$("#needProduct option:selected").val(),
				refund_service_reason:$("#refundReason option:selected").val()
			},
			cache:false,
			success:function(data){
				if(data.success==true)
				{
					alert("提交成功");
					location.href=mobile_url+"member/order";
				}
				else{
					alert(data.message);
				}
			},
			error:function(data){
				alert(data.message);
			}
			
		});
	}
}

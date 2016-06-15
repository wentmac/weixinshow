$(function() {
	if ( current_money > 0 ) {
		$('#settle_div').show();
		$('#settle_button').show();
	} else {
		$('#settle_div').hide();
		$('#settle_button').hide();
	}
	bill_home.settle_click();
	bill_home.update_avatar_click();
});

var bill_home = {
	init: function() {
		
	},	
	settle_click: function() {
		$('#settle_button a').click(function(){
			var money = $('#money').val();
			if ( money > current_money ) {
				M._alert("提现的金额不能大于可提现余额哟");
				return false;
			}
			$.ajax({
				type:"post",
				url:mobile_url+"member/settle.create",
				data:{
					money:money
				},
				cache:false,
				success:function(data){
					if(data.success==true){
						M._alert("已经成功申请提现");
						window.location.reload();		
					}else{
						M._alert(data.message);
					}
				},
				error:function(data){
					M._alert("系统正忙，请稍后再试...")
				}
			});	
		});	
	},
	update_avatar_click: function() {
		$('#update_avatar').click(function(){			
			$.ajax({
				type:"post",
				url:mobile_url+"member/bill.update_avatar",
				data:{},
				cache:false,
				success:function(data){
					if ( data.success == true ) {
						$('#avatar_imgurl').attr('src',data.data);
						M._alert("头像更新成功");
					}else{
						M._alert(data.message);
					}
				},
				error:function(data){
					M._alert("系统正忙，请稍后再试...");
				}
			});	
		});		
	}
}
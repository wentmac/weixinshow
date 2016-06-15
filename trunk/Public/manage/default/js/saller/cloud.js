$(function(){
	data_builder.get_cate_list();
});
var data_builder={
	get_cate_list:function(){
		$.ajax({
			type:"get",
			url:php_self+"?m=seller/cloud.get_category_list",
			cache:false,
			success:function(data){
				$("#btn_cat_list").html("");
				
			}
		});
	}
	
}

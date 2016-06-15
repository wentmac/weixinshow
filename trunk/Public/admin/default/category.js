// JavaScript Document
function checkForm()
{
	if($('cat_name').value == ""){
	   alert("请填写分类名称！");   
	   $('cat_name').focus();
	   return(false);
   	}
	
	if(($('cat_id').value == $('cat_pid').value) && $('cat_id').value > 0){
	   alert("所属栏目父级不能为自己！");   
	   $('cat_name').focus();
	   return(false);
   	}	
	
}

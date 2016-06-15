// JavaScript Document
function chkForm()
{
	if($('#poster_name').val() == ""){
	   alert("请填写位置！");   
	   $('#poster_name').focus();
	   return(false);
   	}
	
	if($('#poster_title').val() == ""){
	   alert("请填写标题！");   
	   $('#poster_title').focus();	   
	   return(false);
   	}	
	
	if($('#poster_width').val() == ""){
	   alert("请填写广告宽度！");   
	   $('#poster_width').focus();
	   return(false);
   	}
	
	if($('#poster_height').val() == ""){
	   alert("请填写广告高度！");   
	   $('#poster_height').focus();
	   return(false);
   	}
	
 function IsURL(str_url){ 
        var strRegex = "^((https|http|ftp|rtsp|mms)?://)"  
        + "?(([0-9a-z_!~*'().&=+$%-]+: )?[0-9a-z_!~*'().&=+$%-]+@)?" //ftp的user@  
        + "(([0-9]{1,3}\.){3}[0-9]{1,3}" // IP形式的URL- 199.194.52.184  
        + "|" // 允许IP和DOMAIN（域名） 
        + "([0-9a-z_!~*'()-]+\.)*" // 域名- www.  
        + "([0-9a-z][0-9a-z-]{0,61})?[0-9a-z]\." // 二级域名  
        + "[a-z]{2,6})" // first level domain- .com or .museum  
        + "(:[0-9]{1,4})?" // 端口- :80  
        + "((/?)|" // a slash isn't required if there is no file name  
        + "(/[0-9a-z_!~*'().;?:@&=+$,%#-]+)+/?)$";  
        var re=new RegExp(strRegex);  
  //re.test() 
        if (re.test(str_url)){ 
            return (true);  
         }else{  
            return (false);  
         } 
     } 

	
	if(!IsURL($('#poster_link').val()))
	{
		/**
		alert("请填写正确广告链接！");
		$('poster_link').focus();
		return false;	
		*/
	}
	

	/*if($('link').val() == "" || $('link').val() == "http://"){
	   alert("请填写广告链接！");   
	   $('link').focus();
	   return(false);
   	}*/			
	
	if(!CheckRadio('type_radio')){
	   alert("请选择广告类型！");   
  	   return(false);
	} else {
		if(GetRadioValue('type_radio') == 2){
			if($('#thumb').val() == ''){
				alert('请上传图片！')
		  	    $('#thumb_upload').focus();	
				return false;		
			}
		}	
		if(GetRadioValue('type_radio') == 3){				
			if($('#externallinks').val() == ''){
				alert('请填写广告代码！')
		  	    $('#externallinks').focus();	
				return false;		
			}			
		}
	}				
	
	if(!CheckRadio('state_radio')){
	   alert("请选择广告期限！");   
  	   return(false);
	} else {
		if(GetRadioValue('state_radio') == 1){
			if($('#start_date').val() == ''){
				alert('请选择开始日期！')
		  	    $('#start_date').focus();	
				return false;		
			}
			if($('#end_date').val() == ''){
				alert('请选择结束日期！')
		  	    $('#end_date').focus();	
				return false;		
			}					
		}
	}
}

function checkDelForm(){
	var check = GetCheckboxValue('id_a[]');
	if( check == '' )
	{
		alert("好像您没有选择任何要删除资讯吧?:-(");	
		return false;
	}
}

function typechange(v)
{
	if(v == '1'){
		$('#changetype2').hide();
	} 
	if(v == '2'){
		$('#changetype2').show();
	} 	
}

function statechange(v){
	if(v == '1'){
		$('#changestate').show();
	} else {
		$('#changestate').hide();
	}
}
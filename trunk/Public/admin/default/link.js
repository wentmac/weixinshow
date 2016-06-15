// JavaScript Document
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
	 
function chkForm()
{
	if($('title').value == ""){
	   alert("请填写标题！");   
	   $('title').focus();	   
	   return(false);
   	}
	
	if(!IsURL($('link').value))
	{
		alert("请填写正确广告链接！");
		return false;	
	}			
	
	if(!CheckRadio('link_type_radio')){
	   alert("请选择友情链接类型！");   
  	   return(false);
	} else {
		if(GetRadioValue('link_type_radio') == 2){
			if($('thumb').value == ''){
				alert('请上传图片！')
		  	    $('thumb_upload').focus();	
				return false;		
			}
		}	
		if(GetRadioValue('link_type_radio') == 3){				
			if($('externallinks').value == ''){
				alert('请填写链接地址！')
		  	    $('externallinks').focus();	
				return false;		
			}			
		}
	}				
	
	if(!CheckRadio('link_state_radio')){
	   alert("请选择友情链接期限！");   
  	   return(false);
	} else {
		if(GetRadioValue('link_state_radio') == 1){
			if($('start_date').value == ''){
				alert('请选择开始日期！')
		  	    $('start_date').focus();	
				return false;		
			}
			if($('end_date').value == ''){
				alert('请选择结束日期！')
		  	    $('end_date').focus();	
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
		$('changetype2').style.display='none';
		$('changetype3').style.display='none';		
	} 
	if(v == '2'){
		$('changetype2').style.display='block';
		$('changetype3').style.display='none';		
	} 
	if(v == '3') {
		$('changetype2').style.display='none';
		$('changetype3').style.display='block';		
	}
}

function statechange(v){
	if(v == '1'){
		$('changestate').style.display='block';
	} else {
		$('changestate').style.display='none';		
	}
}
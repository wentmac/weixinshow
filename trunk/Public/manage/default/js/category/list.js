$('.cate img').click(function(){
	var cate_ul = $(this).parent().parent().parent().next("ul");	
	if(cate_ul.css('display')=='block') {
		cate_ul.hide('normal');		
		$(this).parent().children("img").attr("src",base_v+"image/add.gif");
	} else {
		cate_ul.show('normal');		
		$(this).parent().children("img").attr("src",base_v+"image/desc.gif");					
	}
});

$(function(){
	$(".category_list dl").hover(
	   function(){
		   $(this).addClass("category_list_bg");
	   }, 
	   function (){
		   $(this).removeClass("category_list_bg");
	   }
	);
})



function close_cat()
{
   $('.category_list li').each(function(){	  
  	  $(this).find('ul').hide('normal');	  
  	  $(this).find('.cate img').attr("src",base_v+"image/add.gif");
   });
}

function open_cat()
{
   $('.category_list li').each(function(){
		$(this).find('ul').show('normal');	  
  	  $(this).find('.cate img').attr("src",base_v+"image/desc.gif");
   });
}
;(function($){   
	$.fn.extend({
		popLoading:function(options){
		    //Set the default values, use comma to separate the settings, example:   
            var defaults = {				
				html: '',		//要显示的HTML
                div: 'body',    //body就是全屏遮罩背景 #id就是某个div下面的显示
				mini: true,	//是不是居中小窗口
				closeid: '',	//关闭按钮ID
				DragTitle: '',	//拖动的title div ID 
				dialog_body: 'DragBody',	//可以自定义拖动的层ID   第一层的DIV ID				
				dialog_id : '',	//弹窗的CLASSNAME 用来取默认的宽和高 第二层的DIV ID						
				left : '',	//弹出窗口的宽度 如果是数字就是宽度
				top : '',	//弹出窗口的高度 如果是数字就是高度
                type : 'noDrag'//Drag                   
            }   			   
			
			var dialog;		//弹出窗口遮盖的DIV				
            //覆盖默认参数
            var opts = $.extend(defaults, options);		
			//判断该弹窗是接收HTML内容 还是使用原页面中的DIV层
			if((opts.html == '') && (opts.dialog_body != '')){
				var dialog_html_var = true;				
			} else {
				var dialog_html_var = false;
			}		
			
			//主函数 加了 return this.each... 表示返回jQuery对象，以便链接式操作
			return this.each(function(){			
				//激活事件
				var obj = $(this);
				if(!dialog_html_var){
					if(opts.div == 'body'){	//全屏遮罩	
						dialog = $(document.body);
					}else{
						dialog = $(""+opts.div+"");
					}
				 	var pop_load_div = $("<div id="+opts.dialog_body+">").html(opts.html);					
					pop_load_div.appendTo(dialog);				
				}
				var dialog_width = $('#'+opts.dialog_id).width();	//dialog的宽度
				var dialog_height = $('#'+opts.dialog_id).height(); //dialog的高度				
				
				var body_width = getBodyWidth();
				var body_height = getBodyHeight();					
				
				//dialog的宽度		
				function getWidth(){
					if(opts.left == ''){						
						var dialog_left = ($(window).width()/2)-(dialog_width/2);						
					} else {
						var dialog_left = opts.left;
					}					
					return dialog_left;
				}
			
				function getHeight(){
					if(opts.top == ''){						
						var dialog_top = ($(window).height()/2)-(dialog_height/2);
					} else {
						var dialog_top = opts.top;
					}
					return dialog_top;
				}
				
				function changeDialog(){
					dialog_left = getWidth();
					dialog_top = getHeight();				
					$('#'+opts.dialog_body).css({left:dialog_left,top:dialog_top});									
					body_width = getBodyWidth();
					body_height = getBodyHeight();		
				}		
						
				//取浏览器当前窗口可视区域高度		
				function getBodyWidth(){
					return $(document.body).outerWidth(true)-$("#"+opts.dialog_id).outerWidth();	//浏览器当前窗口文档body的总高度 包括border padding margin
				}
				//取浏览器当前窗口可视区域高度
				function getBodyHeight(){
					return $(window).height()-$("#"+opts.dialog_id).outerHeight();//浏览器当前窗口文档body的总高度 包括border padding margin
				}
				
				dialog_left = getWidth();
				dialog_top = getHeight();
				
				if(opts.div == 'body'){	//全屏遮罩	
					$(window).resize(changeDialog);	
					dialog = $(document.body);					
					var loading_div = $("<div class='loading'>").css({position:"fixed",'z-index':300000,width:"100%",height:"100%",left:"0",top:"0"});
					if(dialog_html_var){						
						$("#"+opts.dialog_body).css({left:dialog_left,top:dialog_top,'z-index':'300001',width:dialog_width,height:dialog_height,position:"fixed",display:'block'});
					} else {												
						$("#"+opts.dialog_body).attr("class","pop_load loading").css({left:dialog_left,top:dialog_top,'z-index':'300001',width:dialog_width,height:dialog_height,position:'fixed',display:'block'});
					}					
				} else {					//DIV遮罩									
					dialog = $(""+opts.div+"");
					var loading_div=$("<div class='loading'>").css({position:"absolute",'z-index':300000,width:dialog.outerWidth(),height:dialog.outerHeight(),left:0,top:0});
					if(opts.mini){ //把opts.div块里这种遮罩起来 弹出的信息框在opts.div块里居中显示
						dialog.css({position:"relative"});																		
						if(dialog_html_var){
							$("#"+opts.dialog_body).css({position:"absolute",'z-index':300001,display:'block',left:(dialog.width()/2)-(dialog_width/2),top:(dialog.height()/2)-(dialog_height/2),width:dialog_width,height:dialog_height});
						} else {														
							$("#"+opts.dialog_body).attr("class","pop_load1 loading").css({position:"absolute",left:(dialog.width()/2)-(dialog_width/2),top:(dialog.height()/2)-(dialog_height/2)}).html(opts.html);
						}
					} else {	//把opts.div块里这种遮罩起来 弹出的信息框在全屏居中显示
						loading_div.css({left:dialog.position().left,top:dialog.position().top});
						if(dialog_html_var){													
							$("#"+opts.dialog_body).css({position:"absolute",'z-index':300001,display:'block',left:dialog_left,top:dialog_top,width:dialog_width,height:dialog_height});
						} else {														
							$("#"+opts.dialog_body).attr("class","pop_load loading").css({position:"absolute",left:dialog_left,top:dialog_top}).html(opts.html);
						}
					}				
				}						

				loading_div.appendTo(dialog);				
				loading_div.css({opacity:0,background:'#B0D1E8'}).fadeTo(700,0.4);				

				if($.browser.msie&&($.browser.version == "6.0")){				
					//蛋疼IE6不支持height:100% 解决IE6不支持的固定定位问题和高度100%的问题					
					$('body').css({height:'100%'});//高度100%很好解决，只要给body加上height:100%;就行了。
					//用_top:expression(eval());让IE6实现固定定位。					
					loading_div.css({position:"absolute"});	//ie6背景										
					//if DIV is ID
					if(dialog_html_var){ 
						$("#"+opts.dialog_body).css({position:"absolute"});												
					} else {
						$(pop_load_div).css({position:"absolute"});
					}
					if(opts.div == 'body'){	//全屏遮罩	
						loading_div.css({height:$(document).height(),width:$(document).width()});	//给背景遮罩高度设为100% hack for IE6
					}
					$(window).scroll(function(){
						var IEtop = $(window).height() / 2 - $("#"+opts.dialog_body).height() / 2 + $(window).scrollTop() + "px";
						
						//if DIV is ID
						if(dialog_html_var){ 
							$("#"+opts.dialog_body).css( 'top' , IEtop );							
						} else {
							$(pop_load_div).css( 'top' , IEtop );							
						}
					});
				}

				//监听关闭弹窗				
				if(opts.closeid !== ''){
					$('#'+opts.dialog_body+' #'+opts.closeid).click(function(){						
						if(dialog_html_var){	//如果弹窗内容是ID的html
							$("#"+opts.dialog_body).css({display:'none'});
						}
						$('.loading').remove();
					});					
				}		
		
				//拖动
				if(opts.DragTitle !== ''){													
					    var _move = false;
						var _x,_y;//鼠标离控件左上角的相对位置 						
						
						$("#"+opts.dialog_body+" #"+opts.DragTitle).mousedown(function(e){														
							_move=true;
							_x=e.pageX-parseInt($("#"+opts.dialog_body).css("left"));  		
							_y=e.pageY-parseInt($("#"+opts.dialog_body).css("top"));  	
							$(this).css('cursor', 'move');
							$("#"+opts.dialog_body).fadeTo(20, 0.25);//开始拖动后透明显示							
							e.preventDefault();//取消事件的默认动作|hack chorome拖动时DragTitle 选中“I”型							
						});						
						$(document).mousemove(function(e){							
							if (_move) {								
								var x=e.pageX-_x;//移动时根据鼠标位置计算控件左上角的绝对位置  
								var y=e.pageY-_y;  
								
								x = x > body_width ? body_width : x;															
								y = y > body_height ? body_height : y;			
								x = x < 0 ? 0 : x;
								y = y < 0 ? 0 : y;								
																				
								$("#"+opts.dialog_body).css({top:y,left:x,'position':'fixed'});																
								if ($.browser.msie) {
									e.preventDefault();//防止拖动时选中层中的文字 ie
									if($.browser.version == "6.0"){					
										$("#"+opts.dialog_body).css("position", "absolute");
									}
								} 								
							}
						}).mouseup(function(){		
							if (_move) {						
								$("#"+opts.dialog_body).fadeTo('fast', 1);//结束拖动后关闭透明显示
							}
							_move = false;															
						});						
				}
				
			});

		}

	})
})(jQuery);


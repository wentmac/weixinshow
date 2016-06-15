/*
			Create By wentmac @2015

                   _ooOoo_
                  o8888888o
                  88" . "88
                  (| -_- |)
                  O\  =  /O
               ____/`---'\____
             .'  \\|     |//  `.
            /  \\|||  :  |||//  \
           /  _||||| -:- |||||-  \
           |   | \\\  -  /// |   |
           | \_|  ''\---/''  |   |
           \  .-\__  `-`  ___/-. /
         ___`. .'  /--.--\  `. . __
      ."" '<  `.___\_<|>_/___.'  >'"".
     | | :  `- \`.;`\ _ /`;.`/ - ` : | |
     \  \ `-.   \_ __\ /__ _/   .-` /  /
======`-.____`-.___\_____/___.-`____.-'======
                   `=---='
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
			佛祖保佑       永无BUG

*/

$(document).ready(function() {

	/*保存商品*/
	$("#submit_i_do_item").bind("click", function() {
		$.AMUI.progress.start();
		var that = $(this);
		//提交后必选校验		
		if (!check_required_options.init($(this))) {
			$.AMUI.progress.done();
			return false;
		}
		var dataParam = postField;
		$.ajax({
			type: "POST",
			url: index_url + php_self + '?m=article.save',
			dataType: "json",
			data: dataParam,
			cache: false,
			success: function(data) {
				//console.log(data);
				if (data.success == true) {
					M._alert('更新成功');
					that.removeClass("isLoading").html("提交");
					$.AMUI.progress.done();
				} else {
					$.AMUI.progress.done();
					M._alert(data.message);
					that.removeClass("isLoading").html("提交");
					$.AMUI.progress.done();

					return false;
				}
			}
		});

	});


});


//校验必选
var check_required_options = {
	init: function(_this) {
		if (_this.hasClass("isLoading")) {
			return false;
		}
		_this.addClass("isLoading").html("loading...");

		//商品名
		var title = $('#title').val();
		if (!title) {
			M._alert("请填写标题");
			$('#title').focus();
			_this.removeClass("isLoading").html("提交");
			return false;
		}
		//分类
		//      var goods_cat_id = new Array();
		//      $('#i_cate_wrap input[name="goods_cat_id"]:checked').each(function() {
		//          goods_cat_id.push($(this).val()); //向数组中添加元素
		//      });

		//var goods_cat_id = new Array();
		//goods_cat_id.push($('#goods_cat_id').val());				
		postField.title = $('#title').val();
		postField.article_image_id = $('#article_image_id').val();
		postField.cat_id = $('#cat_id').val();
		postField.keywords = $('#keywords').val();
		postField.description = $('#description').val();
		postField.article_order = $('#article_order').val();		
		postField.content = $("#content").val();
		
		postField.article_id = $("#article_id").val();						
		return true;		
	}
}

# weixinshow
基于TmacPHP MVC Framework开发的微信公众号/微商系统，支持三级分销，微信登录，微信支付，商品管理，订单管理/发货，订单售后/维权。

# 安装配置
## nginx/apache配置
    公众号微商城主站：
    server_name  m.weixinshow.com;
    root         /var/www/www.weixinshow.com/trunk/mobile/wwwroot/;
    
    静态资源：
    server_name  public.weixinshow.com;
    root         /var/www/www.weixinshow.com/trunk/Public/;
    
    图片服务器站：
    server_name  img.weixinshow.com;
    root         /var/www/www.weixinshow.com/img.weixinshow.com/;  
    
## 程序配置
    /trunk/database.config //数据库及网站url配置文件
    /trunk/Tmac.config.php //网站调试等常规配置文件

# 功能介绍

	* 微商城
	* 
		* 商品展示
		* 下单/购物车
		* 提交订单
		* 微信支付
		* 会员中心
		* 
			* 注册/登录/找回密码|微信快捷登录
			* 订单管理
			* 地址管理
			* 账单流水记录
			* 下级会员
			* 收藏管理
			* 提现


	* 供应商管理中心
	* 
		* 商品管理
		* 订单管理
		* 订单处理
		* 
			* 发货
			* 退款
			* 退货
			* 导出

		* 统计
		* 
			* 销售额统计
			* 订单量统计

		* 退换货售后处理
		* 三级分销/分润
		* 广告位管理
		* 文章系统
		* 资金流水
		* 
			* 商品应收款
			* 退款
			* 佣金分润


	* 总后台
	* 
		* 总订单管理
		* 总商品管理
		* 提现管理
		* 
			* 提现申请列表
			* 执行打款

		* 总用户管理



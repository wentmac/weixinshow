<?php

/**
 * 后台首页
 * ============================================================================
 * TBlog TBlog博客系统　BY Tmac PHP MVC framework
 * $Author: zhangwentao $  <zwttmac@qq.com>
 * $Id: index.php 325 2016-05-31 10:07:35Z zhangwentao $
 * http://www.t-mac.org；
 */
class indexAction extends service_Controller_admin
{

    //定义初始化变量
    private $menu;

    public function _init()
    {
        //加载并返回Model文件夹下的Model对象。
        $this->menu = Tmac::model( 'Menu' );
        $check_model = $this->M( 'Check' );
        $check_model->checkLogin();
        $check_model->CheckPurview( 'tb_admin,tb_editer,tb_finance,tb_customer_manager' );
    }

    public function index()
    {
        $menusMain = "
        <m:top mapitem='0' name='商品管理' rank='tb_admin,tb_editer'>
          <m:item name='商品管理' link='" . PHP_SELF . "?m=goods/index' rank='tb_admin,tb_editer' target='main' />          
        </m:top>  
                
        <m:top mapitem='0' name='商品分类' rank='tb_admin,tb_editer'>
          <m:item name='分类管理' link='" . PHP_SELF . "?m=goods/category' rank='tb_admin,tb_editer' target='main' />
          <m:item name='添加分类' link='" . PHP_SELF . "?m=goods/category.add' rank='tb_admin,tb_editer' target='main' />          
        </m:top>  
        
        <m:top mapitem='0' name='订单管理' rank='tb_admin,tb_editer'>
          <m:item name='订单列表' link='" . PHP_SELF . "?m=order.index' rank='tb_admin,tb_editer' target='main' />          
          <m:item name='维权订单列表' link='" . PHP_SELF . "?m=order.refund_list' rank='tb_admin,tb_editer' target='main' />          
        </m:top>  
        
        <m:top mapitem='0' name='用户' rank='tb_admin,tb_editer,tb_customer_manager'>
          <m:item name='用户管理' link='" . PHP_SELF . "?m=member' rank='tb_admin,tb_editer,tb_customer_manager' target='main' />          
          <m:item name='注册新用户' link='" . PHP_SELF . "?m=member.add' rank='tb_admin,tb_editer,tb_customer_manager' target='main' />                            
        </m:top>     

        <m:top mapitem='0' name='管理员' rank='tb_admin'>
          <m:item name='管理员管理' link='" . PHP_SELF . "?m=user' rank='tb_admin' target='main' />
          <m:item name='添加管理员' link='" . PHP_SELF . "?m=user.add' rank='tb_admin' target='main' />
        </m:top>
     
        <m:top mapitem='0' name='提现管理' rank='tb_admin,tb_finance'>
          <m:item name='申请提现-等待审核' link='" . PHP_SELF . "?m=settle.index&status=untreated' rank='tb_admin,tb_finance' target='main' />
          <m:item name='审核成功-等待打款' link='" . PHP_SELF . "?m=settle.index&status=verify' rank='tb_admin,tb_finance' target='main' />
          <m:item name='所有提现' link='" . PHP_SELF . "?m=settle.index' rank='tb_admin,tb_finance' target='main' />
        </m:top>

        <m:top mapitem='0' name='设置' rank='tb_admin,tb_editer'>
          <m:item name='网站设置' link='" . PHP_SELF . "?m=config' rank='tb_admin' target='main' />
          <m:item name='退出系统' link='" . PHP_SELF . "?m=login.out' rank='tb_admin,tb_editer' target='main' />
        </m:top>
        ";
        //取config配置文件
        $configcache = Tmac::config( 'configcache.config.cfg_indexurl', APP_WWW_NAME, '.inc.php' );
        $this->assign( 'admin_title', $GLOBALS[ 'TmacConfig' ][ 'config' ][ 'admin_title' ] );
        $this->assign( 'indexurl', $configcache );
        $this->assign( 'menua', $this->menu->getMenua( $menusMain ) );
        $this->assign( 'username', $_SESSION[ 'admin' ] );
        $this->V();
    }

    public function body()
    {
        $verLockFile = VAR_ROOT . 'Data/ver.txt';
        $fp = fopen( $verLockFile, 'r' );
        $upTime = trim( fread( $fp, 64 ) );
        fclose( $fp );

        $database_default = $GLOBALS[ 'TmacConfig' ][ 'Common' ][ 'Database' ];
        $dbdriver = $GLOBALS[ 'db' ][ $database_default ][ 'dbdriver' ];
        if ( $dbdriver === 'MySQLi' ) {
            $db = DatabaseDriver::getInstance( $database_default );
            $mysql_version = $db->connect()->server_info;
        } else {
            $mysql_version = mysql_get_server_info();
        }
        $this->assign( 'gdversion', $this->menu->gdversion() );
        $this->assign( 'upTime', $upTime );
        $this->assign( 'mysql_version', $mysql_version );
        $this->assign( 'admin_title', $GLOBALS[ 'TmacConfig' ][ 'config' ][ 'admin_title' ] );
        $this->V( 'index_body' );
    }

}

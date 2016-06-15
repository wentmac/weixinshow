<?php

/**
 * 供应商系统页面
 * ============================================================================
 * @author  by time 22014-07-07
 * 
 */
class helpAction extends service_Controller_mobile
{

    public function __construct()
    {
        parent::__construct();
    }

    //app bill 帮助
    public function bill()
    {
        $this->V( 'help_bill' );
    }

}

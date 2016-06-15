<?php

/*
 * Tmac PHP MVC framework
 * $Author: zhangwentao $
 * $Id: GoodsComment.class.php 325 2016-05-31 10:07:35Z zhangwentao $
 */

/**
 * Description of GoodsComment.class.php
 *
 * @author Tracy McGrady
 */
class entity_GoodsComment_base
{
    public $goods_comment_id;
    public $item_id;
    public $goods_id;
    public $order_id;
    public $order_goods_id;
    public $goods_image_id;
    public $uid;
    public $username;
    public $content;
    public $comment_rank;
    public $add_time;
    public $ip_address;
    public $parent_id;
    public $is_delete;
}
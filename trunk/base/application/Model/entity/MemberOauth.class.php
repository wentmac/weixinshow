<?php

/*
 * Tmac PHP MVC framework
 * $Author: zhangwentao $
 * $Id: MemberOauth.class.php 325 2016-05-31 10:07:35Z zhangwentao $
 */

/**
 * Description of MemberOauth.class.php
 *
 * @author Tracy McGrady
 */
class entity_MemberOauth_base
{
    public $id;
    public $uid;
    public $oauth_type;
    public $openid;
    public $unionid;
    public $access_token;
    public $expires_in;
    public $refresh_token;
    public $nickname;
    public $avatar_imgurl;
    public $oauth_time;
}
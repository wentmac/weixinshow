<?php

/*
 * Tmac PHP MVC framework
 * $Author: zhangwentao $
 * $Id: Member.class.php 325 2016-05-31 10:07:35Z zhangwentao $
 */

/**
 * Description of Member.class.php
 *
 * @author Tracy McGrady
 */
class entity_Member_base
{
    public $uid;
    public $username;
    public $password;
    public $mobile;
    public $realname;
    public $nickname;
    public $email;
    public $register_source;
    public $member_type;
    public $member_class;
    public $member_image_id;
    public $reg_time;
    public $salt;
    public $last_login_time;
    public $last_login_ip;
    public $login_fail_count;
    public $agent_uid;
    public $agent_rank_uid;
    public $locked_type;
    public $sex;
    public $address_info;
    public $member_level;
    public $agent_lock;
    public $mid;
    public $tj_mid;
    public $member_level_time;
    public $member_level_source;
    public $available_integral;
    public $sum_integral;
    public $agent_integral;
}
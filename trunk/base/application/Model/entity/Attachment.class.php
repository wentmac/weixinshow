<?php

/*
 * Tmac PHP MVC framework
 * $Author: zhangwentao $
 * $Id: Attachment.class.php 325 2016-05-31 10:07:35Z zhangwentao $
 */

/**
 * Description of Attachment.class.php
 *
 * @author Tracy McGrady
 */
class entity_Attachment_base
{
    public $attachment_id;
    public $article_id;
    public $time;
    public $filename;
    public $filetype;
    public $filesize;
    public $fileext;
    public $downloads;
    public $filepath;
    public $thumb_filepath;
    public $thumb_width;
    public $thumb_height;
    public $isimage;
}
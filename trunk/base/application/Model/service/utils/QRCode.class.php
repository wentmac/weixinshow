<?php

/**
 * 得到到卖家店铺的宣传二维码
 * 
 */
class service_utils_QRCode_base extends service_Model_base
{

    private $errorMessage;
    private $url;
    private $uid;
    private $qr_width;
    private $qr_height;
    private $qrcode_template;
    private $qrcode_font;
    private $shop_qrcode_status = false;
    private $qrcode_title;
    private $qrcode_description;

    function getErrorMessage()
    {
        return $this->errorMessage;
    }

    function setUrl( $url )
    {
        $this->url = $url;
    }

    function setUid( $uid )
    {
        $this->uid = $uid;
    }

    function setQr_width( $qr_width )
    {
        $this->qr_width = $qr_width;
    }

    function setQr_height( $qr_height )
    {
        $this->qr_height = $qr_height;
    }

    function setQrcode_title( $qrcode_title )
    {
        $this->qrcode_title = mb_convert_encoding( $qrcode_title, "html-entities", "UTF-8" );
    }

    function setQrcode_description( $qrcode_description )
    {
        $this->qrcode_description = mb_convert_encoding( $qrcode_description, "html-entities", "UTF-8" );
    }

    function setQrcode_template( $qrcode_template )
    {
        $this->qrcode_template = $qrcode_template;
    }

    /**
     * 设置店铺二维码
     * @param type $set_shop_qrcode
     */
    function setShop_qrcode_status( $shop_qrcode_status )
    {
        $this->shop_qrcode_status = $shop_qrcode_status;
    }

    public function __construct()
    {
        parent::__construct();
        $this->qrcode_template = STATIC_URL . 'common/qrcode_template.png';
        $this->qrcode_font = TMAC_BASE_PATH . APP_BASE_NAME . DIRECTORY_SEPARATOR . 'var' . DIRECTORY_SEPARATOR . 'Data' . DIRECTORY_SEPARATOR . 'font' . DIRECTORY_SEPARATOR . 'MSYH.TTF';
    }

    /**
     * 取商品的二维码
     * $this->uid
     * $this->qr_width;
     * $this->qr_width;
     */
    public function getItemQRCode( $item_id )
    {
        require_once Tmac::findFile( 'payment/wechatpay/unit/phpqrcode/phpqrcode', APP_WWW_NAME, '.php' );
        $url = $this->url . 'item/' . $item_id . '.html';
        QRcode::png( $url, false, QR_ECLEVEL_H, 13, 2 );
    }

    /**
     * 取店铺的二维码
     * $this->uid
     * $this->qr_width;
     * $this->qr_width;
     */
    public function getShopQRCode()
    {
        require_once Tmac::findFile( 'payment/wechatpay/unit/phpqrcode/phpqrcode', APP_WWW_NAME, '.php' );
        $url = $this->url . 'shop/' . $this->uid;
        QRcode::png( $url, false, QR_ECLEVEL_H, 13, 2 );
    }

    /**
     * 取收款的二维码
     * $this->uid
     * $this->qr_width;
     * $this->qr_width;
     */
    public function getReceivableQRCode( $receivable_id )
    {
        require_once Tmac::findFile( 'payment/wechatpay/unit/phpqrcode/phpqrcode', APP_WWW_NAME, '.php' );
        $url = $this->url . 'receivable/' . $receivable_id . '.html';
        QRcode::png( $url, false, QR_ECLEVEL_H, 13, 2 );
    }

    /**
     * 取商品收款图片带背景
     */
    public function getReceivableQRCodeWithBackImage( $receivable_id )
    {
        header( 'Content-type: image/png' );
        $qrcode_im = imagecreatefrompng( MOBILE_URL . 'tool/get_receivable_qrcode?receivable_id=' . $receivable_id );
        $QR = $this->mergeQrcodeTemplate( $qrcode_im );
        imagepng( $QR );
        imagedestroy( $qrcode_im );
        imagedestroy( $QR );
    }

    /**
     * 取商品图片带背景
     */
    public function getItemQRCodeWithBackImage( $item_id )
    {
        header( 'Content-type: image/png' );
        $qrcode_im = imagecreatefrompng( MOBILE_URL . 'tool/get_item_qrcode?id=' . $item_id );
        $QR = $this->mergeQrcodeTemplate( $qrcode_im );
        imagepng( $QR );
        imagedestroy( $qrcode_im );
        imagedestroy( $QR );
    }

    /**
     * 取店铺图片带背景
     */
    public function getShopQRCodeWithBackImage()
    {
        header( 'Content-type: image/png' );

        $shop_logo_url = $this->getShopLogo();
        $QR = imagecreatefrompng( MOBILE_URL . 'tool/get_shop_qrcode?uid=' . $this->uid );
        $logo = imagecreatefromjpeg( $shop_logo_url );
        $QR_width = imagesx( $QR );
        $QR_height = imagesy( $QR );

        $logo_width = imagesx( $logo );
        $logo_height = imagesy( $logo );

        // Scale logo to fit in the QR Code
        $logo_qr_width = $QR_width / 4;
        $scale = $logo_width / $logo_qr_width;
        $logo_qr_height = $logo_height / $scale;

        $from_width = ($QR_width - $logo_qr_width) / 2; //23是二维码白色边框

        imagecopyresampled( $QR, $logo, $from_width, $from_width, 0, 0, $logo_qr_width, $logo_qr_height, $logo_width, $logo_height );

        $QR_RS = $this->mergeQrcodeTemplate( $QR );
        imagepng( $QR_RS );
        imagedestroy( $QR_RS );
        imagedestroy( $QR );
    }

    /**
     * 取店铺图片带背景
     */
    public function getAgentQRCodeImage()
    {
        header( 'Content-type: image/png' );
        require_once Tmac::findFile( 'payment/wechatpay/unit/phpqrcode/phpqrcode', APP_WWW_NAME, '.php' );
        $filename = VAR_ROOT . 'Data' . DIRECTORY_SEPARATOR . 'qrcode' . DIRECTORY_SEPARATOR . $this->uid . '.png';
        QRcode::png( $this->url, $filename, QR_ECLEVEL_H, 6, 2 );

        $qrcode_im = imagecreatefrompng( $filename );
        //$qrcode_im = imagecreatefromjpeg( 'https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket='.$this->url );        

        $avatar_url = $this->getAvatar();

        $targetFile = VAR_ROOT . 'Data' . DIRECTORY_SEPARATOR . 'qrcode' . DIRECTORY_SEPARATOR . md5( $avatar_url );
        $imgString = Functions::curl_file_get_contents( $avatar_url );
        file_put_contents( $targetFile, $imgString );
        $image_data = getimagesize( $targetFile );
        //var_dump($image_data[ 2 ]);die;
        switch ( $image_data[ 2 ] )
        {
            case IMAGETYPE_JPEG:
            default:
                $avatar = imagecreatefromjpeg( $targetFile );
                break;
            case IMAGETYPE_PNG:
                $avatar = imagecreatefrompng( $targetFile );
                break;
            case IMAGETYPE_GIF:
                $avatar = imagecreatefromgif( $targetFile );
                break;
        }
        $QR_width = imagesx( $qrcode_im );
        $QR_height = imagesy( $qrcode_im );

        $avatar_width = imagesx( $avatar );
        $avatar_height = imagesy( $avatar );

        // Scale logo to fit in the QR Code
        $logo_qr_width = $QR_width / 4;
        $scale = $avatar_width / $logo_qr_width;
        $logo_qr_height = $avatar_height / $scale;

        $from_width = ($QR_width - $logo_qr_width) / 2; //23是二维码白色边框

        imagecopyresampled( $qrcode_im, $avatar, $from_width, $from_width, 0, 0, $logo_qr_width, $logo_qr_height, $avatar_width, $avatar_height );

        $QR = $this->mergeQrcodeTemplate( $qrcode_im );
        imagepng( $QR );
        imagedestroy( $qrcode_im );
        imagedestroy( $QR );
        unlink( $filename );
        unlink( $targetFile );
    }

    /**
     * 取店铺的二维码带logo
     * $this->uid
     * $this->qr_width;
     * $this->qr_width;
     */
    public function getShopQRCodeWithLogo()
    {
        header( 'Content-type: image/png' );

        $shop_logo_url = $this->getShopLogo();
        $QR = imagecreatefrompng( MOBILE_URL . 'tool/get_shop_qrcode?uid=' . $this->uid );
        $logo = imagecreatefromjpeg( $shop_logo_url );
        $QR_width = imagesx( $QR );
        $QR_height = imagesy( $QR );

        $logo_width = imagesx( $logo );
        $logo_height = imagesy( $logo );

        // Scale logo to fit in the QR Code
        $logo_qr_width = $QR_width / 4;
        $scale = $logo_width / $logo_qr_width;
        $logo_qr_height = $logo_height / $scale;

        $from_width = ($QR_width - $logo_qr_width) / 2;

        imagecopyresampled( $QR, $logo, $from_width, $from_width, 0, 0, $logo_qr_width, $logo_qr_height, $logo_width, $logo_height );
        imagepng( $QR );
        imagedestroy( $QR );
    }

    /**
     * 取店铺的logo地址
     */
    private function getShopLogo()
    {
        $dao = dao_factory_base::getMemberSettingDao();
        $dao->setPk( $this->uid );
        $dao->setField( 'shop_image_id' );
        $res = $dao->getInfoByPk();
        if ( !$res || empty( $res->shop_image_id ) ) {
            $logo = STATIC_URL . 'common/qrlogo.jpg';
        } else {
            $logo = $this->getImage( $res->shop_image_id, '110', 'shop' );
        }
        return $logo;
    }

    /**
     * 取头像url
     */
    private function getAvatar()
    {
        $dao = dao_factory_base::getMemberOauthDao();
        $where = "uid={$this->uid} AND oauth_type=" . service_Oauth_base::oauth_type_wechat;
        $dao->setWhere( $where );
        $dao->setField( 'avatar_imgurl' );
        $res = $dao->getInfoByWhere();
        if ( !$res || empty( $res->avatar_imgurl ) ) {
            $logo = STATIC_URL . 'common/avatar.jpg';
        } else {
            $logo = $res->avatar_imgurl;
        }
        return $logo;
    }

    /**
     * 合并二给码背景
     */
    public function mergeQrcodeTemplate( $qrcode_im )
    {
        $QR = imagecreatefrompng( $this->qrcode_template );
        $QR_width = imagesx( $QR );
        $QR_height = imagesy( $QR );

        //创建颜色，用于文字字体的白和阴影的黑
        $grey = imagecolorallocate( $QR, 100, 100, 100 );
        $black = imagecolorallocate( $QR, 0, 0, 0 );

        $title_size = 20;
        $title_y = 80;
        $title_text = $this->qrcode_title;

        //取字体数据
        $text_array = imagettfbbox( $title_size, 0, $this->qrcode_font, $title_text );
        $text_width = $text_array[ 4 ];
        //$text_height = abs( $text_array[ 7 ] );        
        $text_title_w = ($QR_width - $text_width) / 2;
        if ( $text_title_w < 0 ) {
            $text_title_w = 0;
        }
        // Add the text 标题字体
        //imagettftext( $QR, $title_size, 0, $text_title_w, $title_y, $black, $this->qrcode_font, $title_text );
        //描述
        $description_size = 10;
        $description_y = 730;
        $description_text = $this->qrcode_description;
        //取字体数据
        $text_array = imagettfbbox( $description_size, 0, $this->qrcode_font, $description_text );
        $text_width = $text_array[ 4 ];
        //$text_height = abs( $text_array[ 7 ] );        
        $text_title_w = ($QR_width - $text_width) / 2;
        if ( $text_title_w < 0 ) {
            $text_title_w = 0;
        }
        // Add the text 描述字体
        imagettftext( $QR, $description_size, 0, $text_title_w, $description_y, $grey, $this->qrcode_font, $description_text );

        //合并qrcode和qrcode背景图片
        $qrcode_width = 248;
        $qrcode_height = 248;
        $qrcode_x = 125;
        $qrcode_y = 470;


//
//        // Scale logo to fit in the QR Code
//        $logo_qr_width = $QR_width / 4;
//        $scale = $logo_width / $logo_qr_width;
//        $logo_qr_height = $logo_height / $scale;
//
//        $from_width = ($QR_width - $logo_qr_width) / 2;
//
        imagecopyresampled( $QR, $qrcode_im, $qrcode_x, $qrcode_y, 0, 0, $qrcode_width, $qrcode_height, $qrcode_width, $qrcode_height );
        return $QR;
    }

}

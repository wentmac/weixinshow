<?php

/**
 * 前台 404 模块 Controller
 * ============================================================================
 * TBlog TBlog博客系统　BY Tmac PHP MVC framework
 * $Author: zhangwentao $  <zwttmac@qq.com>
 * $Id: error.php 325 2016-05-31 10:07:35Z zhangwentao $
 * http://www.t-mac.org；
 */
class errorAction extends service_Controller_api
{

    /**
     * _init 方法 在执行任何Action前执行
     */
    public function _init()
    {
        $this->assign( 'action', $_GET[ 'TMAC_ACTION' ] );
    }

    /**
     * 城市地标 首页
     */
    public function index()
    {
        $this->assign('title','页面没找到');
        $this->V( '404' );
    }

    public function test()
    {
        $goods_desc = '1501134021103哈哈。<img src="http://www.baidu.com/150113402041.jpg">';
        $goods_desc = preg_replace( '/(1\d{2})\d{4}(\d{4})/', '${1}*****${2}', $goods_desc );
        var_dump($goods_desc);
        
        die;
        include Tmac::findFile( 'array2xml', APP_API_NAME );


        $restaurant = array();
        $restaurant[ '@attributes' ] = array(
            'xmlns:xsi' => 'http://www.w3.org/2001/XMLSchema-instance',
            'xsi:noNamespaceSchemaLocation' => 'http://www.example.com/schmema.xsd',
            'lastUpdated' => date( 'c' )  // dynamic values
        );

        $restaurant[ 'masterChef' ] = array( //empty node with attributes
            '@attributes' => array(
                'name' => 'Mr. Big C.'
            )
        );


        $restaurant[ 'menu' ] = array();
        $restaurant[ 'menu' ][ '@attributes' ] = array(
            'key' => 'english_menu',
            'language' => 'en_US',
            'defaultCurrency' => 'USD'
        );


// we have multiple image tags (without value)
        $restaurant[ 'menu' ][ 'assets' ][ 'image' ][] = array(
            '@attributes' => array(
                'info' => 'Logo',
                'height' => '100',
                'width' => '100',
                'url' => 'http://www.example.com/res/logo.png'
            )
        );
        $restaurant[ 'menu' ][ 'assets' ][ 'image' ][] = array(
            '@attributes' => array(
                'info' => 'HiRes Logo',
                'height' => '300',
                'width' => '300',
                'url' => 'http://www.example.com/res/hires_logo.png'
            )
        );

        $restaurant[ 'menu' ][ 'item' ] = array();
        $restaurant[ 'menu' ][ 'item' ][] = array(
            '@attributes' => array(
                'lastUpdated' => '2011-06-09T08:30:18-05:00',
                'available' => true  // boolean values will be converted to 'true' and not 1
            ),
            'category' => array( 'bread', 'chicken', 'non-veg' ), // we have multiple category tags with text nodes
            'keyword' => array( 'burger', 'chicken' ),
            'assets' => array(
                'title' => 'Zinger Burger',
                'desc' => array( '@cdata' => 'The Burger we all love >_< !' ),
                'image' => array(
                    '@attributes' => array(
                        'height' => '100',
                        'width' => '100',
                        'url' => 'http://www.example.com/res/zinger.png',
                        'info' => 'Zinger Burger'
                    )
                )
            ),
            'price' => array(
                array(
                    '@value' => 10, // will create textnode <price currency="USD">10</price>
                    '@attributes' => array(
                        'currency' => 'USD'
                    )
                ),
                array(
                    '@value' => 450, // will create textnode <price currency="INR">450</price>
                    '@attributes' => array(
                        'currency' => 'INR'
                    )
                )
            ),
            'trivia' => null  // will create empty node <trivia/>
        );
        $restaurant[ 'menu' ][ 'item' ][] = array(
            '@attributes' => array(
                'lastUpdated' => '2011-06-09T08:30:18-05:00',
                'available' => true  // boolean values will be preserved
            ),
            'category' => array( 'salad', 'veg' ),
            'keyword' => array( 'greek', 'salad' ),
            'assets' => array(
                'title' => 'Greek Salad',
                'desc' => array( '@cdata' => 'Chef\'s Favorites' ),
                'image' => array(
                    '@attributes' => array(
                        'height' => '100',
                        'width' => '100',
                        'url' => 'http://www.example.com/res/greek.png',
                        'info' => 'Greek Salad'
                    )
                )
            ),
            'price' => array(
                array(
                    '@value' => 20, // will create textnode <price currency="USD">20</price>
                    '@attributes' => array(
                        'currency' => 'USD'
                    )
                ),
                array(
                    '@value' => 900, // will create textnode <price currency="INR">900</price>
                    '@attributes' => array(
                        'currency' => 'INR'
                    )
                )
            ),
            'trivia' => 'Loved by the Greek!'
        );

        $xml = array2xml::createXML( 'restaurant', $restaurant );
        echo $xml->saveXML();

        die;
        $array2xml->setRootName( 'rss' );
        $array2xml->setRootAttrs( array( 'version' => '2.0' ) );
        $array2xml->setCDataKeys( array( 'description' => TRUE ) );
        $array2xml->setElementsAttrs( $rootAttrs = array( 'first_attr' => 'value_of_first_attr', 'second_atrr' => 'etc' ) );

        $data[ 'channel' ][ 'title' ] = 'News RSS';
        $data[ 'channel' ][ 'link' ] = 'http://yoursite.com/';
        $data[ 'channel' ][ 'description' ] = 'Amazing RSS News';
        $data[ 'channel' ][ 'language' ] = 'en';
        echo $array2xml->convert( $data );

        die;
        $method = 'qunar/city.getList';
        $timestamp = '1357899387';
        $agentKey = 'f0d1f3bcsbs4ab30';
        echo Tmac::model( 'check', APP_API_NAME )->generateSign( $method, $timestamp, $agentKey );
        die;
        $check_model = Tmac::model( 'check', APP_API_NAME );
        $check_model->test();
    }

    public function getSign()
    {
        $check_model = Tmac::model( 'Check' );
        $check_model instanceof service_Check_api;

        echo $timestamp = 1418530590 . '<br>';
        echo $check_model->generateSign( 'member.set_chat_last_read_time', $timestamp, 2 );
    }

}

<?php


if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class plugin_htt_qqlogin {

    function common() {

        global $_G;
        loadcache('plugin');
        $var = $_G['cache']['plugin'];
        $site_url = $var['htt_qqlogin']['site_url'];
        if(empty($site_url)){
            $site_url = $_G['siteurl'];
        }
        $site_url = rtrim($site_url,'/');


        define('IMGDIR','static/image/common');

        //���û��¼��ͬʱ���ֻ���
        if($_G['uid'] <=0 && !defined('IN_MOBILE') ){

            $_G['setting']['pluginhooks']['global_login_text'] =  '<a href="'.$site_url.'/plugin.php?id=htt_qqlogin:qqoauth" target="_top" rel="nofollow"><img src="'.IMGDIR.'/qq_login.gif" class="vm" /></a>';
        }

    }




    function global_login_extra(){
        global $_G;
        loadcache('plugin');
        $var = $_G['cache']['plugin'];
        $site_url = $var['htt_qqlogin']['site_url'];
        if(empty($site_url)){
            $site_url = $_G['siteurl'];
        }
        $site_url = rtrim($site_url,'/');

        //��¼��
        if( $_G['uid'] > 0){
            return '';
        }

        include_once template('htt_qqlogin:qqlogin_connect');
        return $qqlogin_html;
    }

    // ͷ������ ��QQ����ʾ��
    function global_usernav_extra1() {
        global $_G;
        include_once template('htt_qqlogin:module');
        loadcache('plugin');
        $var = $_G['cache']['plugin'];
        $site_url = $var['htt_qqlogin']['site_url'];
        if(empty($site_url)){
            $site_url = $_G['siteurl'];
        }
        $site_url = rtrim($site_url,'/');

        //���û�������û�û��¼��
        if($_G['uid'] < 0){
            return '';
        }
        $query = DB::query("SELECT * FROM  ".DB::table("httqqlogin")." WHERE  `uid`= ".$_G['uid']);
        if($item = DB::fetch($query)) {
            # code...
            //����QQ��¼��Ҫ�����Ƿ���QQ��¼���û��������Ѿ��������ʺ�
            //����������ʺţ�����ʾ�������жϵ����ݡ��ʺ�ʱ����QQ��¼�е�ʱ��Աȡ����������1���ӡ���˵����Ҫ�����ʺš�
            $member_query = DB::query("SELECT * FROM  ".DB::table("ucenter_members")." WHERE  `uid`= ".$_G['uid']);
            $member_info = DB::fetch($member_query);

            if (abs($item['dateline']-$member_info['regdate']) <= 10) {
                # code...
                return '<a href="'.$site_url.'/home.php?mod=spacecp&ac=plugin&op=profile&id=htt_qqlogin:bind_qq" target="_blank"><img src="static/image/common/qq_bind_small.gif" class="qq_bind" align="absmiddle" alt=""></a>';
            }
            return '';
        }
        return tpl_global_usernav_extra1();
    }
}

class plugin_htt_qqlogin_member extends plugin_htt_qqlogin{

    function logging_method() {
        global $_G;
        loadcache('plugin');
        $var = $_G['cache']['plugin'];
        $site_url = $var['htt_qqlogin']['site_url'];
        if(empty($site_url)){
            $site_url = $_G['siteurl'];
        }
        $site_url = rtrim($site_url,'/');
        if( $_G['uid'] > 0){
            return '';
        }
        include_once template('htt_qqlogin:qqlogin_simple_connect');
        return $qqlogin_simple_html;
    }

    function register_logging_method() {
        global $_G;
        loadcache('plugin');
        $var = $_G['cache']['plugin'];
        $site_url = $var['htt_qqlogin']['site_url'];
        if(empty($site_url)){
            $site_url = $_G['siteurl'];
        }
        $site_url = rtrim($site_url,'/');
        if($_G['uid'] > 0){
            return '';
        }
        include_once template('htt_qqlogin:qqlogin_simple_connect');
        return $qqlogin_simple_html;
    }
}


?>
<?php

/**
 * 活动管理。admin负责。
 *
 *
 */

if (!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
    exit('Access Denied');
}

global $_G;

global $lang;

loadcache('plugin');


$plugin_lang = $Plang = $scriptlang['htt_qqlogin'];

$ac = !empty($_GET['ac']) ? $_GET['ac'] : '';

switch ($ac) {
    case 'add'://添加
        if (!submitcheck('submit')) {
            echo '<script type="text/javascript" src="static/js/calendar.js"></script>';
            showformheader('plugins&operation=config&do=' . $pluginid . '&identifier=htt_qqlogin&pmod=qq_member&ac=add', 'enctype');
            showtableheader();
            showsetting($plugin_lang['uid'], 'uid', '', 'text');
            showsetting($plugin_lang['nickname'], 'nickname', '', 'text');
            showsetting($plugin_lang['photo'], 'photo', '', 'text');
            showsetting($plugin_lang['username'], 'username', '', 'text');
            showsetting($plugin_lang['dateline'], 'dateline', '', 'text');
            showsetting($plugin_lang['openid'], 'openid', '', 'text');
            showsetting($plugin_lang['accesstoken'], 'accesstoken', '', 'text');
            showsetting($plugin_lang['password'], 'password', '', 'text');
            showsubmit('submit');
            showtablefooter();
            showformfooter();

        } else {

            if (!$_GET['uid'] && !$_GET['nickname'] && !$_GET['photo'] && !$_GET['username'] && !$_GET['dateline']) {
                cpmsg($plugin_lang['please_write_whole'], '', 'error');
            }


            $insert_array = array(
                'uid' => $_GET['uid'],
                'nickname' => $_GET['nickname'],
                'photo' => $_GET['photo'],
                'username' => $_GET['username'],
                'dateline' => $_GET['dateline'],
                'openid' => $_GET['openid'],
                'access_token' => $_GET['accesstoken'],
                'password' => $_GET['password'],
                'dateline' => TIMESTAMP,
            );
            C::t('#htt_qqlogin#qqlogin')->insert($insert_array);
            cpmsg($plugin_lang['action_success'], 'action=plugins&operation=config&do=' . $pluginid . '&identifier=htt_qqlogin&pmod=qq_member', 'succeed');

        }

        break;
    case 'del': //删除

        if (submitcheck('submit')) {
            foreach ($_GET['delete'] as $delete) {
                C::t('#htt_qqlogin#qqlogin')->delete($delete);
            }
            cpmsg($plugin_lang['action_success'], 'action=plugins&operation=config&do=' . $pluginid . '&identifier=htt_qqlogin&pmod=qq_member', 'succeed');
        }

        break;
    case 'explort':

        $extra = $search = '';

        $ppp = 50;
        $page = max(1, intval($_GET['page']));
        $count = C::t('#htt_qqlogin#qqlogin')->count_by_search($search);
        $qqlogins = C::t('#htt_qqlogin#qqlogin')->fetch_all_by_search($search, ($page - 1) * $ppp, $ppp);
        foreach ($qqlogins as $qqlogin) {
            $qqlogins_new[] = $qqlogin['uid'] . ',' . $qqlogin['openid'] . ',' . $qqlogin['access_token'] . ',' . $qqlogin['nickname'] . ',' . $qqlogin['name'] . ',' . $qqlogin['photo'] . ',' . date('Y-m-d H:i:s', $qqlogin['dateline']);
        }
        $qqlogins = $qqlogins_new;

        ob_end_clean();
        header('Content-Encoding: none');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=showactivity_' . $_GET['tid'] . '.csv');
        header('Pragma: no-cache');
        header('Expires: 0');
        krsort($qqlogins);
        $detail = $plugin_lang['show_export_title'] . "\r\n" . implode("\r\n", $qqlogins);
        if ($_G['charset'] != 'gbk') {
            $detail = diconv($detail, $_G['charset'], 'GBK');
        }
        define('FOOTERDISABLED', true);
        echo $detail;
        exit();

        break;
    case 'edit': //编辑。启用和禁用操作。
        if (!submitcheck('submit')) {

            $qqlogin = C::t('#htt_qqlogin#qqlogin')->fetch_by_uid($_GET['uid']);


            showformheader('plugins&operation=config&do=' . $pluginid . '&identifier=htt_qqlogin&pmod=qq_member&ac=edit&uid=' . $uid, 'enctype');
            showtableheader();
            showsetting($plugin_lang['uid'], 'uid', $qqlogin['uid'], 'text', '', 0, $plugin_lang['bind_user_by_admin']);
            showsetting($plugin_lang['nickname'], 'nickname', $qqlogin['nickname'], 'text', 'readonly');
            showsetting($plugin_lang['photo'], 'photo', $qqlogin['photo'], 'text', 'readonly');
            showsetting($plugin_lang['username'], 'username', $qqlogin['username'], 'text', '', 0, $plugin_lang['bind_user_by_admin']);
            showsetting($plugin_lang['dateline'], 'dateline', date('Y-m-d', $qqlogin['dateline']), 'text', 'readonly');
            showsetting($plugin_lang['openid'], 'openid', $qqlogin['openid'], 'text', 'readonly');
            showsetting($plugin_lang['accesstoken'], 'accesstoken', $qqlogin['access_token'], 'text', 'readonly');
            showsetting($plugin_lang['password'], 'password', $qqlogin['password'], 'text', '', 0, $plugin_lang['bind_user_by_admin']);
            showsubmit('submit');
            showtablefooter();
            showformfooter();


        } else {

            if (!$_GET['uid'] && !$_GET['nickname'] && !$_GET['photo'] && !$_GET['username'] && !$_GET['dateline']) {
                cpmsg($plugin_lang['please_write_whole'], '', 'error');
            }

            $uid = intval($_GET['uid']);

            $insert_array = array(
                'uid' => $_GET['uid'],
//                'nickname'=>$_GET['nickname'],
//                'photo'=>$_GET['photo'],
                'username' => $_GET['username'],
//                'dateline'=>$_GET['dateline'],
//                'openid'=>$_GET['openid'],
//                'access_token'=>$_GET['accesstoken'],
                'password' => $_GET['password'],
//                'dateline'=>TIMESTAMP,
            );

            C::t('#htt_qqlogin#qqlogin')->update($uid, $insert_array);
            cpmsg($plugin_lang['action_success'], 'action=plugins&operation=config&do=' . $pluginid . '&identifier=htt_qqlogin&pmod=qq_member', 'succeed');

        }


        break;
    default: //显示列表.带分页的。

        $extra = $search = '';

        $ppp = 100;
        $page = max(1, intval($_GET['page']));
        $count = C::t('#htt_qqlogin#qqlogin')->count_by_search($search);
        $qqlogins = C::t('#htt_qqlogin#qqlogin')->fetch_all_by_search($search, ($page - 1) * $ppp, $ppp);


        showtips($plugin_lang['member_help']);


        showsubmit('', '', '<a href="' . ADMINSCRIPT . '?action=plugins&operation=config&do=' . $pluginid . '&identifier=htt_qqlogin&pmod=qq_member&ac=explort" >' . $plugin_lang['show_export_btn'] . '</a>');

        showformheader('plugins&operation=config&do=' . $pluginid . '&identifier=htt_qqlogin&pmod=qq_member&ac=del', 'enctype');
        showtableheader();
        echo '<tr class="header"><th></th><th>' .
            $plugin_lang['uid'] . '</th><th>' .
            $plugin_lang['nickname'] . '</th>
            <th>' .
            $plugin_lang['photo'] . '</th>
            <th>' .
            $plugin_lang['username'] . '</th>
            <th>' .
            $plugin_lang['dateline'] . '</th>
            <th></th></tr>';
        foreach ($qqlogins as $pid => $qqlogin) {
            echo '<tr class="hover">
<th class="td25"><input class="checkbox" type="checkbox" name="delete[' . $qqlogin['uid'] . ']" value="' . $qqlogin['uid'] . '"></th>
            <th><a href="forum.php?mod=viewthread&tid=' . $qqlogin['uid'] . '" target="_blank">' . $qqlogin['uid'] . '</a></th>
            <th>' .
                $qqlogin['nickname'] . '</th>
                <th>' .
                $qqlogin['photo'] . '</th>
                <th>' .
                $qqlogin['username'] . '</th>
                <th>' .
                date('Y-m-d', $qqlogin['dateline']) . '</th>
                <th>' .
                '<a href="' . ADMINSCRIPT . '?action=plugins&operation=config&do=' . $pluginid . '&identifier=htt_qqlogin&pmod=qq_member&ac=edit&uid=' . $qqlogin['uid'] . '">' . $lang[edit] . '</a>
                <a href="' . ADMINSCRIPT . '?action=members&operation=group&uid=' . $qqlogin['uid'] . '">' . $lang[usergroup] . '</a>
                <a href="' . ADMINSCRIPT . '?action=members&operation=access&uid=' . $qqlogin['uid'] . '">' . $lang[members_access] . '</a>
                <a href="' . ADMINSCRIPT . '?action=members&operation=credit&uid=' . $qqlogin['uid'] . '">' . $lang[members_access] . '</a>
                <a href="' . ADMINSCRIPT . '?action=members&operation=medal&uid=' . $qqlogin['uid'] . '">' . $lang[medals] . '</a>
                <a href="' . ADMINSCRIPT . '?action=members&operation=repeat&uid=' . $qqlogin['uid'] . '">' . $lang[members_repeat] . '</a>
                <a href="' . ADMINSCRIPT . '?action=members&operation=edit&uid=' . $qqlogin['uid'] . '">' . $lang[detail] . '</a>
                <a href="' . ADMINSCRIPT . '?action=members&operation=ban&uid=' . $qqlogin['uid'] . '">' . $lang[members_ban] . '</a>

                </th>
                </tr>';
        }
//        $add = '<input type="button" class="btn" onclick="location.href=\''.ADMINSCRIPT.'?action=plugins&operation=config&do='.$pluginid.'&identifier=htt_qqlogin&pmod=qq_member&ac=add\'" value="添加" />';
        $add = '';
        if ($qqlogins) {
            showsubmit('submit', $lang['delete'], $add, '', $multipage);
        } else {
            showsubmit('', '', 'td', $add);
        }
        showtablefooter();
        showformfooter();


        echo multi($count, $ppp, $page, ADMINSCRIPT . "?action=plugins&operation=config&do=$pluginid&identifier=htt_qqlogin&pmod=qq_member$extra");

        break;
}


?>
<?php
/**
 * Created by PhpStorm.
 * User: KyleFu
 * Date: 16/4/16
 * Time: 14:22
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
    exit('Access Denied');
}
require_once libfile('function/common', 'plugin/kylefu_spider_movie');
require_once libfile('class/spider', 'plugin/kylefu_spider_movie');

$operation	= trim($_GET['operation']);
$pmod		= trim($_GET['pmod']);
$spider_class= trim($_GET['spider']);
$pluginurl	= ADMINSCRIPT.'?action=plugins&identifier=kylefu_spider_movie&do='.$pluginid.'&pmod='.$pmod;
$submiturl	= 'plugins&identifier=kylefu_spider_movie&do='.$pluginid.'&pmod='.$pmod;
$spider_object = new spider();

$operation_module = array("config", "manage", "spider", "run", "ajax");

if(in_array($operation, $operation_module)) {
    if(empty($operation) || $operation == "config") {
        showtableheader(plugin_lang("spider_manage"));
        showsubtitle(array("name", "version", "copyright", plugin_lang("spider_number"), "dateline", "operation"));
        foreach ($spider_object->module() as $value) {
            if(!$fetch_spider = C::t("#kylefu_spider_movie#spider")->fetch_spider($value["class"])){
                $data = array(
                    "spider"   => $value["class"],
                    "number"    => 0,
                    "dateline"  => $_G["timestamp"]
                );
                C::t("#kylefu_spider_movie#spider")->insert($data);
            }
            showtablerow('', array(), array(
                $value["name"],
                $value['version'],
                $value["copyright"],
                $fetch_spider["number"]?$fetch_spider["number"]:0,
                date("Y-m-d", $value["filemtime"]),
                '<a href="' . $pluginurl . '&operation=manage&spider=' . trim($value["class"]) . '">' . plugin_lang("manage") . '</a>'
            ));
        }
        showtablefooter();
    }elseif ($operation == "manage"){
        if ($spider = $spider_object->load($spider_class)) {
            require_once libfile('function/forumlist');
            $script_content = '
                 function movie_auto(spider, url, obj){
                    var fid = jQ(obj).parent().parent().find("select").val();
                    jQ.post("'.$pluginurl.'&operation=ajax", {mode:"auto", spider:spider, fid:fid, url:url, hash:"'.FORMHASH.'"}, function(data){
                        if(parseInt(data)){
                            jQ(obj).html("'.plugin_lang("cancel/auto").'");
                        }else{
                            jQ(obj).html("'.plugin_lang("auto").'");
                        }
                    });
                 }
              ';
            echo jqueryload($script_content);
            showtips("<li>".plugin_lang("version").":{$spider->version}</li><li>".plugin_lang("copyright").":{$spider->copyright}</li>", "tips", true, plugin_lang("spider_manage") ." - ". $spider_object->iconv($spider->name));
            showtableheader();
            showsubtitle(array("name", "url", "operation", plugin_lang("run_save_fid")));
            foreach ($spider->link as $value) {
                $fetch_url = C::t("#kylefu_spider_movie#auto")->fetch_url($value["url"]);
                showtablerow('', array(), array(
                    '<a href="'.$value['url'].'" target="_blank">'.$spider_object->iconv($value["name"])."</a>",
                    $value['url'],
                    '<a href="' . $pluginurl . '&operation=spider&spider=' . trim($spider_class) . '&url='.urlencode($value["url"]).'&name='.urlencode($spider_object->iconv($value["name"])).'">' . plugin_lang("spider") ."</a> / ".
                    '<a href="javascript:;" onclick="movie_auto(\''.$spider_class.'\',\''.urlencode($value["url"]).'\', this)">'.($fetch_url["status"] ? plugin_lang("cancel/auto") : plugin_lang("auto")).'</a>'
                    ,'<select name="fid">'.forumselect(FALSE, 0, $fetch_url["fid"])."</select>"
                ));
            }
            showtablefooter();
        }else{
            cpmsg("Spider Error!");
        }
    }elseif ($operation == "spider"){
        if ($spider = $spider_object->load($spider_class)) {
            $url = trim($_GET["url"]);
            $name = trim($_GET['name']);
            $spider_page = intval($_POST['spider_page']);
            $fid = intval($_POST['fid']);
            $run = $spider_object->run($url, $spider);
            if(!submitcheck('submit')) {
                require_once libfile('function/forumlist');
                if(!isset($_G['cache']['forums'])) {
                    loadcache('forums');
                }
                showformheader($submiturl . '&operation=spider&spider=' . $spider_class . '&url=' . urlencode($url) . '&name=' . urlencode($name), 'enctype');
                showtableheader($spider_object->iconv($spider->name) . " - " . $name . "({$run[page][all]})");
                showtablerow("", array(''), array(
                    sprintf(plugin_lang("spider_page"), $run["page"]["all"], '<input type="text" name="spider_page" value="' . $run["page"]["all"] . '" />'),
                ), false);
                showtablerow("", array(''), array(
                    plugin_lang("run_save_fid").' : <select name="fid">'.forumselect()."</select>"
                ), false);
                showsubmit('submit', 'submit');
                showtablefooter();
                showformfooter();
            }else{
                if(empty($spider_page) || $spider_page<=0){
                    cpmsg(plugin_lang("spider_page_input"));
                }
                if(empty($fid) || $fid <=0){
                    cpmsg(plugin_lang("run_save_fid"));
                }
                $readlist = $spider_object->readlist($spider_page);
                $script_content = '
                 function movie_tag(url, obj){
                    jQ.post("'.$pluginurl.'&operation=ajax", {mode:"tag",url:url, hash:"'.FORMHASH.'"}, function(data){
                        if(parseInt(data)){
                            jQ(obj).html("'.plugin_lang("cancel/tag").'").parent().parent().find("input").prop("disabled", true);
                        }else{
                            jQ(obj).html("'.plugin_lang("tag").'").parent().parent().find("input").prop("disabled", false);
                        }
                    });
                 }
              ';
                echo jqueryload($script_content);
                showformheader($submiturl . '&operation=run&spider='.$spider_class, 'enctype');
                showtableheader($spider_object->iconv($spider->name) . " - " . $name . "(".count($readlist).")");
                showsubtitle(array("select", plugin_lang("title"), "operation"));
                foreach ($readlist as $value) {
                    $fetch_url = C::t("#kylefu_spider_movie#tag")->fetch_url($value["href"]);
                    showtablerow('', array('width="40px"'), array(
                        '<input type="checkbox" class="checkbox" name="href[]" '.($fetch_url ? 'disabled="true"' : '').' value="'.base64_encode(json_encode($value)).'" />',
                        '<a href="'.$value['href'].'" target="_blank">'.$spider_object->iconv($value["title"])."</a>",
                        '<a href="javascript:;" onclick="movie_tag(\''.urlencode($value["href"]).'\', this)">'.($fetch_url ? plugin_lang('cancel/tag') : plugin_lang('tag')).'</a>'
                    ));
                }
                showhiddenfields(array("fid"=>$fid));
                showsubmit('listsubmit', 'submit', '<input type="checkbox" name="chkall" id="chkall" class="checkbox" onclick="checkAll(\'prefix\', this.form, \'href\')" /><label for="chkall">'.cplang('select_all').'</label>');
                showtablefooter();
                showformfooter();
            }
        }else{
            cpmsg("Spider Error!");
        }
    }elseif ($operation == "run"){
        if ($spider = $spider_object->load($spider_class)) {
            $fid = intval($_POST['fid']);
            $key = intval($_GET["key"]);
            $key = $key ? $key : 0;
            $href = ($_GET["href"]?$_GET["href"]:$_POST["href"]);
            if($key<count($href)){
                $run = $spider_object->run(NULL, $spider);
                if(!$spider_object->uid()){
                    cpmsg("UID Error!");
                }
                $now = json_decode(base64_decode($href[$key]), true);
                if($spider_object->save($fid, $now["href"])){
                    if($fetch_spider = C::t("#kylefu_spider_movie#spider")->fetch_spider($spider_class)){
                        C::t("#kylefu_spider_movie#spider")->update_by_id(array(
                            "number" => $fetch_spider["number"]+1
                        ), $fetch_spider["id"]);
                    }else{
                        $data = array(
                            "spider"   => $spider_class,
                            "number"    => 1,
                            "dateline"  => $_G["timestamp"]
                        );
                        C::t("#kylefu_spider_movie#spider")->insert($data);
                    }
                }
                if(!C::t("#kylefu_spider_movie#tag")->fetch_url($now["href"])){
                    $data = array(
                        "url"   => $now["href"],
                        "dateline"  => $_G["timestamp"]
                    );
                    C::t("#kylefu_spider_movie#tag")->insert($data);
                }
                $script_content = '(function ($, i, k) { setTimeout(function(){$("form").submit();},500); })(jQ, window);';
                echo jqueryload($script_content);
                showformheader($submiturl . '&operation=run&spider='.$spider_class.'&key='.($key+1), 'enctype', "spider_form_save");
                showtableheader(plugin_lang("run"). " FID:".$fid);
                foreach ($href as $value){
                showtablerow('style="display:none"', array(), array(
                    '<input type="text" name="href[]" value="'.$value.'" />',
                ));
                }
                showtablerow('', array(), array(
                    sprintf(plugin_lang("spider_run"), count($href), $key, "<font color='red'>".(count($href)-$key)."</font>" , '<a href="'.$now["href"].'" target="_blank">'.$spider_object->iconv($now["title"]).'</a>')
                ));
                showsubmit('run_submit', plugin_lang("run_submit"));
                showhiddenfields(array("fid"=>$fid));
                showtablefooter();
                showformfooter();
            }else{
                cpmsg(plugin_lang("save_success"), 'action='.$submiturl.'&operation=manage&spider='.$spider_class,'succeed');
            }
        }else{
            cpmsg("Spider Error!");
        }
    }elseif ($operation =="ajax"){
        ob_clean();
        $mode = trim($_POST["mode"]);
        if(addslashes($_POST['hash']) != FORMHASH){
            exit;
        }
        if(in_array($mode, array("tag", "auto"))){
            if($mode == "tag"){
                $url = urldecode(trim($_POST["url"]));
                $fetch_url = C::t("#kylefu_spider_movie#tag")->fetch_url($url);
                if($fetch_url){
                    C::t("#kylefu_spider_movie#tag")->delete_by_id($fetch_url["id"]);
                    echo(0);
                }else{
                    $data = array(
                        "url"   => $url,
                        "dateline"  => $_G["timestamp"]
                    );
                    C::t("#kylefu_spider_movie#tag")->insert($data);
                    echo(1);
                }
            }elseif ($mode == "auto"){
                $url = urldecode(trim($_POST["url"]));
                $spider = trim($_POST["spider"]);
                $fid = intval($_POST["fid"]);
                $fetch_url = C::t("#kylefu_spider_movie#auto")->fetch_url($url);
                if($fetch_url){
                    $status = ($fetch_url["status"]?0:1);
                    C::t("#kylefu_spider_movie#auto")->update_by_id(array("status"=>$status, "fid"=>$fid), $fetch_url["id"]);
                    echo($status);
                }else{
                    $data = array(
                        "url"   => $url,
                        "spider" => $spider,
                        "dateline"  => $_G["timestamp"],
                        "status" => 1,
                        "fid" => $fid
                    );
                    C::t("#kylefu_spider_movie#auto")->insert($data);
                    echo(1);
                }
            }
        }
        exit;
    }
}
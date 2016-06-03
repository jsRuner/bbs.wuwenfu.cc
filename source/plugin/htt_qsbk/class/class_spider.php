<?php
/**
 * User: KyleFu
 * Date: 16/4/16
 * Time: 14:38
 */
if(!defined('IN_DISCUZ')) {
    exit('Access Denied');
}
define('SPIDER_ROOT', DISCUZ_ROOT."./source/plugin/htt_qsbk/spider");
require_once libfile('class/simple_html_dom', 'plugin/htt_qsbk');
class spider{
    public $var;
    public $object;
    public $url;
    public $results;
    public $nextpageurl;
    public $currnetpage;

    function load($class){
        $entry = $class.".php";
        @include_once SPIDER_ROOT . '/' . $entry;
        $spiderclass = substr($entry, 0, -4);
        if (class_exists($spiderclass)) {
            return new $spiderclass();
        }
    }

    function run($url, $object){
        global $kylefu_spider_movie_var;
        $this->var = $kylefu_spider_movie_var;
        $this->object = $object;
        if($url) {
            $this->url = $url;
            $html = $this->request($this->url);
            if(empty($html)){
                $html = $this->request($this->url, array("post"=>true));
            }
            $this->results = str_get_html($html);
            $readpage = $this->readpage();
            $data = array(
                "page"	=> array(
                    "all"	=> $readpage,
                    "url"	=> sprintf($this->nextpageurl, $this->currnetpage+1),
                    "now"	=> $this->currnetpage,
                ),
            );
            return $data;
        }
    }

    function extend($url){
        $extend_content = '';
        if($this->object->dom["extend"]) {
            foreach ($this->results->find($this->object->dom["extend"]) as $d) {
                $extend_content .= trim($this->iconv($d->innertext));
            }
        }
        if($this->object->dom["magnet"]){
            foreach ($this->results->find($this->object->dom["magnet"]) as $d) {
                preg_match_all("/<a.+href=('|\"|)?(.*)(\\1)([\s].*)?>/ismUe", $d->innertext, $matches);
                $href = "plugin.php?id=kylefu_spider_movie&magnet=".base64_encode($matches[2][0]);
                $extend_content .= trim($this->iconv(str_replace($matches[2][0], $href, $d->innertext)));
            }
        }
        return $extend_content;
    }

    function save($fid, $url){
        global $_G;
        if(!$this->object->dom["main"]) return false;
        loaducenter();
        $html = $this->request($url);
        if(empty($html)){
            $html = $this->request($url, array("post"=>true));
        }
        $content = $message ="";
        $this->results = str_get_html($html);
        foreach($this->results->find($this->object->dom["main"]) as $d){
            if($this->object->dom["format"]) {
                $html_del = str_get_html($d->innertext);
                foreach ($html_del->find($this->object->dom["format"]) as $e) {
                    $d->innertext = str_replace($e->outertext, '', $d->innertext);
                }
            }
            $content = trim($this->iconv($d->innertext));
        }
        $content .= $this->extend($url);
        $message = $this->replace(trim($this->html2bbcode($content)));
        $title = $this->replace(trim($this->iconv($this->title())));
        if($fid && $message && $title && $member = getuserbyuid($this->uid())){
            $title = addslashes($title);
            $message = addslashes($message);
            $views = rand(5, 50);
            DB::query("INSERT INTO ".DB::table('forum_thread')." (fid, views, typeid, sortid, author, authorid, subject, dateline, lastpost, lastposter, status, closed) VALUES ($fid, $views, 0, 0, '$member[username]', $member[uid], '$title', '$_G[timestamp]', '$_G[timestamp]', $member[uid], '0', '0')");
            $tid = DB::insert_id();
            $pid = DB::insert('forum_post_tableid', array('pid'=>NULL), true);
            DB::query("INSERT INTO ".DB::table('forum_post')." (pid, fid, tid, first, author, authorid, subject, dateline, message) VALUES ('$pid', $fid, '$tid', '1', '$member[username]', $member[uid], '$title', '$_G[timestamp]', '$message')");
            $title = str_replace("\t", ' ', $title);
            $lastpost = "$tid\t$title\t$_G[timestamp]\t$member[username]";
            DB::query("UPDATE ".DB::table('forum_forum')." SET lastpost='$lastpost', threads=threads+1, posts=posts+1, todayposts=todayposts+1 WHERE fid='$fid'", 'UNBUFFERED');
            return true;
        }
        return false;
    }

    function uid(){
        $uids = explode("\n", $this->var["uid"]);
        if($uids){
            $random = array_rand($uids);
            return trim($uids[$random]);
        }
        return '';
    }

    private function replace($string){
        $replace = explode("\n", $this->var["replace"]);
        foreach($replace as $val) {
            list($before, $after) = explode("=", trim($val));
            $before_array[] = trim($before);
            $after_array[] = trim($after);
        }
        return str_replace($before_array, $after_array, $string);
    }

    private function title(){
        $titleAll = $this->results->find('title',0)->plaintext;
        if(strpos("$titleAll","-") !== false)list($title, ) = explode("-",$titleAll);
        if(strpos("$titleAll","—") !== false)list($title, ) = explode("—",$titleAll);
        if(strpos("$titleAll","_") !== false)list($title, ) = explode("_",$titleAll);
        if(strpos("$titleAll","|") !== false)list($title, ) = explode("|",$titleAll);
        !$title && $title = $titleAll;
        return trim($title);
    }

    function readpage(){
        foreach($this->results->find($this->object->dom["page"]) as $e){
            if(strpos($e->plaintext, ".") !== false || is_numeric($e->plaintext) || strpos($e->plaintext, "[") !== false){
                $PageArray[] = $this->formatnumber($e->plaintext);
                $PageUrlArray[] = $e->href;
            }
        }
        if(count($PageUrlArray)<=0){
            $this->nextpageurl = $this->currnetpage = 0;
            return 0;
        }else{
            $this->nextpageurl = $this->url(array_unique($PageUrlArray));
            $page = $this->formatnumber(end($PageArray)) ? $this->formatnumber(end($PageArray)) : 0;
            return $this->currnetpage>=$page ? $this->currnetpage : $page;
        }
    }

    function url($array){
        $matching = array();
        foreach($array as $k=>$v){
            similar_text($this->formaturl($this->url, $v), $this->url, $percent);
            $matching[] = array(
                "url"		=> $this->formaturl($this->url, $v),
                "len"		=> strlen($this->formaturl($this->url, $v)),
                "percent"	=> (int)$percent
            );
        }

        $matching = $this->array_sort($matching,"percent","desc");

        $cururl = current($matching);
        $endurl = count($matching)>2 ? next($matching) : end($matching);

        list($curformat, ) = $this->formatnumber(count($matching)>1 ? $cururl["url"]:$this->url,false);
        list($endformat, ) = $this->formatnumber($endurl["url"],false);

        if(count($curformat) == count($endformat) || count($curformat) > count($endforamt)){
            foreach($curformat as $k=>$v){
                if($v != $endformat[$k] || empty($endformat[$k])){
                    $replace[] = "%d";
                }else{
                    $replace[]	= $v;
                }
            }
            rsort($curformat);
        }else{
            foreach($endformat as $k=>$v){
                if($v != $curformat[$k]){
                    $replace[] = "%d";
                }else{
                    $replace[]	= $v;
                }
            }
            rsort($endformat);
        }
        $format_url = str_replace($curformat, "%s", $cururl['url']);
        $this->currnetpage = $this->ParsePage($cururl, $this->url);
        return trim(vsprintf($format_url, $replace));

    }

    private function ParsePage($url, $current){
        list($curformat) = $this->formatnumber($url["url"],false);
        list($endformat) = $this->formatnumber($current,false);
        $currentpage = 1;
        foreach($endformat as $k=>$v){
            if($v != $curformat[$k]){
                $currentpage = $v;
            }
        }
        return $currentpage;
    }

    function autolist($url){
        $html = $this->request($url);
        if(empty($html)){
            $html = $this->request($url, array("post"=>true));
        }

        $resultshtml = str_get_html($html);
        foreach($resultshtml->find($this->object->dom["list"]) as $e){
            $list[] = array(
                "href"   => $this->formaturl($url, $e->href),
                "title"  => trim($this->iconv($e->innertext, $this->object->charset)),
                "length" => strlen($this->formaturl($url, $e->href))
            );
        }
        $list = $this->array_sort($list, "length", 'desc');
        $max_length = current($list);
        foreach($list as $value){
            if($value["length"] >= $max_length["length"]+5 || $value["length"] >= $max_length["length"]-5){
                $templist[] = $value;
            }
        }
        return $templist;
    }

    function readlist($page = 1){
        $templist = $list = array();
        for($i=1; $i<=$page; $i++){
            $urlpage = sprintf($this->nextpageurl, $i);
            $html = $this->request($urlpage);
            if(empty($html)){
                $html = $this->request($this->url, array("post"=>true));
            }
            $resultshtml = str_get_html($html);
            foreach($resultshtml->find($this->object->dom["list"]) as $e){
                $list[] = array(
                    "href"   => $this->formaturl($this->url, $e->href),
                    "title"  => trim($this->iconv($e->innertext, $this->object->charset)),
                    "length" => strlen($this->formaturl($this->url, $e->href))
                );
            }
        }

        $list = $this->array_sort($list, "length", 'desc');
        $max_length = current($list);
        $i=0;
        foreach($list as $value){
            if($value["length"] >= $max_length["length"]+5 || $value["length"] >= $max_length["length"]-5){
                $templist[] = $value;
                $i++;
                if($i>=5){
                    break;
                }
            }
        }
        return $templist;
    }

    function module() {
        $spiders = array();
        if(file_exists(SPIDER_ROOT)) {
            $spiderdir = dir(SPIDER_ROOT);
            while ($entry = $spiderdir->read()) {
                if (!in_array($entry, array('.', '..')) && preg_match("/^spider\_[\w\.]+$/", $entry) && substr($entry, -4) == '.php' && strlen($entry) < 30 && is_file(SPIDER_ROOT . '/' . $entry)) {
                    @include_once SPIDER_ROOT . '/' . $entry;
                    $spiderclass = substr($entry, 0, -4);
                    if (class_exists($spiderclass)) {
                        $spider = new $spiderclass();
                        $script = $spiderclass;
                        $spiders[$entry] = array(
                            'class' => $script,
                            'name' => $this->iconv($spider->name),
                            'version' => $spider->version,
                            'copyright' => $this->iconv($spider->copyright),
                            'filemtime' => @filemtime(SPIDER_ROOT . '/' . $entry)
                        );
                    }
                }
            }
            uasort($spiders, 'filemtimesort');
            return $spiders;
        }
    }

    function iconv($string, $charset = CHARSET){
        return diconv($string, 'utf-8', $charset);
    }

    function request($url, $data){
        if($result = dfsockopen($url, 0, $data)){
            return $result;
        }else{
            return dfsockopen($url, 0, $data, '', false, '', 15, true, 'URLENCODE', false);
        }
    }

    function array_sort($arr, $keys, $type = 'asc'){
        $keysvalue = $new_array = array();
        foreach ($arr as $k => $v) {
            $keysvalue[$k] = $v[$keys];
        }
        if ($type == 'asc') {
            //对数组进行排序并保持索引关系
            asort($keysvalue);
        } else {
            //对数组进行逆向排序并保持索引关系
            arsort($keysvalue);
        }
        reset($keysvalue);
        foreach ($keysvalue as $k => $v) {
            $new_array[] = $arr[$k];
        }
        return $new_array;
    }

    function formatnumber($a, $number = true){
        preg_match_all('/\d+/',$a,$arr);
        if($number)$arr = join('',$arr[0]);
        return $arr;
    }

    function formaturl($url, $str){
        if (is_array($str)) {
            $return = array();
            foreach ($str as $href) {
                $return[] = $this->formaturl($url, $href);
            }
            return $return;
        } else {
            if (stripos($str, 'http://')===0 || stripos($str, 'ftp://')===0) {
                return $str;
            }
            $str = str_replace('\\', '/', $str);
            $parseUrl = parse_url(dirname($url).'/');
            $scheme = isset($parseUrl['scheme']) ? $parseUrl['scheme'] : 'http';
            $host = $parseUrl['host'];
            $path = isset($parseUrl['path']) ? $parseUrl['path'] : '';
            $port = isset($parseUrl['port']) ? $parseUrl['port'] : '';

            if (strpos($str, '/')===0) {
                return $scheme.'://'.$host.$str;
            } else {
                $part = explode('/', $path);
                array_shift($part);
                $count = substr_count($str, '../');
                if ($count>0) {
                    for ($i=0; $i<=$count; $i++) {
                        array_pop($part);
                    }
                }
                $path = implode('/', $part);
                $str = str_replace(array('../','./'), '', $str);
                $path = $path=='' ? '/' : '/'.trim($path,'/').'/';
                return $scheme.'://'.$host.$path.$str;
            }
        }
    }

    private function html2bbcode($text) {
        require_once libfile('function/editor');

        $text = strip_tags($text, '<table><tr><td><b><strong><i><em><u><a><div><span><p><strike><blockquote><ol><ul><li><font><img><br><br/><h1><h2><h3><h4><h5><h6>');

        if(ismozilla()) {
            $text = preg_replace("/(?<!<br>|<br \/>|\r)(\r\n|\n|\r)/", ' ', $text);
        }
        $pregfind = array(
            "/<script.*>.*<\/script>/siU",
            '/on(mousewheel|mouseover|click|load|onload|submit|focus|blur)="[^"]*"/i',
            "/(\r\n|\n|\r)/",
            "/<table([^>]*(width|background|background-color|bgcolor)[^>]*)>/siUe",
            "/<table.*>/siU",
            "/<tr.*>/siU",
            "/<td>/i",
            "/<td(.+)>/siUe",
            "/<\/td>/i",
            "/<\/tr>/i",
            "/<\/table>/i",
            '/<h([0-9]+)[^>]*>/siUe',
            '/<\/h([0-9]+)>/siU',
            "/<img[^>]+smilieid=\"(\d+)\".*>/esiU",
            "/<img([^>]*src[^>]*)>/eiU",
            "/<a\s+?name=.+?\".\">(.+?)<\/a>/is",
            "/<br.*>/siU",
            "/<p.*>/siU",
            "/<\/p>/siU",
            "/<span\s+?style=\"float:\s+(left|right);\">(.+?)<\/span>/is",
            "/<hr[^>]*>/si",
            "/\<font(.*?)size=\"([^ >]+)\"(.*?)\>(.*?)<\/font>/i",
            "/\<font(.*?)color=\"#([^ >]+)\"(.*?)\>(.*?)<\/font>/i",
            "/\<([\/]?)strong\>/i",
            "/\<([\/]?)b(.*?)\>/i",
            "/\<([\/]?)i\>/i",
            "/\<DIV[^>]+ALIGN=\"([^\"]+)\"[^>]*\>(.*?)<\/DIV\>/is",
            "/\<P[^>]+STYLE=(\"|')text-align:(center);(\"|')[^>]*\>(.*?)<\/P\>/is",
            "/\<DIV[^>]+STYLE=(\"|')text-align:(center);(\"|')[^>]*\>(.*?)<\/DIV\>/is",
        );
        $pregreplace = array(
            '',
            '',
            '',
            "tabletag('\\1')",
            '[table]',
            '[tr]',
            '[td]',
            "tdtag('\\1')",
            '[/td]',
            '[/tr]',
            '[/table]',
            "\"[b]\"",
            "[/b]\n",
            "smileycode('\\1')",
            "imgtag('\\1')",
            '\1',
            "\n",
            "\n",
            "\n",
            "[float=\\1]\\2[/float]",
            "[hr]",
            "[size=$2]$4[/size]",
            "[color=$2]$4[/color]",
            "[$1b]",
            "[$1b]",
            "[$1i]",
            "[align=$1]$2[/align]",
            "[align=center]\\4[/align]",
            "[align=center]\\4[/align]",
        );
        $text = str_replace(array("<center>", "</center>"), array("[align=center]", "[/align]"), $text);
        $text = preg_replace($pregfind, $pregreplace, $text);

        $text = recursion('b', $text, 'simpletag', 'b');
        $text = recursion('strong', $text, 'simpletag', 'b');
        $text = recursion('i', $text, 'simpletag', 'i');
        $text = recursion('em', $text, 'simpletag', 'i');
        $text = recursion('u', $text, 'simpletag', 'u');
        $text = recursion('a', $text, 'atag');
        $text = recursion('font', $text, 'fonttag');
        $text = recursion('blockquote', $text, 'simpletag', 'indent');
        $text = recursion('ol', $text, 'listtag');
        $text = recursion('ul', $text, 'listtag');
        $text = recursion('div', $text, 'divtag');
        $text = recursion('span', $text, 'spantag');
        $text = recursion('p', $text, 'ptag');

        $pregfind = array("/(?<!\r|\n|^)\[(\/list|list|\*)\]/", "/<li>(.*)((?=<li>)|<\/li>)/iU", "/<p.*>/iU", "/<p><\/p>/i", "/(<a>|<\/a>|<\/li>)/is", "/<\/?(A|LI|FONT|DIV|SPAN)>/siU", "/\[url[^\]]*\]\[\/url\]/i", "/\[url=javascript:[^\]]*\](.+?)\[\/url\]/is");
        $pregreplace = array("\n[\\1]", "\\1\n", "\n", '', '', '', '', "\\1");
        $text = preg_replace($pregfind, $pregreplace, $text);

        $strfind = array('&nbsp;', '&lt;', '&gt;', '&amp;');
        $strreplace = array(' ', '<', '>', '&');
        $text = str_replace($strfind, $strreplace, $text);
        $text = strip_tags($text);

        preg_match_all("[size=(.*?)px]", $text, $matches);
        foreach($matches[1] as $value){
            $size[] = round($value);
        }
        foreach($size as $val){
            $text = preg_replace("[size=(\d+)\.(\d+)px]", "size={$val}px", $text);
        }


        return trim($text);
    }
}
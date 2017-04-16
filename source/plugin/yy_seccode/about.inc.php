<?php
if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}
$developer=array(
	0 => array(
	'id'=>'2580',
	'key'=>'wang11291895',
	'name'=>'烟雨逍遥(<a href="http://addon.discuz.com/?@cat">@cat</a>)',
	'md5hash'=>'7b0e3905631a54514bbff687c5c5fb3a',
	'msg'=>'承接Discuz论坛业务：安装、升级、故障解决、服务器维护、环境配置、插件定制等！QQ:2216019916',
	),
);
echo '<link rel="stylesheet" href="http://addon.discuz.com/resource/common.css" type="text/css" media="all" />';
foreach($developer as $dev){
	$url="http://open.discuz.net/api/getaddons?key=".$dev['key']."&md5hash=".$dev['md5hash'];
	$app = unserialize(file_data($url));
	$s='';
	$downloads=0;
	foreach($app['DATA'] as $a){
		$downloads+=$a['downloads'];
		$t;
		foreach($a['revisions'] as $r){
			if($r['price']!='0.00'){
				
				$t='<div class="yd">
<span class="price">&yen;'.$r['price'].'</span></div>';
			}else{
				$t='<div class="y">
<span class="free">免费</span></div>';
				break;
			}
		}
		$a['ID']=str_replace('.','_',$a['ID']);
		$s.='<li onmouseover="getMemo(this, \''.$a['ID'].'\')">
<div id="base_'.$a['ID'].'">
<div class="x1">
<a href="'.$a['url'].'" class="avt">
<img src="'.$a['logo'].'" alt="'.$a['name'].'" />
</a>
<h5>
'.$t.'
<a href="'.$a['url'].'" onmouseover="getMemo(this, \''.$a['ID'].'\')">'.$a['name'].'</a>
</h5>
<span>
<p class="cl mtn">
<span class="y"><span class="xg1">安装次数:</span> '.$a['downloads'].'</span>
<span class="rate" title="'.number_format($a['score']/$a['scoreds'],2).' 分"><span style="width:'.number_format($a['score']/$a['scoreds']*20,2).'%">&nbsp;</span></span>
</p>
<p class="cl mtn">
<span class="xg1">更新:</span> '.dgmdate($a['lastupdate'],'Y-m-d').'</p>
</div>
<div class="x2" id="memo_'.$a['ID'].'" style="display:none">'.cutstr(bbcodesclear($a['memo']),100,'...').'</div>
</div>
</li>';
	}
	$ratesv='<span title="应用总数" class="devapps">'.$app['COUNT'].'<span> <span title="总安装数" class="devdowns">'.$downloads.'</span>';
	$content='<table class="tb tb2 "><tr><th colspan="15" class="partition">'.$dev['name'].'&nbsp;&nbsp;&nbsp;'.$ratesv.'&nbsp;&nbsp;&nbsp;<a href="http://addon.discuz.com/?@'.$dev['id'].'.developer.doc/license" target="_blank" title="查看授权协议">授权协议</a>&nbsp;&nbsp;&nbsp;'.$dev['msg'].'</th></tr></table>';
	$content.='<div class="mtm mbw"><ul class="ml plb cl applisth">'.$s.'</ul></div>';
	echo $content=diconv($content,'gbk');

}
echo '<script>function getMemo(obj, id) {
	var baseobj = $(\'base_\' + id);
	var memoobj = $(\'memo_\' + id);
	baseobj.className = \'over\';
	memoobj.style.display = \'\';
	if(!obj.onmouseout) {
		obj.onmouseout = function () {
			baseobj.className = \'\';
			memoobj.style.display = \'none\';
		}
	}
}</script>';
function bbcodesclear($str){
	$bbcodes = 'b|i|u|p|color|size|font|align|list|indent|float';
	$str = strip_tags(preg_replace(array(
			"/\[url=?.*?\](.+?)\[\/url\]/si",
			"/\[($bbcodes)=?.*?\]/i",
			"/\[\/($bbcodes)\]/i",
		), array(
			'\\1',
			'',
			'',
		), $str));

	return $str;
}
function str_cut($str, $pre, $end) {
	$pos_pre = strpos($str, $pre) + strlen($pre);
	$str_end = substr($str, $pos_pre);
	$pos_end = strpos($str_end, $end);
	return substr($str, $pos_pre, $pos_end);
}
function file_data($url) {
        for ($i = 0; $i < 3; $i++) {
            $data = file_get_contents($url);
            if ($data)
                return $data;
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
        @ $data = curl_exec($ch);
        curl_close($ch);
        return $data;
}
?>
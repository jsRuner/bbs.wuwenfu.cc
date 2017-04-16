<?php
/**
 *      [Discuz!] (C)2001-2099 Comsenz Inc.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      $Id: home_editor.php 35297 2015-06-05 03:28:45Z hypowang $
 */
if (!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

if (empty($_GET['charset']) || !in_array(strtolower($_GET['charset']), array('gbk', 'big5', 'utf-8')))
	$_GET['charset'] = '';
$allowhtml = empty($_GET['allowhtml']) ? 0 : 1;

$doodle = empty($_GET['doodle']) ? 0 : 1;
$isportal = empty($_GET['isportal']) ? 0 : 1;
if (empty($_GET['op'])) {
	?>
	<html>
		<head>
			<meta http-equiv="Content-Type" content="text/html;charset=<?php echo $_GET['charset']; ?>" />
			<title>Editor</title>
			<script type="text/javascript" src="static/js/common.js"></script>
			<script type="text/javascript" src="static/js/home.js"></script>
			<script language="javascript" src="static/image/editor/editor_base.js"></script>
			<style type="text/css">
				body{margin:0;padding:0;}
				body, td, input, button, select, textarea {font: 12px/1.5em Tahoma, Arial, Helvetica, snas-serif;}
				textarea { resize: none; font-size: 14px; line-height: 1.8em; }
				.submit { padding: 0 10px; height: 22px; border: 1px solid; border-color: #DDD #264F6E #264F6E #DDD; background: #2782D6; color: #FFF; line-height: 20px; letter-spacing: 1px; cursor: pointer; }
				a.dm{text-decoration:none}
				a.dm:hover{text-decoration:underline}
				a{font-size:12px}
				img{border:0}
				td.icon{width:24px;height:24px;text-align:center;vertical-align:middle}
				td.sp{width:8px;height:24px;text-align:center;vertical-align:middle}
				td.xz{width:47px;height:24px;text-align:center;vertical-align:middle}
				td.bq{width:49px;height:24px;text-align:center;vertical-align:middle}
				div a.n{height:16px;line-height:16px;display:block;padding:2px;color:#000000;text-decoration:none}
				div a.n:hover{background:#E5E5E5}
				.r_op { float: right; }
				.eMenu{position:absolute;margin-top: -2px;background:#FFFFFF;border:1px solid #C5C5C5;padding:4px}
				.eMenu ul, .eMenu ul li { margin: 0; padding: 0; }
				.eMenu ul li{list-style: none;float:left}
				#editFaceBox { padding: 5px; }
				#editFaceBox li { width: 25px; height: 25px; overflow: hidden; }
				.t_input { padding: 3px 2px; border-style: solid; border-width: 1px; border-color: #7C7C7C #C3C3C3 #DDD; line-height: 16px; }
				a.n1{height:16px;line-height:16px;display:block;padding:2px;color:#000000;text-decoration:none}
				a.n1:hover{background:#E5E5E5}
				a.cs{height:15px;position:relative}
				*:lang(zh) a.cs{height:12px}
				.cs .cb{font-size:0;display:block;width:10px;height:8px;position:absolute;left:4px;top:3px;cursor:hand!important;cursor:pointer}
				.cs span{position:absolute;left:19px;top:0px;cursor:hand!important;cursor:pointer;color:#333}

				.fRd1 .cb{background-color:#800}
				.fRd2 .cb{background-color:#800080}
				.fRd3 .cb{background-color:#F00}
				.fRd4 .cb{background-color:#F0F}
				.fBu1 .cb{background-color:#000080}
				.fBu2 .cb{background-color:#00F}
				.fBu3 .cb{background-color:#0FF}
				.fGn1 .cb{background-color:#008080}
				.fGn2 .cb{background-color:#008000}
				.fGn3 .cb{background-color:#808000}
				.fGn4 .cb{background-color:#0F0}
				.fYl1 .cb{background-color:#FC0}
				.fBk1 .cb{background-color:#000}
				.fBk2 .cb{background-color:#808080}
				.fBk3 .cb{background-color:#C0C0C0}
				.fWt0 .cb{background-color:#FFF;border:1px solid #CCC}

				.mf_nowchose{height:30px;background-color:#DFDFDF;border:1px solid #B5B5B5;border-left:none}
				.mf_other{height:30px;border-left:1px solid #B5B5B5}
				.mf_otherdiv{height:30px;width:30px;border:1px solid #FFF;border-right-color:#D6D6D6;border-bottom-color:#D6D6D6;background-color:#F8F8F8}
				.mf_otherdiv2{height:30px;width:30px;border:1px solid #B5B5B5;border-left:none;border-top:none}
				.mf_link{font-size:12px;color:#000000;text-decoration:none}
				.mf_link:hover{font-size:12px;color:#000000;text-decoration:underline}

				.ico{height:24px;width:24px;vertical-align:middle;text-align:center}
				.ico2{height:24px;width:27px;vertical-align:middle;text-align:center}
				.ico3{height:24px;width:25px;vertical-align:middle;text-align:center}
				.ico4{height:24px;width:8px;vertical-align:middle;text-align:center}

				.edTb { background: #F2F2F2; }
				.icons a,.sepline,.switch{background-image:url(static/image/editor/editor.gif)}

				.toobar, .toobarmini{position:relative;height:26px;overflow:hidden}
				.toobarmini .icoSwitchTxt, .toobarmini .tble{ display: none !important;}
				.toobar .icoSwitchMdi{ display: none;}

				.tble{position:absolute;left:0;top:2px }
				*:lang(zh) .tble{top:2px}
				.tbri{width:60px;position:absolute;right:3px;top:2px;}

				.icons a{width:20px;height:20px;background-repeat:no-repeat;display:block;float:left;border:1px solid #F2F2F2;}
				*:lang(zh) .icons a{margin-right:1px}
				.icons a:hover{border-color: #369 #CCC;background-color:#FFF}
				a.icoCut{background-position:-140px -60px;}
				a.icoCpy{background-position:-160px -60px;}
				a.icoPse{background-position:-40px -60px}
				a.icoFfm{background-position:-100px 0}
				a.icoFsz{background-position:-120px 0;}
				a.icoWgt{background-position:0 0;}
				a.icoIta{background-position:-20px 0;}
				a.icoUln{background-position:-40px 0;}
				a.icoAgn{background-position:-60px 0}
				a.icoAgL{background-position:-80px -20px}
				a.icoAgC{background-position:-240px -40px}
				a.icoAgR{background-position:-260px -40px}
				a.icoLst{background-position:-100px -20px}
				a.icoOdt{background-position:-180px -60px}
				a.icoIdt{background-position:-180px -60px}
				a.icoFcl{background-position:-60px 0}
				a.icoBcl{background-position:-80px 0}
				a.icoUrl{background-position:-40px -20px;}
				a.icoMoveUrl{background-position:-60px -20px}
				a.icoRenew {background-position:-180px -40px}
				a.icoFace {background-position:-20px -20px}
				a.icoPage {background-position:-200px -60px}
				a.icoDown {background-position:-80px -60px}
				a.icoDoodle {background-position:-260px -60px}
				a.icoImg{background-position:0 -20px}
				a.icoAttach{background-position:-200px -20px}
				a.icoSwf{background-position:-240px -20px}
				a.icoSwitchTxt{background-position:-220px -60px;float:right}
				a.icoFullTxt{ float: right; width: 35px; height: 20px; line-height: 20px; border: 1px solid #C2D5E3; background: url(static/image/common/card_btn.png) repeat-x 0 100%; text-align: center; color: #333; text-decoration: none; }
				a.icoSwitchMdi{background-position:-239px -60px;float:right}


				.edTb{border-bottom:1px solid #c5c5c5;background-position:0 -28px}
				.sepline{width:4px;height:20px;margin-top:2px;margin-right:3px;background-position:-476px 0;background-repeat:no-repeat;float:left }
			</style>
			<script language="JavaScript">
				function fontname(obj){format('fontname',obj.innerHTML);obj.parentNode.style.display='none'}
				function fontsize(size,obj){format('fontsize',size);obj.parentNode.style.display='none'}
			</script>
		</head>
		<body style="overflow-y:hidden">

		 <script type="text/javascript" charset="utf-8" src="ueditor/ueditor.config.js"></script>
    <script type="text/javascript" charset="utf-8" src="ueditor/ueditor.all.min.js"> </script>
    <!--建议手动加在语言，避免在ie下有时因为加载语言失败导致编辑器加载失败-->
    <!--这里加载的语言文件会覆盖你在配置项目里添加的语言类型，比如你在配置项目里配置的是英文，这里加载的中文，那最后就是中文-->
    <script type="text/javascript" charset="utf-8" src="ueditor/lang/zh-cn/zh-cn.js"></script>

    <style type="text/css">
        div{
            width:100%;
        }
    </style>

		<script id="editor" type="text/plain" style="height: 800px;"></script>

		<script type="text/javascript">

    //实例化编辑器
    //建议使用工厂方法getEditor创建和引用编辑器实例，如果在某个闭包下引用该编辑器，直接调用UE.getEditor('editor')就能拿到相关的实例
    var ue = UE.getEditor('editor');

    //填充内容。
    var inihtml = '';
	var obj = parent.document.getElementById('uchome-ttHtmlEditor');
	if(obj) {
		inihtml = obj.value;
	}
	if(! inihtml && !window.Event) {
		inihtml = '<div></div>';
	}
	document.getElementById('editor').innerHTML = inihtml;


    function isFocus(e){
        alert(UE.getEditor('editor').isFocus());
        UE.dom.domUtils.preventDefault(e)
    }
    function setblur(e){
        UE.getEditor('editor').blur();
        UE.dom.domUtils.preventDefault(e)
    }
    function insertHtml() {
        var value = prompt('插入html代码', '');
        UE.getEditor('editor').execCommand('insertHtml', value)
    }
    function createEditor() {
        enableBtn();
        UE.getEditor('editor');
    }
    function getAllHtml() {
        alert(UE.getEditor('editor').getAllHtml())
    }
    function getContent() {
        var arr = [];
        arr.push("使用editor.getContent()方法可以获得编辑器的内容");
        arr.push("内容为：");
        arr.push(UE.getEditor('editor').getContent());
        alert(arr.join("\n"));
    }
    function getPlainTxt() {
        var arr = [];
        arr.push("使用editor.getPlainTxt()方法可以获得编辑器的带格式的纯文本内容");
        arr.push("内容为：");
        arr.push(UE.getEditor('editor').getPlainTxt());
        alert(arr.join('\n'))
    }
    function setContent(isAppendTo) {
        var arr = [];
        arr.push("使用editor.setContent('欢迎使用ueditor')方法可以设置编辑器的内容");
        UE.getEditor('editor').setContent('欢迎使用ueditor', isAppendTo);
        alert(arr.join("\n"));
    }
    function setDisabled() {
        UE.getEditor('editor').setDisabled('fullscreen');
        disableBtn("enable");
    }

    function setEnabled() {
        UE.getEditor('editor').setEnabled();
        enableBtn();
    }

    function getText() {
        //当你点击按钮时编辑区域已经失去了焦点，如果直接用getText将不会得到内容，所以要在选回来，然后取得内容
        var range = UE.getEditor('editor').selection.getRange();
        range.select();
        var txt = UE.getEditor('editor').selection.getText();
        alert(txt)
    }

    function getContentTxt() {
        var arr = [];
        arr.push("使用editor.getContentTxt()方法可以获得编辑器的纯文本内容");
        arr.push("编辑器的纯文本内容为：");
        arr.push(UE.getEditor('editor').getContentTxt());
        alert(arr.join("\n"));
    }
    function hasContent() {
        var arr = [];
        arr.push("使用editor.hasContents()方法判断编辑器里是否有内容");
        arr.push("判断结果为：");
        arr.push(UE.getEditor('editor').hasContents());
        alert(arr.join("\n"));
    }
    function setFocus() {
        UE.getEditor('editor').focus();
    }
    function deleteEditor() {
        disableBtn();
        UE.getEditor('editor').destroy();
    }
    function disableBtn(str) {
        var div = document.getElementById('btns');
        var btns = UE.dom.domUtils.getElementsByTagName(div, "button");
        for (var i = 0, btn; btn = btns[i++];) {
            if (btn.id == str) {
                UE.dom.domUtils.removeAttributes(btn, ["disabled"]);
            } else {
                btn.setAttribute("disabled", "true");
            }
        }
    }
    function enableBtn() {
        var div = document.getElementById('btns');
        var btns = UE.dom.domUtils.getElementsByTagName(div, "button");
        for (var i = 0, btn; btn = btns[i++];) {
            UE.dom.domUtils.removeAttributes(btn, ["disabled"]);
        }
    }

    function getLocalData () {
        alert(UE.getEditor('editor').execCommand( "getlocaldata" ));
    }

    function clearLocalData () {
        UE.getEditor('editor').execCommand( "clearlocaldata" );
        alert("已清空草稿箱")
    }
</script>
			















		</body>
	</html>
	<?php
} else {
	?>
	<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
	<HTML>
		<HEAD>
			<meta http-equiv="Content-Type" content="text/html;charset=<?php echo $_GET['charset']; ?>" />
			<title>New Document</title>
			<style>
				body { margin: 0; padding: 0; word-wrap: break-word; font-size:14px; line-height:1.8em; font-family: Tahoma, Arial, Helvetica, snas-serif; }
			</style>
			<meta content="mshtml 6.00.2900.3132" name=generator>
		</head>
		<body>
		1111
		</body>
	</html>
<?php
}?>
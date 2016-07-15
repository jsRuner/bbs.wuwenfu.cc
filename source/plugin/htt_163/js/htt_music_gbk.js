
var musicPage = 1;

var musiccss = document.createElement('link');
	musiccss.type = 'text/css';
	musiccss.rel = 'stylesheet';
	musiccss.href = '/source/plugin/xhuaian_xiami/images/music.css';
var musiclo = document.getElementsByTagName('link')[0];
	musiclo.parentNode.insertBefore(musiccss, musiclo);

function showEditorMenu(tag, params) {
	var sel, selection;
	var str = '', strdialog = 0, stitle = '';
	var ctrlid = editorid + (params ? '_cst' + params + '_' : '_') + tag;
	var opentag = '[' + tag + ']';
	var closetag = '[/' + tag + ']';
	var menu = $(ctrlid + '_menu');
	var pos = [0, 0];
	var menuwidth = 270;
	//yuqj 调整音乐的宽度
	var musicmenuwidth = 385;
	var menupos = '43!';
	var menutype = 'menu';

	if(BROWSER.ie) {
		sel = wysiwyg ? editdoc.selection.createRange() : document.selection.createRange();
		pos = getCaret();
	}

	selection = sel ? (wysiwyg ? sel.htmlText : sel.text) : getSel();

	if(menu) {
		if($(ctrlid).getAttribute('menupos') !== null) {
			menupos = $(ctrlid).getAttribute('menupos');
		}
		if($(ctrlid).getAttribute('menuwidth') !== null) {
			menu.style.width = $(ctrlid).getAttribute('menuwidth') + 'px';
		}
		if(menupos == '00') {
			menu.className = 'fwinmask';
			if($(editorid + '_' + tag + '_menu').style.visibility == 'hidden') {
				$(editorid + '_' + tag + '_menu').style.visibility = 'visible';
			} else {
				showMenu({'ctrlid':ctrlid,'mtype':'win','evt':'click','pos':menupos,'timeout':250,'duration':3,'drag':ctrlid + '_ctrl'});
			}
		} else {
			showMenu({'ctrlid':ctrlid,'evt':'click','pos':menupos,'timeout':250,'duration':in_array(tag, ['fontname', 'fontsize', 'sml']) ? 2 : 3,'drag':1});
		}


	} else {
		switch(tag) {
			case 'url':
				str = '请输入链接地址:<br /><input type="text" id="' + ctrlid + '_param_1" style="width: 98%" value="" class="px" />'+
					(selection ? '' : '<br />请输入链接文字:<br /><input type="text" id="' + ctrlid + '_param_2" style="width: 98%" value="" class="px" />');
				break;
			case 'forecolor':
				showColorBox(ctrlid, 1);
				return;
			case 'backcolor':
				showColorBox(ctrlid, 1, '', 1);
				return;
			case 'code':
				if(wysiwyg) {
					opentag = '<div class="blockcode"><blockquote>';
					closetag = '</blockquote></div><br />';
				}
			case 'quote':
				if(wysiwyg && tag == 'quote') {
					opentag = '<div class="quote"><blockquote>';
					closetag = '</blockquote></div><br />';
				}
			case 'hide':
			case 'free':
				if(selection) {
					return insertText((opentag + selection + closetag), strlen(opentag), strlen(closetag), true, sel);
				}
				var lang = {'quote' : '请输入要插入的引用', 'code' : '请输入要插入的代码', 'hide' : '请输入要隐藏的信息内容', 'free' : '如果您设置了帖子售价，请输入购买前免费可见的信息内容'};
				str += lang[tag] + ':<br /><textarea id="' + ctrlid + '_param_1" style="width: 98%" cols="50" rows="5" class="txtarea"></textarea>' +
					(tag == 'hide' ? '<br /><label><input type="radio" name="' + ctrlid + '_radio" id="' + ctrlid + '_radio_1" class="pc" checked="checked" />只有当浏览者回复本帖时才显示</label><br /><label><input type="radio" name="' + ctrlid + '_radio" id="' + ctrlid + '_radio_2" class="pc" />只有当浏览者积分高于</label> <input type="text" size="3" id="' + ctrlid + '_param_2" class="px pxs" /> 时才显示' : '');
				break;
			case 'tbl':
				str = '<p class="pbn">表格行数: <input type="text" id="' + ctrlid + '_param_1" size="2" value="2" class="px" /> &nbsp; 表格列数: <input type="text" id="' + ctrlid + '_param_2" size="2" value="2" class="px" /></p><p class="pbn">表格宽度: <input type="text" id="' + ctrlid + '_param_3" size="2" value="" class="px" /> &nbsp; 背景颜色: <input type="text" id="' + ctrlid + '_param_4" size="2" class="px" onclick="showColorBox(this.id, 2)" /></p><p class="xg2 pbn" style="cursor:pointer" onclick="showDialog($(\'tbltips_msg\').innerHTML, \'notice\', \'小提示\', null, 0)"><img id="tbltips" title="小提示" class="vm" src="' + IMGDIR + '/info_small.gif"> 快速书写表格提示</p>';
				str += '<div id="tbltips_msg" style="display: none">“[tr=颜色]” 定义行背景<br />“[td=宽度]” 定义列宽<br />“[td=列跨度,行跨度,宽度]” 定义行列跨度<br /><br />快速书写表格范例：<div class=\'xs0\' style=\'margin:0 5px\'>[table]<br />Name:|Discuz!<br />Version:|X1<br />[/table]</div>用“|”分隔每一列，表格中如有“|”用“\\|”代替，换行用“\\n”代替。</div>';
				break;
			case 'aud':
				//str = '<p class="pbn">请输入音乐文件地址:</p><p class="pbn"><input type="text" id="' + ctrlid + '_param_1" class="px" value="" style="width: 220px;" /></p><p class="xg2 pbn">支持 wma mp3 ra rm 等音乐格式<br />示例: http://server/audio.wma</p>';
				//str = '<div id="music_search_bar"><input class="px" name="" title="输入网易云音乐的歌曲地址" style="width: 220px;" value="" id="htt_music_input"/><a class="btn_sm_m pn pnc pns mtn"  href="javascript:void(0)" id="htt_music_bt" title="插入"  onFocus="this.blur()" unselectable="on"><span>插入</span></a><p class="xg2 pbn">您可以去网易云音乐找到你的喜欢的歌曲，复制歌曲链接例如 <br> http://music.163.com/#/song?id=394718 粘贴到这里。<br> 点击“插入”即可与广大网友分享该歌曲</p></div>';
				str = '<div id="music_search_bar"><input class="px" name="" title="输入网易云音乐的歌曲地址" style="width: 300px;" value="" id="htt_music_input"/><p class="xg2 pbn">您可以去网易云音乐找到你的喜欢的歌曲，复制歌曲链接例如 <br> http://music.163.com/#/song?id=394718 粘贴到这里。<br> 点击“插入”即可与广大网友分享该歌曲</p><div class="pns mtn"><button type="submit" id="htt_music_bt" class="pn pnc"><strong>提交</strong></button></div></div>';
				break;
			case 'vid':
				str = '<p class="pbn">请输入视频222地址:</p><p class="pbn"><input type="text" value="" id="' + ctrlid + '_param_1" style="width: 220px;" class="px" /></p><p class="pbn">宽: <input id="' + ctrlid + '_param_2" size="5" value="500" class="px" /> &nbsp; 高: <input id="' + ctrlid + '_param_3" size="5" value="375" class="px" /></p><p class="xg2 pbn">支持优酷、土豆、56、酷6等视频站的视频网址<br />支持 wmv avi rmvb mov swf flv 等视频格式<br />示例: http://server/movie.wmv</p>';
				break;
			case 'fls':
				str = '<p class="pbn">请输入 Flash 文件地址:</p><p class="pbn"><input type="text" id="' + ctrlid + '_param_1" class="px" value="" style="width: 220px;" /></p><p class="pbn">宽: <input id="' + ctrlid + '_param_2" size="5" value="" class="px" /> &nbsp; 高: <input id="' + ctrlid + '_param_3" size="5" value="" class="px" /></p><p class="xg2 pbn">支持 swf flv 等 Flash 网址<br />示例: http://server/flash.swf</p>';
				break;
			case 'pasteword':
				stitle = '从 Word 粘贴内容';
				str = '<p class="px" style="height:300px"><iframe id="' + ctrlid + '_param_1" frameborder="0" style="width:100%;height:100%" onload="this.contentWindow.document.body.style.width=\'550px\';this.contentWindow.document.body.contentEditable=true;this.contentWindow.document.body.focus();this.onload=null"></iframe></p><p class="xg2 pbn">请通过快捷键(Ctrl+V)把 Word 文件中的内容粘贴到上方</p>';
				menuwidth = 600;
				menupos = '00';
				menutype = 'win';
				break;
			default:
				var haveSel = selection == null || selection == false || in_array(trim(selection), ['', 'null', 'undefined', 'false']) ? 0 : 1;
				if(params == 1 && haveSel) {
					return insertText((opentag + selection + closetag), strlen(opentag), strlen(closetag), true, sel);
				}
				var promptlang = custombbcodes[tag]['prompt'].split("\t");
				for(var i = 1; i <= params; i++) {
					if(i != params || !haveSel) {
						str += (promptlang[i - 1] ? promptlang[i - 1] : '请输入第 ' + i + ' 个参数:') + '<br /><input type="text" id="' + ctrlid + '_param_' + i + '" style="width: 98%" value="" class="px" />' + (i < params ? '<br />' : '');
					}
				}
				break;
		}

		var menu = document.createElement('div');
		menu.id = ctrlid + '_menu';
		menu.style.display = 'none';
		menu.className = 'p_pof upf';
		//yuqj 调整音乐的宽度
		if (tag == 'aud') {
			menu.style.width = musicmenuwidth + 'px';
		} else {
			menu.style.width = menuwidth + 'px';
		}
		if(menupos == '00') {
			menu.className = 'fwinmask';
			s = '<table width="100%" cellpadding="0" cellspacing="0" class="fwin"><tr><td class="t_l"></td><td class="t_c"></td><td class="t_r"></td></tr><tr><td class="m_l">&nbsp;&nbsp;</td><td class="m_c">'
				+ '<h3 class="flb"><em>' + stitle + '</em><span><a onclick="hideMenu(\'\', \'win\');return false;" class="flbc" href="javascript:;">关闭</a></span></h3><div class="c">' + str + '</div>'
				+ '<p class="o pns"><button type="submit" id="' + ctrlid + '_submit" class="pn pnc"><strong>提交</strong></button></p>'
				+ '</td><td class="m_r"></td></tr><tr><td class="b_l"></td><td class="b_c"></td><td class="b_r"></td></tr></table>';
		} else {
			//yuqj 替换原有音乐的样子
			if(tag == 'aud'){
				s = '<div class="p_opt cl"><span class="y" style="margin:-10px -10px 0 0"><a onclick="hideMenu()" class="flbc" href="javascript:;">关闭</a></span><div>' + str + '</div></div>';
			} else {
				s = '<div class="p_opt cl"><span class="y" style="margin:-10px -10px 0 0"><a onclick="hideMenu();return false;" class="flbc" href="javascript:;">关闭</a></span><div>' + str + '</div><div class="pns mtn"><button type="submit" id="' + ctrlid + '_submit" class="pn pnc"><strong>提交</strong></button></div></div>';
			}
		}
		menu.innerHTML = s;
		$(editorid + '_editortoolbar').appendChild(menu);
		showMenu({'ctrlid':ctrlid,'mtype':menutype,'evt':'click','duration':3,'cache':0,'drag':1,'pos':menupos});
	}

	try {
		if($(ctrlid + '_param_1')) {
			$(ctrlid + '_param_1').focus();
		}
	} catch(e) {}

if($('htt_music_input')) {
	_attachEvent($('htt_music_input'),'focus', function(e){
		e = e ? e : event;
		obj = BROWSER.ie ? event.srcElement : e.target;
		if(obj.value == obj.title) obj.value = '';
	});


	_attachEvent($('htt_music_bt'),'click', function(e){
		e = e ? e : event;
		obj = BROWSER.ie ? event.srcElement : e.target;
		if ($('htt_music_input').value != $('htt_music_input').title) {
			httMusicAction();
		}
	});
}

	var objs = menu.getElementsByTagName('*');
	for(var i = 0; i < objs.length; i++) {
		_attachEvent(objs[i], 'keydown', function(e) {
			e = e ? e : event;
			obj = BROWSER.ie ? event.srcElement : e.target;
			if((obj.type == 'text' && e.keyCode == 13) || (obj.type == 'textarea' && e.ctrlKey && e.keyCode == 13)) {
				if($(ctrlid + '_submit') && tag != 'image') $(ctrlid + '_submit').click();
				doane(e);
			} else if(e.keyCode == 27) {
				hideMenu();
				doane(e);
			}
		});
	}
	if($(ctrlid + '_submit')) $(ctrlid + '_submit').onclick = function() {
		checkFocus();
		if(BROWSER.ie && wysiwyg) {
			setCaret(pos[0]);
		}
		switch(tag) {
			case 'url':
				var href = $(ctrlid + '_param_1').value;
				href = (isEmail(href) ? 'mailto:' : '') + href;
				if(href != '') {
					var v = selection ? selection : ($(ctrlid + '_param_2').value ? $(ctrlid + '_param_2').value : href);
					str = wysiwyg ? ('<a href="' + href + '">' + v + '</a>') : '[url=' + href + ']' + v + '[/url]';
					if(wysiwyg) insertText(str, str.length - v.length, 0, (selection ? true : false), sel);
					else insertText(str, str.length - v.length - 6, 6, (selection ? true : false), sel);
				}
				break;
			case 'code':
				if(wysiwyg) {
					opentag = '<div class="blockcode"><blockquote>';
					closetag = '</blockquote></div><br />';
					if(!BROWSER.ie) {
						selection = selection ? selection : '\n';
					}
				}
			case 'quote':
				if(wysiwyg && tag == 'quote') {
					opentag = '<div class="quote"><blockquote>';
					closetag = '</blockquote></div><br />';
					if(!BROWSER.ie) {
						selection = selection ? selection : '\n';
					}
				}
			case 'hide':
			case 'free':
				if(tag == 'hide' && $(ctrlid + '_radio_2').checked) {
					var mincredits = parseInt($(ctrlid + '_param_2').value);
					opentag = mincredits > 0 ? '[hide=' + mincredits + ']' : '[hide]';
				}
				str = $(ctrlid + '_param_1') && $(ctrlid + '_param_1').value ? $(ctrlid + '_param_1').value : (selection ? selection : '');
				if(wysiwyg) {
					str = preg_replace(['<', '>'], ['&lt;', '&gt;'], str);
					str = str.replace(/\r?\n/g, '<br />');
				}
				str = opentag + str + closetag;
				insertText(str, strlen(opentag), strlen(closetag), false, sel);
				break;
			case 'tbl':
				var rows = $(ctrlid + '_param_1').value;
				var columns = $(ctrlid + '_param_2').value;
				var width = $(ctrlid + '_param_3').value;
				var bgcolor = $(ctrlid + '_param_4').value;
				rows = /^[-\+]?\d+$/.test(rows) && rows > 0 && rows <= 30 ? rows : 2;
				columns = /^[-\+]?\d+$/.test(columns) && columns > 0 && columns <= 30 ? columns : 2;
				width = width.substr(width.length - 1, width.length) == '%' ? (width.substr(0, width.length - 1) <= 98 ? width : '98%') : (width <= 560 ? width : '98%');
				bgcolor = /[\(\)%,#\w]+/.test(bgcolor) ? bgcolor : '';
				if(wysiwyg) {
					str = '<table cellspacing="0" cellpadding="0" width="' + (width ? width : '50%') + '" class="t_table"' + (bgcolor ? ' bgcolor="' + bgcolor + '"' : '') + '>';
					for (var row = 0; row < rows; row++) {
						str += '<tr>\n';
						for (col = 0; col < columns; col++) {
							str += '<td>&nbsp;</td>\n';
						}
						str += '</tr>\n';
					}
					str += '</table>\n';
				} else {
					str = '[table=' + (width ? width : '50%') + (bgcolor ? ',' + bgcolor : '') + ']\n';
					for (var row = 0; row < rows; row++) {
						str += '[tr]';
						for (col = 0; col < columns; col++) {
							str += '[td] [/td]';
						}
						str += '[/tr]\n';
					}
					str += '[/table]\n';
				}
				insertText(str, str.length, 0, false, sel);
				break;
			case 'aud':
				insertText('[audio]' + $(ctrlid + '_param_1').value + '[/audio]', 7, 8, false, sel);
				break;
			case 'fls':
				if($(ctrlid + '_param_2').value && $(ctrlid + '_param_3').value) {
					insertText('[flash=' + parseInt($(ctrlid + '_param_2').value) + ',' + parseInt($(ctrlid + '_param_3').value) + ']' + $(ctrlid + '_param_1').value + '[/flash]', 7, 8, false, sel);
				} else {
					insertText('[flash]' + $(ctrlid + '_param_1').value + '[/flash]', 7, 8, false, sel);
				}
				break;
			case 'vid':
				var mediaUrl = $(ctrlid + '_param_1').value;
				var auto = '';
				var ext = mediaUrl.lastIndexOf('.') == -1 ? '' : mediaUrl.substr(mediaUrl.lastIndexOf('.') + 1, mb_strlen(mediaUrl)).toLowerCase();
				ext = in_array(ext, ['mp3', 'wma', 'ra', 'rm', 'ram', 'mid', 'asx', 'wmv', 'avi', 'mpg', 'mpeg', 'rmvb', 'asf', 'mov', 'flv', 'swf']) ? ext : 'x';
				if(ext == 'x') {
					if(/^mms:\/\//.test(mediaUrl)) {
						ext = 'mms';
					} else if(/^(rtsp|pnm):\/\//.test(mediaUrl)) {
						ext = 'rtsp';
					}
				}
				var str = '[media=' + ext + ',' + $(ctrlid + '_param_2').value + ',' + $(ctrlid + '_param_3').value + ']' + mediaUrl + '[/media]';
				insertText(str, str.length, 0, false, sel);
				break;
			case 'image':
				var width = parseInt($(ctrlid + '_param_2').value);
				var height = parseInt($(ctrlid + '_param_3').value);
				var src = $(ctrlid + '_param_1').value;
				var style = '';
				if(wysiwyg) {
					style += width ? ' width=' + width : '';
					style += height ? ' height=' + height : '';
					var str = '<img src=' + src + style + ' border=0 />';
					insertText(str, str.length, 0, false, sel);
				} else {
					style += width || height ? '=' + width + ',' + height : '';
					insertText('[img' + style + ']' + src + '[/img]', 0, 0, false, sel);
				}
				$(ctrlid + '_param_1').value = '';
				break;
			case 'pasteword':
				pasteWord($(ctrlid + '_param_1').contentWindow.document.body.innerHTML);
				hideMenu('', 'win');
				break;
			default:
				var first = $(ctrlid + '_param_1').value;
				if($(ctrlid + '_param_2')) var second = $(ctrlid + '_param_2').value;
				if($(ctrlid + '_param_3')) var third = $(ctrlid + '_param_3').value;
				if((params == 1 && first) || (params == 2 && first && (haveSel || second)) || (params == 3 && first && second && (haveSel || third))) {
					if(params == 1) {
						str = first;
					} else if(params == 2) {
						str = haveSel ? selection : second;
						opentag = '[' + tag + '=' + first + ']';
					} else {
						str = haveSel ? selection : third;
						opentag = '[' + tag + '=' + first + ',' + second + ']';
					}
					insertText((opentag + str + closetag), strlen(opentag), strlen(closetag), true, sel);
				}
				break;
		}
		hideMenu();
	};
}

function httMusicAction(){
	var head = $("music_search_bar");
    //添加判断。不符合要求给出提示。
    var reg = /^http:\/\/music.163.com\/#\/song\?id=(\d)+$/;
    var r = head.children[0].value.match(reg);
    if(r==null){
        alert('对不起，您输入的格式不正确!');
        return ;
    }
    insertMusic(head.children[0].value);
}


function insertMusic(songurl) {
    theform = $("postform");
    h = checkMusicCount(theform);
    if(!h){
        return ;
    }
	var txt = '[163]' +songurl+ '[/163]';
	if(wysiwyg) {
		insertText(txt, false);
	} else {
		insertText(txt, strlen(txt), 0);
	}
	hideMenu();
}


/*音乐*/
function checkMusicCount(theform) {
		var message = wysiwyg ? html2bbcode(getEditorContents()) : (!theform.parseurloff.checked ? parseurl(theform.message.value) : theform.message.value);
		re = /[163](.*?)[\/163]/ig;
		var __currentMusicList = message.match(re);
		if ( __currentMusicList != null ){
			if ( __currentMusicList.length > 1 ) {
				showDialog('只允许插入一首音乐!');
				return false;
			}
		}
		return true;
	}
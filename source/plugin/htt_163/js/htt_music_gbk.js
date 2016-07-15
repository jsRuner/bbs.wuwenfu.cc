
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
	//yuqj �������ֵĿ��
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
				str = '���������ӵ�ַ:<br /><input type="text" id="' + ctrlid + '_param_1" style="width: 98%" value="" class="px" />'+
					(selection ? '' : '<br />��������������:<br /><input type="text" id="' + ctrlid + '_param_2" style="width: 98%" value="" class="px" />');
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
				var lang = {'quote' : '������Ҫ���������', 'code' : '������Ҫ����Ĵ���', 'hide' : '������Ҫ���ص���Ϣ����', 'free' : '����������������ۼۣ������빺��ǰ��ѿɼ�����Ϣ����'};
				str += lang[tag] + ':<br /><textarea id="' + ctrlid + '_param_1" style="width: 98%" cols="50" rows="5" class="txtarea"></textarea>' +
					(tag == 'hide' ? '<br /><label><input type="radio" name="' + ctrlid + '_radio" id="' + ctrlid + '_radio_1" class="pc" checked="checked" />ֻ�е�����߻ظ�����ʱ����ʾ</label><br /><label><input type="radio" name="' + ctrlid + '_radio" id="' + ctrlid + '_radio_2" class="pc" />ֻ�е�����߻��ָ���</label> <input type="text" size="3" id="' + ctrlid + '_param_2" class="px pxs" /> ʱ����ʾ' : '');
				break;
			case 'tbl':
				str = '<p class="pbn">�������: <input type="text" id="' + ctrlid + '_param_1" size="2" value="2" class="px" /> &nbsp; �������: <input type="text" id="' + ctrlid + '_param_2" size="2" value="2" class="px" /></p><p class="pbn">�����: <input type="text" id="' + ctrlid + '_param_3" size="2" value="" class="px" /> &nbsp; ������ɫ: <input type="text" id="' + ctrlid + '_param_4" size="2" class="px" onclick="showColorBox(this.id, 2)" /></p><p class="xg2 pbn" style="cursor:pointer" onclick="showDialog($(\'tbltips_msg\').innerHTML, \'notice\', \'С��ʾ\', null, 0)"><img id="tbltips" title="С��ʾ" class="vm" src="' + IMGDIR + '/info_small.gif"> ������д�����ʾ</p>';
				str += '<div id="tbltips_msg" style="display: none">��[tr=��ɫ]�� �����б���<br />��[td=���]�� �����п�<br />��[td=�п��,�п��,���]�� �������п��<br /><br />������д�������<div class=\'xs0\' style=\'margin:0 5px\'>[table]<br />Name:|Discuz!<br />Version:|X1<br />[/table]</div>�á�|���ָ�ÿһ�У���������С�|���á�\\|�����棬�����á�\\n�����档</div>';
				break;
			case 'aud':
				//str = '<p class="pbn">�����������ļ���ַ:</p><p class="pbn"><input type="text" id="' + ctrlid + '_param_1" class="px" value="" style="width: 220px;" /></p><p class="xg2 pbn">֧�� wma mp3 ra rm �����ָ�ʽ<br />ʾ��: http://server/audio.wma</p>';
				//str = '<div id="music_search_bar"><input class="px" name="" title="�������������ֵĸ�����ַ" style="width: 220px;" value="" id="htt_music_input"/><a class="btn_sm_m pn pnc pns mtn"  href="javascript:void(0)" id="htt_music_bt" title="����"  onFocus="this.blur()" unselectable="on"><span>����</span></a><p class="xg2 pbn">������ȥ�����������ҵ����ϲ���ĸ��������Ƹ����������� <br> http://music.163.com/#/song?id=394718 ճ�������<br> ��������롱�����������ѷ���ø���</p></div>';
				str = '<div id="music_search_bar"><input class="px" name="" title="�������������ֵĸ�����ַ" style="width: 300px;" value="" id="htt_music_input"/><p class="xg2 pbn">������ȥ�����������ҵ����ϲ���ĸ��������Ƹ����������� <br> http://music.163.com/#/song?id=394718 ճ�������<br> ��������롱�����������ѷ���ø���</p><div class="pns mtn"><button type="submit" id="htt_music_bt" class="pn pnc"><strong>�ύ</strong></button></div></div>';
				break;
			case 'vid':
				str = '<p class="pbn">��������Ƶ222��ַ:</p><p class="pbn"><input type="text" value="" id="' + ctrlid + '_param_1" style="width: 220px;" class="px" /></p><p class="pbn">��: <input id="' + ctrlid + '_param_2" size="5" value="500" class="px" /> &nbsp; ��: <input id="' + ctrlid + '_param_3" size="5" value="375" class="px" /></p><p class="xg2 pbn">֧���ſᡢ������56����6����Ƶվ����Ƶ��ַ<br />֧�� wmv avi rmvb mov swf flv ����Ƶ��ʽ<br />ʾ��: http://server/movie.wmv</p>';
				break;
			case 'fls':
				str = '<p class="pbn">������ Flash �ļ���ַ:</p><p class="pbn"><input type="text" id="' + ctrlid + '_param_1" class="px" value="" style="width: 220px;" /></p><p class="pbn">��: <input id="' + ctrlid + '_param_2" size="5" value="" class="px" /> &nbsp; ��: <input id="' + ctrlid + '_param_3" size="5" value="" class="px" /></p><p class="xg2 pbn">֧�� swf flv �� Flash ��ַ<br />ʾ��: http://server/flash.swf</p>';
				break;
			case 'pasteword':
				stitle = '�� Word ճ������';
				str = '<p class="px" style="height:300px"><iframe id="' + ctrlid + '_param_1" frameborder="0" style="width:100%;height:100%" onload="this.contentWindow.document.body.style.width=\'550px\';this.contentWindow.document.body.contentEditable=true;this.contentWindow.document.body.focus();this.onload=null"></iframe></p><p class="xg2 pbn">��ͨ����ݼ�(Ctrl+V)�� Word �ļ��е�����ճ�����Ϸ�</p>';
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
						str += (promptlang[i - 1] ? promptlang[i - 1] : '������� ' + i + ' ������:') + '<br /><input type="text" id="' + ctrlid + '_param_' + i + '" style="width: 98%" value="" class="px" />' + (i < params ? '<br />' : '');
					}
				}
				break;
		}

		var menu = document.createElement('div');
		menu.id = ctrlid + '_menu';
		menu.style.display = 'none';
		menu.className = 'p_pof upf';
		//yuqj �������ֵĿ��
		if (tag == 'aud') {
			menu.style.width = musicmenuwidth + 'px';
		} else {
			menu.style.width = menuwidth + 'px';
		}
		if(menupos == '00') {
			menu.className = 'fwinmask';
			s = '<table width="100%" cellpadding="0" cellspacing="0" class="fwin"><tr><td class="t_l"></td><td class="t_c"></td><td class="t_r"></td></tr><tr><td class="m_l">&nbsp;&nbsp;</td><td class="m_c">'
				+ '<h3 class="flb"><em>' + stitle + '</em><span><a onclick="hideMenu(\'\', \'win\');return false;" class="flbc" href="javascript:;">�ر�</a></span></h3><div class="c">' + str + '</div>'
				+ '<p class="o pns"><button type="submit" id="' + ctrlid + '_submit" class="pn pnc"><strong>�ύ</strong></button></p>'
				+ '</td><td class="m_r"></td></tr><tr><td class="b_l"></td><td class="b_c"></td><td class="b_r"></td></tr></table>';
		} else {
			//yuqj �滻ԭ�����ֵ�����
			if(tag == 'aud'){
				s = '<div class="p_opt cl"><span class="y" style="margin:-10px -10px 0 0"><a onclick="hideMenu()" class="flbc" href="javascript:;">�ر�</a></span><div>' + str + '</div></div>';
			} else {
				s = '<div class="p_opt cl"><span class="y" style="margin:-10px -10px 0 0"><a onclick="hideMenu();return false;" class="flbc" href="javascript:;">�ر�</a></span><div>' + str + '</div><div class="pns mtn"><button type="submit" id="' + ctrlid + '_submit" class="pn pnc"><strong>�ύ</strong></button></div></div>';
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
    //����жϡ�������Ҫ�������ʾ��
    var reg = /^http:\/\/music.163.com\/#\/song\?id=(\d)+$/;
    var r = head.children[0].value.match(reg);
    if(r==null){
        alert('�Բ���������ĸ�ʽ����ȷ!');
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


/*����*/
function checkMusicCount(theform) {
		var message = wysiwyg ? html2bbcode(getEditorContents()) : (!theform.parseurloff.checked ? parseurl(theform.message.value) : theform.message.value);
		re = /[163](.*?)[\/163]/ig;
		var __currentMusicList = message.match(re);
		if ( __currentMusicList != null ){
			if ( __currentMusicList.length > 1 ) {
				showDialog('ֻ�������һ������!');
				return false;
			}
		}
		return true;
	}
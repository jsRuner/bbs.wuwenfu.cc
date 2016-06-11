$(function(){
	var mobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry/i.test(navigator.userAgent);
	var touchstart = "click";
	var touchend = mobile ? "touchend" : "mouseup";
	var touchmove = mobile ? "touchmove" : "mousemove";
	var pubFunction = null;
	var stageW = $(window).width();
	var stageH = $(window).height();
	var num = 0;
	var musicFirst = true;
	var pageUrl = location.href;
	var isWx = window.navigator.userAgent.toLowerCase().match(/MicroMessenger/i) == 'micromessenger';
	var pageAuthor = ZbBase.getParam('author');
	var shake = {
		config: {
			SHAKE_THRESHOLD: 800,
			STATIC_URL: "",
			last_update: 0,
			x: 0,
			y: 0,
			z: 0,
			last_x: 0,
			last_y: 0,
			last_z: 0,
			shake_num: 0,  //摇的数量
			send_status: 0,  //发送数据状态，0未发送，1正在发送，-1停
			draw_num: 0  //摇的次数 抽奖次数
		},
		getRequest: function () {
			var url = location.search; //获取url中"?"符后的字串
			var theRequest = new Object();
			if (url.indexOf("?") != -1) {
				var str = url.substr(1);
				strs = str.split("&");
				for (var i = 0; i < strs.length; i++) {
					theRequest[strs[i].split("=")[0]] = unescape(strs[i].split("=")[1]);
				}
			}
			return theRequest;
		},
		init: function () {
			this.bindEvent();
			//获取url参数
			this.urlParam = this.getRequest();
			this.sendData();
		},
		bindEvent: function () {
			var that = this;
			if (window.DeviceMotionEvent) {
				window.addEventListener('devicemotion', that.deviceMotionHandler, false);
			} else {
				alert('本设备不支持devicemotion事件');
			}
		},
		deviceMotionHandler: function (eventData) {
			var acceleration = eventData.accelerationIncludingGravity;
			var curTime = new Date().getTime();
			if ((curTime - shake.config.last_update) > 100) {
				var diffTime = curTime - shake.config.last_update;
				shake.config.last_update = curTime;
				shake.config.x = acceleration.x;
				shake.config.y = acceleration.y;
				shake.config.z = acceleration.z;
				var speed = Math.abs(shake.config.x + shake.config.y + shake.config.z - shake.config.last_x - shake.config.last_y - shake.config.last_z) / diffTime * 10000;
				var status = document.getElementById("status");
				if (speed > shake.config.SHAKE_THRESHOLD) {
					// 累加摇的次数
					shake.config.shake_num++;

					if (!shake.config.send_status) {
						shake.config.send_status = 1;
						setTimeout(shake.sendData, 600);
						//开始播放声音
					}
				}
				shake.config.last_x = shake.config.x;
				shake.config.last_y = shake.config.y;
				shake.config.last_z = shake.config.z;
			}
		},
		isLuck: false, //是否已中奖
		sendData: function () {
			var that = this;
			var t = new Date().getTime().toString();
			var num = shake.config.shake_num;
			shake.config.shake_num = 0;
			if (num && !shake.isLuck) {
				//如果摇的数量大于0提交服务器
				if(App.shakeNum <= 0){
					//摇了3次，禁止抽奖
					App.prizeResult();
				}else{
					App.lottery();
				}
				//App.prizeResult();
			}
		}
	};
	var stageW,stageH;
	var _isajaxing = 0;
	var App = {
		"pages":{
			"home":$(".page_home"),
			"car":$(".page_car"),
			"shake":$(".page_shake"),
			"prize":$(".page_prize"),
			"form":$(".page_form"),
			"done":$(".page_getdone")
		},
		browser:{
			isWx:navigator.userAgent.toLowerCase().match(/MicroMessenger/i) == "micromessenger"
		},
		selectedCar:null,
		selectedMoney:0,
		selectedLog:0,
		TimeLine:{
			pageCar:new TimelineMax()
		},
		prizeArr:[],
		prizeLog:[],
		prizeArrNum:[],
		prizeKeys:[],
		shakeNum:3,
		keysMoney:[0, 4999, 3000, 2000, 1000, 888, 666],
		init:function(){
			this.orientation();
			initMusic();
			this.resize();  //初始化页面，判断页面高度
			this.handle();  //添加事件
			this.addTimeLine();
			FastClick.attach(document.body);
		},
		/*
		* 检查来源，如果来自微信，那么直接跳转到第二页，也就是选择车型页
		* */
		checkAuthor:function(){
			if(pageAuthor == "wx"){
				App.startGame();
			}
		},
		orientation:function(){
			//alert(window.orientation);
			var stageW = $(window).width();
			var stageH = $(window).height();
			var resize = function(){
				if(window.orientation==180||window.orientation==0){
				}
				if(window.orientation==90||window.orientation==-90){
					//stageW = 1039;
					//stageH = 640;
				}
				$("body").width(stageW).height(stageH);
			};
			resize();
			window.addEventListener("onorientationchange" in window ? "orientationchange" : "resize", resize, false);
		},
		addTimeLine:function(){
			this.TimeLine.pageCar.add(TweenMax.from('.btn_h6',.5,{x:-stageW, y:-200, ease: Expo.easeOut}),0.1);
			this.TimeLine.pageCar.add(TweenMax.from('.btn_h7',.5,{x:-stageW, y:-200, ease: Expo.easeOut}),.3);
			this.TimeLine.pageCar.stop();
			TweenMax.set('.prize_txt', { scale:0});
		},
		resize:function(){
			if($(window).height()<920){
				$("body").addClass("smallMobile");
			}
		},
		handle:function(){
			$('.main').on(touchstart, function (e) {
				if (musicFirst && musicPath != '') {
					e.preventDefault();
					musicFirst = false;
				}
			});
			$(".btn_see_about").on("click",function(){
				$(".pop_about").fadeIn();
				trackEvent("btn_see_about");
			});
			$(".btn_return").on("click",function(){
				$(".pop_about").fadeOut();
				trackEvent("btn_about_return");
			});
			$(".return-home").on(touchstart,function(){
				location.reload();
				trackEvent("btn_return_home");
			});
			$(".btn_get").on(touchstart,function(){
				App.startGame();
				trackEvent("btn_start_game");
				return false;
			});
			$(".gl").on(touchstart,function(){
				trackEvent("page_car_btn_gonglue");
				setTimeout(function(){
					location.href = "http://club.haval.com.cn/forum.php?mod=viewthread&tid=25275";
				}, 200);
			});
			$(".btn_xiu").on(touchstart,function(){
				trackEvent("page_done_btn_gonglue");
				setTimeout(function(){
					location.href = "http://club.haval.com.cn/forum.php?mod=viewthread&tid=25275";
				}, 200);
			});
		},
		startGame:function(){
			App.TimeLine.pageCar.play();
			App.movePage(App.pages.home,App.pages.car, function(){
				App.goCarPageCallback();
			});
			App.gamesatrt();
			if(musicFirst){
				var mySound = $('#media')[0];
				mySound.play();
				$(".musicicon").addClass("musicrotate");
				musicFirst = false;
			}
		},
		savegame:function(){
			var pm = {"point":0};
			pm['set[k]'] = App.prizeKeys.join(',');
			pm['set[client]'] = this.browser.isWx ? "wx" : "m";
			$.post(API.URI.savegame, pm, function(d){
				if(d.status==1){
					//alert('保存成功');
				}else{
					//showJsTip(d.info);
				}
			},'json');
		},
		gamesatrt:function(){
			$.get(API.URI.gamesatrt, {}, function(d){
				//console.log(d);
			},'json');
		},
		lottery:function(){
			if(_isajaxing==1){showJsWait();return false;}_isajaxing = 1;
			var self = this;
			$.ajax({
				url:API.URI.lottery,
				type:"post",
				data:{car:App.selectedCar},
				dataType:"json",
				timeout:5000,
				success:function(d){
					_isajaxing=0;
					if(d.status==1 && d.prize_id>0){
						//alert('奖品ID:'+d.prize_id+' 奖品标记自定义['+d.keys+']'+' 券号['+d.ticket+']'+'奖品日志ID:'+d.rid);
						//$("#v-savewin :input[name='rid']").val(d.rid);
						$(".page_shake").removeClass("noprize").addClass("prizing");
						self.prizeArr.push(App.keysMoney[d.keys%10]);  //中奖money
						self.prizeKeys.push(d.keys);  //中奖keys
						self.prizeLog.push(d.rid);
						var datas = {money:App.keysMoney[Number(d.keys%10)], prize_id: d.prize_id};
						App.setLottery(datas); //处理返回结果
					}else if(d.fg == -202){
						$(".page_shake").addClass('prized');
					}else{
						self.prizeArrNum.push(0);  //保存具体的面额
						App.setLottery({"prize_id":0});
					}
					trackEvent("lottery_num_"+(3-App.shakeNum+1));
				},
				error:function(xhr, err, e){
					if(err == "timeout"){
						//请求超时
						showJsTip("摇奖人数太多啦，请稍后重试哦~", function(){
							location.reload();
						});

					}
					trackEvent("lottery_error_"+err);
				}
			})
		},
		setLottery:function(datas){
			App.shakeNum--;
			var tpl = datas.money > 0 ? "￥<strong>"+ datas.money +"</strong>" : "<strong>系统繁忙</strong>";
			$("#shakeNum").text(App.shakeNum);  //设置还可以摇几次
			if(datas.prize_id >0){
				$("#resultMoney").html(tpl);
				$("#titleMoney").html(datas.money+"元");
				TweenMax.to('.shake_img',.3, { alpha:0, onComplete:function(){}});
				TweenMax.set('.prize_txt', { scale:0});
				TweenMax.to('.prize_txt',.5, { scale:1, onComplete:function(){ }});
			}else {
				$(".page_shake").addClass("noprize");
			}
			if(this.prizeArr.length == 3){
				shake.isLuck = true;
				setTimeout(function(){
					App.prizeResult();
				}, 1500);
			}else{
				setTimeout(function(){
					shake.config.send_status = 0;
				}, 1500);
			}

		},
		//去往选择车型页
		goCarPageCallback:function(){
			TweenMax.to(App.pages.car.find(".bg"), 4, {scale:1.3, ease:Linear.easeNone});
			$(".btn_h6, .btn_h7").on(touchstart,function(){
				App.selectedCar = $(this).attr("data-type");
				if(App.selectedCar == "hf6"){
					$("#v-car")[0].selectedIndex = 1;
					$("#v-car").change();
					$("#car_hidden").val("哈弗H6Coupe1.5T");
				}else{
					$("#car_hidden").val("哈弗H7");
				}
				//去往摇奖页
				API.DATA.remain = "00000000";
				$("#shengyuH").text(API.DATA.remain);   //显示剩余红包
				$("#shakeNum").text(0);
				App.movePage(App.pages.car, App.pages.shake, function(){
					App.goShakePageCallback();
				});
				trackEvent("btn_selected_"+App.selectedCar);
				return false;
			});
			trackEvent("page_car-PV");

		},
		//去往摇一摇页面
		//
		goShakePageCallback:function(){
			TweenMax.to(App.pages.shake.find(".bg"), 4, {scale:1.3, ease:Linear.easeNone});
			showJsTip(actOverTxt||"活动结束，请尽快到店使用红包！", function(){
				location.reload();
			});
			//shake.init();
			trackEvent("page_shake-PV");
		},
		//三次摇奖结束
		prizeResult:function(){
			shake.config.send_status = 1;
			trackEvent("lottery_end");
			var noprize = 0;
			$(".jp_ban p").each(function(i){
				if(App.prizeArr[i] > 0){
					var tpl = "￥<strong>"+App.prizeArr[i] +"</strong>";
				}else{
					$(this).parent().addClass("noprize");
					var tpl = "";
					noprize++;
				}
				$(this).html(tpl);
				$(this).parents(".quan").attr("data-prizelog", App.prizeLog[i]).attr("data-money", App.prizeArr[i]);
			});
			if(noprize >=3){
				$(".jp_ban").addClass("noprize");
			}
			trackEvent("prize_["+App.prizeArr.join(",")+"]");
			App.movePage(App.pages.shake, App.pages.prize, function(){
				TweenMax.to(App.pages.prize.find(".bg"), 4, {scale:1.3, ease:Linear.easeNone});
				$(".page_prize .quan").addClass("on").not(".noprize").on(touchstart,function(){
					App.selectedLog = $(this).attr("data-prizelog");
					App.selectedMoney = $(this).attr("data-money");
					$("#v-savewin :input[name='rid']").val(App.selectedLog);
					App.goFormPageCallback();
					App.savegame();
					trackEvent("prizeLog"+App.selectedLog);
					return false;
				});
			});
			trackEvent("page_prize-PV");
		},
		//去往表单页
		goFormPageCallback:function(){
			$(".btn_submit").on(touchstart,function(){
				App.saveuser();
				return false;
			});
			App.movePage(App.pages.prize, App.pages.form, function(){
				/*TweenMax.to(App.pages.form.find(".bg"), 4, {scale:1.3, ease:Linear.easeNone});*/
			});
			trackEvent("page_form-PV");
		},
		//去往最终页
		goDonePageCallback:function(txt){
			$("#doneMoney").text(this.selectedMoney);
			$("#ticket_txt").text(txt);
			App.movePage(App.pages.form, App.pages.done, function(){
				TweenMax.to(App.pages.form.find(".bg"), 4, {scale:1.3, ease:Linear.easeNone});
			});
			trackEvent("page_done-PV");
		},
		//提交用户信息
		saveuser:function(){
			if($("#v-savewin :input[name='name']").val()==""){
				showJsTip("请输入您的姓名");
				return;
			}
			var mobile = $("#v-savewin :input[name='mobile']").val();
			if(mobile==""||mobile.length!=11){
				showJsTip("请输入您的手机");
				return;
			}
			var province = $('#v-province option:selected').val();
			var city = $('#v-city option:selected').val();
			var dealer = $('#v-dealer option:selected').val();
			if(province == ""){
				showJsTip("请选择省份");
				return;
			}
			if(city == ""){
				showJsTip("请选择市");
				return;
			}
			if(dealer == ""){
				showJsTip("请选择门店");
				return;
			}
			var pm = $("#v-savewin").serialize();
			if(_isajaxing==2){return false;}
			if(_isajaxing==1){showJsWait();_isajaxing = 2; return false;}_isajaxing = 1;
			$.post(API.URI.savewin, pm, function(d){
				_isajaxing=0;
				if(d.status==1){
					if(d.rid > 0){
						$.get(API.URI.sendsms, {id:d.rid},function(d){});
					}
					//成功用户信息
					App.goDonePageCallback(d.ticket || "XX");
					trackEvent("submit_ok"+d.ticket);
					/*showJsTip(d.info, function(){
					 location.reload();
					 });*/
				}else{
					//异常处理
					//App.goDonePageCallback();
					showJsTip(d.info);
				}
			},'json');
		},
		movePage:function(before,after, fn){
			TweenMax.to(before, .5, {left:-stageW, ease: Expo.easeOut});
			TweenMax.to(after, .5, {left:0, ease: Expo.easeOut, onComplete:function(){
				if(fn)fn();
			}});
		},
		startLoadImg:function(fn){
			var loadingPath = '';
			var manifest = [
				{src:"source/plugin/htt_greatwall/template/m/images/120.jpg"},
				{src:"source/plugin/htt_greatwall/template/m/images/about_text.png"},
				{src:"source/plugin/htt_greatwall/template/m/images/btn_about.png"},
				{src:"source/plugin/htt_greatwall/template/m/images/btn_BTN.png"},
				{src:"source/plugin/htt_greatwall/template/m/images/btn_return.png"},
				{src:"source/plugin/htt_greatwall/template/m/images/btn_submit.png"},
				{src:"source/plugin/htt_greatwall/template/m/images/home_bg.jpg"},
				{src:"source/plugin/htt_greatwall/template/m/images/home_title.png"},
				{src:"source/plugin/htt_greatwall/template/m/images/logo.png"},
				{src:"source/plugin/htt_greatwall/template/m/images/p1_car1.png"},
				{src:"source/plugin/htt_greatwall/template/m/images/p1_car2.png"},
				{src:"source/plugin/htt_greatwall/template/m/images/p1_hongbao.png"},
				{src:"source/plugin/htt_greatwall/template/m/images/p1_yb.png"},
				{src:"source/plugin/htt_greatwall/template/m/images/p2_bg.jpg"},
				{src:"source/plugin/htt_greatwall/template/m/images/p2_car1.png"},
				{src:"source/plugin/htt_greatwall/template/m/images/p2_car2.png"},
				{src:"source/plugin/htt_greatwall/template/m/images/p2_carbg.png"},
				{src:"source/plugin/htt_greatwall/template/m/images/p2_t1.png"},
				{src:"source/plugin/htt_greatwall/template/m/images/p2_t2.png"},
				{src:"source/plugin/htt_greatwall/template/m/images/p3_hand.png"},
				{src:"source/plugin/htt_greatwall/template/m/images/p3_hand_wx.png"},
				{src:"source/plugin/htt_greatwall/template/m/images/p3_qt.png"},
				{src:"source/plugin/htt_greatwall/template/m/images/p4_s_bg.png"},
				{src:"source/plugin/htt_greatwall/template/m/images/page2_title.png"},
				{src:"source/plugin/htt_greatwall/template/m/images/pageform_title.png"},
				{src:"source/plugin/htt_greatwall/template/m/images/pdown-b.png"},
				{src:"source/plugin/htt_greatwall/template/m/images/prize_bg.png"},
				{src:"source/plugin/htt_greatwall/template/m/images/radio.png"},
				{src:"source/plugin/htt_greatwall/template/m/images/sy.png"},
				{src:"source/plugin/htt_greatwall/template/m/images/sy-wx.png"},
				{src:"source/plugin/htt_greatwall/template/m/images/yiy.png"},
				{src:"source/plugin/htt_greatwall/template/m/images/radio_hover.png"},
				{src:"source/plugin/htt_greatwall/template/m/images/selectbg.png"},
				{src:"source/plugin/htt_greatwall/template/m/images/smcar.png"}
			];
			//loading
			function handleOverallProgress(event) {
				var num = Math.ceil(event.loaded * 100);
				$("#loadCar").css({left:(470-111)*event.loaded});
				$(".loadtxt").text(num+"%");
			}
			function handleOverallComplete(event) {
				$(".load").fadeOut();
				$(".main").css({opacity:1});
				$('#media')[0].play();
				trackEvent("loading_done");
				App.checkAuthor();
			}
			var loader = new createjs.LoadQueue(false);
			loader.addEventListener("progress", handleOverallProgress);
			loader.addEventListener("complete", handleOverallComplete);
			loader.setMaxConnections(1);
			loader.loadManifest(manifest);
		}
	};
	var musicPath = 'source/plugin/htt_greatwall/template/m/images/bg.mp3';//是否含有背景音乐，有就传路径，没有就为'';
//初始化音乐，如果musicPath=''，相当于什么都没做
	function initMusic(){
		if(musicPath!=''){
			$('body').append('<div class="musicicon musicrotate"></div><audio id="media" loop preload="preload" src="'+musicPath+'"></audio>');
			var mySound = $('#media')[0];
			$('.musicicon').on(touchstart,function(){
				if($(this).hasClass('musicrotate')){
					mySound.pause();
					$(this).removeClass('musicrotate');
				}else{
					mySound.play();
					$(this).addClass('musicrotate');
				}
			})
		}
	}
//耐心等待函数 防止多次重复提交
	function showJsWait(){
		showJsTip('提交中，请等待哦~');
	}
	function showJsTip(str){
		alert(str);
	}

	$(function(){
		App.init();
		App.startLoadImg();
		trackEvent("loading_start");
	});

	function showJsTip(s, fn){
		//alert(s);
		$(".weui_dialog_title").hide();
		$(".weui_dialog_content").html(s);
		$("#dialog2").show();
		$(".weui_btn_dialog").one(touchstart,function(){
			$("#dialog2").hide();
			if(fn)fn();
			return false;
		});
	}

	//初始化媒体来源
	$("#v-savewin :input[name='ext[media]']").val( getParam('media') );
});

	var stageH = $(window).height();
	var stageW = $(window).width();
	var _isajaxing = 0;
	var prize_arr=[];
	var prize_logarr=[];
	var selectcar = '';
	var isIpad = /iPad/i.test(navigator.userAgent);

	$(window).resize(function(){
		checkWindow();
	});
	var prizeMoney = 0;
	checkWindow();
	function checkWindow(){
		if(!isIpad){
			if((stageH-121)>860) { $(".content").height(stageH-121); }
		}else{
			//$("html").width(1350);
			//$(".content").height(stageH-121);
		}
	}
	var change = function(){
		$('.con-st-page1 img').click(function(){
			gamesatrt();
			$('#page1').fadeOut();
			$('#page3').show();
			trackEvent('btn_startgarm');
		});
		$('.con-gz img').click(function(){
			$('#page1').fadeOut();
			$('#page2').fadeIn();
			$.initScroll();
			trackEvent('btn_rule');
		});
		//点击开始
		$('.con-st-page2 img').click(function(){
			gamesatrt();
			$('#page2').fadeOut();
			$('#page3').fadeIn();
			trackEvent('btn_startgarm');
		});
		$('.con-st-page3 img').click(function(){
			if( (!$('.chword-til1 span').hasClass('data-check')) && (!$('.chword-til2 span').hasClass('data-check')) ){
				return false;
			}
			else{
				if($('#check-car1').prop('checked')){
					selectcar = 'hf6';
				}else if($('#check-car2').prop('checked')){
					selectcar = 'hf7';
				}

				$('#page3').fadeOut();

				$('#page4').fadeIn();

				//活动结束了
				var d = dialog({
					content: '活动结束，请尽快到店使用红包！'
				});
				d.showModal();
				$('.roll-word span').html(0);
				$('.prize-total-num').html("00000000");
			}
			trackEvent('btn_active');
		});
		$('.hb-ling').click(function(){

			savegame();
			var rid = $(this).index(".choose-hb .hb-ling");
			$("#v-savewin :input[name='rid']").val(prize_logarr[rid]);
			$('#page5').fadeOut();
			$('#page6').fadeIn();
			prizeMoney = $(this).prev().text();
			
			trackEvent('btn_getprize');
		});
		$('#submit').click(function(){
		    if($("#v-savewin :input[name='name']").val()==""){
		        showJsTip("请输入您的姓名");
		        return;
		    }
		    var mobile = $("#v-savewin :input[name='mobile']").val();
		    if(mobile==""||mobile.length!=11){
		        showJsTip("请输入您的手机号码");
		        return;
		    }else if(mobile.length!=11){
		        showJsTip("请输入正确的手机号码");
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
			if(_isajaxing==1){showJsWait();return false;}_isajaxing = 1;
			$.post(API.URI.savewin, pm, function(d){
				_isajaxing=0;
				if(d.status==1){
					//showJsTip('您的信息提交成功！');
					$('.cjhb-xulie').html(d.ticket);
					$('.cjhb1').show();
					if(d.rid > 0){
						$.get(API.URI.sendsms, {id:d.rid},function(d){});
					}
					$(".cjhb-innumber").text(d.money);//prizeMoney
					trackEvent('btn_submit');
				}else{
					showJsTip(d.info);
				}
			},'json');
		});
		$('.cjhb-img img').click(function(){
		  	$('.cjhb1').hide();
		  	$('#page6').hide();
		  	$('#page1').show();
		  	location.reload();
			trackEvent('btn_backhome');
		});
	}();


	var page3=function(){
		var ckeck=function(){
			if($('#check-car1').prop('checked')){
				$('#check-car1').prop('checked',false);
			}
			if($('#check-car2').prop('checked')){
				$('#check-car2').prop('checked',false);
			}
			$('.con-st-page3 img').attr('src','images/next.png');
		};
		$('.choose-img1 img').bind('click',function(){
			ckeck();
			$('.check-ch1 img').show();
			$('.check-ch2 img').hide();
			$('.chword-til1 span').addClass('data-check');
			$('.chword-til2 span').removeClass('data-check');
			carcheck();
			trackEvent('btn_checkcar1_one');
		});
		$('.choose-img2 img').bind('click',function(){
			ckeck();
			$('.check-ch1 img').hide();
			$('.check-ch2 img').show();
			$('.chword-til1 span').removeClass('data-check');
			$('.chword-til2 span').addClass('data-check');
			carcheck();
			trackEvent('btn_checkcar2_one');
		});
		$('.check-ch1').bind('click',function(){
			ckeck();
			$('.check-ch1 img').show();
			$('.check-ch2 img').hide();
			$('.chword-til1 span').addClass('data-check');
			$('.chword-til2 span').removeClass('data-check');
			carcheck();
			trackEvent('btn_checkcar1_two');
		});
		$('.check-ch2').bind('click',function(){
			ckeck();
			$('.check-ch1 img').hide();
			$('.check-ch2 img').show();
			$('.chword-til1 span').removeClass('data-check');
			$('.chword-til2 span').addClass('data-check');
			carcheck();
			trackEvent('btn_checkcar2_two');
		});
		$('.check-carspan1').bind('click',function(){
			ckeck();
			$('.check-ch1 img').show();
			$('.check-ch2 img').hide();
			$('.chword-til1 span').addClass('data-check');
			$('.chword-til2 span').removeClass('data-check');
			carcheck();
			trackEvent('btn_checkcar1_three');
		});
		$('.check-carspan2').bind('click',function(){
			ckeck();
			$('.check-ch1 img').hide();
			$('.check-ch2 img').show();
			$('.chword-til1 span').removeClass('data-check');
			$('.chword-til2 span').addClass('data-check');
			carcheck();
			trackEvent('btn_checkcar2_three');
		});

	}();

	var carcheck=function(){
		var carch=$('.data-check').html();
		$('#v-car').parent().children().first().html(carch);

		var opti=document.getElementById('v-car').options;
		for(var i=0,len=opti.length;i<2;i++){
			opti[i].index=i;
			opti[i].removeAttribute('selected');
			if( opti[i].text == carch ){
				opti[i].setAttribute('selected','selected');
			}
		};
		$("#v-car").change();
		
	};


	$('select').each(function(i){
		$(this).change(function(){
			$(this).parent().children().first().html( $(this).find("option:selected").text() );
		})
		
	});
	var page4=function(){
		$("#go").bind('click',function(){
			return;
			if(_isajaxing==1){return false;}_isajaxing = 1;//showJsWait();
			$.post(API.URI.lottery, {"car":selectcar}, function(d){
				if(d.status==1 && d.prize_id>0){
					prize_arr.push( d.keys );
					prize_logarr.push( d.rid );
					lottery.goto(parseInt(d.keys));
				}else{
					if(d.fg==-202){
						_isajaxing =0;
						showJsTip(d.info);
					}
					else{
						//lottery.goto(0);
						showJsTip('没有奖品了，谢谢参与！');
					}
				}
			},'json');
			trackEvent('btn_prize');
		});
		$('.cjhb-p img').click(function(){
			$('.cjhb').hide();
		    if($('.roll-word span').html()==0){
		    	$("#go").unbind('click',lottery.reset);
		    	$('#page4').fadeOut();
				$('#page5').fadeIn();
		    }
			trackEvent('btn_backgetprize');
		})
	}();

	$('.prize-total .prize-total-num').html(API.DATA.remain);


	/*=============*/
	//耐心等待函数 防止多次重复提交
	function showJsWait(){
		showJsTip('嗨，程序君开小差了...请再试一次~');
	}

	function gamesatrt(){
		$.get(API.URI.gamesatrt, {}, function(d){
			//console.log(d);
		},'json');
	}
	function savegame(){
		var pm = {"point":0};
		pm['set[k]'] = prize_arr.join(',');
		$.post(API.URI.savegame, pm, function(d){
		},'json');
	}


	function showJsTip(s){
	    var d = dialog({
	        content: s
	    });
	    d.show();
	    setTimeout(function () {
	        d.close().remove();
	    }, 2000);
	};
$(function(){
	//初始化媒体来源
	$("#v-savewin :input[name='ext[media]']").val( getParam('media') );
});




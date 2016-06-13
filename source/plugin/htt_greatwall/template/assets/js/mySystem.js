



var lottery={
    num:3,
    index : 1, //���
    speed : 400, //��ʼ�ٶ�
    roll:0, //��ʱ��id
    cycle : 1, //���ܵ�Ȧ��
    times : 2, //�����ܼ�Ȧ
    prize : -1, //�н�����
    btn:0,
    keytoprize: function(keys){
        var karr = [[0],[1],[4,12],[6,9],[2,11],[3,8,7],[5,10]];//
        var tarr = karr[keys];
        var n = Math.floor(tarr.length*Math.random());
        if(tarr[n] == 0){
            showJsTip('û�н�Ʒ�ˣ�лл���룡');
        }else{
            lottery.prize = tarr[n];
            lottery.reset();
        }
    },
    run : function () {
        lottery.index += 1;
        var before = lottery.index < 1 ? 12 : lottery.index - 1;
        lottery.index = lottery.index > 12 ? 1 : lottery.index;
        $(".roll-" + lottery.index).addClass("active");
        $(".roll-" + before).removeClass("active");
        //�����ӿ�Ĺ���
        lottery.upSpeed();
        lottery.downSpeed();
    },
    //����
    upSpeed : function () {
        if (lottery.cycle < 2 && lottery.speed > 100) {
            lottery.speed -= lottery.index * 8;
            lottery.stop();
            lottery.start();
        }
    },
    //����
    downSpeed:function () {
        if (lottery.index == 12) {
            lottery.cycle += 1;
        }
        if (lottery.cycle > lottery.times - 1 && lottery.speed < 400) {
            lottery.speed += 20;
            lottery.stop();
            lottery.start();
        }
        if (lottery.cycle > lottery.times && lottery.index == lottery.prize) {

            lottery.stop();
            lottery.showPrize();
        }

    },
    //��ֹͣ����ʾ��� ��ť��ʾ����
    showPrize:function(){
        setTimeout(function(){
            //alert("�н��ţ�"+lottery.prize);
            $('.cjhb').show();
            $('.cjhb-innum').html( $(".roll-" + lottery.index).find('.roll-num').html() );

            $('.hb-num').eq(2-lottery.num).html( $(".roll-" + lottery.index).find('.roll-num').html() );

            /*lottery.index =1;
             $(".roll").removeClass("active");
             $(".roll-" + lottery.index).addClass("active");*/
            _isajaxing=0;
        },700);
    },

    //���¿�ʼ
    reset : function () {
        //$("#go").unbind('click',lottery.reset);
        lottery.num-=1;
        $('.roll-word span').html(lottery.num);
        $('.cjhb-words span').html(lottery.num);
        if(lottery.num==0){
            $('.cjhb-p img').attr('src','source/plugin/htt_greatwall/template/assets/images/ling.png');
        }
        $('.cjhb-in').removeClass('cjhb-no');
        $('.cjhb-innum').show();
        lottery.btn=$(this);
        lottery.speed = 400;
        lottery.cycle = 0;
        //lottery.prize = Math.floor(Math.random() * 12) + 1;
        lottery.run();
    },
    start : function () {
        lottery.roll = setInterval(lottery.run, lottery.speed);
    },

    stop : function () {
        clearInterval(lottery.roll);
    },
    goto : function (n){
        //n 0-���н� 1-һ�Ƚ� 2---6  0-6 11-16
        lottery.keytoprize(n%10);
    }

};



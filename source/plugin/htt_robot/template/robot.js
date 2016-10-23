/**
 * Created by Administrator on 2016/4/8.
 */
//jq = jQuery;


jq(window).load(function(){

    var robot_close =  jq('#robot_container_closed'); //���������
    //var close_btn =  jq('#close_btn'); //�رհ�ť
    var close_btn =  jq('#robot_container_open .title .headBtn .zhichiClose'); //�رհ�ť

    var robot_open =  jq('#robot_container_open');

    //var send_btn = jq('#send_button') //���Ͱ�ť
    var send_btn = jq('#sendBtn') //���Ͱ�ť

    var pull_btn = jq('#pullBtn') //���Ұ�ť

    //var send_input = jq('.do_area input') //����
    var send_input = jq('#inputMsg') //����

    var msg_list = jq('.wechat') //�����б�


    // var host = window.location.host; //����
    var host = jq('#discuzurl').val(); //����

    var robot_name = jq('#robot_container_open > div.title > span').text();

    //��ȡ��Ҫ�����֡�
    var close_text = jq('#close_text').val();
    var error_empty = jq('#error_empty').val();
    var mename = jq('#me').val();
    var robot_bug = jq('#robot_bug').val();
    var please_input = jq('#please_input').val();

    var formhashxx = jq('#formhash').val();

    var robot_status = getCookie('robot_status'); //״̬����cookie��ȡһ�Ρ�1�ǿ�����2�ǹرա�

    var robot_pointer_x = getCookie('robot_pointer_x'); //��������λ��
    var robot_pointer_y = getCookie('robot_pointer_y'); //��������λ��

    var win_h = window.innerHeight||document.documentElement.clientHeight;
    var win_w = window.innerWidth||document.documentElement.clientWidth;

    //console.log(win_w+'_'+win_h);
    //console.log(robot_pointer_x+'_'+robot_pointer_y);



    if(robot_pointer_x && robot_pointer_y && robot_pointer_x<=win_w && robot_pointer_y<=win_h){
        robot_close.attr('style','position:absolute;left:'+robot_pointer_x+'px;top:'+robot_pointer_y+'px;');
    }


    if(robot_status == 1){
        robot_open.show();
        robot_close.hide();
    }


    //�رջ����ˡ�
    close_btn.bind('click',function(){

        // var xx = confirm(close_text)
        if (true) {
            robot_open.hide();
            robot_close.show();
            //����cookie
            setCookie('robot_status',2)
        }
       
    })

    //���������ʱ����������ǳ�ʼ�������ݡ������һ�Ρ�
    send_input.bind('focus',function(){
        if(send_input.val() == please_input ){
            send_input.val('')
        }
    })


    //ִ�������߼���
    function sendmsg (){
        var msg = send_input.val();



        if(msg ==''){
            alert(error_empty)
            return;
        }else{
            send_input.val('');
        }

        var sh = msg_list[0].scrollHeight;

        //����һ��li�����뵽�б��С�
        var me = ' <li class="me"> <span>'+mename+'</span> <div>'+msg+'</div></li>';
        msg_list.append(me);

        //ajax�����̨��
        jq.ajax({
            type: 'GET',
            url: host+'/plugin.php?id=htt_robot:robot',
            data: {msg:msg,formhash:formhashxx},
            success: function(data){

                //�ж��·������͡�Ц����
                if(data.msg.indexOf("content") > 0 ){
                    var obj = eval('(' + data.msg + ')');
                    data.msg = obj.content;
                }
                //�������ϵ���ǩ�ӿ�
                if(data.msg.indexOf("type") > 0 ){
                    var obj = eval('(' + data.msg + ')');
                    if(obj.type==moli_datas[0]){
                        console.log(1);
                        data.msg = '['+moli_datas[3]+':]'+obj.qianyu+'\<br\>['+moli_datas[4]+':]'+obj.zhushi+'\<br\>['+moli_datas[5]+':]'+obj.jieqian+'\<br\>['+moli_datas[6]+':]'+obj.jieshuo;
                    }
                    if(obj.type==moli_datas[1]){
                        console.log(2);
                        data.msg = '['+moli_datas[3]+':]'+obj.shiyi+'\<br\>['+moli_datas[4]+':]'+obj.zhushi+'\<br\>['+moli_datas[5]+':]'+obj.jieqian+'\<br\>['+moli_datas[6]+':]'+obj.baihua;
                    }
                    if(obj.type==moli_datas[2]){
                        console.log(3);
                        data.msg = '['+moli_datas[3]+':]'+obj.qianyu+'\<br\>['+moli_datas[4]+':]'+obj.shiyi+'\<br\>['+moli_datas[5]+':]'+obj.jieqian;
                    }
                }
                var robot = ' <li class="robot"> <span>'+robot_name+'</span> <div>'+data.msg+'</div></li>';
                msg_list.append(robot);
                console.log(msg_list[0].scrollTop)
                console.log(msg_list[0].scrollHeight) //�������ĸ߶ȡ�����������١����޸ľ��붥��
                var eh = msg_list[0].scrollHeight;
                msg_list[0].scrollTop = msg_list[0].scrollTop+(eh-sh);
            },
            dataType: 'json',
            error:function(XMLHttpRequest, textStatus, errorThrown){
                var robot = ' <li class="robot"> <span>'+robot_name+'</span> <div>'+robot_bug+'</div></li>';
                msg_list.append(robot);
                var eh = msg_list[0].scrollHeight;
                msg_list[0].scrollTop = msg_list[0].scrollTop+(eh-sh);

            },
            complete:function(XMLHttpRequest, textStatus){
                //������ɺ󡣿�����ť��
                send_btn.attr('disabled',false)
                var eh = msg_list[0].scrollHeight;
                msg_list[0].scrollTop = msg_list[0].scrollTop+(eh-sh);

            }
        });
    }



    //������Ͱ�ť�¼������ul�С����ȴ������ajaj����ȴ������
    send_btn.bind('click',sendmsg);

    pull_btn.bind('click',function(){
        send_input.val('click me');
        sendmsg();
    })

    document.onkeydown=function(event){
        var e = event || window.event || arguments.callee.caller.arguments[0];
        if(e && e.keyCode==27){ // �� Esc
            //Ҫ��������
        }
        if(e && e.keyCode==113){ // �� F2
            //Ҫ��������
        }
        if(e && e.keyCode==13){ // enter ��
            //Ҫ��������
            if(!robot_open.is(":hidden")){
                sendmsg();
            }
        }
    };




});
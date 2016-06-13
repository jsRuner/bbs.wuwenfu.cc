<?php
/**
 * 活动接口。返回需要的参数。
 */
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
error_reporting(E_ALL);
defined('DATA_PATH')    or define('DATA_PATH',      DISCUZ_ROOT.'/data/plugindata/');
define('IS_AJAX',       ((isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') || !empty($_POST['ajax']) || !empty($_GET['ajax'])) ? true : false);


global $_G;
$op = $_GET['op'];



switch($op){
    case 'initdata': //初始化数据
        //登录与没有登录.获取接口,需要携带project=1的参数 需要传递当前登陆者的数据。
        $pm['project'] = 1;
        $pm = http_build_query($pm);
        $uri['gamesatrt']  = "/plugin.php?id=htt_greatwall:greatwall_api&op=gamesatrt&".$pm;
        $uri['savegame']  = "/plugin.php?id=htt_greatwall:greatwall_api&op=savegame&".$pm;
        $uri['lottery']  = "/plugin.php?id=htt_greatwall:greatwall_api&op=lottery&".$pm;
        $uri['savewin']  = "/plugin.php?id=htt_greatwall:greatwall_api&op=savewin&".$pm;
        $uri['sendsms']  = "/plugin.php?id=htt_greatwall:greatwall_api&op=sendsms&".$pm;

        $data['URI'] = $uri;

        $_G['greatwall_userdata'] = array(
            'id'=>$_G['uid'],
            'gstime'=>0,
            'plog'=>array()
        );

        $userinfo['LG_USER'] = array('id'=>$_G['uid']) ;
        $userinfo['remain'] = time() ; //原来是项目的奖品钱

        $data['DATA'] = $userinfo;


        $initda = $data;
        header('Content-Type:application/javascript; charset=utf-8');
        $s[] = 'var API={};';
        foreach ($initda as $k=>$v) $s[] = 'API.'.$k.'='.json_encode($v).';';
        exit(join("\r\n",$s));
        break;

    case 'gamesatrt': //设置游戏开始时间
        $data = array();
        if(!IS_AJAX){
            $data['status'] = -1;
            $data['info'] = 'need ajax';
        }else{

            $_G['greatwall_userdata']['gstime'] = time();


            $data['status'] = 1;
            $data['info'] = 'ok';
            $data['url'] = '';
        }
        echo json_encode($data);
        break;
    case 'savegame': //领取红包
                     //{"status":1,"bi":100,"count":"216","info":"ok"}
        //比较时间。todo 读取数据库
        $projectinfo = array(
            'id'=>1,
            'start_time'=>TIMESTAMP+10,
            'end_date'=>TIMESTAMP+100,
        );
        if($projectinfo['end_date'] < TIMESTAMP){
            $r['status']=0;
            $r['fg']=-404;
            $r['info']='活动已截止，谢谢您的参与！';
            echo json_encode($r);
            exit();
        }

        $member = $_G['greatwall_userdata'];
        $member_id = $member['id'];
        $point = intval($_POST['point']);
        $compare = intval($_POST['compare']);
        $set = $_POST['set'];
        if(!is_array($set)){
            $set = array();
        }

        $set['started'] = date('Y-m-d H:i:s',$member['gstime']);
        $set['ip'] = $_G['clientip'];

        $configs = empty($set)?null: serialize($set);

//        $map['project_id'] = $projectinfo['id'];
        $search = ' AND project_id ='.$projectinfo['id'];
        $count = C::t('#htt_greatwall#game_log')->count_by_search($search);

        $search = ' AND point < '.$point;
        $t = C::t('#htt_greatwall#game_log')->count_by_search($search);

        $bi = $count==0?100:intval($t/$count*100);

        if($compare == 0){
            $bi = 100 -$bi;
        }

        $gda = array(
            'member_id'=>$member_id,
            'to_member_id'=>$member_id,
            'point'=>$point,
            'config'=>$configs,
            'project_id'=>$projectinfo['id'],
            'created'=>TIMESTAMP,
        );

        C::t('#htt_greatwall#game_log')->insert($gda);

        $data = array();
        $data['status'] = 1;
        $data['info'] = 'ok';
        $data['bi'] = $bi;
        $data['count'] = $count;
        echo json_encode($data);
        break;
    case 'lottery'://抽奖
        // {"status":1,"info":"ok","prize_id":"46","rid":"3574","keys":"6","dnum":0,"fg":1,"ticket":""}
        $r = array('status'=>1,'info'=>'系统繁忙，请稍后再试','prize_id'=>0,'rid'=>0,'keys'=>0,'dnum'=>0,'fg'=>0);
        $debug = intval($_GET['debug']);
        $car = $_POST['car'];

        if($car=='') $car = 'hf6';

        if($debug) $r['debug'] = array();

        $projectinfo = array(
            'id'=>1,
            'start_time'=>TIMESTAMP+10,
            'end_date'=>TIMESTAMP+100,
        );
        if($projectinfo['end_date'] < TIMESTAMP){
            $r['status']=0;
            $r['fg']=-404;
            $r['info']='活动已截止，谢谢您的参与！';
            echo json_encode($r);
            exit();
        }
        //时间限制 游戏时间设置
        //$hour = intval(date('G'));
        //if($hour <= 5){$r['fg']=-120;$r['info']='请在6-23点来领取优惠券。';$this->ajaxReturn($r);}
        $member = $_G['greatwall_userdata'];
        $member_id = $member['id'];
        do{
            if($member['gstime']==0) {$r['fg']=-700; break;}
            if(time() - $member['gstime'] < 1) {$r['fg']=-701; break;}//不是机器人
            //$rn = M('GameLog')->where(array("member_id"=>$member_id,'created'=>array('gt',date('Y-m-d H:i:s', $member['gstime']))))->count();
            //if($rn==0) {$r['fg']=-702; break;}
            //$member['gstime']=0;
            //$this->_gsetnologinuser($member);
        }while(0);
        if($r['fg'] < 0) {
            $r['info']='你是机器人吗？';
            echo json_encode($r);
            exit();
        } //time_limit

        //检查用户抽奖机会 通过这减1
        // $dnum = $this->_cdrawnum(0);
        // if($dnum-- <= 0){
        // 	$r['status']=0;$r['fg']=-1;$r['info']='您已经没有抽奖机会了！';
        // 	$this->ajaxReturn($r);
        // }
        // $this->_cdrawnum(-1);
        // $r['dnum'] = $dnum;

        $odds = 0;
        $max = 100000000;
        $rand = mt_rand(0,$max);
        if($debug) $r['debug'][] = 'rand:'.$rand;

        $prize_idAllows = array(array(1,2,3,4),array(5),array(6));
        if($car!='hf6'){
            $prize_idAllows = array(array(11,12,13,14),array(15),array(16));
        }
        $pL= count($prize_idAllows);
        if( array_key_exists('plog', $member) && count($member['plog']) >=3 ){
            $k = 2;
        }else{
            $rarr= array(0,1,2);
            if( is_array($member['plog']) )
            {
                $marr = array_diff($rarr, $member['plog']);
                $marr = array_values($marr);
                $kk = mt_rand(0, count($marr)-1);
                $k = $marr[$kk];
            }
            else
            {
                $k = mt_rand(0, 2);
            }
            $member['plog'][] = $k;
            $_G['greatwall_userdata'] = $member;
//            $this->_gsetnologinuser($member);
        }
        $prize_idAllow = $prize_idAllows[$k];
        if($debug) $r['debug']['pida'] = $prize_idAllow;

        $pdas = C::t('#htt_greatwall#prize')->fetch_all();

        foreach($pdas as $v){
            //跳过不在抽奖范围的奖品
            /*if(C('project_config.pointlimit')){
                $user_point = intval(I('user_point'));
                if( $user_point < $v['point'] && $v['point']>0 ) continue;
            }*/
            //不再的跳过中奖
            if(!in_array($v['keys'], $prize_idAllow)) continue;

            //状态未启用则跳过
            if($v['status']!=1)continue;

            $odds += $v['probability'];
            if($debug) $r['debug'][$v['id']]['rt'] = $v['probability'];
            if($debug) $r['debug'][$v['id']]['b'] = $max*$odds;
            //不在区间不中奖 继续判定
            if($rand > $max*$odds) continue;

            //IP限制是否开启
            /*if(C('project_config.iplimit')){
                $ipn = get_client_ip(1);
                $rn = M('IpLimit')->where("project_id='".$this->project['id']."' and $ipn between startipn and endipn")->count();
                if($ipn==0 || $rn){ $r['fg']=-1001; break; }
            }*/

            lottery($r, $v, $debug);
            //是否叠加中奖判定 如果一个奖品因为数量限制问题没有中奖，则继续判定下一个奖品 不跳出
            $iswinoverlap = 1;//gmarr($this->project['func'], 'iswinoverlap', 0);
            if($r['keys']==0 || $r['fg'] < 0){
                if($iswinoverlap){continue;}
                else{break;}
            }else{
                break;
            }
        }

        echo json_encode($r);

        /*$data = array();
        $data['status'] = 1;
        $data['info'] = 'ok';
        $data['prize_id'] = '46';
        $data['rid'] = '3574';
        $data['keys'] = '6';
        $data['dnum'] = '0';
        $data['fg'] = '1';
        $data['ticket'] = '';
        echo json_encode($data);*/
        break;
    case 'savewin': //保存中奖用户信息 savewin 对应的路由就是 saveapply
                      //{"status":1,"info":"\u60a8\u7684\u4fe1\u606f\u63d0\u4ea4\u6210\u529f\uff01","ticket":"YYYWKKXF9MF9","money":"1000","rid":"244"}
        $member = $_G['greatwall_userdata'];
        $projectinfo = $_G['greatwall_projectdata'];

        $member_id = $member['id'];
        $rid = intval($_POST['rid']); // 奖品ID
        $dao =C::t('#htt_greatwall#game_log');
        $map=array();
//        $map['status'] = array('neq', '-1');
        //$map['member_id'] = $member_id;
//        $map['project_id'] = $this->project['id'];
//        $map['id'] = $rid;

        $search = ' AND status != -1 ';
        $search .= ' AND project_id = '.$projectinfo['id'];
        $search .= ' AND id !=  '.$rid;

        $przielog = $dao->count_by_search($search);
        if(!$przielog){
//            $this->ajaxReturn(array('status'=>-1,'info'=>'服务器繁忙！'));
            echo json_encode(array('status'=>-1,'info'=>'服务器繁忙！'));
            exit();
        }
        if($przielog['status']=='1' && $przielog['mobile']!=''){
//            $this->ajaxReturn(array('status'=>-2,'info'=>'提交按钮只能点一次哦！'));
            echo json_encode(array('status'=>-2,'info'=>'提交按钮只能点一次哦！'));
            exit();
        }

        $da = array();


       /* $da['name'] = I('name');
        $da['mobile'] = I('mobile');
        $da['addr'] = I('address');
        $ext = I('ext');*/
       $da['name'] = $_POST['name'];
        $da['mobile'] = $_POST['mobile'];
        $da['addr'] = $_POST['address'];
        $ext =  $_POST['ext'];


        $da['config'] = serialize($ext);//可扩展对象 gender,car,province,city,dealer


        if(strlen($da['mobile'])!=11){
//            $this->ajaxReturn(array('status'=>-3,'info'=>'请填写有效手机号码！'));
            echo json_encode(array('status'=>-3,'info'=>'请填写有效手机号码！'));
            exit();

        }
        if(!array_key_exists('dealer', $ext) || $ext['dealer']=='' || $ext['province']=='' || $ext['city']==''){
//            $this->ajaxReturn(array('status'=>-4,'info'=>'请选择经销商信息！'));
            echo json_encode(array('status'=>-4,'info'=>'请选择经销商信息！'));
            exit();
        }

        //验证同一手机号不能重复中奖
       /* $map=array();
        $map['status'] = array('neq', '-1');
        $map['project_id'] = $this->project['id'];
        $map['mobile'] = $da['mobile'];
        $map['id'] = array('neq', $rid);*/

        $search = ' AND status != -1 ';
        $search .= ' AND project_id = '.$projectinfo['id'];
        $search .= ' AND id !=  '.$rid;
        $search .= ' AND mobile !=  '.$da['mobile'];


        //$map['member_id'] = array('neq', $member_id);

//        $uniqie_rn = $dao->where($map)->count();
        $uniqie_rn = $dao->count_by_search($search);

        if($uniqie_rn>0){
            echo json_encode(array('status'=>-31,'info'=>'手机号码已存在！'));
            exit();
        }
//            $this->ajaxReturn(array('status'=>-31,'info'=>'手机号码已存在！'));
        //更新数据。

//        M('Member')->where(array('id'=>$member_id))->save($da);
        C::t('#htt_greatwall#member')->update($member_id,$da);
        //代金券奖品获取代金号
        $ticket='';
        $ticket_batch = '';$prize=array();
        $pdas = C::t('#htt_greatwall#prize')->fetch_all();
        foreach($pdas as $v){
            if($przielog['prize_id']==$v['id']){
                $ticket_batch = $v['ticket_batch'];
                $prize = $v;
                break;
            }
        }
        if($ticket_batch!=''){
            //检查代金券是否还有余
            /*$tbda = M('TicketBatch')->where("batch='".$ticket_batch."'")->find();

            if(!$tbda){$this->ajaxReturn(array('status'=>-2,'info'=>'信息异常，代金券批次不存在')); break;} //代金券批次不存在
            $ticket = $this->getOnlyTicket($tbda['id']);
            if($ticket==''){$this->ajaxReturn(array('status'=>-2,'info'=>'代金券已领取完')); break;} //该批次代金券已领取完*/
        }
//        $da['ticket'] = $ticket;
        $da['ticket'] = 'xxxxx';

        //使用获取奖品的车型 不使用提交车型
        //$car = $ext['car'];
        if($prize['keys'] < 10){
            $car = 'hf6';
        }else{
            $car = 'hf7';
        }
        $da['car'] = $car;
        $da['province'] = $ext['province'];
        $da['city'] = $ext['city'];
        $da['dealer'] = $ext['dealer'];

        $da['id'] = $przielog['id'];
        $da['status'] = '1';

        $dao->update($da['id'],$da);

        //删除待审的为删除。todo 为何删除
       /* $map=array();

        $map['status'] = '0';
        $map['project_id'] = $this->project['id'];
        $map['member_id'] = $member_id;
        $dao->where($map)->save(array('status'=>'-1'));//*/


/*
        unset($map['member_id']);
        $map['created'] = array('lt', date('Y-m-d H:i:s', time()-3600));
        $dao->where($map)->save(array('status'=>'-1'));*/

        $money = str_replace('元', '', $prize['name']);
        $rid = 0;
        if($ticket!=''){
            $carArr = array('hf6'=>'哈弗H6 Coupe 1.5T','hf7'=>'哈弗H7');
            $car = $carArr[$car];
            $content = "尊敬的".$da['name']."您好，恭喜您获得".$prize['name'].$car."购车红包，红包序列号：".$da['ticket']."，红包使用截止日期为2016年6月30日。请尽快到店使用，活动详情可到店详询。本短信为使用红包重要凭证，请妥善保存，转发无效。";
            $sda=array();
            $sda['mobile'] = $da['mobile'];
            $sda['content'] = $content;
            $sda['status'] = 0;
            $sda['created'] = date('Y-m-d H:i:s');

//            $rid = M('SmsLog')->add($sda);
            //$r = $this->_sendSms($da['mobile'], $content, $id);
        }

        $data = array();
        $data['status'] = 1;
        $data['info'] = '你的信息已经提交';
        $data['ticket'] = $ticket;
        $data['money'] = $money;
        $data['rid'] = $rid;
        echo json_encode($data);
        break;
    case 'sendsms': //发送短息。
        $data = array();
        $data['status'] = 1;
        $data['info'] = 'ok';
        $data['url'] = '';
        echo json_encode($data);
        break;
    default: //todo 默认需要待定处理
        $data = array();
        $data['status'] = 1;
        $data['info'] = 'ok';
        $data['url'] = '';
        echo json_encode($data);
        break;
}





?>
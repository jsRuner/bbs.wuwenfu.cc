<?php
/**
 * ��ӿڡ�������Ҫ�Ĳ�����
 */
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
error_reporting(E_ALL);
defined('DATA_PATH')    or define('DATA_PATH',      DISCUZ_ROOT.'/data/plugindata/');
define('IS_AJAX',       ((isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') || !empty($_POST['ajax']) || !empty($_GET['ajax'])) ? true : false);


function htt_random_str($length=5){
    $hash = '';
    $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
    $max = strlen($chars) - 1;
    PHP_VERSION < '4.2.0' && mt_srand((double)microtime() * 1000000);
    for($i = 0; $i < $length; $i++) {
        $hash .= $chars[mt_rand(0, $max)];
    }
    return $hash;
}
global $_G;
$op = $_GET['op'];

function get_rand($proArr) {
    $result = '';

    //����������ܸ��ʾ���
    $proSum = array_sum($proArr);

    //��������ѭ��
    foreach ($proArr as $key => $proCur) {
        $randNum = mt_rand(1, $proSum);
        if ($randNum <= $proCur) {
            $result = $key;
            break;
        } else {
            $proSum -= $proCur;
        }
    }
    unset ($proArr);

    return $result;
}

switch($op){
    case 'initdata': //��ʼ������
        //��¼��û�е�¼.��ȡ�ӿ�,��ҪЯ��project=1�Ĳ��� ��Ҫ���ݵ�ǰ��½�ߵ����ݡ�
        $pm['project'] = $_GET['project'];

        $pm = http_build_query($pm);
        $uri['gamesatrt']  = "/plugin.php?id=htt_greatwall:greatwall_api&op=gamesatrt&".$pm;
        $uri['savegame']  = "/plugin.php?id=htt_greatwall:greatwall_api&op=savegame&".$pm;
        $uri['lottery']  = "/plugin.php?id=htt_greatwall:greatwall_api&op=lottery&".$pm;
        $uri['savewin']  = "/plugin.php?id=htt_greatwall:greatwall_api&op=savewin&".$pm;
        $uri['sendsms']  = "/plugin.php?id=htt_greatwall:greatwall_api&op=sendsms&".$pm;

        $data['URI'] = $uri;

        $_G['greatwall_userdata'] = array(
//            'id'=>$_G['uid'],
            'id'=>$_G['uid'],
            'gstime'=>0,
            'plog'=>array()
        );

        $userinfo['LG_USER'] = array('id'=>$_G['uid']) ;
//        $userinfo['remain'] = time() ; //ԭ������Ŀ�Ľ�ƷǮ

        #��ȡ��Ʒ�б�
        $search = ' AND project_id = '.intval($_GET['project']).' AND prizes_nums != 0 AND status = \'1\'';
        $prize_arr = C::t('#htt_greatwall#prize')->fetch_all_by_search($search,0,1000);
        //����һ�µ�ǰ�ĺ���ܼ�ֵ���鹹һ�¡�����1466303721 +���ʵ�ʵļ�ֵ
        $all_prices = 0;

        foreach($prize_arr as $key=>$prize){
            $current = $prize['prizes_nums'] - $prize['prizes_nums_used'];
            $all_prices += $current * $prize['price'];
        }
        $userinfo['remain'] = $all_prices;


        $data['DATA'] = $userinfo;


        $initda = $data;
        header('Content-Type:application/javascript; charset=utf-8');
        $s[] = 'var API={};';
        foreach ($initda as $k=>$v) $s[] = 'API.'.$k.'='.json_encode($v).';';
        exit(join("\r\n",$s));
        break;

    case 'gamesatrt': //������Ϸ��ʼʱ��
        $data = array();
        if(!IS_AJAX){
            $data['status'] = -1;
            $data['info'] = 'need ajax';
        }else{
            //��Ҫ�ж��Ƿ��¼�ˡ�


            $_G['greatwall_userdata']['gstime'] = time();


            $data['status'] = 1;
            $data['info'] = 'ok';
            $data['url'] = '';
        }
        echo json_encode($data);
        break;
    case 'savegame': //��ȡ���.
        $data = array();
        $data['status'] = 1;
        $data['info'] = 'ok';
        $data['bi'] = $bi;
        $data['count'] = $count;
        echo json_encode($data);
        break;
    case 'lottery'://�齱


        #��ȡ��Ʒ�б�
        $search = ' AND project_id = '.intval($_GET['project']).' AND prizes_nums != 0 AND status = \'1\'';
        $prize_arr = C::t('#htt_greatwall#prize')->fetch_all_by_search($search,0,100);


//        var_dump($prize_arr);

        $count = 0;

        $pids = array();

        $uesed_probability = 0;
        foreach ($prize_arr as $key => $val) {

            //������������ˡ��򲻲��롣
            if($val['prizes_nums'] <= $val['prizes_nums_used']){
                continue;
            }

            $arr[$val['id']] = $val['probability'];

            $count += $val['probability'];






        }
        //��100%�� ��С�Ľ�Ʒ�н���������
        $min_pid = $prize_arr[count($prize_arr)-1]['id'];
        if($count < 100){
            $arr[$min_pid] += 100-$count;
        }




        //�����Ʒ����û�ˣ���ô�ơ�


        //�����н���Ҳ���Ǳ���100%��������汨��
       /* if($count <100){
            $data = array();
            $data['status'] = 0;
//            $data['info'] = "��Ʒ�Ĳ������ô���";
            $data['fg'] = '-202';
            echo json_encode($data);
            break;
//            $count = 100-$count; //�������100�򲹳�һ�����н��ĸ���
//            $arr[0] = $count;
        }*/

        $rid = get_rand($arr); //���ݸ��ʻ�ȡ����id

        //��Ҫ�ж���ʲô��Ʒ�����ݶ�Ӧ��ָ������Ӧ����0-5
        //0��ʾû�н�Ʒ��
        $keys = 0;

        foreach($prize_arr as $key=>$val){
            if($val['id'] == $rid){
                $keys = $key+1;
                break;
            }
        }

        $data = array();
        $data['status'] = 1;
        $data['info'] = 'ok';
        $data['prize_id'] = $rid;
        $data['rid'] = $rid;
        $data['keys'] = $keys;//ȷ���˳齱�ı�ǡ�
        $data['dnum'] = '0';
        $data['fg'] = '1';
        $data['ticket'] = '';
        echo json_encode($data);
        break;

    case 'savewin': //�����н��û���Ϣ savewin ��Ӧ��·�ɾ��� saveapply
                      //{"status":1,"info":"\u60a8\u7684\u4fe1\u606f\u63d0\u4ea4\u6210\u529f\uff01","ticket":"YYYWKKXF9MF9","money":"1000","rid":"244"}
//       var_dump($_POST['ext']);
//       exit();
        //�洢��Ϣ��
//        error_reporting(E_ALL);

        $ticket = htt_random_str(11);

        //����������⡣��ǰ��gbk�������ˣ�˵����utf8��ʹ��utf8תgbk
        $ext_temp = $_POST['ext'];
        foreach($ext_temp as $key=>$item){
            $ext[$key] =  iconv("UTF-8", "gbk", $item);
        }
        $name =   iconv("UTF-8", "gbk", $_GET['name']);
//        $addr =  iconv("UTF-8", "gbk", $_GET['ext']['city']);


       $insertdata = array(
           'project_id'=>$_GET['project'],
           'member_id'=>$_G['uid'],
           'prize_id'=>$_POST['rid'],
           'name'=>$name,
           'mobile'=>$_POST['mobile'],
           'addr'=>$ext['city'],
           'config'=>serialize($ext),
           'ticket'=>$ticket,
           'ip'=>$_G['clientip'],
           'status'=>0,
           'created'=>date('Y-m-d H:i:s'),
       );
//        echo 11;

       C::t('#htt_greatwall#prize_log')->insert($insertdata);




       $prize =  C::t('#htt_greatwall#prize')->fetch_by_pid($_POST['rid']);

        $updatedata = array(
            'prizes_nums_used' => $prize['prizes_nums_used'] +1,
        );

        C::t('#htt_greatwall#prize')->update($_POST['rid'],$updatedata);



        //��Ӧ�Ľ�Ʒ�������١�


//        var_dump($prize);
//        exit();

        /*$data = array();
        $data['status'] = 1;
        $data['info'] = '�����Ϣ�Ѿ��ύ';
        $data['ticket'] = $ticket; //�����Ӧ��ȯ��
        $data['money'] = $prize['price']; //����Ľ�
        $data['rid'] = $_POST['rid'];*/

       /* var_dump($data);

        $xx = $data;

        echo 222;
        echo json_encode(array('status'=>1));
        echo 111;
        exit();*/

        echo json_encode(array(
            'status'=>1,
            'info'=>'ok',
            'ticket'=>$ticket,
            'money'=>$prize['price'],
            'rid'=>$_POST['rid']
        ));

        break;
    case 'sendsms': //���Ͷ�Ϣ��
        $data = array();
        $data['status'] = 1;
        $data['info'] = 'ok';
        $data['url'] = '';
        echo json_encode($data);
        break;
    default: //todo Ĭ����Ҫ��������
        $data = array();
        $data['status'] = 1;
        $data['info'] = 'ok';
        $data['url'] = '';
        echo json_encode($data);
        break;
}





?>
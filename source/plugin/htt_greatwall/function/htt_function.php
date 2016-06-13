<?php

function gsetnologinuser($member = array()){
    global $_G;
    if(is_null($member)){session('lg_no',null);}
    $lg = session('lg_no');
    if(!$lg){
        $id = time().mt_rand(100000,999999);
        $lg = array('id'=>-$id,'gstime'=>0, 'plog'=>array());
        session('lg_no', $lg);
    }
    if(!empty($member) && is_array($member)){
        $lg = array_merge($lg, $member);
        session('lg_no', $lg);
    }
//    $this->member = $lg;
    return $lg;
}

//��Խ�ƷID ���úͻ�ȡ�н�����
//@param $pid ��ƷID
//@param $n ���� ֧�ָ��� 0�����ѯ null �������»�ȡ
//return ���ص�ǰ����
function gsetPrizeNum($id,$n=0){
    global $_G;
    //--���ļ���ȡ
    $id = intval($id);
    if($id==0) return 0;
    $project = $_G['greatwall_projectdata'];
    $keys = $project['id'].'/prizenum_da'.$id;
    $filename = DATA_PATH.$keys.'.php';
    @mkdir(dirname($filename), true);
    //û���ļ���ʼ��
    if(!is_file($filename) || is_null($n)) {
        $fp = fopen($filename , 'w');
        if(flock($fp , LOCK_EX)){
            $rn = M('PrizeLog')->where("status!='-1' and project_id='".$this->project['id']."' and prize_id='".$id."'")->count();
            fwrite($fp , $rn);
            flock($fp , LOCK_UN);
        }
        fclose($fp);
    }
    $fp = fopen($filename , 'r+');
    if(flock($fp , LOCK_EX)){
        $rns = fread($fp, filesize ($filename));
        $r = intval($rns);
        do {
            if(is_null($n)) break;
            if(abs($n)!=0){
                $r += intval($n);
            }
            ftruncate($fp,0);fseek($fp,0);
            fwrite($fp , $r);
        }while(0);
        flock($fp , LOCK_UN);
    }
    fclose($fp);
    return $r;
}



//������Ʒ�ĳ齱
//@param $v ��Ʒ��¼�Ķ����¼
//@param $r ���ض���
//@param $debug ����ģʽ
function lottery(&$r=array(), $v, $debug=0){
    global $_G;
    do{
        $dao = C::t('#htt_greatwall#game_log');

        $member = $_G['greatwall_data'];

        $member_id = $member['id'];
        //�����ֽ�Ʒ��������
        if(in_array(intval($v['keys']), array(1,2,3,11,12,13))){
            //�ļ�ϵͳ1�αȽ�
            $rn = gsetPrizeNum($v['id']);
            //��֤��Ʒ��������
            if($debug) $r['debug'][$v['id']]['rn'] = $rn;
            if($v['prizes_nums']>=0 && $rn>=$v['prizes_nums']) { $r['fg']=-200; $r['info']='��Ʒ�Ѿ�������';break; }//�������ý�Ʒ��
            //��֤��Ʒÿ�շ�����
            $limit = getPrizeLimit($v['config']);
            if($debug) $r['debug'][$v['id']]['lt'] = $limit;
            if($limit>=0 && $rn>=$limit) { $r['fg']=-201; $r['info']='���ս�Ʒ�Ѿ�������';break; }//�������õ��շ����� ������ �����ѷ��꣬����������Ŷ��
            //��ӽ�Ʒ����
            gsetPrizeNum($v['id'],1);

            //���ݿ�2�αȽϲ�ѯ
            //$rn = $dao->where("prize_id={$v['id']} and status!='-1'")->count();
            //if($v['prizes_nums']>=0 && $rn>=$v['prizes_nums']) { $r['fg']=-200; $r['info']='��Ʒ�Ѿ�������';break; }//�������ý�Ʒ��
            //if($limit>=0 && $rn>=$limit) { $r['fg']=-201; $r['info']='���ս�Ʒ�Ѿ�������';break; }//�������õ��շ����� ������ �����ѷ��꣬����������Ŷ��
            //�ظ��н�1
            //$rn = $dao->where("member_id={$member_id}")->count();
            //if($rn>=3) { $r['fg']=-202; $r['info']='���Ѿ�û�г齱������';break; }//�ظ��н�
        }

        //����н�����
        $da=array();
        $da['project_id'] = $this->project['id'];
        $da['member_id']=$member_id;
        $da['prize_id']=$v['id'];
        $da['name']='';
        $da['ticket']='';
        $da['ip']= get_client_ip();
        $da['status']='0';
        $da['created']=NOW;
        $r['rid']=$rid = $dao->add($da);
        $r['info']='ok';
        $r['keys']=$v['keys'];
        $r['ticket']='';
        $r['prize_id']=$v['id'];
        $r['fg']=1;

        break;
    }while(0);
}


//��ȡ��Ʒ�ֲ� $prizearr ���л��ֶ���: '2014-07-30'=>1,'2014-08-04'=>2
//���أ��������õĽ�Ʒ�� -1 ����δ����
function getPrizeLimit($sets,$d=0){
    if(empty($sets)) return -1;
    $seta = unserialize($sets);
    if(!is_array($seta) || empty($seta)) return -1;
    $lim = 0;
    if($d==0){$d=time();}
    foreach($seta as $k=>$v){
        $start = strtotime($k);
        if($start > $d) break;
        else{
            $_k = date('Y-m-d', $d);
            if($k == $_k){
                //���⴦�� 9 12 17�� ����һ��3��ʱ��� ��Ʒƽ���ֲ�
                $h = date('G', $d);
                if($h<9) break;
                $v3 = floor($v/3);
                if($h<12){
                    $lim += $v3;
                }elseif($h<17){
                    $lim += $v3*2;
                }else{
                    $lim += $v;
                }
            }else{
                $lim += $v;
            }
        }
    }
    return $lim;
}

?>
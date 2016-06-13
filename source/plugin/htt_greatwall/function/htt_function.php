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

//针对奖品ID 设置和获取中奖数量
//@param $pid 奖品ID
//@param $n 数量 支持负数 0代表查询 null 代表重新获取
//return 返回当前数量
function gsetPrizeNum($id,$n=0){
    global $_G;
    //--锁文件获取
    $id = intval($id);
    if($id==0) return 0;
    $project = $_G['greatwall_projectdata'];
    $keys = $project['id'].'/prizenum_da'.$id;
    $filename = DATA_PATH.$keys.'.php';
    @mkdir(dirname($filename), true);
    //没有文件初始化
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



//单个奖品的抽奖
//@param $v 奖品记录的对象记录
//@param $r 返回对象
//@param $debug 调试模式
function lottery(&$r=array(), $v, $debug=0){
    global $_G;
    do{
        $dao = C::t('#htt_greatwall#game_log');

        $member = $_G['greatwall_data'];

        $member_id = $member['id'];
        //仅部分奖品限制数量
        if(in_array(intval($v['keys']), array(1,2,3,11,12,13))){
            //文件系统1次比较
            $rn = gsetPrizeNum($v['id']);
            //验证奖品发放总数
            if($debug) $r['debug'][$v['id']]['rn'] = $rn;
            if($v['prizes_nums']>=0 && $rn>=$v['prizes_nums']) { $r['fg']=-200; $r['info']='奖品已经发完了';break; }//超出设置奖品数
            //验证奖品每日发放数
            $limit = getPrizeLimit($v['config']);
            if($debug) $r['debug'][$v['id']]['lt'] = $limit;
            if($limit>=0 && $rn>=$limit) { $r['fg']=-201; $r['info']='今日奖品已经发完了';break; }//超出设置当日发放数 则跳出 今日已发完，明日请早来哦！
            //添加奖品数量
            gsetPrizeNum($v['id'],1);

            //数据库2次比较查询
            //$rn = $dao->where("prize_id={$v['id']} and status!='-1'")->count();
            //if($v['prizes_nums']>=0 && $rn>=$v['prizes_nums']) { $r['fg']=-200; $r['info']='奖品已经发完了';break; }//超出设置奖品数
            //if($limit>=0 && $rn>=$limit) { $r['fg']=-201; $r['info']='今日奖品已经发完了';break; }//超出设置当日发放数 则跳出 今日已发完，明日请早来哦！
            //重复中奖1
            //$rn = $dao->where("member_id={$member_id}")->count();
            //if($rn>=3) { $r['fg']=-202; $r['info']='您已经没有抽奖机会了';break; }//重复中奖
        }

        //添加中奖数据
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


//获取奖品分布 $prizearr 序列化字段如: '2014-07-30'=>1,'2014-08-04'=>2
//返回：当日设置的奖品数 -1 代表未设置
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
                //特殊处理 9 12 17点 划分一天3个时间段 奖品平均分布
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
<?php
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class yy_seccode {
	private $seccode = array();
	private $color = array();
	private $chk;
	private $cvar;
	private $width = 150;
	private $height = 80;
	var $font;
	var $intfline = 5;
	var $intfpix = 5;
	var $scatter = 0;
	var $angle = 0;
	public function __construct(){
		$this->color = array(
			array(array(255,0,0),'红'), 
			array(array(255,200,0),'橙'), 
			array(array(0,0,255),'蓝'),
			array(array(128,128,128),'灰'),
			array(array(0, 0, 0),'黑'),
			array(array(130, 0, 180),'紫'),
		);
	}
	function getsec(){
		$this->seccode = $this->make();
		$this->chk = rand(0,(count($this->color)-1));
		$sect = array();
		foreach($this->seccode as $k => $v){
			$sect[$k] = array("text"=>$v,"color"=>$this->createcolor());
		}
		$r = rand(0,3);
		$sect[$r]['color'] = $this->color[$this->chk][0];
		$this->seccode = $sect;
		$chktext = $sect[$r]['text'];
		return $chktext;
	}
	function createcolor(){
		$color = array();
		for($i=0;$i<3;$i++){
			$color[$i] = rand(0, 255);
			if(abs($color[$i] - $this->color[$this->chk][0][$i])<50){
				$color[$i] = ($color[$i] + 50)>255?(255 - $color[$i]):($color[$i] + 50);
			}
		}
		return $color;
	}
	function make(){
		$rand = random(12, 1);
		$lang = lang('seccode');
		$len = strtoupper(CHARSET) == 'GBK' ? 2 : 3;
		$seccode = array();
		for($i = 0; $i < 4; $i++) {
			$code = substr($rand, $i * 3, 3);
			$seccode[]= substr($lang['chn'], $code * $len, $len);
		}
		return $seccode;
	}
	function display(){
		include $this->includepath.'class_chinese.php';
		$cvt = new Chinese(CHARSET, 'utf8');
		header('Content-Type: image/png');
		$im = imagecreatetruecolor($this->width, $this->height);
		$white = imagecolorallocate($im, 255, 255, 255);
		$black = imagecolorallocate($im, 0, 0, 0);
		imagefilledrectangle($im, 0, 0, $this->width, $this->height, $white);
		$i = 0;
		foreach($this->seccode as $v){
			$color = imagecolorallocate($im, $v['color'][0], $v['color'][1], $v['color'][2]);
			$arg = rand(-$this->angle,$this->angle);
			if($arg>0){
				$x = 11 + $i*30 + 30*(float)sin(deg2rad($arg));
				$y = 35;
			}else{
				$x = 11 + $i*30;
				$y = 35 + 20*(float)sin(deg2rad($arg));
			}
			imagettftext($im, 25, $arg, $x, $y, $color, $this->font, $cvt->Convert($v['text']));
			$i++;
		}
		$this->scatter && $this->scatter($im);
		$this->intfcode($im);
		imagefilledrectangle($im, 0, $this->height-30, $this->width, $this->height, $black);
		imagettftext($im, 14, 0, 10, $this->height - 10, $white, $this->font, "请输入".$this->color[$this->chk][1]."色的字");
		imageline ($im, 0, 0, 0, $this->height, $black);
		imageline ($im, $this->width-1, 0, $this->width-1, $this->height, $black);
		imageline ($im, 0, 0, $this->width, 0, $black);
		imageline ($im, 0, $this->height-1, 0, $this->height-1, $black);
		imagepng($im);
		imagedestroy($im);
	}
	function intfcode(&$obj){
		$ifcolor = ImageColorAllocate($obj, $this->color[$this->chk][0][0], $this->color[$this->chk][0][1], $this->color[$this->chk][0][2]);
		for($i=0; $i<$this->intfline; $i++)
		{
			$color = rand(0,1)?ImageColorAllocate($obj, rand(0,255), rand(0,255), rand(0,255)):$ifcolor; 
			if(rand(0,1)){
				imageline ($obj, rand(0,$this->width), rand(0,$this->height-30), rand(0,$this->width), rand(0,$this->height-30), $color);
			}else{
				ImageArc($obj, rand(-5,$this->width), rand(-5,$this->height-30), rand(20,300), rand(20,200), 55, 44, $color);
			}
		}
		for($i=0; $i<$this->intfpix * 40; $i++)
		{   
			$color = ImageColorAllocate($obj, rand(0,255), rand(0,255), rand(0,255)); 
			ImageSetPixel($obj, mt_rand(0,$this->width), rand(0,$this->height-30), $color);
			ImageSetPixel($obj, mt_rand(0,$this->width), rand(0,$this->height-30), $ifcolor);
		}
		
	}
	function scatter(&$obj, $level = 0) {
		$rgb = array();
		$this->scatter = $level ? $level : $this->scatter;
		$width = $this->width;
		$height = $this->height-30;
		for($j = 0;$j < $height;$j++) {
			for($i = 0;$i < $width;$i++) {
				$rgb[$i] = imagecolorat($obj, $i , $j);
			}
			for($i = 0;$i < $width;$i++) {
				$r = rand(-$this->scatter, $this->scatter);
				imagesetpixel($obj, $i + $r , $j , $rgb[$i]);
			}
		}
	}
}

?>
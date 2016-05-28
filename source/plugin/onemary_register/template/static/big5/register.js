(function(jQuery) {
	if (typeof register == "undefined") {
		register = {};
	}
	var isNameOk,isEmailOK,isPwdOK,isToPwdOK,isVerifycodeOK = "F";
	var okVerifycode;
	var emailReg = /\b(^[_a-zA-Z0-9-]+(\.[_a-zA-Z0-9-]+)*@[a-zA-Z0-9_-]+(\.[a-zA-Z0-9_-]+)*jQuery)\b/;
	
	register.checkNickName = function(obj){
		var nickName = jQuery.trim(obj.value);
		if(nickName.length < 2){
			jQuery("#input_nickname").show();
			jQuery("#input_nickname_s").html("昵??度需??字符以上");
			jQuery("#input_nickname").attr("class","reg-check-tip wrong");
			isNameOk = "F";
			return false;
		}


		jQuery.ajax({
			url:"plugin.php?id=onemary_register:register_ajax",
			type:"get",
			data:{"nickName":nickName},
			success:function(response){
				jQuery("#input_nickname").show();
				if(response=="register.fuhao.nickName"){
					jQuery("#input_nickname_s").html("用?名不合法");
					jQuery("#input_nickname").attr("class","reg-check-tip wrong");
					isNameOk = "F";
				}else if(response=="register.error.nickName"){
					jQuery("#input_nickname_s").html("包含有不允?注?的字符");
					jQuery("#input_nickname").attr("class","reg-check-tip wrong");
					isNameOk = "F";
				}else if(response=="register.check.nickName"){
					jQuery("#input_nickname_s").html("此昵?太受?迎了，?一???。");
					jQuery("#input_nickname").attr("class","reg-check-tip wrong");
					isNameOk = "F";
				}else{
					jQuery("#input_nickname").attr("class","reg-check-tip ok");
					isNameOk = "T";
				}
			}
		});
	}
	
	register.focusNickName = function(obj){
		jQuery("#input_nickname").show();
	}
	
	register.focusEmail = function(obj){
		jQuery("#input_email").show();
	}
	
	register.checkEmail = function(obj){
		var email = jQuery.trim(obj.value);
		if(email.length <= 0){
			jQuery("#input_email_s").html("??入登??箱");
			jQuery("#input_email").show();
			jQuery("#input_email").attr("class","reg-check-tip wrong");
			isEmailOK = "F";
			return false;
		}
		jQuery.ajax({
			url:"plugin.php?id=onemary_register:register_ajax",
			type:"get",
			data:jQuery.param({"email":email}),
			success:function(response){
				jQuery("#input_email").show();
				if(response=="register.error.email"){
					jQuery("#input_email_s").html("??怎么可能是正确?箱呢？");
					jQuery("#input_email").attr("class","reg-check-tip wrong");
					isEmailOK = "F";
				}else if(response=="register.buyunxu.email"){
					jQuery("#input_email_s").html("天！???箱不允?被注?");
					jQuery("#input_email").attr("class","reg-check-tip wrong");
					isEmailOK = "F";
				}else if(response=="register.beizhuche.email"){
					jQuery("#input_email_s").html("???箱已?有人注?了哦");
					jQuery("#input_email").attr("class","reg-check-tip wrong");
					isEmailOK = "F";
				}else{
					jQuery("#input_email").attr("class","reg-check-tip ok");
					isEmailOK = "T";
				}
			}
		});
	}
	
	register.showStrengthDiv = function(){
		jQuery("#input_password_s").show();
		jQuery("#input_password_s2").hide();
	}
	
	register.checkPw = function(obj){
		var password = jQuery.trim(obj.value);
		jQuery("#input_password").show();
		if(password.length == 0){
			jQuery("#input_password_s").hide();
			jQuery("#input_password_s2").html("??入密?").show();
			jQuery("#input_password").attr("class","reg-check-tip wrong");
			isPwdOK = "F";
		}else if(password.indexOf(" ") != -1){
			jQuery("#input_password_s").hide();
			jQuery("#input_password_s2").html("密?不能有?禁字符").show();
			jQuery("#input_password").attr("class","reg-check-tip wrong");
			isPwdOK = "F";
		}else if((password.length<6) || (password.length>24)){
			jQuery("#input_password_s").hide();
			jQuery("#input_password_s2").html("密??度6到24位").show();
			jQuery("#input_password").attr("class","reg-check-tip wrong");
			isPwdOK = "F";
		}else{
			jQuery("#input_password_s").hide();
			jQuery("#input_password_s2").hide();
			jQuery("#input_password").attr("class","reg-check-tip ok");
			isPwdOK = "T";
		}
	}

	register.pwStrength = function (pwd){
		O_color="pswStrong"; 
	    L_color="pswStrong1"; 
	    M_color="pswStrong2"; 
	    H_color="pswStrong3"; 
	    S_level=checkStrong(pwd); 
	    switch(S_level) { 
	    	case 0: 
				Lcolor=Mcolor=Hcolor=O_color; 
			case 1: 
	    		Lcolor=L_color; 
	    		Mcolor=Hcolor=O_color; 
	    		break; 
			case 2: 
	        	Lcolor=Mcolor=M_color; 
	        	Hcolor=O_color; 
	       	 	break; 
	   	 	default: 
				Lcolor=Mcolor=Hcolor=H_color; 
	   	}
		jQuery("#password_L").attr("class",Lcolor);
		return; 
	}

	var checkStrong = function (sPW){ 
		if (sPW.length<6) 
			return 0; 
	    Modes=0; 
	    for (i=0;i<sPW.length;i++){ 

	        Modes|=CharMode(sPW.charCodeAt(i)); 
		} 
		return bitTotal(Modes); 
	},

	CharMode = function (iN){ 
	    if (iN>=48 && iN <=57)
	    	return 1; 
	    if (iN>=65 && iN <=90)
	    	return 2; 
	    if (iN>=97 && iN <=122)
	    	return 4; 
	    else 
	    	return 8;  
	}, 

	bitTotal = function (num){
	    modes=0; 
	    for (i=0;i<4;i++){ 
	        if (num & 1) modes++; 
	        num>>>=1; 
	    } 
	    return modes; 
	}


	register.focusPassword = function(obj){
		jQuery("#input_password").show();
		jQuery("#input_password").attr("class","reg-check-tip");
		register.showStrengthDiv();
	}
	
	register.focusConfirmPwd = function(obj){
		jQuery("#input_passwordConfirm").show();
	}
	
	register.confirmPw = function(obj) {
		var password = jQuery.trim(jQuery("#password").val());
		var passwordConfirm = obj.value;
		if((password.length<6) || (password.length>24)) {
			jQuery("#input_password_s").hide();
			jQuery("#input_password_s2").html("密??度6到24位").show();
			jQuery("#input_password").show();
			jQuery("#input_password").attr("class","reg-check-tip wrong");
			isToPwdOK = "F";
		}else if(passwordConfirm != password) {
			jQuery("#error_comfirm").html("?次?入的密?不一?,?重新?入!");
			jQuery("#input_passwordConfirm").show();
			jQuery("#input_passwordConfirm").attr("class","reg-check-tip wrong");
			isToPwdOK = "F";
		}else{
			jQuery("#input_passwordConfirm").show();
			jQuery("#input_passwordConfirm").attr("class","reg-check-tip ok");
			isToPwdOK = "T";
		}
	}
	
	jQuery(document).on("focus","input[name='seccodeverify']",function() {
		jQuery("#verifycodeErrorMsg").html("??入???");
		jQuery("#input_verifycode").attr("class","reg-check-tip");
		jQuery("#input_verifycode").show();
	});
	jQuery(document).on("blur","input[name='seccodeverify']",function() {
		var validateCode=jQuery.trim(this.value);
		var seccodehash = jQuery("input[name='seccodehash']").val;
		var seccodemodid = jQuery("input[name='seccodemodid']").val;
		if(validateCode == ''){
			jQuery("#verifycodeErrorMsg").html("??入???");
			jQuery("#input_verifycode").attr("class","reg-check-tip wrong");
			jQuery("#input_verifycode").show();
		} else {
			jQuery.ajax({
				url:"plugin.php?id=onemary_register:register_ajax",
				type:"get",
				data:jQuery.param({"validateCode":validateCode}),
				success:function(response){
					jQuery("#input_verifycode").show();
					if(response=="register.error.validateCode"){
						jQuery("#verifycodeErrorMsg").html("????入??，?重新?入");
						jQuery("#input_verifycode").attr("class","reg-check-tip wrong");
						isVerifycodeOK = "F";
					}else{
						jQuery("#input_verifycode").attr("class","reg-check-tip ok");
						isVerifycodeOK = "T";
					}
				}
			});
		}
	});
	
	function addbirth_ok(add) {	
		jQuery("#input_"+add).attr("class","reg-check-tip ok");
	}
	function addbirth_next(str,add){
		jQuery("#input_"+add+"_s").html(str);	
		jQuery("#input_"+add).attr("class","reg-check-tip");
	}
	function addbirth_wrong(str,add){
		jQuery("#input_"+add+"_s").html(str);
		jQuery("#input_"+add).attr("class","reg-check-tip wrong");
	}
	jQuery(document).on("change","#birthprovince",function() {
		var birthprovince = jQuery(this).find("option:selected").val();
		var add = birthprovince.replace(/([省市])|(自治?)|(特?行政?)/,'');
		jQuery("#input_birthcity").show();
		if(birthprovince == ''){
			addbirth_wrong("???省份/地?","birthcity");
		} else {
			addbirth_next("???城市","birthcity");
		}
	});
	
	jQuery(document).on("change","#birthcity",function() {
		var birthprovince = jQuery("#birthprovince").find("option:selected").val();
		var birthcity = jQuery(this).find("option:selected").val();
		var birthcity_did = jQuery(this).find("option:selected").attr("did");
		if(birthcity == ''){
			addbirth_wrong("???城市","birthcity");
		} else if (birthcity_did > 493 || birthcity_did == 342 || birthcity_did == 343 || birthcity_did == 344) {
			addbirth_ok("birthcity");
			
		} else {
			addbirth_next("???州?","birthcity");
		}
	});
	jQuery(document).on("change","#birthdist",function() {
		var birthprovince = jQuery("#birthprovince").find("option:selected").val();
		var birthcity = jQuery("#birthcity").find("option:selected").val();
		var birthdist = jQuery(this).find("option:selected").val();
		var birthprovince_did = jQuery("#birthprovince").find("option:selected").attr("did")
		if(birthdist == ''){
			addbirth_wrong("???州?","birthcity");
		} else if (birthprovince_did != 1 && birthprovince_did != 2 && birthprovince_did != 9 && birthprovince_did != 22) {
			addbirth_next("?????/街道","birthcity");
		} else {
			addbirth_ok("birthcity");
		}
	});
	jQuery(document).on("change","#birthcommunity",function() {
		var birthprovince = jQuery("#birthprovince").find("option:selected").val();
		var birthcity = jQuery("#birthcity").find("option:selected").val();
		var birthdist = jQuery("#birthdist").find("option:selected").val();
		var birthcommunity = jQuery(this).find("option:selected").val();
		if(birthcommunity == ''){
			addbirth_wrong("?????/街道","birthcity");
		} else {
			addbirth_ok("birthcity");
		}
	});
	
	jQuery(document).on("change","#resideprovince",function() {
		var resideprovince = jQuery(this).find("option:selected").val();
		var add = resideprovince.replace(/([省市])|(自治?)|(特?行政?)/,'');
		jQuery("#input_residecity").show();
		if(resideprovince == ''){
			addbirth_wrong("???省份/地?","residecity");
		} else {
			addbirth_next("???城市","residecity");
		}
	});
	
	jQuery(document).on("change","#residecity",function() {
		var resideprovince = jQuery("#resideprovince").find("option:selected").val();
		var residecity = jQuery(this).find("option:selected").val();
		var residecity_did = jQuery(this).find("option:selected").attr("did");
		if(residecity == ''){
			addbirth_wrong("???城市","residecity");
		} else if (residecity_did > 493 || residecity_did == 342 || residecity_did == 343 || residecity_did == 344) {
			addbirth_ok("residecity");
			
		} else {
			addbirth_next("???州?","residecity");
		}
	});
	jQuery(document).on("change","#residedist",function() {
		var resideprovince = jQuery("#resideprovince").find("option:selected").val();
		var residecity = jQuery("#residecity").find("option:selected").val();
		var residedist = jQuery(this).find("option:selected").val();
		var resideprovince_did = jQuery("#resideprovince").find("option:selected").attr("did")
		if(residedist == ''){
			addbirth_wrong("???州?","residecity");
		} else if (resideprovince_did != 1 && resideprovince_did != 2 && resideprovince_did != 9 && resideprovince_did != 22) {
			addbirth_next("?????/街道","residecity");
		} else {
			addbirth_ok("residecity");
		}
	});
	jQuery(document).on("change","#residecommunity",function() {
		var resideprovince = jQuery("#resideprovince").find("option:selected").val();
		var residecity = jQuery("#residecity").find("option:selected").val();
		var residedist = jQuery("#residedist").find("option:selected").val();
		var residecommunity = jQuery(this).find("option:selected").val();
		if(residecommunity == ''){
			addbirth_wrong("?????/街道","residecity");
		} else {
			addbirth_ok("residecity");
		}
	});
	/*
	saveRegisterFirst = function(){
		function art_dialog(content_text){
			art.dialog({
				title: '提示',
				content: content_text,
				icon: 'warning',
				ok: true,
				okVal: '确?',
				lock: true,
			});
		}
		if(jQuery.trim(jQuery("#nickName").val())=="" || isNameOk == "F"){
			art_dialog('?按要求?入昵?');
			return false;
		}
		if(jQuery.trim(jQuery("#email").val())=="" || isEmailOK == "F"){
			art_dialog('?按要求?入密保?箱');
			return false;
		}
		if(jQuery.trim(jQuery("#password").val())=="" || isPwdOK == "F"){
			art_dialog('?按要求?入密?');
			return false;
		}
		if(jQuery.trim(jQuery("#passwordConfirm").val()) != jQuery.trim(jQuery("#password").val())){
			art_dialog('?按要求?入确?密?');
			return false;
		}
		if(jQuery.trim(jQuery(".reg-box .txt").val())=="" || isVerifycodeOK == "F"){
			art_dialog('??入???');
			return false;
		}
		if(!jQuery("#agreebbrule").prop("checked")){
			art_dialog('?先??并同意<服??款>');
		    return false;
		
		}
		if(jQuery("input").attr("class")!="reg-check-tip ok" && jQuery("input").attr("required")){
			//var val = jQuery(this).val;
			art_dialog('ff');
			return false;

		}else {
			document.onemary_register.submit();
		}
		
	}
	*/

})(jQuery);


/*
 captcha.js gomezshui 2013/08/01
 $Id$
 */

(function(g) {
    var cloudCaptcha = function() {}
    cloudCaptcha.prototype = {
        run : function(src, idhash, tips) {
            this.src = src;
            this.idhash = idhash;
            this.tips = tips;
            document.getElementById('seccode_js' + idhash).innerHTML = this.buildHtml();
        }, 
        buildHtml : function() {
            return this.tips +
                '<img onclick="updateseccode(\''+this.idhash+'\')" src="'+this.src+'" class="vm" alt="" />';
        }
    }
    g.cloudCaptcha = new cloudCaptcha();
})(window);
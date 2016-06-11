var isComplete=0;
var isGameOver = false;
function wxShare(data){
    var newData = $.extend({},wxDefault, data);
    wx.onMenuShareAppMessage(newData);
    wx.onMenuShareQQ(newData);
    wx.onMenuShareWeibo(newData);
    wx.onMenuShareTimeline({
        title:newData.desc,
        imgUrl:newData.imgUrl,
        link:newData.link,
        success: newData.successtl
    });
}
$(function(){
    var pageUrl = location.href;
    $.ajax({
        url:"http://wxrouter.yescia.com/Api/wxJsConfig/appid/wx3e3e1bca0352eb04.html",
        dataType:"jsonp",
        jsonp:"jsoncallback",
        data:{url:encodeURIComponent(pageUrl)},
        success:function(data){
            data.debug = false;
            wx.config(data);
            wx.ready(function(){
                wxShare();
            });
        }
    })
});












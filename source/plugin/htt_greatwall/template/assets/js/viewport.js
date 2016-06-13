var screenWidth = window.screen.width;
var screenheight = window.screen.height;
orientationChange();
if(screenWidth < 1350){
    var phoneScale = parseInt(screenWidth)/1350;
    document.write('<meta name="viewport" content="width=1350, initial-scale='+ phoneScale +', minimum-scale = '+ phoneScale +', maximum-scale = 2, target-densitydpi=device-dpi">');
    /*   var screenW = parseInt(window.screen.width);
     var phoneScale = parseInt(window.screen.width)/1400;
     document.write('<meta name="viewport" content="width='+ screenW +', initial-scale='+ phoneScale +', minimum-scale = '+ phoneScale +', maximum-scale = 2, target-densitydpi=device-dpi">');*/
}else{
    document.write('<meta name="viewport" content="width=device-width,user-scalable=no, initial-scale=1, maximum-scale=1, target-densitydpi=device-dpi">');
}

function orientationChange(){
    screenWidth = window.screen.height;
    screenheight = window.screen.width;
    /*    switch(window.orientation) {
     case 0: // Portrait
     case 180: // Upside-down Portrait
     // ˙∆¡
     screenWidth = window.screen.width;
     screenheight = window.screen.height;
     break;
     case -90: // Landscape: turned 90 degrees counter-clockwise
     case 90: // Landscape: turned 90 degrees clockwise
     // Javascript to steup Landscape view
     //∫·∆¡
     screenWidth = window.screen.height;
     screenheight = window.screen.width;
     break;
     }*/
}
//window.addEventListener("onorientationchange", orientationChange, false);
if (typeof(console) == "undefined") console = {
    log: function () {
    }
};
var ZbBase = {
    project: 1,//项目ID 每个项目需要管理员重新生成
    addScript: function (u) {
        document.write("<s" + "cript type='text/javascript' src='" + u + (u.indexOf('?') > -1 ? "&" : "?") + "_t=" + Math.random() + "&gourl=" + encodeURIComponent(location.href) + "'></scr" + "ipt>");
    },
    getParam: function (n) {
        var query = location.search.substring(1).split('&');
        for (var i = 0; i < query.length; i++) {
            var kv = query[i].split('=');
            if (kv[0] == n) {
                return kv[1]
            }
        }
        return null
    },
    basePath: (function () {
        var elements = document.getElementsByTagName('script');
        for (var i = 0, len = elements.length; i < len; i++) {
            if (elements[i].src && elements[i].src.match(/initdata.js/)) {
                return elements[i].src.substring(0, elements[i].src.lastIndexOf('/'));
            }
        }
        return '';
    })(),
    init: function () {
        if (typeof(ZbBase_Project) != "undefined") this.project = ZbBase_Project;
        if (ZbBase.getParam('project')) this.project = ZbBase.getParam('project');

        //var _uapi = ZbBase.basePath + '/index.php/Index/index/callback/js/project/' + this.project;
        var _uapi = '/plugin.php?id=htt_greatwall:greatwall_api&op=initdata&project=' + this.project;

        console.log(_uapi);
        if (ZbBase.getParam('toid') != null) {
            _uapi += '&toid=' + ZbBase.getParam('toid');
        }
        ZbBase.addScript(_uapi);
    }
};
ZbBase.init();



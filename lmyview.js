/**
 * 根据文件扩展名 在线预览文件 Lmy
 * @Author   liulong                  <335111164@qq.com>
 * @DateTime 2018-06-28T20:30:37+0800
 * @param    {[type]}                 $                  jquery
 * @param    {[type]}                 win                浏览器窗口
 * @return   {[type]}                                    [description]
 */
;!function($,win){
    "use strict";

    var config = {
        elem:'',
        ext:'',
        url:'',
    }

    var Lmy = function(options){
       this.v = '1.0.0_rls'; //版本号
       this.options = $.extend({}, config, options);
    };

    Lmy.fn = Lmy.prototype;

    //图片展示
    Lmy.fn.img = function(){
        var url = this.options.url;
        var elem = this.options.elem;
        var html = '<img src="'+url+'">';
        $(html).appendTo(elem);
    };

    //word文档展示
    Lmy.fn.word = function(){
        var url = this.options.url;
        var elem = this.options.elem;
        var html = '<iframe src="'+url+'" frameborder="0" width="100%" height="100%"></iframe>';
        $(html).appendTo(elem);
    };

    //pdf展示
    Lmy.fn.pdf = function(){
        var url = this.options.url;
        var elem = this.options.elem;
        //var html = '<embed src="'+url+'" type="application/pdf" width="100%" height="100%">';
        var xhr = new XMLHttpRequest();    
        xhr.open("get", url, true);
        xhr.responseType = "blob";
        xhr.onload = function() {
            if (this.status == 200) {
                    var blob = this.response;
                    var ado = document.createElement("embed");
                    //var so = document.createElement("source");  
                    ado.style.width = '100%';
                    ado.style.height = '100%';
                    ado.onload = function(e) {
                      window.URL.revokeObjectURL(ado.src); 
                    };
                    ado.src = window.URL.createObjectURL(blob);
                    ado.type = 'application/pdf';
        　　　　　　$(elem).html(ado);
                } 
            } 
        xhr.send();
    };

    //excel展示
    Lmy.fn.excel = function(){
        var url = this.options.url;
        var elem = this.options.elem;
        var html = '<iframe src="'+url+'" frameborder="0" width="100%" height="100%"></iframe>';
        $(html).appendTo(elem);
    };

    //音频展示
    Lmy.fn.audio = function(){
        var url = this.options.url;
        var elem = this.options.elem;
        //xmlhttprequest blob;
        var xhr = new XMLHttpRequest();    
        xhr.open("get", url, true);
        xhr.responseType = "blob";
        xhr.onload = function() {
            if (this.status == 200) {
                    var blob = this.response;
                    var ado = document.createElement("audio"); 
                    ado.style.width = '100%';
                    ado.onload = function(e) {
                      window.URL.revokeObjectURL(ado.src); 
                    };
                    ado.src = window.URL.createObjectURL(blob);
                    ado.controls = 'controls';
        　　　　　　$(elem).html(ado);
                } 
            } 
        xhr.send();
    };

    //视频展示
    Lmy.fn.video = function(){
        var url = this.options.url;
        var elem = this.options.elem;
        var html = '<iframe src="'+url+'" frameborder="0" width="100%" height="100%"></iframe>';
        $(html).appendTo(elem);
    };

    //错误信息
    Lmy.fn.error = function(code){
        var msg = '';
        switch(code){
            case 1001:
                msg = '扩展名错误';
                break;
            case 1002:
                msg = '未匹配到扩展名';
                break;
            default:
                msg = '';
        }
        console.log(msg);
    };

    //映射关系
    Lmy.fn.relation = function(ext){
        var rl = {
            mp4:'video',wav:'audio',mp3:'audio',doc:'word',docx:'word',pdf:'pdf',xls:'excel',
            xlsx:'excel',png:'img',gif:'img',jpe:'img',jpg:'img',jpeg:'img',bmp:'img' 
        }
        return eval('(rl.' + ext + ')');
    };

    //适配器
    Lmy.fn.adapter = function(method){
        var callback = eval('(this.' + method + ')');
        if(typeof callback === 'function'){
            callback.call(this);
        }
    };

    Lmy.fn.init = function(){
        var ext = this.options.ext;
        if(typeof ext !== 'string' && ext.length <= 2){
            this.error(1001);
            return false;
        }
        var method = this.relation(ext);
        if(typeof method != 'string'){
            this.error(1002);
            return false;
        }
        //加载
        //loading;
        this.adapter(method);
        //close 
    };

    //暴露所有函数 如果要单独调用方法 直接实例化对象
    //win.Lmys = new Lmy();  
    //接口
    $.lmyview = function(options){
        options = options || {};
        var lmy = new Lmy(options);
        lmy.init();
    };
}($,window);
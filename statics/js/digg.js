/**
 * 顶一下处理函数
 */
var digg = {
    done: false,
    data: null,
    get: function(c) {
        var b = this;
        $.getJSON("?app=digg&controller=index&action=digg&contentid=" + c + "&jsoncallback=?",
        function(a) {
            digg.data = a;
            b.render(a.supports, a.againsts);
            if (a.done) {
                b.done = true;
                $("#supports").attr("title", "\u4f60\u5df2\u7ecf\u9876\u8fc7\u4e86");
                $("#againsts").attr("title", "\u4f60\u5df2\u7ecf\u8e29\u8fc7\u4e86")
            }
        })
    },
    set: function(c, b) {
        var a = this;
        if (a.done) b == 1 ? alert("\u60a8\u5df2\u7ecf\u9876\u8fc7\u4e86") : alert("\u4f60\u5df2\u7ecf\u8e29\u8fc7\u4e86");
        else $.getJSON("?app=digg&controller=index&action=digg&contentid=" + c + "&jsoncallback=?&flag=" + b,
        function(d) {
            if (d > 0) {
                a.done = true;
                b == 1 ? a.render(d, a.data.againsts) : a.render(a.data.supports, d)
            }
        })
    },
    render: function(c, b) {
        c = parseInt(c);
        b = parseInt(b);
        var a = c + b ? c + b: false;
        a = Math.floor((a ? c / a: 0) * 100);
        $("#supports").children(0).html(c).next().html(a + "%");
        $("#againsts").children(0).html(b).next().html((a ? 100 - a: 0) + "%")
    }
};
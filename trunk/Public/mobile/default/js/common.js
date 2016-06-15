$(function() {
    $(".my_shop").click(function() {
        location.href = mobile_url + "member/home";
        /**
        if ($.cookie("uid") != null) {
            location.href = mobile_url + "member/home";
        } else {
            location.href = mobile_url + "order/cart";
        }*/
    });
});

    
var M = {
    version: 1,
    doc: document,
    win: window,
    w: document.body.offsetWidth,
    h: document.body.offsetHeight,
    ua: function() {
        return navigator.userAgent.toLowerCase()
    },
    isMobile: function() {
        return M.ua().match(/iPhone|iPad|iPod|Android|IEMobile/i)
    },
    isAndroid: function() {
        return - 1 != M.ua().indexOf("android") ? 1 : 0
    },
    isIOS: function() {
        var e = M.ua();
        return - 1 != e.indexOf("iphone") || -1 != e.indexOf("ipad") || -1 != e.indexOf("ipod") ? 1 : 0
    },
    platform: function() {
        return M.isMobile() ? M.isIOS() ? "IOS": M.isAndroid() ? "Android": "other-mobile": "PC"
    },
    _alert: function(e, t) {
        if ($("#_alert_bg").length) $("#_alert_bg").show();
        else {
            var n = document,
            r = n.createElement("div");
            r.setAttribute("id", "_alert_bg"),
            n.body.appendChild(r);
            var i = n.createElement("div");
            i.setAttribute("id", "_alert_content"),
            r.appendChild(i)
        }
        var o = $("#_alert_content");
        o.html(e).fadeIn(function() {
            setTimeout(function() {
                o.fadeOut(function() {
                    $("#_alert_bg").hide(),
                    t && t()
                })
            },
            1e3)
        })
    },
    _confirm: function(e, t, n, r, i) {
        var o = document,
        a = o.createElement("div");
        a.setAttribute("id", "_confirm_bg"),
        o.body.appendChild(a);
        var s = o.createElement("div");
        s.setAttribute("id", "_confirm_content"),
        a.appendChild(s);
        var l = $("#_confirm_content"),
        u = "";
        u = u + "<p>" + e + "</p>",
        u += "<em id='_confirm_shadowA'>&nbsp;</em>",
        u += "<em id='_confirm_shadowB'>&nbsp;</em>",
        u += "<div id='_confirm_btnW'>",
        n[0] ? (u = u + "<div id='_confirm_btnA'class='" + t[1] + "'>" + t[0] + "</div>", u += "<em id='_confirm_shadowC'>&nbsp;</em>", u += "<em id='_confirm_shadowD'>&nbsp;</em>", u = u + "<div id='_confirm_btnB' class='" + n[1] + "'>" + n[0] + "</div>") : u = u + "<div id='_confirm_btnA' class='" + t[1] + "'style='width: 100%'>" + t[0] + "</div>",
        u += "</div>",
        l.html(u).fadeIn(),
        $("#_confirm_btnA").bind("click",
        function() {
            i && i(),
            l.fadeOut(function() {
                $("#_confirm_bg").remove()
            })
        }),
        n[0] && $("#_confirm_btnB").bind("click",
        function() {
            r(),
            l.fadeOut(function() {
                $("#_confirm_bg").remove()
            })
        })
    },
    _prompt: function(e, t, n, r, i, o) {
        var a = document,
        s = a.createElement("div");
        s.setAttribute("id", "_prompt_bg"),
        a.body.appendChild(s);
        var l = a.createElement("div");
        l.setAttribute("id", "_prompt_content"),
        s.appendChild(l);
        var u = $("#_prompt_content"),
        c = "";
        c = c + "<p>" + e + "</p>",
        c += "<div id='_prompt_textareaW'>",
        c = c + "<textarea id='_prompt_textarea'>" + t + "</textarea>",
        c += "</div>",
        c += "<em id='_prompt_shadowA'>&nbsp;</em>",
        c += "<em id='_prompt_shadowB'>&nbsp;</em>",
        c += "<div id='_prompt_btnW'>",
        r[0] ? (c = c + "<div id='_prompt_btnA'class='" + n[1] + "'>" + n[0] + "</div>", c += "<em id='_prompt_shadowC'>&nbsp;</em>", c += "<em id='_prompt_shadowD'>&nbsp;</em>", c = c + "<div id='_prompt_btnB' class='" + r[1] + "'>" + r[0] + "</div>") : c = c + "<div id='_prompt_btnA' class='" + n[1] + "'style='width: 100%'>" + n[0] + "</div>",
        c += "</div>",
        u.html(c).fadeIn(),
        $("#_prompt_btnA").bind("click",
        function() {
            o && o(),
            u.fadeOut(function() {
                $("#_prompt_bg").remove()
            })
        }),
        r[0] && $("#_prompt_btnB").bind("click",
        function() {
            return $("#_prompt_textarea").val() ? (i($("#_prompt_textarea").val()), void u.fadeOut(function() {
                $("#_prompt_bg").remove()
            })) : void M._alert("请填写内容")
        })
    }       
};


Array.prototype.indexOf = function(val) {  
   for (var i = 0; i < this.length; i++) {  
	   if (this[i] == val) return i;  
   }  
   return -1;  
};  
Array.prototype.remove = function(val) {  
   var index = this.indexOf(val);  
   if (index > -1) {  
	   this.splice(index, 1);  
   }  
};  
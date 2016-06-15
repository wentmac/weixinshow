var MODAL_HTML = {
  _confirm: function(id, t, c, l, f) { //输出confirm的html代码
  	
    var dom_id = id;
    var tmp = '';
    tmp += '<div class="am-modal am-modal-confirm" tabindex="-1" id="' + dom_id + '">';
    tmp += '  <div class="am-modal-dialog">';
    tmp += '    <div class="am-modal-hd"><strong>{title}</strong><span data-am-modal-close class="am-close"><i class="am-icon-times-circle"></i></span></div>';
    tmp += '    <div class="am-modal-bd">{content}</div>';
    tmp += '    <div class="am-modal-footer">';
    tmp += '      <span class="am-modal-btn" data-am-modal-cancel>{cancel}</span>';
    tmp += '      <span class="am-modal-btn" data-am-modal-confirm>{confirm}</span>';
    tmp += '    </div>';
    tmp += '  </div>';
    tmp += '</div>';
    tmp = tmp.replace('{title}', t);
    tmp = tmp.replace('{content}', c);
    tmp = tmp.replace('{cancel}', l);
    tmp = tmp.replace('{confirm}', f);
    if ($("#" + dom_id).length > 0) {
      $("#" + dom_id).remove();
    }
    $("body").append(tmp);
  },
  _alert: function(id, t, c, f) {
    var dom_id = id;
    var tmp = '';
    tmp += '<div class="am-modal am-modal-alert" tabindex="-1" id="' + dom_id + '">';
    tmp += '  <div class="am-modal-dialog">';
    tmp += '    <div class="am-modal-hd"><strong>{title}</strong><span data-am-modal-close class="am-close"><i class="am-icon-times-circle"></i></span></div>';
    tmp += '    <div class="am-modal-bd">{content}</div>';
    tmp += '    <div class="am-modal-footer">';
    tmp += '      <span class="am-modal-btn">{confirm}</span>';
    tmp += '    </div>';
    tmp += '  </div>';
    tmp += '</div>';
    tmp = tmp.replace('{title}', t);
    tmp = tmp.replace('{content}', c);
    tmp = tmp.replace('{confirm}', f);
    if ($("#" + dom_id).length > 0) {
      $("#" + dom_id).remove();
    }
    $("body").append(tmp);
  },
  _prompt: function(id, t, c, l, f) {
    var dom_id = id;
    var tmp = '';
    tmp += '<div class="am-modal am-modal-prompt" tabindex="-1" id="' + dom_id + '">';
    tmp += '  <div class="am-modal-dialog">';
    tmp += '    <div class="am-modal-hd"><strong>{title}</strong><span data-am-modal-close class="am-close"><i class="am-icon-times-circle"></i></span></div>';
    tmp += '    <div class="am-modal-bd">{content}<input type="text" class="am-modal-prompt-input"></div>';
    tmp += '    <div class="am-modal-footer">';
    tmp += '      <span class="am-modal-btn" data-am-modal-cancel>{cancel}</span>';
    tmp += '      <span class="am-modal-btn" data-am-modal-confirm>{confirm}</span>';
    tmp += '    </div>';
    tmp += '  </div>';
    tmp += '</div>';
    tmp = tmp.replace('{title}', t);
    tmp = tmp.replace('{content}', c);
    tmp = tmp.replace('{cancel}', l);
    tmp = tmp.replace('{confirm}', f);
    if ($("#" + dom_id).length > 0) {
      $("#" + dom_id).remove();
    }
    $("body").append(tmp);
  }
};

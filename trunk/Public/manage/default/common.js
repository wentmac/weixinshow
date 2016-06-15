// JavaScript Document
$(function() {
  $("ul.menu li").hover(function() {
    $(this).addClass("hover");
    // $('ul:first',this).css('visibility', 'visible');
    $('ul:first', this).animate({
      opacity: "show",
      height: "show"
    }, "normal");
  }, function() {
    $(this).removeClass("hover");
    // $('ul:first',this).css('visibility', 'hidden');
    $('ul:first', this).animate({
      opacity: "hide",
      height: "hide"
    }, "100");
  });
  $("ul.menu li ul li:has(ul)").find("a:first").append(" &raquo; ");
});

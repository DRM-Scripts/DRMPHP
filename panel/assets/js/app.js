! function(s) {
  "use strict";
  var e, t = localStorage.getItem("language"), n = "en";

  function a(e) {
    document.getElementById("header-lang-img") && ("en" == e ? document.getElementById("header-lang-img").src = "assets/images/flags/us.jpg" : "sp" == e ? document.getElementById("header-lang-img").src = "assets/images/flags/spain.jpg" : "gr" == e ? document.getElementById("header-lang-img").src = "assets/images/flags/germany.jpg" : "it" == e ? document.getElementById("header-lang-img").src = "assets/images/flags/italy.jpg" : "ru" == e && (document.getElementById("header-lang-img").src = "assets/images/flags/russia.jpg"), localStorage.setItem("language", e), null == (t = localStorage.getItem("language")) && a(n), s.getJSON("assets/lang/" + t + ".json", function(e) {
      s("html").attr("lang", t), s.each(e, function(e, t) {
        "head" === e && s(document).attr("title", t.title), s("[key='" + e + "']").text(t)
      })
    }))
  }

  function c() {
    for (var e = document.getElementById("topnav-menu-content").getElementsByTagName("a"), t = 0, s = e.length; t < s; t++) "nav-item dropdown active" === e[t].parentElement.getAttribute("class") && (e[t].parentElement.classList.remove("active"), null !== e[t].nextElementSibling && e[t].nextElementSibling.classList.remove("show"))
  }

  function r(e) {
    1 == s("#light-mode-switch").prop("checked") && "light-mode-switch" === e 
    ? (
      s("html").removeAttr("dir"), 
      s("#dark-mode-switch").prop("checked", !1), 
      s("#bootstrap-style").attr("href", "assets/css/bootstrap.min.css"), 
      s("#app-style").attr("href", "assets/css/app.min.css"), 
      sessionStorage.setItem("is_visited", "light-mode-switch")
    ) 
    : 1 == s("#dark-mode-switch").prop("checked") && "dark-mode-switch" === e 
    ? (
      s("html").removeAttr("dir"), 
      s("#light-mode-switch").prop("checked", !1), 
      s("#bootstrap-style").attr("href", "assets/css/bootstrap-dark.min.css"), 
      s("#app-style").attr("href", "assets/css/app-dark.min.css"), 
      sessionStorage.setItem("is_visited", "dark-mode-switch")
    ) 
    : 1 == s("#rtl-mode-switch").prop("checked") && "rtl-mode-switch" === e 
    ? (
      s("#light-mode-switch").prop("checked", !1), 
      s("#dark-mode-switch").prop("checked", !1), 
      s("#bootstrap-style").attr("href", "assets/css/bootstrap-rtl.min.css"), 
      s("#app-style").attr("href", "assets/css/app-rtl.min.css"), 
      s("html").attr("dir", "rtl"), 
      sessionStorage.setItem("is_visited", "rtl-mode-switch")) 
    : 1 == s("#dark-rtl-mode-switch").prop("checked") && "dark-rtl-mode-switch" === e && (
      s("#light-mode-switch").prop("checked", !1), 
      s("#dark-mode-switch").prop("checked", !1), 
      s("#bootstrap-style").attr("href", "assets/css/bootstrap-dark-rtl.min.css"), 
      s("#app-style").attr("href", "assets/css/app-dark-rtl.min.css"), 
      s("html").attr("dir", "rtl"), sessionStorage.setItem("is_visited", "dark-rtl-mode-switch")
    )
  }

  function l() {
    document.webkitIsFullScreen || document.mozFullScreen || document.msFullscreenElement || (console.log("pressed"), s("body").removeClass("fullscreen-enable"))
  }
  s("#side-menu").metisMenu(), s("#vertical-menu-btn").on("click", function(e) {
    e.preventDefault(), s("body").toggleClass("sidebar-enable"), 992 <= s(window).width() ? s("body").toggleClass("vertical-collpsed") : s("body").removeClass("vertical-collpsed")
  }), s("#sidebar-menu a").each(function() {
    var e = window.location.href.split(/[?#]/)[0];
    this.href == e && (s(this).addClass("active"), s(this).parent().addClass("mm-active"), s(this).parent().parent().addClass("mm-show"), s(this).parent().parent().prev().addClass("mm-active"), s(this).parent().parent().parent().addClass("mm-active"), s(this).parent().parent().parent().parent().addClass("mm-show"), s(this).parent().parent().parent().parent().parent().addClass("mm-active"))
  }), s(document).ready(function() {
    var e;
    0 < s("#sidebar-menu").length && 0 < s("#sidebar-menu .mm-active .active").length && (300 < (e = s("#sidebar-menu .mm-active .active").offset().top) && (e -= 300, s(".vertical-menu .simplebar-content-wrapper").animate({
      scrollTop: e
      }, "slow")))
  }), s(".navbar-nav a").each(function() {
    var e = window.location.href.split(/[?#]/)[0];
    this.href == e && (s(this).addClass("active"), s(this).parent().addClass("active"), s(this).parent().parent().addClass("active"), s(this).parent().parent().parent().addClass("active"), s(this).parent().parent().parent().parent().addClass("active"), s(this).parent().parent().parent().parent().parent().addClass("active"), s(this).parent().parent().parent().parent().parent().parent().addClass("active"))
  }), s('[data-bs-toggle="fullscreen"]').on("click", function(e) {
    e.preventDefault(), s("body").toggleClass("fullscreen-enable"), document.fullscreenElement || document.mozFullScreenElement || document.webkitFullscreenElement ? document.cancelFullScreen ? document.cancelFullScreen() : document.mozCancelFullScreen ? document.mozCancelFullScreen() : document.webkitCancelFullScreen && document.webkitCancelFullScreen() : document.documentElement.requestFullscreen ? document.documentElement.requestFullscreen() : document.documentElement.mozRequestFullScreen ? document.documentElement.mozRequestFullScreen() : document.documentElement.webkitRequestFullscreen && document.documentElement.webkitRequestFullscreen(Element.ALLOW_KEYBOARD_INPUT)
  }), document.addEventListener("fullscreenchange", l), document.addEventListener("webkitfullscreenchange", l), document.addEventListener("mozfullscreenchange", l), s(".right-bar-toggle").on("click", function(e) {
    s("body").toggleClass("right-bar-enabled")
  }), s(document).on("click", "body", function(e) {
    0 < s(e.target).closest(".right-bar-toggle, .right-bar").length || s("body").removeClass("right-bar-enabled")
  }),
  function() {
    if (document.getElementById("topnav-menu-content")) {
      for (var e = document.getElementById("topnav-menu-content").getElementsByTagName("a"), t = 0, s = e.length; t < s; t++) e[t].onclick = function(e) {
        "#" === e.target.getAttribute("href") && (e.target.parentElement.classList.toggle("active"), e.target.nextElementSibling.classList.toggle("show"))
        };
      window.addEventListener("resize", c)
    }
  }(), [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]')).map(function(e) {
    return new bootstrap.Tooltip(e)
  }), [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]')).map(function(e) {
    return new bootstrap.Popover(e)
  }), [].slice.call(document.querySelectorAll(".offcanvas")).map(function(e) {
    return new bootstrap.Offcanvas(e)
  }), 
  window.sessionStorage && (
    (e = sessionStorage.getItem("is_visited")) ? 
    (s(".right-bar input:checkbox").prop("checked", !1), s("#" + e).prop("checked", !0), r(e)) 
    : sessionStorage.setItem("is_visited", "dark-mode-switch")), 
  s("#light-mode-switch, #dark-mode-switch").on("change", function(e) {r(e.target.id)}), 
  s("#password-addon").on("click", function() {
    0 < s(this).siblings("input").length && ("password" == s(this).siblings("input").attr("type") ? s(this).siblings("input").attr("type", "input") : s(this).siblings("input").attr("type", "password"))
  }), null != t && t !== n && a(t), s(".language").on("click", function(e) {
    a(s(this).attr("data-lang"))
  }), s(window).on("load", function() {
    s("#status").fadeOut(), s("#preloader").delay(350).fadeOut("slow")
  }), Waves.init(), s("#checkAll").on("change", function() {
    s(".table-check .form-check-input").prop("checked", s(this).prop("checked"))
  }), s(".table-check .form-check-input").change(function() {
    s(".table-check .form-check-input:checked").length == s(".table-check .form-check-input").length ? s("#checkAll").prop("checked", !0) : s("#checkAll").prop("checked", !1)
  })
}(jQuery);

function SetNotiSeen(id){
  $.post("_func.php",{Func:"SetNotiSeen", ID:id})
  .done(function(data){
    window.location.reload();
  })
}
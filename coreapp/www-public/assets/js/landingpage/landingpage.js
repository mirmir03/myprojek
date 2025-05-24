$(function () {

    // scroll

    $(".scroll-link").on("click", function (t) {
        var o = $(this);
        $("html, body").stop().animate({
            scrollTop: $(o.attr("href")).offset().top - 10
        }, 1e3), t.preventDefault()
    })

    // Aos

    AOS.init({
        once: true,
    });

});
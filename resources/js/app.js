require("./bootstrap");


$(window).ready(function () {
    $(".table-responsive").on("show.bs.dropdown", function () {
        $(".table-responsive").css("overflow", "inherit");
    });

    $(".table-responsive").on("hide.bs.dropdown", function () {
        $(".table-responsive").css("overflow", "auto");
    })
});

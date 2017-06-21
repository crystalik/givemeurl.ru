$(document).ready(function () {init();});

function init() {
    $("form").on("submit", function () {
        var url = $("form input[name='url']").val();
        $.get(
            "/getshort",
            {"url": url},
            function (response) {
                if (typeof response.error != "undefined") {
                    $("#shorturl").text(response.error);
                } else {
                    $("#shorturl").text("http://givemeurl.ru/" + response.result);
                }
            }
        );
        return false;
    });
}

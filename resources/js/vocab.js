$("#refresh").on("click", function () {
    $.ajax({
        url: "/vocabs",
        type: "GET",
        success: function (response) {
            // handle response
            // console.log(response);
            let $responseHtml = $("<div>").html(response);
            if ($responseHtml.find("#vocabs").length > 0) {
                $("#vocabs").replaceWith($responseHtml.find("#vocabs"));
            }
        },
        error: function (err) {
            // handle error
        },
    });
});

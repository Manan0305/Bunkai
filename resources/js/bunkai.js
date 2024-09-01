$(function () {
    let tokenizer = null;
    kuromoji
        .builder({
            dicPath: import.meta.env.VITE_KUROMOJI_DICT_PATH,
        })
        .build(function (err, tk) {
            tokenizerReadyCallback(tk);
        });

    $("#submit").prop("disabled", true);
    $("#loading-spinner").show();
    const tokenizerReadyCallback = (tk) => {
        // tokenizer is ready
        console.log("tokenizer is ready");
        tokenizer = tk;
        $("#submit").prop("disabled", false);
        $("#loading-spinner").hide();
    };

    // const fetchMeaning = async (word) => {
    //     const URL = `https://jisho.org/api/v1/search/words?keyword=${encodeURIComponent(
    //         word
    //     )}`;
    //     console.log(URL);
    //     const response = await fetch(URL, { mode: "no-cors" });
    //     console.log(response);
    //     return response;
    // };

    // const generateMeanings = async (path) => {
    //     const KNOWN_WORDS = [];
    //     console.log(path);
    //     path.forEach((word) => {
    //         if (word.word_type === "KNOWN") {
    //             KNOWN_WORDS.push(word.basic_form);
    //         }
    //     });
    //     console.log(KNOWN_WORDS);
    //     const promises = KNOWN_WORDS.map((word) => fetchMeaning(word));
    // };

    $("#vocabs").on("click", function () {
        window.open("/vocabs", "_blank");
    });

    // $("#exampleModal").on("change", 'input[type="radio"]', function () {});

    $("#submit-question").on("click", function () {
        let $checkedAnswer = $("#modal-body").find(
            'input[type="radio"]:checked'
        );
        if ($checkedAnswer.length > 0) {
            let id = $checkedAnswer.attr("id");
            let $checkedAnswerText = $("#modal-body").find(
                'label[for="' + id + '"]'
            );
            let answer = $checkedAnswerText.text().trim();
            let word = $("#modal-body").find("#word").attr("name");
            $.ajax({
                url: "/test/vocab",
                type: "POST",
                contentType: "application/json",
                data: JSON.stringify({
                    word: word,
                    meaning: answer,
                }),
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                        "content"
                    ),
                },
                success: function (response) {
                    // handle response
                    let $responseHtml = $("<div>").html(response);
                    let $testResult = $responseHtml.find("#test-result");
                    $("#test-result").replaceWith($testResult);
                },
                error: function (err) {
                    // handle error
                },
            });
        }
    });

    $("#test, #update-question").on("click", function () {
        $.ajax({
            url: "/test",
            type: "GET",
            success: function (response) {
                // handle response
                // console.log(response);
                let $responseHtml = $("<div>").html(response);
                if ($responseHtml.find("#modal-body").length > 0) {
                    $("#modal-body").replaceWith(
                        $responseHtml.find("#modal-body")
                    );
                    if (
                        !$("#modal-body").find('input[type="radio"]').length > 0
                    ) {
                        $("#submit-question").prop("disabled", true);
                    }
                }
                $("#test-result").text("");
            },
            error: function (err) {
                // handle error
                // console.log(err);
            },
        });
    });

    $("#output").on("click", "svg", function () {
        // alert("List item clicked: " + $(this).attr("custom-action"));
        let $currentParagraph = $(this);
        const result_dict = {};
        const [word, meaning] = $(this).closest("p").text().trim().split(":");
        const vocab = {
            word: word.trim(),
            meaning: meaning.trim(),
        };
        let results = $("#results p")
            .map(function () {
                return $(this).text().trim();
            })
            .get();
        results.forEach((element) => {
            const [word, meaning] = element.split(":");
            result_dict[word.trim()] = meaning.trim();
        });

        if ($(this).attr("custom-action") == "add") {
            $.ajax({
                url: "/vocabs/add",
                type: "POST",
                contentType: "application/json",
                data: JSON.stringify({
                    vocab: vocab,
                    results: result_dict,
                }),
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                        "content"
                    ),
                },
                success: function (response) {
                    // handle response
                    // console.log(response);
                    let $responseHtml = $("<div>").html(response);
                    let id = $currentParagraph.attr("id");
                    // console.log(id);
                    if ($responseHtml.find("#" + id).length > 0) {
                        // console.log("hello");
                        $("#" + id).replaceWith($responseHtml.find("#" + id));
                    }
                },
                error: function (err) {
                    // handle error
                },
            });
        } else {
            $.ajax({
                url: "/vocabs/delete",
                type: "POST",
                contentType: "application/json",
                data: JSON.stringify({
                    word: vocab["word"],
                    results: result_dict,
                }),
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                        "content"
                    ),
                },
                success: function (response) {
                    // handle response
                    let $responseHtml = $("<div>").html(response);
                    let id = $currentParagraph.attr("id");
                    if ($responseHtml.find("#" + id).length > 0) {
                        $("#" + id).replaceWith($responseHtml.find("#" + id));
                    }
                },
                error: function (err) {
                    // handle error
                    // console.log(err);
                },
            });
        }
    });

    $("#frmMain").on("submit", (e) => {
        $("#error-message").text("");
        e.preventDefault();
        $("#submit").prop("disabled", true);
        $("#loading-spinner").show();
        const sentence = $("#sentence").val();
        let path = tokenizer.tokenize(sentence);
        // console.log(path);
        const KNOWN_WORDS = [];
        path.forEach((word) => {
            if (word.word_type === "KNOWN") {
                KNOWN_WORDS.push(word.surface_form);
            }
        });
        // console.log(KNOWN_WORDS);
        $.ajax({
            url: "/",
            type: "POST",
            contentType: "application/json",
            data: JSON.stringify({ words: KNOWN_WORDS }),
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            success: function (response) {
                // Handle response here
                // console.log(response);
                let $responseHtml = $("<div>").html(response);
                if ($responseHtml.find("#results").length > 0) {
                    $("#results").replaceWith($responseHtml.find("#results"));
                }
                $("#submit").prop("disabled", false);
                $("#loading-spinner").hide();
                $("#sentence").val("");
            },
            error: function (err) {
                // Handle error here
                console.log(err);
                if (err.status === 419 || err.status == 401) {
                    swal.fire({
                        title: "Session Expired",
                        text: "Your session has expired ! Please reload the page to continue.",
                        icon: "warning",
                        confirmButtonText: "Reload Page",
                        showCancelButton: true,
                        cancelButtonText: "Close",
                    }).then((result) => {
                        if (result.isConfirmed) {
                            location.reload();
                        }
                    });
                } else {
                    $("#error-message").text(
                        "Sentence too long. Please write a shorter sentence!"
                    );
                }
                $("#submit").prop("disabled", false);
                $("#loading-spinner").hide();
                $("#sentence").val("");
            },
        });
    });
});

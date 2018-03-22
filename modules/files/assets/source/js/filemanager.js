$(document).ready(function() {
    var ajaxRequest = null,
        fileInfoContainer = $("#fileinfo"),
        strictThumb = $(window.frameElement).parents('[role="filemanager-modal"]').attr("data-thumb");

    function setAjaxLoader() {
        $("#fileinfo").html(
            '<div class="progress">' +
            '<div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%; height: 50px;">' +
            '<span class="sr-only">45% Complete</span>' +
            '</div>' +
            '</div>');
    }

    $('[href="#mediafile"]').on("click", function(e) {
        e.preventDefault();

        if (ajaxRequest) {
            ajaxRequest.abort();
            ajaxRequest = null;
        }

        $(".item a").removeClass("active");
        $(this).addClass("active");
        var _csrf = yii.getCsrfToken();
        var id = $(this).attr("data-key");
        var url = $("#filemanager").attr("data-url-info");

        ajaxRequest = $.ajax({
            type: "POST",
            url: url,
            data: "_csrf=" + _csrf + "&id=" + id + "&strictThumb=" + strictThumb,
            beforeSend: function() {
                setAjaxLoader();
            },
            success: function(html) {
                $("#fileinfo").html(html);
            }
        });
    });

    fileInfoContainer.on("click", '[role="delete"]', function(e) {
        e.preventDefault();

        var url = $(this).attr("href"),
            id = $(this).attr("data-id"),
            confirmMessage = $(this).attr("data-message");

        $.ajax({
            type: "POST",
            url: url,
            data: "id=" + id,
            beforeSend: function() {
                if (!confirm(confirmMessage)) {
                    return false;
                }
                $("#fileinfo").html('<div class="loading"><span class="glyphicon glyphicon-refresh spin"></span></div>');
            },
            success: function(json) {
                if (json.success) {
                    $("#fileinfo").html('');
                    $('[data-key="' + id + '"]').fadeOut();
                }
            }
        });
    });

    fileInfoContainer.on("submit", "#control-form", function(e) {
        e.preventDefault();

        var url = $(this).attr("action"),
            data = $(this).serialize();

        $.ajax({
            type: "POST",
            url: url,
            data: data,
            beforeSend: function() {
                setAjaxLoader();
            },
            success: function(html) {
                $("#fileinfo").html(html);
            }
        });
    });
});

$(document).ready(function() {
    window.ajaxRequest = null;
    window.fileInfoContainer = $('[role="fileinfo"]');
    window.fileManagerContainer = $('[role="filemanager"]');
    window.filemanagerModalContainer = $('[role="filemanager-modal"]');

    function setAjaxLoader() {
        window.fileInfoContainer.html(
            '<div class="progress">' +
            '<div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%; height: 50px;">' +
            '<span class="sr-only">45% Complete</span>' +
            '</div>' +
            '</div>');
    }

    function getFileInfo(id) {

        if (window.ajaxRequest) {
            window.ajaxRequest.abort();
            window.ajaxRequest = null;
        }

        var strictThumb = window.filemanagerModalContainer.attr("data-thumb");

        var _csrf = yii.getCsrfToken();
        var url = window.fileManagerContainer.attr("data-url-info");

        window.ajaxRequest = $.ajax({
            type: "POST",
            url: url,
            data: "_csrf=" + _csrf + "&id=" + id + "&strictThumb=" + strictThumb,
            beforeSend: function() {
                setAjaxLoader();
            },
            success: function(html) {
                window.fileInfoContainer.html(html);
            }
        });
    }

    $('[href="#mediafile"]').on("click", function(e) {
        e.preventDefault();

        $(".item a").removeClass("active");
        $(this).addClass("active");

        var id = $(this).attr("data-key");

        getFileInfo(id);
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
                window.fileInfoContainer.html('<div class="loading"><span class="glyphicon glyphicon-refresh spin"></span></div>');
            },
            success: function(json) {
                if (json.success) {
                    window.fileInfoContainer.html('');
                    $('[data-key="' + id + '"]').fadeOut();
                }
            }
        });
    });

    window.fileInfoContainer.on("submit", "#control-form", function(e) {
        e.preventDefault();

        var url = $(this).attr("action"),
            data = $(this).serialize(),
            id = $('[role="file-inputs"]').attr("data-file-id");

        $.ajax({
            type: "POST",
            url: url,
            data: data,
            beforeSend: function() {
                setAjaxLoader();
            },
            success: function(json) {
                //alert(json.meta.message);
                getFileInfo(id);
            }
        });
    });
});

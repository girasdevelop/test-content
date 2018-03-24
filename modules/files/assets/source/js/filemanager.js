$(document).ready(function() {
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

        var strictThumb = window.filemanagerModalContainer.attr("data-thumb");
        var url = window.fileManagerContainer.attr("data-url-info");
        var params = {
            _csrf: yii.getCsrfToken(),
            id: id,
            strictThumb: strictThumb
        };

        AJAX(url, 'POST', params, false, function () {
            setAjaxLoader();

        }, function(data) {
            window.fileInfoContainer.html(data);

        }, function(data, xhr) {
            alert('Server Error!');

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

    fileInfoContainer.on("click", '[role="update"]', function(e) {
        e.preventDefault();

        var url = $('[role="file-inputs"]').attr("data-save-src");

        var params = {
            _csrf: yii.getCsrfToken(),
            id: $('[role="file-inputs"]').attr("data-file-id"),
            description: $('[name="description"]').val()
        };

        if ($('[role="file-inputs"]').attr("data-is-image") == true){
            params.alt = $('[name="alt"]').val();
        }

        var fileInput = $('[name="file"]');
        if (fileInput[0].files[0]) {
            params.files = {
                file: fileInput[0].files[0]
            }
        }

        AJAX(url, 'POST', params, true, function () {
            setAjaxLoader();

        }, function(data) {
            alert(data.meta.message);
            getFileInfo(params.id);

        }, function(data, xhr) {
            alert('Server Error!');

        });
    });
});

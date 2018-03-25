$(document).ready(function() {
    window.fileInfoContainer = $('[role="fileinfo"]');
    window.fileManagerContainer = $('[role="filemanager"]');
    window.filemanagerModalContainer = $('[role="filemanager-modal"]');

    function getFileInfo(id, isAjaxLoader) {

        var popupElement = $('[role="popup"]');
        var strictThumb = window.filemanagerModalContainer.attr("data-thumb");
        var url = window.fileManagerContainer.attr("data-url-info");
        var params = {
            _csrf: yii.getCsrfToken(),
            id: id,
            strictThumb: strictThumb
        };

        AJAX(url, 'POST', params, false, function () {
            if (isAjaxLoader){
                setAjaxLoader(popupElement);
            }
        }, function(data) {
            window.fileInfoContainer.html(data);
            if (isAjaxLoader){
                closeContainer(popupElement);
            }

        }, function(data, xhr) {
            showPopup(popupElement, 'Server Error!', true);
        });
    }

    $('[href="#mediafile"]').on("click", function(e) {
        e.preventDefault();

        $(".item a").removeClass("active");
        $(this).addClass("active");

        var id = $(this).attr("data-key");

        getFileInfo(id, true);
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
        var popupElement = $('[role="popup"]');

        var params = {
            _csrf: yii.getCsrfToken(),
            id: $('[role="file-inputs"]').attr("data-file-id"),
            description: $('[role="file-description"]').val()
        };

        if ($('[role="file-inputs"]').attr("data-is-image") == true){
            params.alt = $('[role="file-alt"]').val();
        }

        var fileInput = $('[role="file-new"]');
        if (fileInput[0].files[0]) {
            params.files = {
                file: fileInput[0].files[0]
            }
        }

        AJAX(url, 'POST', params, true, function () {
            setAjaxLoader(popupElement);

        }, function(data) {

            if (data.meta.status == 'success'){
                showPopup(popupElement, data.meta.message, false);
            } else {
                showPopup(popupElement, data.meta.message, true);
            }

            getFileInfo(params.id, false);

        }, function(data, xhr) {
            showPopup(popupElement, 'Server Error!', true);
            getFileInfo(params.id);
        });
    });
});

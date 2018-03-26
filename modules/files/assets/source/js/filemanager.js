$(document).ready(function() {
    window.fileInfoContainer = $('[role="fileinfo"]');
    window.fileManagerContainer = $('[role="filemanager"]');
    window.filemanagerModalContainer = $('[role="filemanager-modal"]');

    /**
     * Get file information function.
     * @param id
     * @param isAjaxLoader
     */
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
                clearContainer(popupElement);
            }

        }, function(data, xhr) {
            showPopup(popupElement, 'Server Error!', true);
        });
    }

    /**
     * Get file information by click on the media file item.
     */
    $('[href="#mediafile"]').on("click", function(e) {
        e.preventDefault();

        $(".item a").removeClass("active");
        $(this).addClass("active");

        var id = $(this).attr("data-key");

        getFileInfo(id, true);
    });

    /**
     * Update file information.
     */
    window.fileInfoContainer.on("click", '[role="update"]', function(e) {
        e.preventDefault();

        var fileInputs = $('[role="file-inputs"]'),
            url = fileInputs.attr("data-save-src"),
            popupElement = $('[role="popup"]'),
            params = {
                _csrf: yii.getCsrfToken(),
                id: fileInputs.attr("data-file-id"),
                description: $('[role="file-description"]').val()
            };

        if (fileInputs.attr("data-is-image") == true){
            params.alt = $('[role="file-alt"]').val();
        }

        var fileInputField = $('[role="file-new"]');
        if (fileInputField[0].files[0]) {
            params.files = {
                file: fileInputField[0].files[0]
            }
        }

        AJAX(url, 'POST', params, true, function () {
            setAjaxLoader(popupElement);

        }, function(data) {

            if (data.meta.status == 'success'){
                showPopup(popupElement, data.meta.message, false);
                getFileInfo(params.id, false);
                if (data.data.files && data.data.files[0]){
                    $('[data-key="' + params.id + '"] img:first').attr('src', '/' + data.data.files[0].thumbnailUrl);
                }
            } else {
                showPopup(popupElement, data.data.errors, true, 4000);
            }

        }, function(data, xhr) {
            showPopup(popupElement, data.message, true);
            getFileInfo(params.id);
        });
    });

    window.fileManagerContainer.find(".redactor").on('click', '[role="insert"]', function(e) {
        e.preventDefault();

        var modal = getModal(),
            imageContainer = $(modal.attr("data-image-container")),
            insertedData = modal.attr("data-inserted-data"),
            mainInput = $("#" + modal.attr("data-input-id")),
            fileInputs = window.fileManagerContainer.find('[role="file-inputs"]');

        mainInput.trigger("fileInsert", [insertedData]);

        if (imageContainer) {
            imageContainer.html('<img src="/' + fileInputs.attr("data-file-url") + '">');
        }

        mainInput.val(fileInputs.attr("data-file-" + insertedData));
        modal.modal("hide");
    });

    /**
     * Delete file.
     */
    window.fileInfoContainer.on("click", '[role="delete"]', function(e) {
        e.preventDefault();

        var fileInputs = $('[role="file-inputs"]'),
            url = fileInputs.attr("data-delete-src"),
            confirmMessage = fileInputs.attr("data-confirm-message"),
            popupElement = $('[role="popup"]'),
            params = {
                _csrf: yii.getCsrfToken(),
                id: fileInputs.attr("data-file-id")
            };

        if (confirm(confirmMessage)) {
            AJAX(url, 'POST', params, true, function () {
                setAjaxLoader(popupElement);

            }, function(data) {

                if (data.meta.status == 'success'){
                    $('[data-key="' + params.id + '"]').fadeOut();
                    clearContainer(window.fileInfoContainer);
                    showPopup(popupElement, data.meta.message, false);
                } else {
                    showPopup(popupElement, data.meta.message, true, 4000);
                }

            }, function(data, xhr) {
                showPopup(popupElement, data.message, true);
            });
        }
    });
});

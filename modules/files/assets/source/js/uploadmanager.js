$(document).ready(function() {
    window.fileNumber = null;
    window.preparedFiles = {};
    window.workspace = $('#workspace');
    window.uploadManagerContainer = $('[role="uploadmanager"]');
    window.fileManagerModalContainer = $(window.frameElement).parents('[role="filemanager-modal"]');

    /**
     * Add file to upload.
     */
    $('[role="add-file"]').change(function(event) {

        var file = event.target.files[0],
            tmpPath = URL.createObjectURL(file),
            fileType = file.type,
            baseUrl = window.uploadManagerContainer.attr('data-base-url'),
            preview;

        preview = getPreview({fileType: fileType, fileUrl: tmpPath, baseUrl: baseUrl});

        if (window.fileNumber == null){
            window.fileNumber = 1;
        } else {
            window.fileNumber += 1;
        }

        window.preparedFiles[window.fileNumber] = file;

        var newHtml = renderTemplate('file-template', {
                preview: preview,
                title: file.name,
                size: displayFileSize(file.size),
                fileNumber: window.fileNumber
            }),
            workspace = $('#workspace'),
            oldHtml = workspace.html();

        workspace.html(oldHtml + newHtml);
    });

    /**
     * Single upload file.
     */
    window.workspace.on("click", '[role="upload-file"]', function(e) {
        e.preventDefault();

        var fileNumber = $(this).attr('data-file-number'),
            fileBlock = $(this).parents('[role="file-block"]'),
            progressBlock = fileBlock.find('[role="progress-block"]'),
            buttonBlockUpload = fileBlock.find('[role="button-block-upload"]'),
            buttonBlockDelete = fileBlock.find('[role="button-block-delete"]'),
            url = window.uploadManagerContainer.attr('data-save-src'),
            subDir = window.fileManagerModalContainer.attr("data-sub-dir"),
            params = {
                _csrf: yii.getCsrfToken(),
                title: fileBlock.find('[role="file-title"]').val(),
                description: fileBlock.find('[role="file-description"]').val(),
                files: {
                    file: window.preparedFiles[fileNumber]
                }
            };

        if (subDir && subDir != ''){
            params.subDir = subDir;
        }

        AJAX(url, 'POST', params, true, function () {
            setAjaxLoader(progressBlock, 1000);

        }, function(data) {

            if (data.meta.status == 'success'){
                clearContainer(progressBlock);
                clearContainer(buttonBlockUpload);
                buttonBlockDelete.css('display', 'block');

                if (data.data.files && data.data.files[0]){
                    fileBlock.find('[role="delete-file-button"]').attr('data-file-id', data.data.files[0].id);
                }

            } else {
                showPopup(progressBlock, data.data.errors, true, 0);
            }

        }, function(data, xhr) {
            showPopup(progressBlock, data.message, true, 0);
        });
    });

    /**
     * Single delete file.
     */
    window.workspace.on("click", '[role="delete-file-button"]', function(e) {
        e.preventDefault();

        var fileNumber = $(this).attr('data-file-number'),
            fileBlock = $(this).parents('[role="file-block"]'),
            progressBlock = fileBlock.find('[role="progress-block"]'),
            url = window.uploadManagerContainer.attr('data-delete-src'),
            params = {
                _csrf: yii.getCsrfToken(),
                id: $(this).attr('data-file-id')
            };

        AJAX(url, 'POST', params, true, function () {

        }, function(data) {

            if (data.meta.status == 'success'){
                delete window.preparedFiles[fileNumber];
                fileBlock.fadeOut();
            } else {
                showPopup(progressBlock, data.meta.message, true, 0);
            }

        }, function(data, xhr) {
            showPopup(progressBlock, data.message, true, 0);
        });
    });

    /**
     * Cancel before single upload.
     */
    window.workspace.on("click", '[role="cancel-upload"]', function(e) {
        e.preventDefault();

        var fileNumber = $(this).attr('data-file-number'),
            fileBlock = $(this).parents('[role="file-block"]');

        delete window.preparedFiles[fileNumber];

        fileBlock.fadeOut();
    });

    /**
     * Total cancel upload.
     */
    $('[role="total-cancel-upload"]').on("click", function(e) {
        e.preventDefault();

        window.workspace.find('[role="cancel-upload"]').click();
    });

    /**
     * Total upload.
     */
    $('[role="total-upload-file"]').on("click", function(e) {
        e.preventDefault();

        window.workspace.find('[role="upload-file"]').click();
    });

    /**
     * Total delete.
     */
    $('[role="total-delete-file-button"]').on("click", function(e) {
        e.preventDefault();

        window.workspace.find('input:checked[role="delete-file-checkbox"]').each(function () {
            $(this).siblings('[role="delete-file-button"]').click();
        });
    });

    /**
     * Total check for delete.
     */
    $('[role="total-delete-file-checkbox"]').change(function() {

        var allCheckBoxes = window.workspace.find('[role="delete-file-checkbox"]');

        if (this.checked) {
            allCheckBoxes.prop('checked', true);
        } else {
            allCheckBoxes.prop('checked', false);
        }
    });

    /**
     * Render data for upload file by template.
     * @param name
     * @param data
     * @returns {string}
     */
    function renderTemplate(name, data) {
        var template = document.getElementById(name).innerHTML;

        for (var property in data) {
            if (data.hasOwnProperty(property)) {
                var search = new RegExp('{' + property + '}', 'g');
                template = template.replace(search, data[property]);
            }
        }
        return template;
    }

    /**
     * Display file size in smart format.
     * @param size
     * @returns {string}
     */
    function displayFileSize(size) {
        var kbSize = size/1024;
        if (kbSize < 1024){
            return kbSize.toFixed(2) + ' KB';
        } else {
            return (kbSize/1024).toFixed(2) + ' MB';
        }
    }
});

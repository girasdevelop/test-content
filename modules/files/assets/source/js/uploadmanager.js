$(document).ready(function() {
    window.fileNumber = null;
    window.preparedFiles = {};
    window.workspace = $('#workspace');
    window.uploadManagerContainer = $('[role="uploadmanager"]');

    $('[role="add-file"]').change(function(event) {

        var file = event.target.files[0],
            tmpPath = URL.createObjectURL(file),
            fileType = file.type,
            baseUrl = window.uploadManagerContainer.attr('data-base-url'),
            preview;

        switch (fileType.split('/')[0]) {
            case 'image':
                preview = '<img src="' + tmpPath + '">';
                break;

            case 'audio':
                preview =
                    '<audio controls>' +
                    '<source src="' + tmpPath + '" type="' + fileType + '" preload="auto" >' +
                    '<track kind="subtitles">' +
                    '</audio>';
                break;

            case 'video':
                preview =
                    '<video controls width="300" height="240">' +
                    '<source src="' + tmpPath + '" type="' + fileType + '" preload="auto" >' +
                    '<track kind="subtitles">' +
                    '</video>';
                break;

            case 'text':
                preview = '<img src="' + baseUrl + '/images/text.png' + '">';
                break;

            case 'application':
                if (strpos({str: fileType.split('/')[1], find: 'word', index: 1})){
                    preview = '<img src="' + baseUrl + '/images/word.png' + '">';

                } else if (strpos({str: fileType.split('/')[1], find: 'excel', index: 1})){
                    preview = '<img src="' + baseUrl + '/images/excel.png' + '">';

                } else if (fileType.split('/')[1] == 'pdf'){
                    preview = '<img src="' + baseUrl + '/images/pdf.png' + '">';

                } else {
                    preview = '<img src="' + baseUrl + '/images/app.png' + '">';
                }
                break;

            default:
                preview = '<img src="' + baseUrl + '/images/other.png' + '">';
        }

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

    window.workspace.on("click", '[role="upload-file"]', function(e) {
        e.preventDefault();

        var fileNumber = $(this).attr('data-file-number'),
            fileBlock = $(this).parents('[role="file-block"]'),
            url = window.uploadManagerContainer.attr('data-save-src'),
            subDir = window.uploadManagerContainer.attr("data-sub-dir"),
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

        /*AJAX(url, 'POST', params, true, function () {
            setAjaxLoader(popupElement);

        }, function(data) {

            if (data.meta.status == 'success'){
                showPopup(popupElement, data.meta.message, false);
                getFileInfo(params.id, false);
                if (data.data.files && data.data.files[0]){
                    $('[data-key="' + params.id + '"] img:first').attr('src', data.data.files[0].thumbnailUrl);
                }
            } else {
                showPopup(popupElement, data.data.errors, true, 4000);
            }

        }, function(data, xhr) {
            showPopup(popupElement, data.message, true);
            getFileInfo(params.id);
        });*/
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
     * Analog for "strpos" php function.
     * @param data
     * @returns {null}
     */
    function strpos(data) {
        // Created by Mark Tali [webcodes.ru]
        // Example. Return 8, but if index > 2, then return null
        // strpos({str: 'Bla-bla-bla...', find: 'bla', index: 2});
        var haystack = data.str, needle = data.find, offset = 0;
        for (var i = 0; i < haystack.split(needle).length; i++) {
            var index = haystack.indexOf(needle, offset + (data.index != 1 ? 1 : 0));
            if (i == data.index - 1) return (index != -1 ? index : null); else offset = index;
        }
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

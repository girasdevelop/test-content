$(document).ready(function() {

    /**
     * Handler to catch press on insert button.
     *
     * @param e
     */
    function frameInsertHandler(e) {

        var modal = $(this).parents('[role="filemanager-modal"]');

        $(this).contents().find(".redactor").on('click', '[role="insert"]', function(e) {
            e.preventDefault();

            var fileInputs = $(this).parents('[role="file-inputs"]'),
                imageContainer = $(modal.attr("data-image-container")),
                insertedData = modal.attr("data-inserted-data"),
                mainInput = $("#" + modal.attr("data-input-id"));

            mainInput.trigger("fileInsert", [insertedData]);

            if (imageContainer) {
                imageContainer.html('<img src="' + fileInputs.attr("data-file-url") + '">');
            }

            mainInput.val(fileInputs.attr("data-file-" + insertedData));
            modal.modal("hide");
        });
    }

    /**
     * Load file manager.
     */
    $('[role="filemanager-load"]').on("click", function(e) {
        e.preventDefault();

        var modal = $('div[role="filemanager-modal"]');
        var srcToFiles = modal.attr("data-src-to-files");
        var owner = modal.attr("data-owner");
        var ownerId = modal.attr("data-owner-id");
        var ownerAttribute = modal.attr("data-owner-attribute");

        var paramsArray = [];
        var paramsQuery = '';

        if (owner){
            paramsArray.owner = owner;

            if (ownerId){
                paramsArray.ownerId = ownerId;
            }
        }

        if (ownerAttribute){
            paramsArray.ownerAttribute = ownerAttribute;
        }

        for (var index in paramsArray){
            var paramString = index + '=' + paramsArray[index];
            paramsQuery += paramsQuery == '' ? paramString : '&' + paramString;
        }

        if (paramsQuery != ''){
            srcToFiles += '?' + paramsQuery;
        }

        var iframe = $('<iframe src="' + srcToFiles + '" frameborder="0" role="filemanager-frame"></iframe>');

        iframe.on("load", frameInsertHandler);
        modal.find(".modal-body").html(iframe);
        modal.modal("show");
    });

    /**
     * Clear value in main input.
     */
    $('[role="clear-input"]').on("click", function(e) {
        e.preventDefault();

        $("#" + $(this).attr("data-clear-element-id")).val("");
        $($(this).attr("data-image-container")).empty();
    });
});

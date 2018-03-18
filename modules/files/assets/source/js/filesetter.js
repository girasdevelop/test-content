function getFormData(form) {
    var formArray = form.serializeArray(),
        modelMap = {
            'Mediafile[alt]': 'alt',
            'Mediafile[description]': 'description',
            url: 'url',
            id: 'id'
        },
        data = [];

    for (var i=0; formArray.length > i; i++) {
        if (modelMap[formArray[i].name]) {
            data[modelMap[formArray[i].name]] = formArray[i].value;
        }
    }

    return data;
}

$(document).ready(function() {

    function frameHandler(e) {
        var modal = $(this).parents('[role="filemanager-modal"]'),
            imageContainer = $(modal.attr("data-image-container")),
            pasteData = modal.attr("data-paste-data"),
            input = $("#" + modal.attr("data-input-id"));

        $(this).contents().find(".dashboard").on("click", "#insert-btn", function(e) {
            e.preventDefault();

            var data = getFormData($(this).parents("#control-form"));

            input.trigger("fileInsert", [data]);

            if (imageContainer) {
                imageContainer.html('<img src="' + data.url + '" alt="' + data.alt + '">');
            }

            input.val(data[pasteData]);
            modal.modal("hide");
        });
    }

    $('[role="filemanager-launch"]').on("click", function(e) {
        e.preventDefault();

        var modal = $('div[role="filemanager-modal"]');
            //iframe = $('<iframe src="' + modal.attr("data-frame-src") + '" id="' + modal.attr("data-frame-id") + '" frameborder="0" role="filemanager-frame"></iframe>');

        //iframe.on("load", frameHandler);

        $.ajax({
            type: "POST",
            url: modal.attr("data-frame-src"),
            data: "owner='post'",
            success: function(data) {
                modal.find(".modal-body").html(data);
                modal.modal("show");
            },
            error: function(data){
                alert('Status code: ' + data.status);
            }
        });
    });

    $('[role="clear-input"]').on("click", function(e) {
        e.preventDefault();

        $("#" + $(this).attr("data-clear-element-id")).val("");
        $($(this).attr("data-image-container")).empty();
    });
});
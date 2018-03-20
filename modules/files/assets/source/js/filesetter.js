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

        var modal = getModal();
        var owner = modal.attr("data-owner");
        var ownerId = modal.attr("data-owner-id");
        var ownerAttribute = modal.attr("data-owner-attribute");

        getFiles(1, owner, ownerId, ownerAttribute);
    });

    $('[role="clear-input"]').on("click", function(e) {
        e.preventDefault();

        $("#" + $(this).attr("data-clear-element-id")).val("");
        $($(this).attr("data-image-container")).empty();
    });
});

function getFiles(page, owner, ownerId, ownerAttribute) {

    if (!page || null === page){
        page = 1;
    }

    var modal = getModal();

    var params = 'page=' + page;

    if (owner && ownerId){
        params += '&owner=' + owner + '&ownerId=' + ownerId;
    }

    if (ownerAttribute){
        params += '&ownerAttribute=' + ownerAttribute;
    }

    $.ajax({
        type: "GET",
        url: modal.attr("data-src-to-files"),
        data: params,
        success: function(data) {
            modal.find(".modal-body").html(data);
            modal.modal("show");
        },
        error: function(data){
            alert('Status code: ' + data.status);
        }
    });
}

function getModal() {
    return $('div[role="filemanager-modal"]');
}

$(document).ready(function() {

    $('[role="filemanager-launch"]').on("click", function(e) {
        e.preventDefault();

        var modal = getModal();
        var srcToFiles = modal.attr("data-src-to-files");
        var owner = modal.attr("data-owner");
        var ownerId = modal.attr("data-owner-id");
        var ownerAttribute = modal.attr("data-owner-attribute");

        if (owner && ownerId){
            srcToFiles += '?owner=' + owner + '&ownerId=' + ownerId;

            if (ownerAttribute){
                srcToFiles += '&ownerAttribute=' + ownerAttribute;
            }
        }

        var iframe = $('<iframe src="' + srcToFiles + '" frameborder="0" role="filemanager-frame"></iframe>');

        modal.find(".modal-body").html(iframe);
        modal.modal("show");
    });

    $('[role="clear-input"]').on("click", function(e) {
        e.preventDefault();

        $("#" + $(this).attr("data-clear-element-id")).val("");
        $($(this).attr("data-image-container")).empty();
    });
});

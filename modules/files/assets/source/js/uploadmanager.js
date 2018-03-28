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

$('[role="add-file"]').change(function(event) {

    var file = event.target.files[0],
        tmppath = URL.createObjectURL(file);
//alert(file.type);
    var fileTypes = $('[role="uploadmanager"]').attr('data-file-types');
    alert(fileTypes);
    var newHtml = renderTemplate('file-template', {
            title: file.name,
            src: tmppath
        }),
        workspace = $('#workspace'),
        oldHtml = workspace.html();

    workspace.html(oldHtml + newHtml);
});
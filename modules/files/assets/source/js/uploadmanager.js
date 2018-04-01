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
        tmpPath = URL.createObjectURL(file),
        fileType = file.type.split('/')[0],
        baseUrl = $('[role="uploadmanager"]').attr('data-base-url'),
        preview;
    
    switch (fileType) {
        case 'image':
            preview = '<img width="75" src="' + tmpPath + '">';
            break;
        case 'audio':
            preview = '<audio src="' + tmpPath + '" controls></audio>';
            break;
        case 'video':
            preview = '<img width="75" src="' + tmpPath + '">';
            break;
        case 'text':
            preview = '<img width="75" src="' + tmpPath + '">';
            break;
        case 'application':
            preview = '<img width="75" src="' + tmpPath + '">';
            break;
        default:
            alert( 'Я таких значений не знаю' );
    }

    var newHtml = renderTemplate('file-template', {
            title: file.name,
            preview: preview
        }),
        workspace = $('#workspace'),
        oldHtml = workspace.html();

    workspace.html(oldHtml + newHtml);
});

/**
 * Serialize object to string
 *
 * @param   {object} obj    - Object incoming
 * @returns {string}        - Object as Get params string
 */
function serializeParams(obj) {
    return Object.keys(obj).reduce(function(a,k){a.push(k+'='+encodeURIComponent(obj[k]));return a},[]).join('&');
}

/**
 * AJAX function
 *
 * @param {string} url           - Request URL
 * @param {string} method        - Request type ('post' || 'get')
 * @param {object} params        - Object with params (for files { name: 'vasya' (sended to $_POST[]), files: { custom_filename: element.files[0] } (sended to $_FILES[]))
 * @param {bool}   response_json - Type of response (JSON or not)
 * @param {func}   func_waiting  - Function while waiting
 * @param {func}   func_callback - Function on success
 * @param {func}   func_error    - Function on error
 * @param {func}   func_progress - Function on uploading progress
 */
function AJAX(url, method, params, response_json, func_waiting, func_callback, func_error, func_progress) {
    var xhr = null;

    try { // For: chrome, firefox, safari, opera, yandex, ...
        xhr = new XMLHttpRequest();
    } catch(e) {
        try { // For: IE6+
            xhr = new ActiveXObject("Microsoft.XMLHTTP");
        } catch(e1) { // if JS not supported or disabled
            console.log("Browser Not supported!");
            return;
        }
    }

    xhr.onreadystatechange = function() {

        // ready states:
        // 0: uninitialized
        // 1: loading
        // 2: loaded
        // 3: interactive
        // 4: complete

        if (xhr.readyState == 4) { // when result is ready

            var response_text = xhr.responseText;

            if (response_json){
                try {
                    response_text = JSON.parse(response_text);
                } catch (e) { }
            }

            if (xhr.status === 200) { // on success
                if (typeof func_callback == 'function') {
                    func_callback(response_text);
                }
            } else { // on error
                if (typeof func_error == 'function') {
                    func_error(response_text, xhr);
                    console.log(xhr.status + ': ' + xhr.statusText);
                }
            }
        } else { // waiting for result
            if (typeof func_waiting == 'function') {
                func_waiting();
            }
        }
    }

    var data = null;

    if (params.files) {
        method = 'POST';

        data = new FormData();
        for (var index_param in params) {
            if (typeof params[index_param] == 'object') {
                for (var index_file in params[index_param]) {
                    data.append(index_file, params[index_param][index_file]);
                }
            } else {
                data.append(index_param, params[index_param]);
            }
        }

        if (typeof func_progress == 'function') {
            xhr.upload.onprogress = function(event) {
                // 'progress: ' + event.loaded + ' / ' + event.total;
                func_progress(event);
            }
        }
    } else {
        data = serializeParams(params);
    }

    method = method.toUpperCase();

    if (method == 'GET' && data) {
        url += '?' + data;
    }

    xhr.open(method, url, true);

    if ( ! params.files) {
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    }

    xhr.setRequestHeader("X-Requested-With", "XMLHttpRequest");
    xhr.send(data);
}

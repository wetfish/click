// click's requests to its own php pages. wetfish-basic has no ajax, so these
// use fetch.

// Calls back with the page's text when the request succeeds. A failed request
// is ignored: the refresh loop tries again, and a post that didn't go through
// stays in the box to send again. Errors in the callback still show in the
// browser's console.
function Request(url, options, callback)
{
    fetch(url, options)
        .then(function(response) { return response.ok ? response.text() : null; },
              function() { return null; })
        .then(function(text)
        {
            if(text !== null)
                callback(text);
        });
}

function Get(url, callback)
{
    Request(url, {}, callback);
}

// Sends data as a form, which php reads into $_POST
function Post(url, data, callback)
{
    Request(url, {method: 'POST', body: new URLSearchParams(data)}, callback);
}

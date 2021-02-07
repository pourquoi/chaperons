var system = require('system');
var args = system.args;

if (args.length != 7) {
    console.log('usage: ' + args[0] + ' [url] [user_id] [api_key] [width] [height] [output_path]');
    phantom.exit(1);
} else {

    url = args[1];
    user_id = args[2];
    api_key = args[3];
    width = args[4];
    height = args[5];
    output_path = args[6];

    var page = require('webpage').create();
    page.viewportSize = {
        width: width,
        height: height
    };

    // create fake page to save the auth token
    page.setContent('', url);

    page.evaluate(function (id, key) {
        localStorage.setItem('auth_token', JSON.stringify({user_id: id, api_key: key}));
    }, user_id, api_key);

    function onRenderReady() {
        page.render(output_path);
        phantom.exit(0);
    }

    page.open(url, function () {

        var tries = 40; // 40*500ms ~ 20seconds max

        function checkReadyState() {
            tries = tries-1;

            if(tries==0) {
                onRenderReady();
                return;
            }

            setTimeout(function () {
                var renderReady = page.evaluate(function () {
                    return 'map_render_ready' in window;
                });

                if (renderReady) {
                    onRenderReady();
                } else {
                    checkReadyState();
                }
            }, 500);
        }

        checkReadyState();
    });

}
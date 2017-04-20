define('ekyna-api/api', ['routing', 'es6-promise', 'jquery'], function (Router, es6Promise, $) {
    "use strict";

    es6Promise.polyfill();
    var Promise = es6Promise.Promise;

    function getToken(route) {
        return new Promise(function (resolve, reject) {
            var cache = window.localStorage;
            if (cache) {
                var auth = cache.getItem(route);
                if (auth) {
                    auth = JSON.parse(auth);

                    if (auth.expires_at > Math.floor((new Date()).getTime() / 1000)) {
                        resolve(auth.token);

                        return;
                    }

                }
            }

            var xhr = $.ajax({
                url: Router.generate(route),
                method: 'GET'
            });

            xhr.done(function (data) {
                if (cache) {
                    cache.setItem(route, JSON.stringify(data));
                }

                resolve(data.token)
            });
        });
    }

    var Api = {};

    Api.init = function (route) {
        getToken(route).then(function (token) {
            $(document).ajaxSend(function (event, xhr) {
                xhr.setRequestHeader('X-Auth-Token', token);
            });
        });
    };

    return Api;
});

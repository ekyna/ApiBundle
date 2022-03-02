define('ekyna-api/api', ['routing', 'jquery'], function (Router, $) {
    "use strict";

    function getToken(route) {
        return new Promise(function (resolve, reject) {
            let cache = window.localStorage;
            if (cache) {
                let auth = cache.getItem(route);
                if (auth) {
                    auth = JSON.parse(auth);

                    if (auth.hasOwnProperty('expires_at') && auth.expires_at > Math.floor((new Date()).getTime() / 1000)) {
                        resolve(auth.token);

                        return;
                    }
                }
            }

            let xhr = $.ajax({
                url: Router.generate(route), method: 'GET'
            });

            xhr.done(function (data) {
                if (cache) {
                    cache.setItem(route, JSON.stringify(data));
                }

                resolve(data.token)
            });

            xhr.fail(function () {
                reject()
            });
        });
    }

    function clearToken(route) {
        const cache = window.localStorage;
        if (!cache) {
            return;
        }

        cache.removeItem(route);
    }

    const Api = {
        route: null, token: null, loading: false,
    };

    Api.load = function () {
        if (this.loading) {
            return;
        }

        if (!this.route) {
            return;
        }

        this.loading = true;

        getToken(this.route)
            .then((token) => {
                this.token = token;
                this.loading = false;
            }, () => {
                clearToken(this.route);
                this.token = null;
                this.loading = false;
            });
    };

    Api.init = function (route) {
        this.route = route;

        this.load();

        $(document).ajaxError((event, xhr, settings) => {
            if (401 !== xhr.status) {
                return;
            }

            if (0 !== (new URL(settings.url, window.location.href)).pathname.indexOf('/api')) {
                return;
            }

            clearToken(this.route);
            this.token = null;
            this.load();
        });

        $(document).ajaxSend((event, xhr) => {
            if (!this.token) {
                return;
            }

            xhr.setRequestHeader('X-Auth-Token', this.token);
        });
    };

    return Api;
});

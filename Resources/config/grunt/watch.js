module.exports = function (grunt, options) {
    return {
        api_js: {
            files: ['src/Ekyna/Bundle/ApiBundle/Resources/private/js/*.js'],
            tasks: ['copy:api_js'],
            options: {
                spawn: false
            }
        }
    }
};

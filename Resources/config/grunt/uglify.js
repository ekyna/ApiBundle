module.exports = function (grunt, options) {
    return {
        api_js: {
            files: [{
                expand: true,
                cwd: 'src/Ekyna/Bundle/ApiBundle/Resources/private/js',
                src: '**/*.js',
                dest: 'src/Ekyna/Bundle/ApiBundle/Resources/public/js'
            }]
        }
    }
};

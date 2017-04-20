module.exports = function (grunt, options) {
    return {
        api_js: { // for watch:api_js
            expand: true,
            cwd: 'src/Ekyna/Bundle/ApiBundle/Resources/private/js',
            src: ['**'],
            dest: 'src/Ekyna/Bundle/ApiBundle/Resources/public/js'
        }
    }
};

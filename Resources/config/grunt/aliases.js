module.exports = {
    'build:api': [
        'clean:api_pre',
        'uglify:api_js',
        'clean:api_post'
    ]
};

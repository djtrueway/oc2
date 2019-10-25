'use strict';

module.exports = {
    options: {
        sourcemap: 'none',
    },
    plugin: {
        files: {
            '<%= path %>/css/custom/front.css':         'stylesheets/front.scss',
            '<%= path %>/css/custom/front-single.css':  'stylesheets/front-single.scss',
            '<%= path %>/css/custom/front-archive.css': 'stylesheets/front-archive.scss',
        }
    },
};

"use strict";

module.exports = function (grunt) {

    // We need to include the core Moodle grunt file too, otherwise we can't run tasks like "amd".
    require("grunt-load-gruntfile")(grunt);
    grunt.loadGruntfile("../../Gruntfile.js");

    // Load all grunt tasks.
    grunt.loadNpmTasks("grunt-contrib-watch");
    grunt.loadNpmTasks("grunt-contrib-clean");
    grunt.loadNpmTasks("grunt-fixindent");

    grunt.initConfig({
        watch: {
            amd: {
                files: "amd/src/*.js",
                tasks: ["amd"]
            }
        },
        eslint: {
            amd: {src: "amd/src"}
        },
        uglify: {
            amd: {
                files: {
                    "amd/build/starred.min.js": ["amd/src/starred.js"]
                },
                options: {report: 'none'}
            }
        }
    });
    // The default task (running "grunt" in console).
    grunt.registerTask("default", ["eslint", "uglify"]);
    grunt.registerTask("dev", ["uglify"]);
};

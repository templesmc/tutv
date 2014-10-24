'use strict';
module.exports = function(grunt) {

  grunt.initConfig({
    jshint: {
      options: {
        jshintrc: '.jshintrc'
      },
      all: [
        'Gruntfile.js',
        'assets/js/*.js',
        '!assets/js/*.min.js',
        '!assets/js/_show.js',
        '!assets/js/_vimeo-slideshow.js'
      ]
    },
    sass: {
      dist: {
        options: {
          style: 'compressed',
            compass: true,
            // Source maps are available, but require Sass 3.3.0 to be installed
            // https://github.com/gruntjs/grunt-contrib-sass#sourcemap
            sourcemap: true
        },
        files: {
          'assets/css/main.min.css': [
            'assets/scss/style.scss'
          ],
          'editor-style.css': [
            'assets/scss/editor-style.scss'
          ]
        }
      },
      dev: {
        options: {
          style: 'expanded',
          compass: true,
          // Source maps are available, but require Sass 3.3.0 to be installed
          // https://github.com/gruntjs/grunt-contrib-sass#sourcemap
          sourcemap: true
        },
        files: {
          'assets/css/main.min.css': [
            'assets/scss/style.scss'
          ],
          'editor-style.css': [
            'assets/scss/editor-style.scss'
          ]
        }
      }
    },
    uglify: {
      dist: {
        files: {
          'assets/js/vendor/selectivizr.min.js': [
            'bower_components/selectivizr/selectivizr.js'
          ],
          'assets/js/scripts.min.js': [
            'bower_components/superfish/dist/js/hoverIntent.js',
            'bower_components/superfish/dist/js/superfish.min.js',
            'assets/js/plugins/*.js',
            'assets/js/_main.js'
          ],
          'assets/js/front-page.min.js': [
            'bower_components/flexslider/jquery.flexslider.js',
            'bower_components/jquery-textfill/jquery.textfill.js'
          ],
          'assets/js/shows-page.min.js': [
            'bower_components/imagesloaded/imagesloaded.pkgd.min.js',
            'bower_components/isotope/dist/isotope.pkgd.min.js'
          ]
        },
        options: {
          // JS source map: to enable, uncomment the lines below and update sourceMappingURL based on your install
          // sourceMap: 'assets/js/scripts.min.js.map',
          // sourceMappingURL: '/app/themes/roots/assets/js/scripts.min.js.map'
        }
      }
    },
    version: {
      default: {
        options: {
          manifest: 'assets/manifest.json',
          rename: true,
          length: 32
        },
        files: {
          'lib/scripts.php': [
            'assets/css/main.min.css',
            'assets/js/scripts.min.js',
            'assets/js/front-page.min.js',
            'assets/js/shows-page.min.js'
          ]
        }
      }
    },
    watch: {
      sass: {
        files: [
          'assets/scss/**/*.scss'
        ],
        tasks: ['sass:dev', 'version']
      },
      js: {
        files: [
          '<%= jshint.all %>'
        ],
        tasks: ['jshint', 'uglify', 'version']
      },
      livereload: {
        // Browser live reloading
        // https://github.com/gruntjs/grunt-contrib-watch#live-reloading
        options: {
          livereload: true
        },
        files: [
          'assets/css/main.min.css',
          'assets/js/scripts.min.js',
          'templates/*.php',
          '*.php'
        ]
      }
    },
    bump: {
      options: {
        files: [
          'style.css',
          'package.json',
          'bower.json'
        ],
        commit: false,
        createTag: false,
        push: false
      }
    },
    clean: {
      dist: [
        'assets/css/main.*.min.css',
        'assets/js/scripts.*.min.js'
      ]
    }
  });

  // Load tasks
  grunt.loadNpmTasks('grunt-contrib-clean');
  grunt.loadNpmTasks('grunt-contrib-jshint');
  grunt.loadNpmTasks('grunt-contrib-uglify');
  grunt.loadNpmTasks('grunt-contrib-watch');
  grunt.loadNpmTasks('grunt-contrib-sass');
  grunt.loadNpmTasks('grunt-wp-assets');
  grunt.loadNpmTasks('grunt-bump');

  // Register tasks
  grunt.registerTask('default', [
    'clean',
    'sass:dist',
    'uglify:dist',
    'version'
  ]);
  grunt.registerTask('dev', [
    'watch'
  ]);

};

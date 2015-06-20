module.exports = function(grunt){

    grunt.initConfig({
        appDir: 'app',
        skinDir: 'skin',
        webRoot: 'webroot',
        pkg: grunt.file.readJSON('package.json'),
        currentYear: grunt.template.today('yyyy'),

        jshint: {
            files: {
                src: [
                    '<%=skinDir%>/js/blueacorn/**/*.js',
                    '<%=skinDir%>/js/development/**/*.js'
                ],
            },
            options: {
                "globals": {
                    "jQuery": true,
                    "prototypejs": true,
                    "$": true,
                    "$$": true,
                    "$j": true
                },
                "evil": true,
                "expr": true,
                "nonew": true,
                "newcap": false
            }
        },

        autoprefixer: {
            dev: {
                options: {
                    remove: true,
                    cascade: false,
                    map: {
                        inline: false,
                        prev: true,
                        annotation: false
                    },
                    browsers: [
                        'last 3 Explorer versions',
                        'last 2 Chrome versions',
                        'last 2 Safari versions',
                        'last 2 Firefox Versions',
                        'last 2 iOS versions',
                        'last 2 ChromeAndroid versions',
                        '> 1%'
                    ],
                },
                expand: true,
                flatten: true,
                src: ['<%=skinDir%>/css/**/*.css', '!<%=skinDir%>/css/**/*ie8.css'],
                dest: '<%=skinDir%>/css'
            },
            ie: {
                options: {
                    remove: true,
                    cascade: false,
                    map: false,
                    browsers: ['Explorer 8'],
                },
                expand: true,
                flatten: true,
                src: ['<%=skinDir%>/css/**/*ie8.css'],
                dest: '<%=skinDir%>/css'
            },
            production: {
                options: {
                    remove: true,
                    cascade: false,
                    map: false,
                    browsers: [
                        'last 3 Explorer versions',
                        'last 2 Chrome versions',
                        'last 2 Safari versions',
                        'last 2 Firefox Versions',
                        'last 2 iOS versions',
                        'last 2 ChromeAndroid versions',
                        '> 1%'
                    ],
                },
                expand: true,
                flatten: true,
                src: ['<%=skinDir%>/css/**/*.css', '!<%=skinDir%>/css/**/*ie8.css'],
                dest: '<%=skinDir%>/css'
            },
        },

        sass: {
            options: {
                includePaths: ['bower_components/compass-mixins/lib', 'bower_components/sass-list-maps/_sass-list-maps.scss'],
                sourceComments: false,
                precision: 4,
                outputStyle: 'nested',
                sourceMapContents: false,
                sourceMapComments: false,
            },
            dev: {
                options: {
                    sourceMap: true,
                },
                files: {
                    '<%=skinDir%>/css/styles.css' : '<%=skinDir%>/scss/styles.scss',
                    '<%=skinDir%>/css/styleguide.css' : '<%=skinDir%>/scss/styleguide.scss',
                    '<%=skinDir%>/css/madisonisland.css' : '<%=skinDir%>/scss/madisonisland.scss',
                    '<%=skinDir%>/css/styles-category.css' : '<%=skinDir%>/scss/styles-category.scss',
                    '<%=skinDir%>/css/styles-product.css' : '<%=skinDir%>/scss/styles-product.scss',
                    '<%=skinDir%>/css/styles-cart.css' : '<%=skinDir%>/scss/styles-cart.scss',
                    '<%=skinDir%>/css/styles-checkout.css' : '<%=skinDir%>/scss/styles-checkout.scss',
                    '<%=skinDir%>/css/styles-account.css' : '<%=skinDir%>/scss/styles-account.scss',
                    '<%=skinDir%>/css/styles-cms.css' : '<%=skinDir%>/scss/styles-cms.scss',
                }
            },
            ie: {
                options: {
                    sourceMap: false,
                },
                files: {
                    '<%=skinDir%>/css/styles-ie8.css' : '<%=skinDir%>/scss/styles-ie8.scss',
                    '<%=skinDir%>/css/styleguide-ie8.css' : '<%=skinDir%>/scss/styleguide-ie8.scss',
                    '<%=skinDir%>/css/madisonisland-ie8.css' : '<%=skinDir%>/scss/madisonisland-ie8.scss',
                    '<%=skinDir%>/css/styles-category-ie8.css' : '<%=skinDir%>/scss/styles-category-ie8.scss',
                    '<%=skinDir%>/css/styles-product-ie8.css' : '<%=skinDir%>/scss/styles-product-ie8.scss',
                    '<%=skinDir%>/css/styles-cart-ie8.css' : '<%=skinDir%>/scss/styles-cart-ie8.scss',
                    '<%=skinDir%>/css/styles-checkout-ie8.css' : '<%=skinDir%>/scss/styles-checkout-ie8.scss',
                    '<%=skinDir%>/css/styles-account-ie8.css' : '<%=skinDir%>/scss/styles-account-ie8.scss',
                    '<%=skinDir%>/css/styles-cms-ie8.css' : '<%=skinDir%>/scss/styles-cms-ie8.scss',
                }
            },
            production: {
                options: {
                    sourceMap: false,
                },
                files: {
                    '<%=skinDir%>/css/styles.css' : '<%=skinDir%>/scss/styles.scss',
                    '<%=skinDir%>/css/styleguide.css' : '<%=skinDir%>/scss/styleguide.scss',
                    '<%=skinDir%>/css/madisonisland.css' : '<%=skinDir%>/scss/madisonisland.scss',
                    '<%=skinDir%>/css/styles-category.css' : '<%=skinDir%>/scss/styles-category.scss',
                    '<%=skinDir%>/css/styles-product.css' : '<%=skinDir%>/scss/styles-product.scss',
                    '<%=skinDir%>/css/styles-cart.css' : '<%=skinDir%>/scss/styles-cart.scss',
                    '<%=skinDir%>/css/styles-checkout.css' : '<%=skinDir%>/scss/styles-checkout.scss',
                    '<%=skinDir%>/css/styles-account.css' : '<%=skinDir%>/scss/styles-account.scss',
                    '<%=skinDir%>/css/styles-cms.css' : '<%=skinDir%>/scss/styles-cms.scss',
                }
            }
        },

        shell: {
            cache: {
                command: [
                    'cd ../<%=webRoot%>',
                    'rm -rf var/cache/* var/full_page_cache/*'
                ].join(' && ')
            },
            setup: {
                command: [
                    'mv ../<%=webRoot%>/app/design/frontend/blueacorn/gp ../<%=webRoot%>/app/design/frontend/blueacorn/<%=defaultTheme%>',
                    'mv ../<%=webRoot%>/skin/frontend/blueacorn/gp ../<%=webRoot%>/skin/frontend/blueacorn/<%=defaultTheme%>',
                    'ln -s ../<%=webRoot%>/app/design/frontend/blueacorn/<%=defaultTheme%>/ app',
                    'ln -s ../<%=webRoot%>/skin/frontend/blueacorn/<%=defaultTheme%>/ skin'
                ].join('&&')
            }
        },

        uglify: {
            dev: {
                options: {
                    mangle: false,
                    beautify: true,
                    compress: false
                },
                files: {
                    '<%=skinDir%>/jsmin/blueacorn.min.js':['<%=skinDir%>/js/blueacorn/**/*.js'],
                    '<%=skinDir%>/jsmin/blueacorn.development.min.js':['<%=skinDir%>/js/development/**/*.js'],
                }
            },
            production: {
                options: {
                    mangle: false,
                    beautify: false
                },
                files: {
                    '<%=skinDir%>/jsmin/blueacorn.min.js':['<%=skinDir%>/js/blueacorn/**/*.js']
                }
            }
        },

        copy: {
            app: {
                cwd: '../<%=webRoot%>/app/design/frontend/rwd',
                src: '**/*',
                dest: '../<%=webRoot%>/app/design/frontend/blueacorn',
                expand: true
            },
            skin: {
                cwd: '../<%=webRoot%>/skin/frontend/rwd',
                src: '**/*',
                dest: '../<%=webRoot%>/skin/frontend/blueacorn',
                expand: true
            },
            js: {
                cwd: '<%=skinDir%>/build',
                src: '**/*.js',
                dest: '<%=skinDir%>/jsmin',
                expand: true
            }
        },

        imagemin: {
            dynamic: {
                options: {
                    optimizationLevel: 5
                },
                files: [{
                    expand: true,
                    cwd: '<%=skinDir%>/src/',
                    src: ['**/*.{png,jpg,gif,svg}'],
                    dest: '<%=skinDir%>/images/'
                }]
            }
        },

        watch: {
            app: {
                files: ['<%=appDir%>/**/*.xml', '<%=appDir%>/**/*.phtml'],
                tasks: ['shell:cache'],
            },
            sass: {
                files: ['<%=skinDir%>/scss/**/*.scss'],
                tasks: ['concurrent:sass', 'concurrent:autoprefixer'],
                sourceComments: 'normal',
                options: {
                    sourceMap: true
                }
            },
            js: {
                files: ['<%=skinDir%>/js/**/*.js'],
                tasks: ['newer:jshint', 'newer:uglify:dev'],
            },
            livereload: {
                files: ['<%=skinDir%>/css/**/*.css'],
                options: {
                    livereload: true,
                    sourceMap: true
                }
            },
            images: {
                files: ['<%=skinDir%>/src/**.*{png,jpg,gif,svg}'],
                task: ['newer:imagemin']
            },
        },

        concurrent: {
            setup: ['copy:app', 'copy:skin'],
            sass: ['sass:dev', 'sass:ie'],
            autoprefixer: ['autoprefixer:dev', 'autoprefixer:ie']
        },

    });

    // Measure the Time Each Task Takes
    require('time-grunt')(grunt);

    // Automatic Dependency Loading
    require('jit-grunt')(grunt);

    // Default Grunt Task, used during main development.
    grunt.registerTask('default', ['watch']);

    // Quality Control Task, used to verify content quality of Frontend Assets
    grunt.registerTask('qc', ['newer:jshint']);

    // Grunt Setup Task
    grunt.registerTask('setup', function(){
        grunt.config.set('defaultTheme', arguments[0]);
        grunt.task.run('concurrent:setup');
        grunt.task.run('shell:setup');
        grunt.task.run('compile');
    });

    // Compilation Task, used to re-compile Frontend Assets.
    grunt.registerTask('compile', ['concurrent:sass', 'concurrent:autoprefixer', 'jshint', 'uglify:dev', 'shell:cache']);

    // Staging Deployment Task, used for post-deployment compilation of Frontend Assets on Staging.
    grunt.registerTask('staging', ['sass:production', 'sass:ie', 'autoprefixer:production', 'autoprefixer:ie', 'jshint', 'uglify:production', 'newer:imagemin', 'shell:cache']);

    // Production Deployment Task, used for post-deployment compilation of Frontend Assets on Production.
    grunt.registerTask('production', ['sass:production', 'sass:ie', 'autoprefixer:production', 'autoprefixer:ie', 'jshint', 'uglify:production', 'newer:imagemin', 'shell:cache']);

}
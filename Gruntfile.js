module.exports = function (grunt) {
	var sourceJsFiles = [
		'app/js/src/**/*.js'
	];
	var jsFiles = [
		'www/libs/jquery.js',
		'www/libs/knockout.js',
		'www/libs/moment.js',
		'libs/nette/nette/client-side/netteForms.js'
	].concat(sourceJsFiles);
	var testFiles = ['app/js/tests/**/*.js'];
	var lessFile = 'app/less/main.less';

	grunt.initConfig({
		concat: {
			options: {
				separator: ';'
			},
			dist: {
				src: jsFiles,
				dest: 'www/generated/web.js'
			},
			test: {
				src: testFiles,
				dest: 'temp/tests.js'
			}
		},
		uglify: {
			dist: {
				files: {
					'www/generated/web.min.js': ['<%= concat.dist.dest %>']
				}
			}
		},
		jshint: {
			files: ['Gruntfile.js'].concat(sourceJsFiles),
			options: {
				globals: {
					jQuery: true,
					console: true,
					module: true,
					document: true
				}
			}
		},
		watch: {
			scripts: {
				files: ['<%= jshint.files %>'],
				tasks: ['jshint', 'concat']
			},
			tests: {
				files: testFiles,
				tasks: ['jshint', 'concat']
			},
			less: {
				files: ['app/less/**/*.less'],
				tasks: ['less']
			}
		},
		less: {
			dev: {
				files: {
					'www/generated/presidos.css': 'app/less/main.less'
				}
			}
		},
		mocha: {
			test: {
				src: ['app/js/tests/runner.html'],
				options: {
					run: true
				}
			}
		}
	});

	grunt.loadNpmTasks('grunt-contrib-uglify');
	grunt.loadNpmTasks('grunt-contrib-jshint');
	grunt.loadNpmTasks('grunt-contrib-watch');
	grunt.loadNpmTasks('grunt-contrib-concat');
	grunt.loadNpmTasks('grunt-contrib-less');
	grunt.loadNpmTasks('grunt-mocha');

	grunt.registerTask('test', ['jshint', 'concat', 'mocha']);

	grunt.registerTask('default', ['jshint', 'concat', 'uglify', 'less']);
};
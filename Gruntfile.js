module.exports = function (grunt) {
	var sourceJsFiles = [
		'app/js/**/*.js'
	];
	var jsFiles = [
		'www/libs/jquery.js',
		'www/libs/knockout.js',
	].concat(sourceJsFiles);
	var lessFile = 'app/less/main.less';

	grunt.initConfig({
		concat: {
			options: {
				separator: ';'
			},
			dist: {
				src: jsFiles,
				dest: 'www/generated/web.js'
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
			files: ['<%= jshint.files %>', 'app/less/**/*.less'],
			tasks: ['default']
		},
		less: {
			development: {
				files: {
					'www/generated/presidos.css': 'app/less/main.less'
				}
			}
		}
	});

	grunt.loadNpmTasks('grunt-contrib-uglify');
	grunt.loadNpmTasks('grunt-contrib-jshint');
	grunt.loadNpmTasks('grunt-contrib-watch');
	grunt.loadNpmTasks('grunt-contrib-concat');
	grunt.loadNpmTasks('grunt-contrib-less');

	grunt.registerTask('test', ['jshint']);

	grunt.registerTask('default', ['jshint', 'concat', 'uglify', 'less']);
};
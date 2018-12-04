module.exports = function(grunt) {
	grunt.initConfig({
		pkg: grunt.file.readJSON('package.json'),
		// JS
		uglify: {
			tinymce: {
				options: {
					beautify: false,
					mangle: true
				},
				files: {
					'webroot/js/Plugins/tinymce/plugin.min.js': [
						'webroot/js/Plugins/tinymce/plugin.js'
					]
				}
			},
		},
		watch: {
			scripts: {
				files: [
					'webroot/js/Plugins/tinymce/*.js'
				],
				tasks: ['uglify:tinymce'],
				options: {
					nospawn: true
				}
			}
		}
	});

	grunt.loadNpmTasks('grunt-contrib-uglify');
	grunt.loadNpmTasks('grunt-contrib-watch');

	grunt.registerTask('default',['watch']);
}

module.exports = function( grunt ) {

	// Project configuration
	grunt.initConfig( {
		pkg    : grunt.file.readJSON( 'package.json' ),
		meta   : {
			banner : '/*! <%= pkg.name %> - v<%= pkg.version %> - Copyright (c) <%= grunt.template.today("yyyy") %> */\n'
		},
		jshint : {
			all     : [
				'Gruntfile.js',
				'js/*.dev.js'
			],
			options: {
				jshintrc: true
			}
		},
		wp_readme_to_markdown: {
			default: {
				files: {
					'readme.md': 'readme.txt'
				}
			}
		},
		pot: {
			options:{
				text_domain: 'bsw-spc-locale',
				package_name: '<%= pkg.name %>',
				package_version: '<%= pkg.version %>',
				dest: 'languages/',
				keywords: [
					'__', 
					'_e', 
					'__ngettext:1,2', 
					'_n:1,2', 
					'__ngettext_noop:1,2', 
					'_n_noop:1,2', 
					'_x:1,2c',
					'_nx:4c,1,2', 
					'_nx_noop:4c,1,2', 
					'_ex:1,2c', 
					'esc_attr__', 
					'esc_attr_e', 
					'esc_attr_x:1,2c',
					'esc_html__', 
					'esc_html_e', 
					'esc_html_x:1,2c' 
				]
			},
			files:{
				src:  [ 
					'**/*.php', 
					'!node_modules/**'
				],
				expand: true
			}
		},
		po2mo: {
			files: {
				src: 'languages/*.po',
				expand: true,
			},
		},
		checktextdomain: {
			options:{
				text_domain: 'bsw-spc-locale',
				correct_domain: true, //Will correct missing/variable domains
				keywords: [ //WordPress localisation functions
					'__:1,2d',
					'_e:1,2d',
					'_x:1,2c,3d',
					'esc_html__:1,2d',
					'esc_html_e:1,2d',
					'esc_html_x:1,2c,3d',
					'esc_attr__:1,2d', 
					'esc_attr_e:1,2d', 
					'esc_attr_x:1,2c,3d', 
					'_ex:1,2c,3d',
					'_n:1,2,4d', 
					'_nx:1,2,4c,5d',
					'_n_noop:1,2,3d',
					'_nx_noop:1,2,3c,4d'
				],
			},
			files: {
				src:  [ 
					'**/*.php', 
					'!node_modules/**'
				],
				expand: true,
			},
		},
		clean:    {
			main:    ['release/<%= pkg.version %>']
		},
		copy: {
			main: {
				src: [
					'**',
					'!node_modules/**',
					'!release/**',
					'!assets/**',
					'!.git/**',
					'!.gitignore',
					'!.jshintrc',
					'!Gruntfile.js',
					'!package.json',
					'!readme.md'
				],
				dest: 'release/<%= pkg.version %>/'
			}
		}
	} );

	// Load other tasks
	grunt.loadNpmTasks( 'grunt-contrib-jshint' );
	grunt.loadNpmTasks( 'grunt-wp-readme-to-markdown' );
	grunt.loadNpmTasks( 'grunt-po2mo' );
	grunt.loadNpmTasks( 'grunt-pot' );
	grunt.loadNpmTasks( 'grunt-checktextdomain');
	grunt.loadNpmTasks( 'grunt-contrib-clean' );
	grunt.loadNpmTasks( 'grunt-contrib-copy' );

	// Default task
	grunt.registerTask( 'default', ['jshint'] );

	// Additional tasks
	grunt.registerTask( 'localize', ['pot', 'po2mo', 'checktextdomain'] );
	grunt.registerTask( 'readme', ['wp_readme_to_markdown'] );
	grunt.registerTask( 'build', ['default', 'localize', 'clean', 'copy'] );

	grunt.util.linefeed = '\n';
};
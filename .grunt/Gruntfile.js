

module.exports = function(grunt) {

	// load all grunt tasks
	require('matchdep').filterDev('grunt-*').forEach(grunt.loadNpmTasks);

	grunt.initConfig({
		pkg: grunt.file.readJSON('package.json'),
		
		
		// Concatenate JavaScript files to one big file (because fewer HTTP request)
		concat: {
			options: {
				separator: ';'
			},
			dist: {
				src: [
					'!../site/behaviour/vendor/*.js',
					'!../site/behaviour/plugins/respond.js',
					'../site/behaviour/plugins/*.js',
					'../site/behaviour/*.js',
					'!../site/behaviour/<%= pkg.name %>.js',
					'!../site/behaviour/<%= pkg.name %>.min.js'
				],
				dest: '../site/behaviour/<%= pkg.name %>.js'
			}
		},
		
		// Remove whitespace and comments from JavaScript (because reduce filesize)
		uglify: {
			options: {
				banner: '/*! <%= pkg.name %> <%= grunt.template.today("dd-mm-yyyy") %> */\n'
			},
			dist: {
				files: {
					'../site/behaviour/<%= pkg.name %>.min.js': ['<%= concat.dist.dest %>']
				}
			}
		},
		
		// Run JavaScript past JSLint (because maintain code quality and reduce bugs from bad code)
		jslint: {
			
			client: {
				src: [
					'../site/behaviour/main.js'
				],
				directives: {
					browser: true,
					devel: true,
					white: true,
					todo: true,
					unparam: true,
					unused: true,
					predef: [
						'jQuery',
						'requestAnimationFrame',
						'Modernizr',
						'Hammer',
						'hoverIntent',
						'smoothScroll'
					]
				},
				options: {
				}
			}
			
		},
		
		// Turn SASS into CSS (because SASS easier to maintain than CSS)
		sass: {   // Task
	        dist: {   // Target
	            files: { // Dictionary of files
	                '../site/style/main.css': '../_sass/main.scss'     // 'destination': 'source'
	            }
	        }
	    },
		
		// Remove whitespace and comments from CSS (because reduce filesize)
		cssmin: {
		  combine: {
		    files: {
		      '../site/style/main.min.css': ['../site/style/main.css']
		    }
		  }
		},
		
		// Optimise images (because filesize)
		imagemin: {
			png: {
		      options: {
		        optimizationLevel: 7
		      },
		      files: [
		        {
		          // Set to true to enable the following options…
		          expand: true,
		          // cwd is 'current working directory'
		          cwd: '../site/images/',
		          src: ['**/*.png'],
		          // Could also match cwd line above. i.e. project-directory/img/
		          dest: '../site/images/',
		          ext: '.png'
		        }
		      ]
		    },
		    jpg: {
		      options: {
		        progressive: true
		      },
		      files: [
		        {
		          // Set to true to enable the following options…
		          expand: true,
		          // cwd is 'current working directory'
		          cwd: '../site/images/',
		          src: ['**/*.jpg'],
		          // Could also match cwd. i.e. project-directory/img/
		          dest: '../site/images/',
		          ext: '.jpg'
		        }
		      ]
		    }
		},
		
		// Watch file, if changes happen run Grunt tasks
		watch: {
			files: [
				'../_sass/*.scss', 
				'../_sass/*/*.scss', 
				'../site/behaviour/*.js', 
				'!../site/behaviour/<%= pkg.name %>.js', 
				'!../site/behaviour/<%= pkg.name %>.min.js'
			],
			tasks: ['sass', 'cssmin', 'concat'/*, 'uglify'*/],
			options: {
		      livereload: true,
		    }
		}
	});
	
	grunt.loadNpmTasks('grunt-contrib-sass');
	grunt.loadNpmTasks('grunt-contrib-uglify');
	grunt.loadNpmTasks('grunt-contrib-concat');
	grunt.loadNpmTasks('grunt-contrib-cssmin');
	grunt.loadNpmTasks('grunt-contrib-sass');
	grunt.loadNpmTasks('grunt-contrib-imagemin');
	grunt.loadNpmTasks('grunt-jslint');
	grunt.loadNpmTasks('grunt-contrib-watch');

	grunt.registerTask('default', ['sass', 'cssmin', 'concat', 'uglify', 'jslint', 'imagemin']);

};
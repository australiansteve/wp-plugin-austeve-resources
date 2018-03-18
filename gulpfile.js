// Load our plugins
var	gulp			=	require('gulp');

var paths = {
	sassPath: 'sass/',
	destPath: '.'
};

//Our 'deploy' task which deploys on a local dev environment
gulp.task('deploy', function() {

	var files = ['images/**.*',
		'*.php',
		'*.css',
		'js/*.js'];

	var destSSJ = '/Applications/MAMP/htdocs/ssj/wp-content/plugins/austeve-resources';

	return gulp.src(files, {base:"."})
    		.pipe(gulp.dest(destSSJ));
});

// Our default gulp task, which runs all of our tasks upon typing in 'gulp' in Terminal
gulp.task('default', ['deploy']);

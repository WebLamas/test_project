'use strict';

var gulp = require('gulp'), 					// основа
   watch2 = require('chokidar'),				//смотрит за изменениями файлов
    sourcemaps = require('gulp-sourcemaps'), 	// соурсмапы
	uglify = require('gulp-uglify'),			//углифаер для js
	cleanCSS = require('gulp-clean-css'),		// сжимает css
	less	= require('gulp-less'), 				//less
	php2html = require("gulp-php2html"),		//php
	fileinclude = require('gulp-file-include'), //суммирование джаваскриптов
	serveStatic = require('serve-static'), 		//сервер
	http = require('http'), 					//что-то нужное серверу
	finalhandler = require('finalhandler'),		//что-то нужное серверу
	//del = require('del'),
	replace = require('gulp-string-replace'),
	fs = require("fs"),
	gcmq = require('gulp-group-css-media-queries'),
	open = require('open');					//открыть ссылку
 
var serve = serveStatic('./build', { 'index': ['index.html', 'index.htm'] });
var server = http.createServer(function onRequest (req, res) {
	serve(req, res, finalhandler(req, res));
})

var path = {
    build: { //Тут мы укажем куда складывать готовые после сборки файлы
        html: 'build/',
        js: 'build/js/',
        css: 'build/css/',
        images: 'build/images/',
        fonts: 'build/fonts/'
    },
    src: { //Пути откуда брать исходники
        html: 'src/*.html', //Синтаксис src/*.html говорит gulp что мы хотим взять все файлы с расширением .html
        php: 'src/*.php', //Синтаксис src/*.html говорит gulp что мы хотим взять все файлы с расширением .html
        js: 'src/js/*.js',//В стилях и скриптах нам понадобятся только main файлы
        style: 'src/css/main.less',
        images: 'src/images/**/*.*', //Синтаксис img/**/*.* означает - взять все файлы всех расширений из папки и из вложенных каталогов
        fonts: 'src/fonts/**/*.*'
    },
    watch: { //Тут мы укажем, за изменением каких файлов мы хотим наблюдать
        php: 'src/**/*.php',
        html: 'src/**/*.html',
        js: 'src/js/**/*.js',
        css: 'src/css/**/*.*ss',
        images: 'src/images/**/*.*',
        fonts: 'src/fonts/**/*.*'
    },
    clean: './build'
};

 
// Listen

var config = {
    server: {
        baseDir: "./build"
    },
  /*  tunnel: true,*/
    host: 'localhost',
    port: 80,
    logPrefix: "shiziksama"
};

gulp.task('html:build', function () {
	return gulp.src(path.src.php)
		.pipe(php2html({router: 'router.php'}))
		.pipe(gulp.dest(path.build.html));
});
/*

gulp.task('html:deploy', function () {
	//del.sync(['build/**']);
	gulp.src('src/index.php').pipe(gulp.dest('build/'));
	return gulp.src(['src/*.php','src/template/sub_*','src/template/mod_*'])
		.pipe(replace(/\<\?php require\('template\/(.*)'\);\?\>/g,function(s,s1){
			if(s1.substring(0,4)=='mod_'||s1.substring(0,4)=='sub_')return s;
			return  fs.readFileSync("./src/template/"+s1, "utf8");
		},{logs: {enabled: false}}))
		.pipe(gulp.dest('build/html/'));
});
*/

gulp.task('js:build', function () {
   return gulp.src(path.src.js) //Найдем наш main файл
        .pipe(fileinclude({
			prefix: '@@',
			basepath: '@file'
		})) //Прогоним через rigger
        .pipe(sourcemaps.init()) //Инициализируем sourcemap
        .pipe(sourcemaps.write()) //Пропишем карты
        .pipe(gulp.dest(path.build.js)); //Выплюнем готовый файл в build
       // .pipe(reload({stream: true})); //И перезагрузим сервер
});
gulp.task('js:deploy', function () {
   return gulp.src(path.src.js) //Найдем наш main файл
        .pipe(fileinclude({
			prefix: '@@',
			basepath: '@file'
		})) //Прогоним через rigger
        .pipe(uglify()) //Сожмем наш js
        .pipe(gulp.dest(path.build.js)); //Выплюнем готовый файл в build
});

gulp.task('css:build', function(){
  return gulp.src(path.src.style)
	.pipe(sourcemaps.init())
    .pipe(less())
	//.pipe(gcmq())
    .pipe(cleanCSS({
			level: {
					1:{all:true},
					2:{}
					}
		}))
    .pipe(sourcemaps.write())
    .pipe(gulp.dest('build/css'));
});
gulp.task('css:deploy', function(){ 
  return gulp.src(path.src.style) 
    .pipe(less({ modifyVars: { imageurl: '/webps/themes/2bgroup_covid/images' } }))
	.pipe(gcmq())
    .pipe(cleanCSS({
			level: {
					1:{all:true},
					2:{
						restructureRules:true
						}
					}
		}))
    .pipe(gulp.dest('build/css'));
});
gulp.task('static:build', function() {
    gulp.src(path.src.fonts).pipe(gulp.dest(path.build.fonts))
	return gulp.src(path.src.images).pipe(gulp.dest(path.build.images))
});

gulp.task('build',gulp.series('html:build', 'js:build','css:build', 'static:build'));
gulp.task('deploytest',gulp.series('html:build', 'js:deploy','css:deploy', 'static:build'));

gulp.task('deploy',gulp.series('js:deploy','css:deploy', 'static:build'));
gulp.task('watch', function(){
	watch2.watch([path.watch.php]).on('change',gulp.series('html:build'));
	watch2.watch([path.watch.html]).on('change',gulp.series('html:build'));
	watch2.watch([path.watch.css]).on('change',gulp.series('css:build'));
	watch2.watch([path.watch.js]).on('change',gulp.series('js:build'));
	watch2.watch([path.watch.fonts]).on('change',gulp.series('static:build'));
	watch2.watch([path.watch.images]).on('change',gulp.series('static:build'));
});
gulp.task('webserver', function () {
	server.listen(80);
	open('http://localhost/');
});
gulp.task('test',gulp.series('deploytest',gulp.parallel('webserver','watch')));
gulp.task('default', gulp.series('build',gulp.parallel('webserver','watch')));

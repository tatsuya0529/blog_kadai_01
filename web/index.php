<?php
require_once __DIR__.'/../vendor/autoload.php';
require_once __DIR__.'/model/article.php';

$app = new Silex\Application();
$loader = new Twig_Loader_String();
$twig = new Twig_Environment($loader);
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

// デバッグモード有効
$app['debug'] = true;

// template
$app->before(function () use ($app) {
	$app['twig']->addGlobal('template', $app['twig']->loadTemplate('template.html'));
});

$app->register(new Silex\Provider\TwigServiceProvider(), array(
	'twig.path' => __DIR__.'/views/',
	'twig.class.path' => __DIR__.'/../vendor/twig/lib',
));

$app->register(new FranMoreno\Silex\Provider\ParisServiceProvider());
$app['idiorm.config'] = array(
	'connection_string' => 'sqlite:'.__DIR__.'/../blog_kadai_01.sqlite',
);
$app['paris.model.prefix'] = '';

// TOP
$app->get('/', function () use ($app) {
	return $app['twig']->render('index.html');
});

// 新規投稿画面
$app->get('/create', function () use ($app) {
	$article = $app['paris']->getModel('Articles')->create();
	$value = array(
		'id' => '',
		'title' => '',
		'content' => '',
		'created_at' => '',
		'updated_at' => '',
	);
	$article->set($value);

	return $app['twig']->render('create.html', array(
		'article' => $article,
		'title' => 'タイトル',
		'content' => '本文',
	));
});

// 新規投稿実行
$app->post('/save', function (Request $request) use ($app) {
	$article = $app['paris']->getModel('Articles')->create();
	$value = array(
		'title' => $request->get('title'),
		'content' => $request->get('content'),
		'created_at' => date('Y/m/d H:i:s'),
		'updated_at' => ''
	);
	$article->set($value);
	$article->save();

	return $app->redirect('/');
});

// エラーハンドリング
$app->error(function (\Exception $e, $code) use ($app) {
	$app['twig']->addGlobal('template', $app['twig']->loadTemplate('template.html'));

	return new Response($app['twig']->render('error.html', array(
		'message' => $e->getMessage()
	)), $code);
});

$app->run();

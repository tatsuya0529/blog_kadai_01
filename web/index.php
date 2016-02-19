<?php
require_once __DIR__.'/../vendor/autoload.php';
require __DIR__.'/model/article.php';

$app = new Silex\Application();
$loader = new Twig_Loader_String();
$twig = new Twig_Environment($loader);
use Symfony\Component\HttpFoundation\Request;

// デバッグモード有効
$app['debug'] = true;

// template
$app->before(function () use ($app) {
	$app['twig']->addGlobal('template', $app['twig']->loadTemplate('template.php'));
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
	$article_model = $app['paris']->getModel('Articles');
	$articles = $article_model->order_by_desc('id')->find_many();

	return $app['twig']->render('index.php', array(
		'articles' => $articles,
	));
});

// 新規投稿画面
$app->get('/create', function () use ($app) {
	$article = $app['paris']->getModel('Articles')->create();
	$article->set($article->_properties);

	return $app['twig']->render('create/index.php', array(
		'name' => '新規投稿',
		'article' => $article,
		'title' => 'タイトル',
		'content' => '本文',
	));
});

// 新規投稿実行
$app->post('/create/complete', function (Request $request) use ($app) {
	$article = $app['paris']->getModel('Articles')->create();
	$article->set_properties($request);
	$article->set($article->_properties);
	$article->save();

	return $app['twig']->render('create/complete.php', array(
		'message' => '新規投稿が完了しました！',
	));
});

$app->run();

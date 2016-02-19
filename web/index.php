<?php
require_once __DIR__.'/../vendor/autoload.php';
require __DIR__.'/model/article.php';

$app = new Silex\Application();
$loader = new Twig_Loader_String();
$twig = new Twig_Environment($loader);
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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

// TOP(ブログ一覧画面)
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
	$value = array(
		'id' => '',
		'title' => '',
		'content' => '',
		'created_at' => '',
		'updated_at' => '',
	);
	$article->set($value);

	return $app['twig']->render('create/index.php', array(
		'name' => '新規投稿',
		'article' => $article,
		'title' => 'タイトル',
		'content' => '本文',
	));
});

// 新規投稿実行
$app->post('/create', function (Request $request) use ($app) {
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
	$app['twig']->addGlobal('template', $app['twig']->loadTemplate('template.php'));

	return new Response($app['twig']->render('index.php', array(
		'message' => $e->getMessage(),
		'articles' => $app['paris']->getModel('Articles')->order_by_desc('id')->find_many()
	)), $code);
});

$app->run();

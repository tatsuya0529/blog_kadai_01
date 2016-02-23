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

// TOP(投稿一覧)画面
$app->get('/', function () use ($app) {
	$article_model = $app['paris']->getModel('Articles');
	$articles = $article_model->order_by_desc('id')->find_many();

	return $app['twig']->render('index.html', array(
		'articles' => $articles,
	));
});

// 投稿詳細画面
$app->get('/detail/{id}', function ($id) use ($app) {
	$article_model = $app['paris']->getModel('Articles');
	$article = $article_model->find_one($id);

	if ( ! $article) {
		$app->abort(404, "お探しのページは存在しません。");
	}

	return $app['twig']->render('detail.html', array(
		'article' => $article,
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

	return $app['twig']->render('form.html', array(
		'article' => $article,
		'name' => '新規投稿'
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

// 編集画面
$app->get('/edit/{id}', function ($id) use ($app) {
	$article_model = $app['paris']->getModel('Articles');
	$article = $article_model->find_one($id);

	if ( ! $article) {
		$app->abort(404, "お探しのページは存在しません。");
	}

	return $app['twig']->render('form.html', array(
		'article' => $article,
		'name' => '編集'
	));
});

// 編集実行
$app->post('/edit/{id}', function (Request $request, $id) use ($app) {
	$article_model = $app['paris']->getModel('Articles');
	$article = $article_model->find_one($id);

	if ( ! $article) {
		$app->abort(404, "お探しのページは存在しません。");
	}

	$value = array(
		'id' => $id,
		'title' => $request->get('title'),
		'content' => $request->get('content'),
		'updated_at' => date('Y/m/d H:i:s')
	);

	$article->set($value);
	$article->save();

	return $app->redirect('/');
});

// 削除実行
$app->get('/delete/{id}', function ($id) use ($app) {
	$article_model = $app['paris']->getModel('Articles');
	$article = $article_model->find_one($id);

	if ( ! $article) {
		$app->abort(404, "お探しのページは存在しません。");
	}

	$article->delete();

	return $app->redirect('/');
});

$app->run();

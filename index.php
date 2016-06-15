<?php
require __DIR__ . '/vendor/autoload.php';

date_default_timezone_set('Europe/London');

$log = new Monolog\Logger('name');
$log->pushHandler(new Monolog\Handler\StreamHandler('app.log', Monolog\Logger::WARNING));
$log->addWarning('Foo');


$app = new \Slim\Slim(array(
	'view'=> new \Slim\Views\Twig()
));

$view = $app->view();
$view->parserOptions = array(
    'debug' => true
    
);

$view->parserExtensions = array(
    new \Slim\Views\TwigExtension(),
);

$app->get('/',function() use($app){
$app->render('index.twig');
})->name('home');

$app->get('/services',function() use($app){
$app->render('services.twig');
})->name('services');


$app->get('/about',function() use($app){
$app->render('about.twig');
})->name('about');

$app->get('/contact',function() use($app){
$app->render('contact.twig');
})->name('contact');

//to route the form

$app->post('/contact', function()use($app){

	$name=$app->request->post('name');
	$email=$app->request->post('email');
	$msg=$app->request->post('msg');

if(!empty($name)&& !empty($email)&& !empty($msg)){

	$cleanName = filter_var($name, FILTER_SANITIZE_STRING);
	$cleanEmail = filter_var($email, FILTER_SANITIZE_EMAIL);
	$cleanMsg = filter_var($msg, FILTER_SANITIZE_STRING);


	}else{

	//message the user that theres a problem
	$app->redirect('/contact');
}	

// //swiftmailer code

$transport = Swift_SendmailTransport::newInstance('/usr/sbin/sendmail -bs');

$mailer = \Swift_Mailer::newInstance($transport);
$message = \Swift_Message::newInstance();
$message -> setSubject('Email from our website');
$message -> setFrom(array(
	 $cleanEmail => $cleanName 
));
$message -> setTo(array('ahdesigns@outlook.com' => 'Ambermaria'));
$message -> setBody($cleanMsg);




//code to send email is here

$result = $mailer->send($message);

if ($result > 0){
	//send message which says thank you
	$app->redirect ('/');
}else{
	//send message to the user that the message failer to send
	//log that there was an error
	$app->redirect('/contact');
}

});

$app -> run();


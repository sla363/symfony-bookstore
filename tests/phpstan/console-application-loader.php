<?php
use App\Kernel;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Bundle\FrameworkBundle\Console\Application;

require __DIR__ . '/../vendor/autoload.php';

(new Dotenv())->bootEnv(__DIR__ . '/../.env');

$kernel = new Kernel($_SERVER['APP_ENV'], (bool) $_SERVER['APP_DEBUG']);
$kernel->boot();

$application = new Application($kernel);
$application->setAutoExit(false);

return $application;
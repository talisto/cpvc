<?php

use Doctrine\Common\Cache\FilesystemCache;

use Talisto\Composer\VersionCheck\Checker;
use Talisto\Composer\VersionCheck\Formatter\HTML as Formatter;

require_once __DIR__."/../vendor/autoload.php";

// error handler for dev mode
if (class_exists('Whoops\Run')) {
    $whoops = new \Whoops\Run;
    $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
    $whoops->register();
}

// check for file upload
if ( ! empty($_FILES['file'])) {
    // use the system's temp dir for a file cache
    $cache = new FilesystemCache(sys_get_temp_dir().DIRECTORY_SEPARATOR.'cpvc');
    // new checker class from uploaded composer.json file
    $checker = new Checker($_FILES['file']['tmp_name'], $cache);
    // check and render output
    $formatter = new Formatter;
    $result = $formatter->render($checker->checkAll());
}

?>
<html>
    <head>
        <link rel="stylesheet" type="text/css" href="css/default.css" />
    </head>
<body>
    <h1>Composer package version checker</h1>

<?php

if (! empty($result)) {
    die($result);
}

?>

<form enctype="multipart/form-data" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
    Select your composer.json file: <input name="file" type="file" onchange="this.form.submit()" />
    <input type="submit" name="form_submit" value="Send File" />
</form>
</body>
</html>
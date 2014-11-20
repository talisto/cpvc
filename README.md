Composer.json Package Version Checker (cpvc)
============================================

[![Build Status](https://travis-ci.org/talisto/cpvc.svg?branch=master)](https://travis-ci.org/talisto/cpvc)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/talisto/cpvc/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/talisto/cpvc/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/talisto/cpvc/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/talisto/cpvc/?branch=master)
[![Build Status](https://scrutinizer-ci.com/g/talisto/cpvc/badges/build.png?b=master)](https://scrutinizer-ci.com/g/talisto/cpvc/build-status/master)

Do you lock down your composer.json dependencies to a particular version for stability in your application,
but often wonder if there's new versions of your dependencies that you should be implementing?  

This small application/library will check your composer.json file, and output a table showing the latest version
that will be installed based on your "require" statements, as well as the latest version (of the same stability)
of the package in the repository.  You can then compare the versions and investigate any updates to your
dependencies for refactoring.

### Sample output (CLI mode):

    +-----------------------------+------------------+--------------------+
    | Package                     | Required Version | Repository Version |
    +-----------------------------+------------------+--------------------+
    | * talisto/fake-repository   | 1.0.1            | 2.0.0              |
    | talisto/fake-dev-repository | 2.5.1            | 2.5.1              |
    +-----------------------------+------------------+--------------------+

Usage
-----

You can use this package as a library in your application, or a CLI app, or a web app.

### Library Usage:

For a quick install with Composer use:

    $ composer require talisto/cpvc
    
Then in your application, use the following:

    use Talisto\Composer\VersionCheck\Checker;
    
    $checker = new Checker('/path/to/your/composer.json');
    $result = $checker->checkAll();

The `Checker()` constructor will take a [Doctrine Cache](https://github.com/doctrine/cache) object as the second
parameter to cache the lookup results:

    use Talisto\Composer\VersionCheck\Checker;
    use Doctrine\Common\Cache\FilesystemCache;
    
    $cache = new FilesystemCache(sys_get_temp_dir());
    $checker = new Checker('/path/to/your/composer.json', $cache);
    $result = $checker->checkAll();

*Alternatively you can use the `setCache()` method to set the cache handler.*

There is an output formatter class that will take the results and output an HTML table:

    use Talisto\Composer\VersionCheck\Checker;
    use Talisto\Composer\VersionCheck\Formatter\HTML as Formatter;
    
    $checker = new Checker('/path/to/your/composer.json');
    $formatter = new Formatter;
    echo $formatter->render($checker->checkAll());

If you only want to check a subset of your dependencies:

    use Talisto\Composer\VersionCheck\Checker;
    use Talisto\Composer\VersionCheck\Formatter\HTML as Formatter;
    
    $checker = new Checker('/path/to/your/composer.json');
    $packages = $checker->getPackageLinks();
    $result = $checker->checkPackages(array(
        'talisto/fake-repository' => $packages['talisto/fake-repository']
    ));

### CLI usage:

There are two ways to run the checker from the CLI.  You'll need to run `composer install` in the package root
to install the dependencies before running the scripts.

1.  *php script*:
    `php cli/index.php`
2.  *phar archive*:
    `cd build; php create_phar.php; chmod u+x cpvc.phar; ./cpvc.phar`

The CLI script takes multiple parameters, run with `-h` for help:

    Usage:
     cpvc [--no-dev] [--no-cache] [path]
    
    Arguments:
     path                  Path to composer.json (default: "/Users/matt/Projects/cpvc/build")
    
    Options:
     --no-dev              Don't include dev dependencies.
     --no-cache            Don't cache the results.
     --help (-h)           Display this help message.
     --quiet (-q)          Do not output any message.
     --verbose (-v|vv|vvv) Increase the verbosity of messages: 1 for normal output, 2 for more verbose output and 3 for debug.
     --version (-V)        Display this application version.
     --ansi                Force ANSI output.
     --no-ansi             Disable ANSI output.
     --no-interaction (-n) Do not ask any interactive question.

### Web usage:

There is a simple index.php script in the `www` folder that will allow you to upload a `composer.json` file through
a web browser.  Just point your webserver to that folder.
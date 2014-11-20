<?php

/*
 * (c) Matt Lyon <talisto@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Talisto\Composer\VersionCheck\Console;

use Symfony\Component\Console\Command\Command as ConsoleCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Doctrine\Common\Cache\FilesystemCache;

use Talisto\Composer\VersionCheck\Checker;
use Talisto\Composer\VersionCheck\Formatter\Console as Formatter;

class Command extends ConsoleCommand
{
    /**
     * configure the console command.
     *
     * @access protected
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('cpvc')
            ->setDescription('Check your composer.json dependencies for newer versions.')
            ->addArgument(
                'path',
                InputArgument::OPTIONAL,
                'Path to composer.json',
                getcwd()
            )
            ->addOption(
               'no-dev',
               null,
               InputOption::VALUE_NONE,
               'Don\'t include dev dependencies.'
            )
            ->addOption(
               'no-cache',
               null,
               InputOption::VALUE_NONE,
               'Don\'t cache the results.'
            );
    }

    /**
     * execute the console command.
     *
     * @access protected
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // command-line switch to disable checking of dev packages
        if ($input->getOption('no-dev')) {
            $include_dev = false;
        } else {
            $include_dev = true;
        }

        // command-line switch to disable the cache
        if ($input->getOption('no-cache')) {
            $cache = null;
        } else {
            $cache = new FilesystemCache(sys_get_temp_dir().DIRECTORY_SEPARATOR.'cpvc');
        }

        $checker = new Checker($input->getArgument('path').'/composer.json', $cache);
        $formatter = new Formatter($output);
        $output->writeln($formatter->render($checker->checkAll($include_dev)));
    }
}
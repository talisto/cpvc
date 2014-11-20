<?php

/*
 * (c) Matt Lyon <talisto@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Talisto\Composer\VersionCheck\Formatter;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\Table;

class Console
{
    private $output = null;

    /**
     * constructor.  We store the Output object here for the renderer to use later.
     *
     * @access public
     * @param OutputInterface $output
     * @return void
     */
    public function __construct(OutputInterface $output)
    {
        $this->output = $output;
    }

    /**
     * renders the package information to an ASCII table for the console.
     *
     * @access public
     * @param array $packages
     * @return void
     */
    public function render($packages)
    {
        $table = new Table($this->output);
        $table->setHeaders(array(
            'Package', 'Required Version', 'Repository Version'
        ));

        foreach ($packages as $name => $package) {
            if (empty($package['current']) or empty($package['latest'])) {
                $table->addRow(array(
                   $name, '<fg=red>Not Found</fg=red>', '<fg=red>Not Found</fg=red>'
                ));
            } elseif ($package['current']!=$package['latest']) {
                $table->addRow(array(
                   '<options=bold><fg=yellow>*</fg=yellow> '.$name.'</options=bold>',
                   '<options=bold>'.$package['current']->getPrettyVersion().'</options=bold>',
                   '<options=bold>'.$package['latest']->getPrettyVersion().'</options=bold>'
                ));
            } else {
                $table->addRow(array(
                   $name, $package['current']->getPrettyVersion(), $package['latest']->getPrettyVersion()
                ));
            }
        }

        $table->render();
    }
}
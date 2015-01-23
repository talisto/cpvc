<?php

/*
 * (c) Matt Lyon <talisto@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Talisto\Composer\VersionCheck\Formatter;

class HTML
{
    /**
     * renders the package data to an HTML table.
     *
     * @access public
     * @param array $packages
     * @return string
     */
    public function render($packages)
    {
        $output = "<table><tr><th>Package</th><th>Required Version</th><th>Repository Version</th></tr>\n";
        foreach ($packages as $name => $package) {
            if (empty($package['required']) or empty($package['latest'])) {
                $output .= "<tr><td>".$name."</td><td colspan=2>Not Found</td></tr>\n";
            } else {
                if ($package['required']->getVersion()!=$package['latest']->getVersion()) {
                    $new = true;
                } else {
                    $new = false;
                }
                $output .= "<tr".($new?" class=\"new\"":false).">\n";
                $output .= "<td>".$name."</td>\n";
                $output .= "<td>".$package['required']->getPrettyVersion()."</td>\n";
                $output .= "<td>".$package['latest']->getPrettyVersion()."</td>\n";
                $output .= "</tr>\n";
            }
        }
        $output .= "</table>";
        return $output;
    }
}
<?php

/*
 * (c) Matt Lyon <talisto@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Talisto\Composer\VersionCheck;

use Composer\Factory as ComposerFactory;
use Composer\IO\NullIO;
use Composer\Repository\CompositeRepository;
use Composer\DependencyResolver\Pool;
use Composer\Package\Package;
use Composer\Package\Link;
use Composer\Package\LinkConstraint\VersionConstraint;
use Composer\Package\LinkConstraint\LinkConstraintInterface;
use Composer\Package\Version\VersionParser;
use Doctrine\Common\Cache\Cache;

class Checker
{
    protected $composer;            // main composer object
    protected $versionParser;       // composer version parser

    protected $cache;               // doctrine cache object
    protected $cacheTTL = 3600;     // cache result TTL (in seconds)

    /**
     * constructor.
     *
     * @access public
     * @param string $composer_file full path and name to the composer.json file
     * @param Cache $cache (default: null)  doctrine cache
     * @return null
     */
    public function __construct($composer_file, Cache $cache = null)
    {
        $factory = new ComposerFactory;
        $composer = $factory->createComposer(new NullIO, $composer_file);

        $this->composer      = $composer;
        $this->cache         = $cache;
        $this->versionParser = new VersionParser;
    }

    /**
     * returns the package links parsed from the composer.json file (by the Composer class).
     *
     * @access public
     * @param bool $includeDev (default: true)  include dev packages
     * @return array
     */
    public function getPackageLinks($includeDev = true)
    {
        $result = $this->composer->getPackage()->getRequires();
        if ($includeDev and $packages = $this->composer->getPackage()->getDevRequires()) {
            $result = $result + $packages;
        }
        return $result;
    }

    /**
     * attempts to find a particular package by name and returns the latest version.
     *
     * @access protected
     * @param string $package_name
     * @param LinkConstraintInterface $constraint (default: null)
     * @param string $stability (default: null)
     * @return null|Package
     */
    protected function find($package_name, LinkConstraintInterface $constraint = null, $stability = null)
    {
        $repo = new CompositeRepository($this->composer->getRepositoryManager()->getRepositories());
        $pool = new Pool('dev');
        $pool->addRepository($repo);

        if ( ! $matches = $pool->whatProvides($package_name, $constraint)) {
            return;
        }

        $latest = false;
        foreach ($matches as $match) {
            if (($match->getName() == $package_name)
                and ( ! $stability or $match->getStability() == $stability)
                and ( ! $latest or version_compare($match->getVersion(), $latest->getVersion())==1)) {
                $latest = $match;
            }
        }

        if ($latest) {
            // we return a new package here, rather than the existing complete package,
            // in order to make caching easier.  Otherwise the cache ends up being
            // massive, and PHP runs out of memory reading it back in!
            return new Package($latest->getName(), $latest->getVersion(), $latest->getPrettyVersion());
        }
    }

    /**
     * checks a particular package name and link data for the current (required)
     * package as well as the latest (most recent) package of the same stability.
     *
     * @access public
     * @param mixed $name
     * @param Link $link
     * @return null|array
     */
    public function checkPackageLink($name, Link $link)
    {
        if ($this->cache) {
            $cache_id = __METHOD__.':'.$name.':'.md5(serialize($link));
            if ($result = $this->cache->fetch($cache_id)) {
                return $result;
            }
        }

        $result = array(
            'current' => false,
            'latest'  => false
        );

        $stability = $this->versionParser->parseStability($link->getPrettyConstraint());
        if ($current = $this->find($name, $link->getConstraint(), $stability)) {
            $latest = $this->find($name, new VersionConstraint('>', $current->getVersion()), $stability);
            $result['current'] = $current;
            $result['latest'] = $latest?:$current;

            if ($this->cache) {
                $this->cache->save($cache_id, $result, $this->cacheTTL);
            }
        }
        return $result;
    }

    /**
     * checks multiple packages to return the current (required) package as well as
     * the latest (most recent) package of the same stability.
     *
     * @access public
     * @param mixed $packages   indexed array of Link classes
     * @return array
     */
    public function checkPackages($packages)
    {
        $result = array();
        foreach ($packages as $name => $link) {
            $result[$name] = $this->checkPackageLink($name, $link);
        }
        return $result;
    }

    /**
     * checks the current composer.json requirements for the current required versions,
     * as well as the latest (most recent) versions of the same stability.
     *
     * @access public
     * @param bool $includeDev (default: true)  include dev packages
     * @return array
     */
    public function checkAll($includeDev = true)
    {
        if ($packages = $this->getPackageLinks($includeDev)) {
            return $this->checkPackages($packages);
        }
    }
}
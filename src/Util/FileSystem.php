<?php
namespace svenlie\CodeceptionCssRegression\Util;

use Codeception\Module\CssRegression;

/**
 * Provide some methods for filesystem related actions
 */
class FileSystem
{

    /**
     * @var CssRegression
     */
    protected $module;

    /**
     * @param CssRegression $module
     */
    public function __construct(CssRegression $module)
    {
        $this->module = $module;
    }

    /**
     * Create a directory recursive
     *
     * @param $path
     */
    public function createDirectoryRecursive($path)
    {
        if (substr($path, 0, 1) !== '/' && substr($path, 1, 1) !== ':') {
            $path = \Codeception\Configuration::projectDir() . $path;
        } elseif (!strstr($path, \Codeception\Configuration::projectDir())) {
            throw new \InvalidArgumentException(
                'Can\'t create directroy "' . $path
                . '" as it is outside of the project root "'
                . \Codeception\Configuration::projectDir() . '"'
            );
        }

        if (!is_dir(dirname($path))) {
            self::createDirectoryRecursive(dirname($path));
        }

        if (!is_dir($path)) {
            \Codeception\Util\Debug::debug('Directory "' . $path . '" does not exist. Try to create it ...');
            mkdir($path);
        }
    }

    /**
     * Get path for the reference image
     *
     * @param string $identifier
     * @return string path to the reference image
     */
    public function getReferenceImagePath($identifier, $sizeString)
    {
        return $this->getReferenceImageDirectory()
        . $sizeString . DIRECTORY_SEPARATOR
        . $this->sanitizeFilename($identifier) . '.png';
    }

    /**
     * Get the directory where reference images are stored
     *
     * @return string
     */
    public function getReferenceImageDirectory()
    {
        return \Codeception\Configuration::dataDir()
        . rtrim($this->module->_getConfig('referenceImageDirectory'), DIRECTORY_SEPARATOR)
        . DIRECTORY_SEPARATOR;
    }

    /**
     * @return string
     */
    public function getCurrentWindowSizeString(\Codeception\Module\WebDriver $webDriver)
    {
        $windowSize = $webDriver->webDriver->manage()->window()->getSize();
        return ($windowSize->getWidth() - $this->module->_getConfig('widthOffset')) . 'x' . $windowSize->getHeight();
    }

    /**
     * @param $name
     * @return string
     */
    public function sanitizeFilename($name)
    {
        // remove non alpha numeric characters
        $name = preg_replace('/[^A-Za-z0-9\._\- ]/', '', $name);

        // capitalize first character of every word convert single spaces to underscrore
        $name = str_replace(" ", "_", ucwords($name));

        return $name;
    }

    /**
     * Get path for the fail image with a suffix
     *
     * @param string $identifier test identifier
     * @param string $suffix suffix added to the filename
     * @return string path to the fail image
     */
    public function getFailImagePath($identifier, $sizeString, $suffix = 'fail')
    {
        $testName = $this->module->_getCurrentTestCase()->getMetadata()->getName();

        $fileNameParts = array(
            $suffix,
            $identifier,
            'png'
        );

        return $this->getFailImageDirectory()
        . $testName . DIRECTORY_SEPARATOR
        . $sizeString . DIRECTORY_SEPARATOR
        . $this->sanitizeFilename(implode('.', $fileNameParts));
    }

    /**
     * Get directory where fail images are stored
     *
     * @return string
     */
    public function getFailImageDirectory()
    {
        return \Codeception\Configuration::outputDir()
        . rtrim($this->module->_getConfig('failImageDirectory'), DIRECTORY_SEPARATOR)
        . DIRECTORY_SEPARATOR . $this->module->_getModuleInitTime() . DIRECTORY_SEPARATOR;
    }

    /**
     * Get path to the temp image for the given identifier
     *
     * @param string $identifier identifier for the test
     * @return string Path to the temp image
     */
    public function getTempImagePath($identifier)
    {
        $fileNameParts = array(
            $this->module->_getModuleInitTime(),
            $this->getCurrentWindowSizeString($this->module->_getWebdriver()),
            $this->sanitizeFilename($identifier),
            'png'
        );
        return $this->getTempDirectory() . implode('.', $fileNameParts);
    }

    /**
     * Get the directory to store temporary files
     *
     * @return string
     */
    public function getTempDirectory()
    {
        return \Codeception\Configuration::outputDir() . 'debug' . DIRECTORY_SEPARATOR;
    }
}

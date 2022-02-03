<?php
/**
 * Document
 *
 * @package Framework\Dominator
 * @version 1.0.0
 */
namespace Framework\Dominator;

/**
 * Document: The web page object
 *
 * Document is the fulfillment center for your website. You should never
 * use echo or print just write your conent to the document and when
 * everything is done call render
 * @todo Implement a purge method
 */
class Document extends \Framework\Support\Abstraction\Singleton
{
    /**
     * This is where all of your information is stored
     * @var mixed
     * @access private
     */
    private $data = "";

    /**
     * This is your method for writing to the buffer
     * @param $data
     * @access public
     */
    public function write($data)
    {
        $this->data .= $data;
    }

    /**
     * Render the page and clear the data
     *
     * We split the data into chunks and output it
     * this way we avoid apache having to fill the buffer repeatedly
     * then empty it
     * @return void
     * @access public
     * @todo set the chunk size to a more reasonable amount
     */
    public static function render()
    {
        $output_array = str_split(self::getInstance()->data, 4096);
        foreach ($output_array as $index => $chunk) {
            echo $chunk;
        }
        return true;
    }

    /**
     * purge
     *
     * Clear the buffer
     * @return void
     * @access public
     */
    public static function purge()
    {
        self::getInstance()->data = "";
    }
}

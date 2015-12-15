<?php

/**
 * Sainsburys Product Scrapper Test
 * @author Ciaran Synnott <hello@synnott.co.uk>
 */
class SainsburysScrapper {

    const URL = "http://hiring-tests.s3-website-eu-west-1.amazonaws.com/2015_Developer_Scrape/5_products.html";

    public $doc;
    public $xpath;
    public $html;
    public $basket;

    public function __construct() {
        // cURL first product page
        $listings = $this->curl(self::URL);

        if($listings->status !== 200){
            throw new Exception('$listings->status code returned. Could not retieve data.');
        }
        
        // Set Base HTML
        $this->html = $listings->data;


        libxml_use_internal_errors(true); // Compress of errors
        $this->doc = new DOMDocument();
        $this->doc->loadHTML($this->html);

        // Set XPath for Queries
        $this->xpath = new DOMXPath($this->doc);

        // Setup default basket
        $this->basket = (object) array(
                    "results" => null,
                    "total" => 0
        );
    }

    /**
     * Returns JSON encoded string of results.
     * @access public 
     * @return string
     */
    public function fetch() {
        $items = $this->scrape();
        $this->basket->results = $items;
        foreach ($items as $i) {
            // add totals
            $this->basket->total += $i->unit_price;
        }
        // format number to two decimal places.
        $this->basket->total = number_format($this->basket->total, 2, ".", "");
        return json_encode($this->basket);
    }

    /**
     * Runs through first page to perform cURL reques to product pages and get
     * details.
     * @access private 
     * @return array
     */
    private function scrape() {
        $items = $this->get_items();
        foreach ($items as $i) {

            $url = $i->url;

            // we don't need this anymore
            unset($i->url);

            $item_page = $this->curl($url);
        if($item_page->status !== 200){
            throw new Exception('$listings->status code returned. Could not retieve data.');
        }
            
            
            // Set each single page as a new Document
            $this->doc = new DOMDocument();
            $this->doc->loadHTML($item_page->data);
            $this->xpath = new DOMXPath($this->doc);

            $i->size = $item_page->size;
            $i->unit_price = $this->get_price_per_unit();
            $i->description = $this->get_description();
        }

        return $items;
    }

    /**
     * Fetchs the price per unit of a product page
     * @access private 
     * @return string
     */
    private function get_price_per_unit() {
        $product_texts = $this->xpath->query('//p[@class="pricePerUnit"]');
        $text = trim(rtrim($product_texts->item(0)->textContent));
        // Remove currency and '/unit'
        return str_replace(array(
            '£', '/unit'
                ), '', $text);
    }

    /**
     * Fetchs the description of a product page
     * @access private 
     * @return string
     */
    private function get_description() {
        $product_texts = $this->xpath->query('//div[@class="productText"]');
        // Find Description
        return trim(rtrim($product_texts->item(0)->textContent)); // This is the first child
    }

    /**
     * Return an array of the first page of Sainsburys products
     * @access private 
     * @return array
     */
    private function get_items() {
        $items = array();

        foreach ($this->xpath->query('//div[@class="productInfo"]/h3') as $item) {

            $a = $this->xpath->query("./a", $item); // Find Text
            $href = $this->xpath->query("./a/@href", $item); // Find URL

            $product = new StdClass();

            $product->title = trim(rtrim($a->item(0)->nodeValue));
            $product->url = $href->item(0)->value;

            $items[] = $product;
        }

        return $items;
    }

    /**
     * Performs a cURL request to a webpage
     * @access private 
     * @param string $url
     * @return object
     */
    private function curl($url) {
        if (!is_string($url)) {
            throw new InvalidArgumentException('$url must be a string');
        }
        $ch = curl_init();
        $agent = "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)";
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_USERAGENT, $agent);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        $data = curl_exec($ch);
        return (object) array(
                    "status" => curl_getinfo($ch, CURLINFO_HTTP_CODE),
                    "size" => $this->format_bytes((int) curl_getinfo($ch, CURLINFO_CONTENT_LENGTH_DOWNLOAD)),
                    "data" => $data
        );
    }

    /**
     * Converts bytes to a user-friendly format
     * @access private 
     * @param string $bytes
     * @return string
     */
    private function format_bytes($bytes) {
        if (!is_int($bytes)) {
            throw new InvalidArgumentException('$bytes must be an integer');
        }
        if ($bytes >= 1073741824) {
            $bytes = number_format($bytes / 1073741824, 2) . 'gb';
        } elseif ($bytes >= 1048576) {
            $bytes = number_format($bytes / 1048576, 2) . 'mb';
        } elseif ($bytes >= 1024) {
            $bytes = number_format($bytes / 1024, 2) . 'kb';
        } elseif ($bytes > 1) {
            $bytes = $bytes . ' bytes';
        } elseif ($bytes == 1) {
            $bytes = $bytes . ' byte';
        } else {
            $bytes = '0 bytes';
        }

        return $bytes;
    }

}

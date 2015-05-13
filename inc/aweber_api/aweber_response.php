<?php

/**
 * AWeberResponse
 *
 * Base class for objects that represent a response from the AWeberAPI. 
 * Responses will exist as one of the two AWeberResponse subclasses:
 *  - AWeberEntry - a single instance of an AWeber resource
 *  - AWeberCollection - a collection of AWeber resources
 * @uses AWeberAPIBase
 * @package 
 * @version $id$
 */
class AWeberResponse extends AWeberAPIBase {

    public $adapter = false;
    public $data = array();

    public function __construct($response, $url, $adapter) {
        $this->adapter = $adapter;
        $this->url     = $url;
        $this->data    = $response;
    }

    /**
     * __get
     *
     * PHP "MagicMethod" to allow for dynamic objects.  Defers first to the 
     * data in $this->data.
     *
     * @param String $value  Name of the attribute requested
     * @access public
     * @return mixed
     */
    public function __get($value) {
        if (in_array($value, $this->_privateData)) {
            return null;
        }
        if (isset($this->data[$value])) {
            return $this->data[$value];
        }
        if ($value == 'type') return $this->_type();
    }

}



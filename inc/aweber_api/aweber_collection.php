<?php
class AWeberCollection extends AWeberResponse implements ArrayAccess, Iterator, Countable {

    protected $pageSize = 100;
    protected $_entries = array();

    /**
     * @var array Holds list of keys that are not publicly accessible
     */
    protected $_privateData = array(
        'entries',
        'start',
        'next_collection_link',
    );

    /**
     * getById
     *
     * Gets an entry object of this collection type with the given id
     * @param mixed $id     ID of the entry you are requesting
     * @access public
     * @return AWeberEntry
     */
    public function getById($id) {
        try {
            $data = $this->adapter->request('GET', "{$this->url}/{$id}");
            return $this->_makeEntry($data, $id, "{$this->url}/{$id}");
        } catch (AWeberException $e) {
            return null;
        }
    }

    /**
     * _getPageParams
     *
     * Returns an array of GET params used to set the page of a collection
     * request
     * @param int $start    Which entry offset should this page start on?
     * @param int $size     How many entries should be included in this page?
     * @access protected
     * @return void
     */
    protected function _getPageParams($start=0, $size=20) {
        if ($start > 0) {
            $params = array(
                'ws.start' => $start,
                'ws.size'  => $size,
            );
            ksort($params);
        } else {
            $params = array();
        }
        return $params;
    }

    /**
     * _type
     *
     * Interpret what type of resources are held in this collection by 
     * analyzing the URL
     *
     * @access protected
     * @return void
     */
    protected function _type() {
        $urlParts = explode('/', $this->url);
        $type = array_pop($urlParts);
        return $type;
    }

    /**
     * _calculatePageSize
     *
     * Calculates the page size of this collection based on the data in the 
     * next and prev links.
     *
     * @access protected
     * @return integer
     */
    protected function _calculatePageSize() {
        if (isset($this->data['next_collection_link'])) {
            $url = $this->data['next_collection_link'];
            $urlParts = parse_url($url);
            if (empty($urlParts['query'])) return $this->pageSize;
            $query = array();
            parse_str($urlParts['query'], $query);
            if (empty($query['ws_size'])) return $this->pageSize;
            $this->pageSize = $query['ws_size'];
        }
        return $this->pageSize;
    }

    /**
     * _loadPageForOffset
     * 
     * Makes a request for an additional page of entries, based on the given 
     * offset.  Calculates the start / size of the page needed to get that 
     * offset, requests for it, and then merges the data into it internal 
     * collection of entry data.
     *
     * @param mixed $offset     The offset requested, 0 to total_size-1
     * @access protected
     * @return void
     */
    protected function _loadPageForOffset($offset, $attempt=1) {
        $this->_calculatePageSize();
        $start = round($offset / $this->pageSize) * $this->pageSize;
        $params = $this->_getPageParams($start, $this->pageSize);

        // Loading page
        try {
            $data = $this->adapter->request('GET', $this->url, $params);
            $this->adapter->debug = false;
        }
        catch (Exception $e) {
            if ($attempt < 3) $this->_loadPageForOffset($offset, ++$attempt);
            return;
        }
        $rekeyed = array();
        foreach ($data['entries'] as $key => $entry) {
            $rekeyed[$key+$data['start']] = $entry;
        }
        $this->data['entries'] = array_merge($this->data['entries'], $rekeyed);
    }

    /**
     * _getEntry
     *
     * Makes sure that entry offset's page is loaded, then returns it. Returns
     * null if the entry can't be loaded, even after requesting the needed 
     * page.
     *
     * @param mixed $offset     Offset being requested.
     * @access protected
     * @return void
     */
    protected function _getEntry($offset) {
        if (empty($this->data['entries'][$offset])) {
            $this->_loadPageForOffset($offset);
        }
        return (empty($this->data['entries'][$offset]))? null :
            $this->data['entries'][$offset];
    }

    /**
     * _makeEntry
     *
     * Creates an entry object from the given entry data.  Optionally can take 
     * the id and URL of the entry, though that data can be infered from the
     * context in which _makeEntry is being called.
     *
     * @param mixed $data   Array of data returned from an API request for 
     *      entry, or as part of the entries array in this collection.
     * @param mixed $id     ID of the entry. (Optional)
     * @param mixed $url    URL used to retrieve this entry (Optional) 
     * @access protected
     * @return void
     */
    protected function _makeEntry($data, $id = false, $url= false) {
        if (!$id) {
            $id = $data['id'];
        }
        if (!$url) {
            $url = "{$this->url}/{$id}";
        }
        return new AWeberEntry($data, $url, $this->adapter);
    }


    /**
     * ArrayAccess interface methods
     *
     * Allows this object to be accessed via bracket notation (ie $obj[$x])
     * http://php.net/manual/en/class.arrayaccess.php
     */
    public function offsetSet($offset, $value) { }
    public function offsetUnset($offset) {}
    public function offsetExists($offset) {
        if ($offset >=0 && $offset < $this->total_size) {
            return true;
        }
        return false;
    }
    public function offsetGet($offset) {
        if (!$this->offsetExists($offset))    return null;
        if (!empty($this->_entries[$offset])) return $this->_entries[$offset];

        $this->_entries[$offset] = $this->_makeEntry($this->_getEntry($offset));
        return $this->_entries[$offset];
    }

    /**
     * Iterator interface methods
     * 
     * Provides iterator functionality.
     * http://php.net/manual/en/class.iterator.php
     */
    protected $_iterationKey = 0;
    public function current() {
        return $this->offsetGet($this->_iterationKey);
    }

    public function key() {
        return $this->_iterationKey;
    }

    public function next() {
        $this->_iterationKey++;
    }

    public function rewind() {
        $this->_iterationKey = 0;
    }

    public function valid() {
        return $this->offsetExists($this->key());
    }

    /**
     * Countable interface methods
     *
     * Allows PHP's count() and sizeOf() functions to act on this object
     * http://www.php.net/manual/en/class.countable.php
     */
    public function count() {
        return $this->total_size;
    }

}

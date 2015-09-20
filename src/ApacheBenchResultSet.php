<?php

namespace Benchy;

class ApacheBenchResultSet implements \ArrayAccess, \IteratorAggregate
{
    /**
     * @var object
     */
    private $config;

    /**
     * @var ApacheBenchResult[]
     */
    private $results = array();

    /**
     * @param object $config
     */
    public function __construct($config)
    {
        $this->config = $config;
    }

    public function save($filename)
    {
        $ret_obj = new \stdClass();
        $ret_obj->config = $this->config;
        $ret_obj->results = array();

        foreach ($this->results as $result) {
            $ret_obj->results[] = $result->getResults();
        }

        $ret_obj->zipped_results = $this->zip_associative_arrays($ret_obj->results);
        file_put_contents($filename, json_encode($ret_obj));
    }

    function zip_associative_arrays(array $array_list)
    {
        if (count($array_list) == 0) {
            return array();
        }
        $keys = array_keys($array_list[0]);
        $ret = array();

        foreach ($keys as $k) {
            $ret[$k] = array();
        }

        foreach ($array_list as $a) {
            foreach ($keys as $k) {
                $ret[$k][] = $a[$k];
            }
        }

        return $ret;
    }

    /**
     * @inheritdoc
     */
    public function offsetExists($offset)
    {
        return isset($this->results[$offset]);
    }

    /**
     * @inheritdoc
     *
     * @return ApacheBenchResult|null
     */
    public function offsetGet($offset)
    {
        return isset($this->results[$offset]) ? $this->results[$offset] : null;
    }

    /**
     * @inheritdoc
     */
    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->results[] = $value;
        } else {
            $this->results[$offset] = $value;
        }
    }

    /**
     * @inheritdoc
     */
    public function offsetUnset($offset)
    {
        unset($this->results[$offset]);
    }

    /**
     * @inheritdoc
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->results);
    }
}
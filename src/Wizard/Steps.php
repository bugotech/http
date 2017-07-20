<?php namespace Bugotech\Http\Wizard;

use ArrayAccess;

class Steps implements ArrayAccess
{
    /**
     * Lista de itens.
     * @var array
     */
    protected $steps = [];

    /**
     * Step atual.
     * @var Step
     */
    protected $current;

    /**
     * @var string
     */
    protected $prefixRoute;

    /**
     * Add step in list.
     *
     * @param $key
     * @param $title
     * @param $desc
     * @param array $validates
     * @return Step
     */
    public function add($key, $title, $desc, array $validates = [])
    {
        return $this->steps[$key] = new Step($this, $key, $title, $desc, $validates);
    }

    /**
     * Setar step atual.
     * @param $current
     * @return bool
     */
    public function setCurrent($current)
    {
        $this->current = null;

        if (! $this->exists($current)) {
            return false;
        }

        $this->current = $this->steps[$current];

        return true;
    }

    /**
     * @param $prefix
     * @return $this
     */
    public function setPrefixRoute($prefix)
    {
        $this->prefixRoute = $prefix;

        return $this;
    }

    /**
     * @return string
     */
    public function getPrefixRoute()
    {
        return $this->prefixRoute;
    }

    /**
     * @return array
     */
    public function all()
    {
        return $this->steps;
    }

    /**
     * @return int
     */
    public function count()
    {
        return count($this->steps);
    }

    /**
     * Step atual.
     * @return Step
     */
    public function current()
    {
        return $this->current;
    }

    /**
     * @param $stepId
     * @return bool
     */
    public function exists($stepId)
    {
        return $this->offsetExists($stepId);
    }

    /**
     * @return string
     */
    public function firstId()
    {
        return array_keys($this->steps)[0];
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Whether a offset exists.
     * @link http://php.net/manual/en/arrayaccess.offsetexists.php
     * @param mixed $offset <p>
     * An offset to check for.
     * </p>
     * @return bool true on success or false on failure.
     * </p>
     * <p>
     * The return value will be casted to boolean if non-boolean was returned.
     */
    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->steps);
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to retrieve.
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     * @param mixed $offset <p>
     * The offset to retrieve.
     * </p>
     * @return Step
     */
    public function offsetGet($offset)
    {
        return $this->steps[$offset];
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to set.
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     * @param mixed $offset <p>
     * The offset to assign the value to.
     * </p>
     * @param Step $value <p>
     * The value to set.
     * </p>
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        $this->steps[$offset] = $value;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to unset.
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     * @param mixed $offset <p>
     * The offset to unset.
     * </p>
     * @return void
     */
    public function offsetUnset($offset)
    {
        unset($this->steps[$offset]);
    }
}
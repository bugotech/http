<?php namespace Bugotech\Http\Wizard;

class Step
{
    /**
     * @var Steps
     */
    protected $steps;

    /**
     * @var string
     */
    public $key = '';

    /**
     * @var string
     */
    public $title = '';

    /**
     * @var string
     */
    public $desc = '';

    /**
     * @param $key
     * @param $title
     * @param $desc
     */
    public function __construct($steps, $key, $title, $desc)
    {
        $this->steps = $steps;
        $this->key = $key;
        $this->title = $title;
        $this->desc = $desc;
    }

    /**
     * Retorna se este step esta ativo.
     * @return bool
     */
    public function isActive()
    {
        $curr = $this->steps->current();
        if (is_null($curr)) {
            return false;
        }

        return ($curr->key == $this->key);
    }
}
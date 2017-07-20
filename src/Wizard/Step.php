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
     * @var array
     */
    public $validates = [];

    /**
     * @param $key
     * @param $title
     * @param $desc
     */
    public function __construct($steps, $key, $title, $desc, array $validates = [])
    {
        $this->steps = $steps;
        $this->key = $key;
        $this->title = $title;
        $this->desc = $desc;
        $this->validates = $validates;
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

        return $curr->key == $this->key;
    }

    /**
     * URL do passo.
     * @return string
     */
    public function url($method = 'get')
    {
        $id = sprintf('%s.%s', $this->steps->getPrefixRoute(), $method);

        return route($id, ['step' => $this->key]);
    }

    /**
     * Redirecionar para a URL do passo.
     * @param string $method
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function redirect($method = 'get')
    {
        return redirect($this->url($method));
    }

    /**
     * Próximo passo.
     *
     * @return Step|null
     */
    public function next()
    {
        $keys = array_keys($this->steps->all());
        $i = array_search($this->key, $keys);

        if (is_null($i)) {
            return;
        }
        if (! isset($keys[$i + 1])) {
            return;
        }

        return $this->steps->offsetGet($keys[$i + 1]);
    }

    /**
     * Passo anterior.
     *
     * @return Step|null
     */
    public function back()
    {
        $keys = array_keys($this->steps->all());
        $i = array_search($this->key, $keys);

        if (is_null($i)) {
            return;
        }
        if (! isset($keys[$i - 1])) {
            return;
        }

        return $this->steps->offsetGet($keys[$i - 1]);
    }
}
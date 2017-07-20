<?php namespace Bugotech\Http\Wizard;

/**
 * Class WizardTrait
 * @see Controller
 */
trait WizardTrait
{
    /**
     * Lista de passos.
     * @var Steps
     */
    protected $steps;

    /**
     * Prefixo do nome da visao dos passos.
     * @var string
     */
    protected $prefixViewName;

    /**
     * Prefixo da rota *.get e *.post.
     * @var string
     */
    protected $prefixRoute;

    /**
     * @var array
     */
    protected $viewParams = [];

    /**
     * Contructor.
     */
    public function __construct()
    {
        $this->steps = new Steps();
    }

    /**
     * Preparar passos.
     */
    protected function prepareSteps()
    {
        $step = router()->current()->parameter('step', $this->steps->firstId());
        if (! $this->steps->exists($step)) {
            error('Step "%s" not found', $step);
        }
        $this->steps->setCurrent($step);
        $this->steps->setPrefixRoute($this->prefixRoute);
    }

    /**
     * @return mixed
     */
    public function getSteps()
    {
        $this->prepareSteps();

        // Carregar step atual
        $step = $this->steps->current();

        // Carregar view
        $view_id = sprintf('%s.%s', $this->prefixViewName, $step->key);
        if (! view()->exists($view_id)) {
            error('View of step "%s" not found', $view_id);
        }

        $view = view($view_id);
        $view->with('steps', $this->steps);
        $view->with('step', $step);
        $view->with($this->viewParams);

        return $view;
    }

    public function setSteps()
    {
        $this->prepareSteps();
        //..
    }
}
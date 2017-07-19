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
     * Nome da visao dos passos.
     * @var string
     */
    protected $prefixViewName;

    /**
     * @var array
     */
    protected $viewParams = [];

    /**
     * Preparar passos.
     */
    protected function prepareSteps()
    {
        $this->steps = new Steps();

        $step = router()->current()->parameter('step', $this->steps->firstId());
        if (! $this->steps->exists($step)) {
            error('Step "%s" not found', $step);
        }
        $this->steps->setCurrent($step);
    }

    /**
     * @return mixed
     */
    public function getSteps()
    {
        // Carregar step atual
        $step = $this->steps->current();

        // Carregar view
        $view_id = sprintf('%s.%s', $this->prefixViewName, $step);
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
        //..
    }
}
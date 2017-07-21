<?php namespace Bugotech\Http\Wizard;

use Bugotech\Db\FlowModel;
use Bugotech\Db\Flow\Step;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

/**
 * Class WizardTrait.
 * @see Controller
 */
trait WizardTrait
{
    /**
     * Model flow
     * @var FlowModel
     */
    protected $model;

    /**
     * Nome da classe do model.
     * @var string
     */
    protected $modelName = '';

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
     * Retorna o model.
     * @return FlowModel
     */
    public function model()
    {
        if (! is_null($this->model)) {
            return $this->model;
        }

        return $this->model = app($this->modelName);
    }

    /**
     * Define com step atual e retornal o objeto
     * @param $step
     * @return Step
     */
    protected function step($step)
    {
        if (! $this->model()->steps->exists($step)) {
            error('Step "%s" not found', $step);
        }

        $this->model()->steps->setCurrent($step);

        return $this->model()->steps->current();
    }

    /**
     * Retorna a view do step.
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getSteps()
    {
        // Carregar step do conexto e atual
        $step = router()->current()->parameter('step', $this->model()->steps->firstId());
        $step = $this->step($step);

        // Carregar view
        $view_id = sprintf('%s.%s', $this->prefixViewName, $step->key);
        if (! view()->exists($view_id)) {
            error('View of step "%s" not found', $view_id);
        }

        $view = view($view_id);
        $view->with('steps', $this->model()->steps);
        $view->with('step', $step);
        $view->with($this->viewParams);

        $view->with('step_url', function ($step, $method = 'get') {
            return $this->stepUrl($step, $method);
        });

        return $view;
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function setSteps(Request $request)
    {
        return $this->transaction(function () use ($request) {

            // Carregar step do conexto e atual
            $step = request()->get('step', $this->model()->steps->firstId());
            $step = $this->step($step);

            // Validar inputs
            if (count($step->validates) > 0) {
                $this->validate($step->validates);
            }

            // Exeutar methodo do step
            $method = sprintf('postStep%s', Str::studly($step->key));
            if (! method_exists($this, $method)) {
                error('Step method "%s" not found', $step->key);
            }

            return call_user_func_array([$this, $method], [$request, $step]);
        });
    }

    /**
     * Retorna url do step.
     * @param Step $step
     * @param string $method
     * @return string
     */
    protected function stepUrl(Step $step, $method = 'get')
    {
        $id = sprintf('%s.%s', $this->prefixRoute, $method);

        return route($id, ['step' => $step->key]);
    }

    /**
     * Redireciona para a url do step.
     * @param Step $step
     * @param string $method
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    protected function stepRedirect(Step $step, $method = 'get')
    {
        return redirect($this->stepUrl($step, $method));
    }
}
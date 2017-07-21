<?php namespace Bugotech\Http\Wizard;

use Bugotech\Http\Router;
use Bugotech\Db\FlowModel;
use Bugotech\Db\Flow\Step;
use Illuminate\Support\Arr;
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
     * Define com step atual e retornal o objeto
     * @param string $step
     * @return Step
     */
    protected function flow($step)
    {
        // Carregar id do flow
        $flow = router()->current()->parameter('flow');
        if (is_null($flow)) {
            error('Flow not information');
        }

        // Carregar model
        $this->model = call_user_func_array([$this->modelName, 'find'], [$flow]);
        if (is_null($this->model)) {
            error('Flow "%s" not found', $flow);
        }

        // Carregar step
        $step = is_null($step) ? $this->model->steps->firstId() : $step;
        if (! $this->model->steps->exists($step)) {
            error('Step "%s" not found', $step);
        }

        $this->model->steps->setCurrent($step);

        return $this->model->steps->current();
    }

    /**
     * Cria novo flow.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function createFlow()
    {
        $model = app($this->modelName);
        $model->save();

        $step = $model->steps->firstId();

        return redirect()->route(sprintf('%s.get', $this->prefixRoute), ['flow' => $model->_id, 'step' => $step]);
    }

    /**
     * Retorna a view do step.
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getSteps()
    {
        // Carregar step do conexto e atual
        $step = $this->flow(router()->current()->parameter('step'));

        // Carregar view
        $view_id = sprintf('%s.%s', $this->prefixViewName, $step->key);
        if (! view()->exists($view_id)) {
            error('View of step "%s" not found', $view_id);
        }

        $view = view($view_id);
        $view->with('steps', $this->model->steps);
        $view->with('step', $step);
        $view->with('model', $this->model);
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
            $step = $this->flow(request()->get('step'));

            // Validar inputs
            if (count($step->validates) > 0) {
                $this->validate($step->validates);
            }

            // Guardar informações do nivel
            $data = Arr::except($request->all(), ['step']);
            foreach ($data as $k => $v) {
                $this->model->{$step->key}->{$k} = $v;
            }
            $this->model->{$step->key}->save();

            // Exeutar methodo do step
            $method = sprintf('postStep%s', Str::studly($step->key));
            $return = null;
            if (method_exists($this, $method)) {
                $return = call_user_func_array([$this, $method], [$request, $step, $this->model]);
            }

            $this->model->save();

            // Verificar se tem um return customizavel
            if (! is_null($return)) {
                return $return;
            }

            // Ir para o proximo passo
            $next = $step->next();
            if (! is_null($next)) {
                return $this->stepRedirect($next);
            }

            // Finalizar passo
            $method = 'terminateFlow';
            if (! method_exists($this, $method)) {
                error('Method "%s" not found in flow', $method);
            }

            // Finalizar fluxo
            $return = call_user_func_array([$this, $method], [$this->model]);

            // Excluir fluxo
            $this->model->delete();

            return $return;
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

    /**
     * Register routes of flow.
     *
     * @param Router $router
     * @param $prefixPart
     * @param $routePrefix
     */
    public static function routesRegister(Router $router, $prefixPart, $routePrefix)
    {
        $class = get_called_class();

        // Get - Create flow
        $part = sprintf('%s', $prefixPart);
        $uses = sprintf('\%s@createFlow', $class);
        $as = sprintf('%s.create', $routePrefix);
        $router->get($part, ['uses' => $uses, 'as' => $as]);

        // Get - Step
        $part = sprintf('%s/{flow}/{step}', $prefixPart);
        $uses = sprintf('\%s@getSteps', $class);
        $as = sprintf('%s.get', $routePrefix);
        $router->get($part, ['uses' => $uses, 'as' => $as]);

        // Post - Step
        $part = sprintf('%s/{flow}', $prefixPart);
        $uses = sprintf('\%s@setSteps', $class);
        $as = sprintf('%s.post', $routePrefix);
        $router->post($part, ['uses' => $uses, 'as' => $as]);
    }
}
<?php namespace Vaccuum\Dispatcher\Traits;

use Closure;
use ReflectionFunction;
use Vaccuum\Contracts\Dispatcher\DispatcherException;

trait TClosureInvocation
{
    /**
     * Invoke closure using arguments.
     *
     * @param array   $arguments
     * @param Closure $action
     *
     * @throws DispatcherException
     * @return mixed
     */
    protected function invokeClosure(array $arguments, Closure $action)
    {
        $reflection = new ReflectionFunction($action);
        $parameters = [];

        foreach ($reflection->getParameters() as $parameter)
        {
            if (isset($arguments[$parameter->name]))
            {
                $parameters[] = $arguments[$parameter->name];
            }
            elseif ($parameter->isDefaultValueAvailable())
            {
                $parameters[] = $parameter->getDefaultValue();
            }
            else
            {
                $message = "{$parameter->name} argument cannot be resolved.";
                throw new DispatcherException($message, $arguments, $action);
            }
        }

        return $reflection->invokeArgs($parameters);
    }
}
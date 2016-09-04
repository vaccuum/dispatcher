<?php namespace Vaccuum\Dispatcher\Traits;

use ReflectionMethod;
use Vaccuum\Contracts\Dispatcher\DispatcherException;

trait TMethodInvocation
{
    /**
     * Invoke object method using arguments.
     *
     * @param array  $arguments
     * @param string $action
     * @param object $object
     *
     * @throws DispatcherException
     * @return mixed
     */
    protected function invokeMethod(array $arguments, $action, $object)
    {
        if (!method_exists($object, $action))
        {
            $message = get_class($object) . " object has no {$action} method.";
            throw new DispatcherException($message);
        }

        $reflection = new ReflectionMethod($object, $action);
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
                throw new DispatcherException($message, $arguments, $action, $object);
            }
        }

        return $reflection->invokeArgs($object, $parameters);
    }
}
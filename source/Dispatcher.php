<?php namespace Vaccuum\Dispatcher;

use Closure;
use Vaccuum\Contracts\Container\IContainer;
use Vaccuum\Contracts\Dispatcher\DispatcherException;
use Vaccuum\Contracts\Dispatcher\IDispatcher;
use Vaccuum\Dispatcher\Traits\TClosureInvocation;
use Vaccuum\Dispatcher\Traits\TMethodInvocation;

class Dispatcher implements IDispatcher
{
    use TClosureInvocation;
    use TMethodInvocation;

    /** @var IContainer */
    protected $container;

    /**
     * Dispatcher constructor.
     *
     * @param IContainer $container
     */
    public function __construct(IContainer $container)
    {
        $this->container = $container;
    }

    /** @inheritdoc */
    public function dispatch(array $arguments, $handler)
    {
        if ($handler instanceof Closure)
        {
            return $this->invokeClosure($arguments, $handler);
        }
        elseif (is_array($handler))
        {
            return $this->invokeMethod(
                $arguments,
                $handler[1],
                $this->container->make($handler[0])
            );
        }
        else
        {
            $message = "Action cannot be dispatched.";
            throw new DispatcherException($message);
        }
    }
}
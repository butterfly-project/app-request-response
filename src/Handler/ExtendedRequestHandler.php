<?php

namespace Butterfly\Application\RequestResponse\Handler;

use Butterfly\Application\RequestResponse\Routing\IRouter;
use Butterfly\Component\DI\Compiler\Annotation\ReflectionClass;
use Butterfly\Component\DI\Container;
use Butterfly\Component\Form\IConstraint;
use Butterfly\Interfaces\TemplateRender\IRender;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Marat Fakhertdinov <marat.fakhertdinov@gmail.com>
 */
class ExtendedRequestHandler implements IRequestHandler
{
    /**
     * @var Container
     */
    protected $container;

    /**
     * @var IRouter
     */
    protected $router;

    /**
     * @var IRender
     */
    protected $render;

    /**
     * @var array
     */
    protected $annotations;

    /**
     * @param Container $container
     * @param IRouter $router
     * @param IRender $render
     * @param array $annotations
     */
    public function __construct(Container $container, IRouter $router, IRender $render, array $annotations)
    {
        $this->container   = $container;
        $this->router      = $router;
        $this->render      = $render;
        $this->annotations = $annotations;
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function handle(Request $request)
    {
        list($actionCode, $parameters) = $this->router->getAction($request);
        list($actionServiceName, $method) = explode(':', $actionCode);

        $actionService = $this->container->get($actionServiceName);
        $actionMethod  = $method . 'Action';

        $reflectionClass = new ReflectionClass($actionService);

        try {
            $arguments = $this->resolveArguments($reflectionClass, $actionMethod, $request, $actionCode);
        } catch (\RuntimeException $e) {
            if ($e->getCode() != 1000) {
                throw $e;
            }

            $actionCode = $this->container->getParameter('bfy_app.routing.action_name_of_404');
            list($actionServiceName, $method) = explode(':', $actionCode);

            $actionService = $this->container->get($actionServiceName);
            $actionMethod  = $method . 'Action';
            $arguments     = array($request);
        }

        $response = call_user_func_array(array($actionService, $actionMethod), $arguments);

        if (is_array($response)) {
            $templateName = $this->getControllerTemplatePrefix($reflectionClass->getName()) . $method . '.html.twig';
            $response     = new Response($this->render->render($templateName, $response));
        }

        return $response;
    }

    /**
     * @param ReflectionClass $reflectionClass
     * @param string $actionMethod
     * @param Request $request
     * @param string $actionCode
     * @return array
     */
    protected function resolveArguments(ReflectionClass $reflectionClass, $actionMethod, Request $request, $actionCode)
    {
        if (!$reflectionClass->hasMethod($actionMethod)) {
            throw new \RuntimeException(sprintf('Method %s in controller %s is not found', $actionMethod, $reflectionClass->getName()));
        }

        $reflectionMethod     = $reflectionClass->getMethod($actionMethod);
        $reflectionParameters = $reflectionMethod->getParameters();

        $paramsConstraints = $this->getParamsConstraints($reflectionClass->getName(), $actionMethod);
        $importantParams   = $this->getImportantParams($reflectionClass->getName(), $actionMethod);

        $arguments = array();

        foreach ($reflectionParameters as $reflectionParameter) {
            $name = $reflectionParameter->getName();
            if ('request' == $name) {
                $arguments[] = $request;
                continue;
            }

            $value = $this->getRequestValue($request, $name);

            if (null === $value && $reflectionParameter->isDefaultValueAvailable()) {
                $value = $reflectionParameter->getDefaultValue();
            }

            if (!empty($paramsConstraints[$name])) {
                $constraint = $paramsConstraints[$name];
                $constraint->filter($value);

                if (!$constraint->isValid() && in_array($name, $importantParams)) {
                    throw new \RuntimeException($constraint->getFirstErrorMessage(), 1000);
                }

                $value = $constraint;
            } elseif (null === $value) {
                throw new \RuntimeException(sprintf('Parameter %s for route %s is not found', $name, $actionCode), 1000);
            }

            $arguments[] = $value;
        }

        return $arguments;
    }

    /**
     * @param string $className
     * @param string $methodName
     * @return IConstraint[]
     */
    protected function getParamsConstraints($className, $methodName)
    {
        if (
            empty($this->annotations[$className]) ||
            empty($this->annotations[$className]['methods'][$methodName]) ||
            empty($this->annotations[$className]['methods'][$methodName]['param'])
        ) {
            return array();
        }

        $params = (array)$this->annotations[$className]['methods'][$methodName]['param'];

        $constraints = array();

        foreach ($params as $param) {
            $paramWords = array_values(array_filter(explode(' ', $param)));

            if (3 > count($paramWords)) {
                continue;
            }

            list ($type, $variable, $constraint) = $paramWords;
            if (!$this->container->has($constraint)) {
                continue;
            }

            $argumentName = ltrim($variable, '$');

            $constraints[$argumentName] = $this->container->get($constraint);
        }

        return $constraints;
    }

    /**
     * @param string $className
     * @param string $methodName
     * @return IConstraint[]
     */
    protected function getImportantParams($className, $methodName)
    {
        if (
            empty($this->annotations[$className]) ||
            empty($this->annotations[$className]['methods'][$methodName]) ||
            empty($this->annotations[$className]['methods'][$methodName]['action\importantParams'])
        ) {
            return array();
        }

        return (array)$this->annotations[$className]['methods'][$methodName]['action\importantParams'];
    }

    /**
     * @param Request $request
     * @param string $key
     * @return mixed|null
     */
    protected function getRequestValue(Request $request, $key)
    {
        if ($this !== $result = $request->attributes->get($key, $this)) {
            return $result;
        }

        if ($this !== $result = $request->query->get($key, $this)) {
            return $result;
        }

        if ($this !== $result = $request->request->get($key, $this)) {
            return $result;
        }

        if ($this !== $result = $request->files->get($key, $this)) {
            return $result;
        }

        return null;
    }

    /**
     * @param string $className
     * @return string
     */
    protected function getControllerTemplatePrefix($className)
    {
        if (
            empty($this->annotations[$className]) ||
            empty($this->annotations[$className]['class']['template\basePath'])
        ) {
            return '';
        }

        return $this->annotations[$className]['class']['template\basePath'];
    }
}

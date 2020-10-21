<?php


namespace App;

use ReflectionClass;
use ReflectionException;
use ReflectionFunction;
use ReflectionParameter;

class Route
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $path;

    /**
     * @var callable|array $callable
     */
    private $callable;

    private $routeParameters;

    public function __construct(
        string $name,
        string $path,
        $callable
    ) {
        $this->name = $name;
        $this->path = $path;
        $this->callable = $callable;
        $this->routeParameters = [];
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $path
     * @return false|int
     */
    public function test(string $path)
    {
        // transform path into regex
        $pattern = str_replace("/", "\/", $this->path);
        $pattern = sprintf('/^%s$/', $pattern);
        $pattern = preg_replace('/({\w+})/', '(.+)', $pattern);

        // test if pattern match with given path
        $result = preg_match($pattern, $path, $matchesValue);

        // value route parameters
        array_shift($matchesValue);
        // key route parameters
        preg_match_all('/{(\w+)}/', $this->path, $matchesKey);

        $this->routeParameters = array_combine($matchesKey[1], $matchesValue);

        return $result;
    }

    /**
     * @return mixed
     * @throws ReflectionException
     */
    public function call()
    {
        $parameters = [];
        if (count ($this->routeParameters) > 0) {

            // Make reflectionClass and get method if callable is array
            // Else make reflectionFunction
            if (is_array($this->callable)) {
                $reflection = (new ReflectionClass($this->callable[0]))->getMethod($this->callable[1]);
            } else {
                $reflection = new ReflectionFunction($this->callable);
            }

            $orderKeyParameters = array_map(function (ReflectionParameter $parameter) {
                return $parameter->getName();
            }, $reflection->getParameters());

            $parameters = $this->sortArrayByArray($this->routeParameters, $orderKeyParameters);
        }

        $callable = $this->callable;
        if (is_array($callable)) {
            $callable = [new $callable[0](), $callable[1]];
        }

        return call_user_func_array($callable, $parameters);
    }

    /**
     * @param array $array
     * @param array $orderKeyArray
     * @return string[]
     */
    private function sortArrayByArray(array $array, array $orderKeyArray): array
    {
        $ordered = [];
        foreach ($orderKeyArray as $key) {
            if (array_key_exists($key, $array)) {
                $ordered[] = $array[$key];
            }
        }
        return $ordered;
    }
}

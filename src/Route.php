<?php


namespace App;


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
     * @var callable $callable
     */
    private $callable;

    private $parameters;

    public function __construct(
        string $name,
        string $path,
        $callable
    ) {
        $this->name = $name;
        $this->path = $path;
        $this->callable = $callable;
        $this->parameters = [];
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPath(): string
    {
        return $this->path;
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

        $this->parameters = array_combine($matchesKey[1], $matchesValue);

        return $result;
    }

    public function call()
    {
        if (count ($this->parameters) > 0) {

            $reflection = new ReflectionFunction($this->callable);
            $orderKeyParameters = array_map(function (ReflectionParameter $parameter) {
                return $parameter->getName();
            }, $reflection->getParameters());

            $parametersOrdered = $this->sortArrayByArray($this->parameters, $orderKeyParameters);
            return call_user_func_array($this->callable, $parametersOrdered);
        }

        return call_user_func_array($this->callable, []);
    }

    private function sortArrayByArray(array $array, array $orderKeyArray): array
    {
        $ordered = [];
        foreach ($orderKeyArray as $key) {
            if (array_key_exists($key, $array)) {
                $ordered[$key] = $array[$key];
            }
        }
        return $ordered;
    }
}

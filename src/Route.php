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

    /**
     * @var array
     */
    private $routeParameters;

    /**
     * @var array
     */
    private $defaults;

    /**
     * Route constructor.
     * @param string $name
     * @param string $path
     * @param callable|array $callable
     */
    public function __construct(
        string $name,
        string $path,
        $callable
    ) {
        $this->name = $name;
        $this->path = $path;
        $this->callable = $callable;
        $this->routeParameters = [];
        $this->defaults = [];
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function addDefaults(array $defaults): self
    {
        $this->defaults = $defaults;

        return $this;
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

        $pattern = preg_replace_callback('/({\w+})/', function ($match) {
            $key = substr($match[0], 1, -1);
            return (array_key_exists($key, $this->defaults)) ? '?(.+)?' : '(.+)';
        }, $pattern);

        $result = preg_match($pattern, $path, $matchesValue);

        // value route parameters
        array_shift($matchesValue);

        // key route parameters
        preg_match_all('/{(\w+)}/', $this->path, $matchesKey);

        // Defaults parameters
        if ($this->hasDefaults($matchesKey[1], $matchesValue)) {
            $this->resolveDefaults($matchesKey[1], $matchesValue);
        } else {
            $this->routeParameters = array_combine($matchesKey[1], $matchesValue);
        }

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

    /**
     * @param array $matchesKey
     * @param array $matchesValue
     * @return bool
     */
    private function hasDefaults(array $matchesKey, array $matchesValue): bool
    {
        return count($matchesKey) !== count($matchesValue);
    }

    /**
     * @param array $matchesKey
     * @param array $matchesValue
     */
    private function resolveDefaults(array $matchesKey, array $matchesValue): void
    {
        foreach ($matchesKey as $key) {
            if (array_key_exists($key, $this->defaults)) {
                $matchesValue[] = $this->defaults[$key];
            }
        }
        $this->routeParameters = array_combine($matchesKey, $matchesValue);
    }
}

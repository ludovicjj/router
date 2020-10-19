<?php


namespace App;


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

    public function __construct(
        string $name,
        string $path,
        $callable
    ) {
        $this->name = $name;
        $this->path = $path;
        $this->callable = $callable;
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

        // test if regex math with given path
        return preg_match($pattern, $path);
    }
}

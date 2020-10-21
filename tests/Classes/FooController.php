<?php


namespace App\Test\Classes;


class FooController
{
    public function index(string $id, string $bar): string
    {
        return $bar . ' : ' . $id;
    }
}
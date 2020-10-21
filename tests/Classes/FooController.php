<?php


namespace App\Test\Classes;


class FooController
{
    public function index(string $id, string $message): string
    {
        return $message . ' : ' . $id;
    }
}
<?php


namespace App\Test\Classes;


class BarController
{
    public function index(string $message, string $id): string
    {
        return $message . ' : ' .$id;
    }
}
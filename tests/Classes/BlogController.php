<?php


namespace App\Test\Classes;


class BlogController
{
    public function index($page)
    {
        return 'Current page is : ' . $page;
    }
}
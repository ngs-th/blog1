<?php

it('loads the homepage successfully', function () {
    $page = visit('http://localhost:8000');
    
    $page->assertSee('Laravel');
});

it('can see the Laravel starter kit text', function () {
    $page = visit('http://localhost:8000');
    
    $page->assertSee('Laravel Starter Kit');
});

it('works on mobile viewport', function () {
    $page = visit('http://localhost:8000')->on()->mobile();
    
    $page->assertSee('Laravel');
});
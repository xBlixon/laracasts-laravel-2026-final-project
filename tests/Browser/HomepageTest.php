<?php

declare(strict_types=1);

it('may welcome the user', function () {
    $page = visit('/');

    $page->assertSee('Home');
});

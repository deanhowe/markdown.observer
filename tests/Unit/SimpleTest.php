<?php

test('string contains substring', function () {
    $string = 'Hello, World!';
    expect($string)->toContain('World');
});

test('array has specific item', function () {
    $array = ['apple', 'banana', 'orange'];
    expect($array)->toContain('banana');
});

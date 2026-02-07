<?php

test('true is true', function () {
    expect(true)->toBeTrue();
});

test('1 + 1 equals 2', function () {
    expect(1 + 1)->toBe(2);
});

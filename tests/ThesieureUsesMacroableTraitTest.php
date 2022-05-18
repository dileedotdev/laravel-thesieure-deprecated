<?php

use Dinhdjj\Thesieure\Facades\Thesieure;
use Illuminate\Support\Traits\Macroable;

it('uses Macroable trait', function (): void {
    expect(class_uses_recursive(resolve('thesieure')))->toContain(Macroable::class);
});

it('test example of macroable trait', function (): void {
    Thesieure::macro('testMethod', fn () => $this);
    expect(Thesieure::testMethod())->toBe(resolve('thesieure'));
});

it('test example of macroable trait 2', function (): void {
    Thesieure::macro('testMethod', fn ($number) => $number);
    expect(Thesieure::testMethod(10009))->toBe(10009);
});

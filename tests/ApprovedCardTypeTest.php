<?php

beforeEach(function (): void {
    $this->card = createApprovedCard('success');
});

test('its isApproving is working', function (): void {
    $this->card->status = 99;

    expect($this->card->isApproving())->toBeTrue();
});

test('its isError is working', function ($status): void {
    $this->card->status = $status;

    expect($this->card->isError())->toBeTrue();
})->with([3, 4, 100]);

test('its isSuccess is working', function ($status): void {
    $this->card->status = $status;

    expect($this->card->isSuccess())->toBeTrue();
})->with([1, 2]);

test('its getReceivedValue is working', function (): void {
    $this->card->amount = 100;
    expect($this->card->getReceivedValue())->toBe(100);

    $this->card->status = 3;
    expect($this->card->getReceivedValue())->toBe(0);
});

test('its getRealFaceValue is working', function (): void {
    $this->card->value = 100;
    expect($this->card->getRealFaceValue())->toBe(100);

    $this->card->status = 3;
    expect($this->card->getRealFaceValue())->toBe(0);
});

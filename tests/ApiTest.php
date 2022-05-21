<?php

use Dinhdjj\Thesieure\Facades\Thesieure;
use Dinhdjj\Thesieure\Types\ApprovedCard;
use function Pest\Laravel\post;
use function Pest\Laravel\postJson;

beforeEach(function (): void {
    $card = createApprovedCard();
    $this->data = [
        'callback_sign' => Thesieure::generateSign($card->serial, $card->code),
        'request_id' => 'sdfjsdjkfdjhsk',
        'telco' => $card->telco,
        'declared_value' => $card->declared_value,
        'serial' => $card->serial,
        'code' => $card->code,
        'status' => $card->status,
    ];
});

it('required fields', function ($field): void {
    unset($this->data[$field]);
    post(config('thesieure.callback.route.uri'), $this->data)
        ->assertSessionHasErrors($field)
    ;
})->with(['callback_sign', 'request_id', 'telco', 'declared_value', 'serial', 'code', 'status']);

it('throws if callback_sign is invalid', function (): void {
    $this->data['callback_sign'] = 'invalid';
    postJson(config('thesieure.callback.route.uri'), $this->data)
        ->assertStatus(401)
    ;
});

it('will handle callbacks if everything okay', function (): void {
    /** @var ?ApprovedCard */
    $card = null;
    Thesieure::onCallback(function (ApprovedCard $approvedCard) use (&$card): void {
        $card = $approvedCard;
    });

    postJson(config('thesieure.callback.route.uri'), $this->data);

    expect($card->telco)->toBe($this->data['telco']);
    expect($card->declared_value)->toBe($this->data['declared_value']);
    expect($card->serial)->toBe($this->data['serial']);
    expect($card->code)->toBe($this->data['code']);
    expect($card->request_id)->toBe($this->data['request_id']);
});

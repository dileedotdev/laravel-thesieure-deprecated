<?php

use Dinhdjj\Thesieure\Exceptions\InvalidThesieureResponseException;
use Dinhdjj\Thesieure\Facades\Thesieure;
use Dinhdjj\Thesieure\Types\ApprovedCard;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

test('its forceFetchCardTypes method is working', function (): void {
    Http::fake([
        'thesieure.com/chargingws/v2/getfee*' => function (Request $request) {
            expect($request->url())->toStartWith('https://');
            expect($request->data()['partner_id'])->toBe(config('thesieure.partner_id'));

            return Http::response([
                [
                    'telco' => 'VNMOBI',
                    'value' => 10000,
                    'fees' => 17,
                    'penalty' => 50,
                ],
                [
                    'telco' => 'VIETTEL',
                    'value' => 10000,
                    'fees' => 19,
                    'penalty' => 50,
                ],
            ]);
        },
    ]);

    $cardTypes = Thesieure::forceFetchCardTypes();

    expect($cardTypes)->toHaveCount(2);
    expect($cardTypes[0]->telco)->toBe('VNMOBI');
    expect($cardTypes[0]->value)->toBe(10000);
    expect($cardTypes[0]->fees)->toBe(17);
    expect($cardTypes[0]->penalty)->toBe(50);
});

test('its fetchCardTypes method should cache', function (): void {
    Cache::shouldReceive('store')->once()->with(config('thesieure.fetch_card_types.cache.store'))->andReturnSelf();
    Cache::shouldReceive('remember')->withSomeOfArgs(config('thesieure.fetch_card_types.cache.key'), config('thesieure.fetch_card_types.cache.ttl'))->once()->andReturn([]);

    Thesieure::fetchCardTypes();
});

test('its fetchCardTypes method should not cache cache', function (): void {
    Http::fake([
        'thesieure.com/chargingws/v2/getfee*' => Http::response([]),
    ]);
    config(['thesieure.fetch_card_types.cache.enabled' => false]);
    Cache::shouldReceive('store')->never()->with(config('thesieure.fetch_card_types.cache.store'))->andReturnSelf();
    Cache::shouldReceive('remember')->withSomeOfArgs(config('thesieure.fetch_card_types.cache.key'), config('thesieure.fetch_card_types.cache.ttl'))->never()->andReturn([]);

    Thesieure::fetchCardTypes();
});

test('its forceFetchCardTypes method throws InvalidThesieureResponseException', function (): void {
    Http::fake([
        'thesieure.com/chargingws/v2/getfee*' => Http::response([], 300),
    ]);
    Thesieure::forceFetchCardTypes();
})->throws(InvalidThesieureResponseException::class);

test('its approveCard method is working', function (): void {
    /** @var ApprovedCard */
    $callbackCard = null;
    Thesieure::onCallback(function (ApprovedCard $card) use (&$callbackCard): void {
        $callbackCard = $card;
    });

    $card = createApprovedCard();
    Http::fake([
        'thesieure.com/chargingws/v2' => function (Request $request) use ($card) {
            expect($request->url())->toStartWith('https://');

            $data = [
                ...$request->data(),
                'status' => 99,
            ];

            expect($data['command'])->toBe('charging');
            expect($data['telco'])->toBe($card->telco);
            expect($data['amount'])->toBe($card->declared_value);
            expect($data['serial'])->toBe($card->serial);
            expect($data['code'])->toBe($card->code);
            expect($data['status'])->toBe($card->status);
            expect($data['request_id'])->toBe($card->request_id);

            expect($data['partner_id'])->toBe(config('thesieure.partner_id'));
            expect($data['sign'])->toBe(Thesieure::generateSign($card->serial, $card->code));

            return Http::response($data);
        },
    ]);

    $approvedCard = Thesieure::approveCard($card->telco, $card->declared_value, $card->serial, $card->code, $card->request_id);

    expect($callbackCard)->toBe($approvedCard);
    expect($approvedCard->telco)->toBe($card->telco);
    expect($approvedCard->declared_value)->toBe($card->declared_value);
    expect($approvedCard->serial)->toBe($card->serial);
    expect($approvedCard->code)->toBe($card->code);
    expect($approvedCard->status)->toBe($card->status);
    expect($approvedCard->request_id)->toBe($card->request_id);
});

test('its approveCard method throws InvalidThesieureResponseException', function (): void {
    $card = createApprovedCard();
    Http::fake([
        'thesieure.com/chargingws/v2' => Http::response(get_object_vars($card), 300),
    ]);
    Thesieure::approveCard($card->telco, $card->declared_value, $card->serial, $card->code, $card->request_id);
})->throws(InvalidThesieureResponseException::class);

test('its updateApprovedCard method is working', function (): void {
    /** @var ApprovedCard */
    $callbackCard = null;
    Thesieure::onCallback(function (ApprovedCard $card) use (&$callbackCard): void {
        $callbackCard = $card;
    });

    $card = createApprovedCard();
    Http::fake([
        'thesieure.com/chargingws/v2' => function (Request $request) use ($card) {
            expect($request->url())->toStartWith('https://');

            $data = [
                ...$request->data(),
                'status' => 99,
            ];

            expect($data['command'])->toBe('check');
            expect($data['telco'])->toBe($card->telco);
            expect($data['amount'])->toBe($card->declared_value);
            expect($data['serial'])->toBe($card->serial);
            expect($data['code'])->toBe($card->code);
            expect($data['status'])->toBe($card->status);
            expect($data['request_id'])->toBe($card->request_id);

            expect($data['partner_id'])->toBe(config('thesieure.partner_id'));
            expect($data['sign'])->toBe(Thesieure::generateSign($card->serial, $card->code));

            return Http::response($data);
        },
    ]);

    $approvedCard = Thesieure::updateApprovedCard($card->telco, $card->declared_value, $card->serial, $card->code, $card->request_id);

    expect($callbackCard)->toBe($approvedCard);
    expect($approvedCard->telco)->toBe($card->telco);
    expect($approvedCard->declared_value)->toBe($card->declared_value);
    expect($approvedCard->serial)->toBe($card->serial);
    expect($approvedCard->code)->toBe($card->code);
    expect($approvedCard->status)->toBe($card->status);
    expect($approvedCard->request_id)->toBe($card->request_id);
});

test('its on callback closure mechanism', function (): void {
    $approvedCard = null;
    Thesieure::onCallback(function (ApprovedCard $card) use (&$approvedCard): void {
        $approvedCard = $card;
    });

    $card = createApprovedCard();
    Thesieure::handleCallback($card);

    expect($approvedCard)->toBe($card);
});

test('its checkSign method is working', function (): void {
    $card = createApprovedCard();
    $result = Thesieure::checkSign(md5(config('thesieure.partner_key').$card->code.$card->serial), $card->serial, $card->code);

    expect($result)->toBe(true);
});

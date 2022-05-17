<?php

use Dinhdjj\Thesieure\Exceptions\InvalidThesieureConfigException;

test('its thesieure singleton throws InvalidThesieureConfigException if config is valid', function ($config): void {
    config(['thesieure.'.$config => null]);
    resolve('thesieure');
})
    ->with(['domain', 'partner_id', 'partner_key'])
    ->throws(InvalidThesieureConfigException::class)
;

<?php

use Dinhdjj\Thesieure\Exceptions\InvalidThesieureConfigException;

test('its getConfig method throws InvalidThesieureConfigException if config is valid', function ($config): void {
    config(['thesieure.'.$config => null]);
    resolve('thesieure')->getConfig('domain');
})
    ->with(['domain', 'partner_id', 'partner_key'])
    ->throws(InvalidThesieureConfigException::class)
;

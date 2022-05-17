<?php

use Dinhdjj\Thesieure\Tests\TestCase;
use Dinhdjj\Thesieure\Types\ApprovedCard;

uses(TestCase::class)->in(__DIR__);

/**
 * @param string $status approving|success|error|failed
 */
function createApprovedCard(string $status = 'approving'): ApprovedCard
{
    $data = [
        'telco' => ['VNMOBI', 'VIETTEL'][random_int(0, 1)],
        'declared_value' => random_int(10000, 100000),
        'request_id' => 'request_id',
        'serial' => 'serial',
        'code' => 'code',
        'message' => null,
        'amount' => null,
        'value' => null,
        'status' => 99,
    ];

    if ('approving' === $status) {
        return new ApprovedCard(
            ...[
                ...$data,
                'status' => 99,
            ]
        );
    }

    if ('success' === $status) {
        return new ApprovedCard(
            ...[
                ...$data,
                'status' => 1,
                'value' => $data['declared_value'],
                'amount' => random_int(1, 100),
            ]
        );
    }

    if ('error' === $status) {
        return new ApprovedCard(
            ...[
                ...$data,
                'status' => 100,
                'message' => 'validation error',
            ]
        );
    }

    if ('failed' === $status) {
        return new ApprovedCard(
            ...[
                ...$data,
                'status' => 3,
                'value' => 0,
                'amount' => 0,
            ]
        );
    }
}

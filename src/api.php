<?php

use Dinhdjj\Thesieure\Facades\Thesieure;
use Dinhdjj\Thesieure\Types\ApprovedCard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::{config('thesieure.routes.callback.method', 'post')}(config('thesieure.routes.callback.uri', 'api/thesieure/callback'), function (Request $request) {
    $request->validate([
        'callback_sign' => ['required', 'string'],
        'request_id' => ['required', 'string'],
        'telco' => ['required', 'string'],
        'declared_value' => ['required', 'integer'],
        'serial' => ['required', 'string'],
        'code' => ['required', 'string'],
        'status' => ['required', 'integer'],
        'value' => ['nullable', 'integer'],
        'amount' => ['nullable', 'integer'],
        'message' => ['nullable', 'string'],
    ]);

    if (!Thesieure::checkSign($request->callback_sign, $request->serial, $request->code)) {
        abort(401, 'Unauthenticated.');
    }

    $card = new ApprovedCard(
        telco: $request->telco,
        value: $request->value,
        declared_value: $request->declared_value,
        serial: $request->serial,
        code: $request->code,
        request_id: $request->request_id,
        amount: $request->amount,
        message: $request->message,
        status: $request->status,
    );

    Thesieure::handleCallback($card);

    return response()->json(status: 200);
})
    ->middleware(config('thesieure.routes.callback.middleware', []))
    ->name(config('thesieure.routes.callback.name', 'api.thesieure.callback'))
;

<?php

namespace Dinhdjj\Thesieure\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static mixed getConfig(string $name)                                                                                                        Get thesieure config.
 * @method static void onCallback(Closure $closure)                                                                                                    Register a closure will be invoke when thesieure callback to server.
 * @method static void handleCallback(\Dinhdjj\Thesieure\Types\ApprovedCard $card)                                                                     Invoke all closures registered by onCallback.
 * @method static \Dinhdjj\Thesieure\Types\FetchedCardType[] fetchCardTypes()                                                                          Get card types from thesieure.
 * @method static \Dinhdjj\Thesieure\Types\ApprovedCard approveCard(string $telco, int $value, string $serial, string $code, string $requestId)        Send card to thesieure for approving.
 * @method static \Dinhdjj\Thesieure\Types\ApprovedCard updateApprovedCard(string $telco, int $value, string $serial, string $code, string $requestId) Resend card to thesieure for checking.
 * @method static bool checkSign(string $sign, string $serial, string $code)
 * @method static string generateSign(string $serial, string $code)                                                                                    Generate sign used when communicate with service server                                                                Check whe request sign from thesieure is valid.
 *
 * @see \Dinhdjj\Thesieure\Thesieure
 */
class Thesieure extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'thesieure';
    }
}

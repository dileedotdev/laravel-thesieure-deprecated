<?php

namespace Dinhdjj\Thesieure;

use Closure;
use Dinhdjj\Thesieure\Exceptions\InvalidThesieureConfigException;
use Dinhdjj\Thesieure\Exceptions\InvalidThesieureResponseException;
use Dinhdjj\Thesieure\Types\ApprovedCard;
use Dinhdjj\Thesieure\Types\FetchedCardType;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Traits\Macroable;

class Thesieure
{
    use Macroable;

    protected array $onCallbackClosures = [];

    public function __construct(
    ) {
    }

    public function getConfig(string $name): mixed
    {
        if (!config('thesieure.domain') || !config('thesieure.partner_id') || !config('thesieure.partner_key')) {
            throw new InvalidThesieureConfigException();
        }

        return config('thesieure.'.$name);
    }

    /**
     * Register a callback to be invoked when thesieure callback to server.
     */
    public function onCallback(Closure $closure): void
    {
        $this->onCallbackClosures[] = $closure;
    }

    /**
     * Call all closures registered by onCallback.
     */
    public function handleCallback(ApprovedCard $card): void
    {
        foreach ($this->onCallbackClosures as $closure) {
            $closure($card);
        }
    }

    /**
     * Fetch card types from thesieure.
     *
     * @return \Dinhdjj\Thesieure\Types\FetchedCardType[]
     */
    public function fetchCardTypes(): array
    {
        $response = Http::get('https://'.$this->getConfig('domain').'/chargingws/v2/getfee?partner_id='.$this->getConfig('partner_id'));

        $status = $response->status();
        if ($status >= 300) {
            throw new InvalidThesieureResponseException("Received status [{$status}] from thesieure server, when fetching card types.");
        }

        return array_map(fn ($cardType) => new FetchedCardType(
            $cardType['telco'],
            (int) $cardType['value'],
            (int) $cardType['fees'],
            (int) $cardType['penalty'],
        ), $response->json());
    }

    /**
     * Send card to thesieure for approving.
     *
     * @throws \Dinhdjj\Thesieure\Exceptions\InvalidThesieureResponseException
     */
    public function approveCard(string $telco, int $value, string $serial, string $code, string $requestId): ApprovedCard
    {
        $response = Http::post('https://'.$this->getConfig('domain').'/chargingws/v2', [
            'telco' => $telco,
            'amount' => $value,
            'serial' => $serial,
            'code' => $code,
            'request_id' => $requestId,
            'partner_id' => $this->getConfig('partner_id'),
            'sign' => $this->generateSign($serial, $code),
            'command' => 'charging',
        ])
        ;

        $card = $this->turnResponseIntoApprovedCard($response, $telco, $value, $serial, $code, $requestId);
        $this->handleCallback($card);

        return $card;
    }

    /**
     * Send card to thesieure to update latest status card.
     *
     * @throws \Dinhdjj\Thesieure\Exceptions\InvalidThesieureResponseException
     */
    public function updateApprovedCard(string $telco, int $value, string $serial, string $code, string $requestId): ApprovedCard
    {
        $response = Http::post('https://'.$this->getConfig('domain').'/chargingws/v2', [
            'telco' => $telco,
            'amount' => $value,
            'serial' => $serial,
            'code' => $code,
            'request_id' => $requestId,
            'partner_id' => $this->getConfig('partner_id'),
            'sign' => $this->generateSign($serial, $code),
            'command' => 'check',
        ]);

        $card = $this->turnResponseIntoApprovedCard($response, $telco, $value, $serial, $code, $requestId);
        $this->handleCallback($card);

        return $card;
    }

    /** Generate sign used when communicate with service server */
    public function generateSign(string $serial, string $code): string
    {
        return md5($this->getConfig('partner_key').$code.$serial);
    }

    /** Check sign whether is valid */
    public function checkSign(string $sign, string $serial, string $code): bool
    {
        return $sign === $this->generateSign($serial, $code);
    }

    /**
     * @throws \Dinhdjj\Thesieure\Exceptions\InvalidThesieureResponseException
     * @infection-ignore-all
     */
    protected function turnResponseIntoApprovedCard(Response $response, string $telco, int $value, string $serial, string $code, string $requestId): ApprovedCard
    {
        $status = $response->status();
        if ($status >= 300) {
            throw new InvalidThesieureResponseException("Received status [{$status}] from thesieure server, when checking card.");
        }

        $data = $response->json();

        return new ApprovedCard(
            telco: $data['telco'] ?? $telco,
            value: $data['value'] ?? null,
            declared_value: $data['declared_value'] ?? $value,
            serial: $data['serial'] ?? $serial,
            code: $data['code'] ?? $code,
            request_id: $data['request_id'] ?? $requestId,
            amount: $data['amount'] ?? 0,
            message: $data['message'] ?? null,
            status: $data['status'],
        );
    }
}

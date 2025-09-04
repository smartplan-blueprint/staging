<?php

namespace App\Service;



use http\Client;

class SmsService
{
    private $twilio;
    private $fromNumber;

    public function __construct(string $accountSid, string $authToken, string $fromNumber)
    {
        $this->twilio = new Client($accountSid, $authToken);
        $this->fromNumber = $fromNumber;
    }

    public function sendSMS(string $to, string $message): bool
    {
        try {
            // Format phone number
            $formattedTo = $this->formatPhoneNumber($to);

            $this->twilio->messages->create(
                $formattedTo,
                [
                    'from' => $this->fromNumber,
                    'body' => $message
                ]
            );

            return true;
        } catch (\Exception $e) {
            error_log('SMS sending failed: ' . $e->getMessage());
            return false;
        }
    }

    private function formatPhoneNumber(string $phoneNumber): string
    {
        // Remove any non-digit characters
        $cleanNumber = preg_replace('/[^0-9]/', '', $phoneNumber);

        // Add country code if missing (assuming Botswana +267)
        if (!str_starts_with($cleanNumber, '267') && strlen($cleanNumber) === 8) {
            $cleanNumber = '267' . $cleanNumber;
        }

        return '+' . $cleanNumber;
    }
}

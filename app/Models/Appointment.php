<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Appointment extends Model
{
    protected $fillable = [
        'client_name',
        'client_phone',
        'service_id',
        'start_time',
        'end_time',
    ];

    protected function casts(): array
    {
        return [
            'start_time' => 'datetime',
            'end_time' => 'datetime',
        ];
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * Link do WhatsApp com a mensagem de lembrete já montada.
     */
    public function whatsappReminderUrl(): string
    {
        $digits = preg_replace('/\D/', '', $this->client_phone);

        // Sem DDI (10 ou 11 dígitos): assume Brasil
        if (strlen($digits) <= 11) {
            $digits = '55' . $digits;
        }

        $message = sprintf(
            'Olá, %s! Passando para lembrar do seu horário de %s no dia %s às %s. Até lá!',
            $this->client_name,
            $this->service->name,
            $this->start_time->format('d/m/Y'),
            $this->start_time->format('H:i'),
        );

        return 'https://wa.me/' . $digits . '?text=' . rawurlencode($message);
    }
}

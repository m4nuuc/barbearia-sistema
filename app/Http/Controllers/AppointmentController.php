<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Service;
use App\Support\Availability;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AppointmentController extends Controller
{
    public function index(Request $request)
    {
        $date = $this->parseDate($request->query('date'));

        $services = Service::withCount('appointments')->orderBy('name')->get();

        $isAdmin = (bool) session('is_admin', false);

        // Agenda do dia (só o barbeiro vê: contém nome e telefone dos clientes)
        $agenda = $isAdmin
            ? Appointment::with('service')
                ->whereDate('start_time', $date->toDateString())
                ->orderBy('start_time')
                ->get()
            : collect();

        // Relatório do dia (soma dos preços via relacionamento Eloquent)
        $revenue = (float) $agenda->sum(fn ($a) => $a->service->price);
        $count = $agenda->count();
        $average = $count > 0 ? $revenue / $count : 0;

        // Ocupação futura enviada ao JS para listar só horários livres
        $busy = Appointment::where('end_time', '>', now()->startOfDay())
            ->get(['id', 'start_time', 'end_time'])
            ->groupBy(fn ($a) => $a->start_time->toDateString())
            ->map(fn ($group) => $group->map(fn ($a) => [
                $a->start_time->format('H:i'),
                $a->end_time->format('H:i'),
                $a->id,
            ])->values());

        return view('welcome', compact('services', 'agenda', 'date', 'revenue', 'count', 'average', 'busy', 'isAdmin'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'client_name' => 'required|string|max:100',
            'client_phone' => 'required|string|max:20',
            'service_id' => 'required|exists:services,id',
            'appointment_date' => 'required|date_format:Y-m-d',
            'appointment_time' => 'required|date_format:H:i',
        ], $this->messages());

        $start = DB::transaction(function () use ($data) {
            [$start, $end] = $this->resolveSlot(
                (int) $data['service_id'],
                $data['appointment_date'],
                $data['appointment_time'],
            );

            Appointment::create([
                'client_name' => $data['client_name'],
                'client_phone' => $data['client_phone'],
                'service_id' => $data['service_id'],
                'start_time' => $start,
                'end_time' => $end,
            ]);

            return $start;
        });

        return redirect()
            ->route('appointments.index', ['date' => $start->toDateString()])
            ->with('success', 'Agendamento confirmado para ' . $start->format('d/m/Y \à\s H:i') . '!');
    }

    // Remarcação: muda só data e horário, mantendo cliente e serviço
    public function update(Request $request, Appointment $appointment)
    {
        $data = $request->validate([
            'appointment_date' => 'required|date_format:Y-m-d',
            'appointment_time' => 'required|date_format:H:i',
        ], $this->messages());

        $start = DB::transaction(function () use ($data, $appointment) {
            [$start, $end] = $this->resolveSlot(
                $appointment->service_id,
                $data['appointment_date'],
                $data['appointment_time'],
                $appointment->id, // ignora o próprio agendamento na checagem de conflito
            );

            $appointment->update(['start_time' => $start, 'end_time' => $end]);

            return $start;
        });

        return redirect()
            ->route('appointments.index', ['date' => $start->toDateString()])
            ->with('success', 'Agendamento remarcado para ' . $start->format('d/m/Y \à\s H:i') . '!');
    }

    public function destroy(Appointment $appointment)
    {
        $appointment->delete();

        return redirect()->back()->with('success', 'Agendamento cancelado com sucesso!');
    }

    /**
     * Calcula início/fim e aplica as regras: não pode ser no passado,
     * precisa caber das 9h às 18h e não pode colidir com outro agendamento.
     *
     * @return array{0: Carbon, 1: Carbon}
     */
    private function resolveSlot(int $serviceId, string $date, string $time, ?int $ignoreId = null): array
    {
        $service = Service::findOrFail($serviceId);

        $start = Carbon::createFromFormat('Y-m-d H:i', "{$date} {$time}")->seconds(0);
        $end = $start->copy()->addMinutes($service->duration_minutes);

        if ($start->isPast()) {
            throw ValidationException::withMessages([
                'appointment_time' => 'Escolha um horário que ainda não passou.',
            ]);
        }

        if (! Availability::withinBusinessHours($start, $end)) {
            $hours = Availability::hoursFor($start);

            throw ValidationException::withMessages([
                'appointment_time' => $hours
                    ? sprintf(
                        'Neste dia atendemos das %s às %s. O serviço "%s" dura %d min e precisa terminar até às %s.',
                        $hours[0], $hours[1], $service->name, $service->duration_minutes, $hours[1],
                    )
                    : 'A barbearia está fechada neste dia. Escolha outra data.',
            ]);
        }

        if (Availability::hasConflict($start, $end, $ignoreId)) {
            throw ValidationException::withMessages([
                'appointment_time' => 'Esse horário já está ocupado. Escolha outro.',
            ]);
        }

        return [$start, $end];
    }

    private function parseDate(?string $value): Carbon
    {
        try {
            return $value ? Carbon::createFromFormat('Y-m-d', $value)->startOfDay() : Carbon::today();
        } catch (\Throwable) {
            return Carbon::today();
        }
    }

    private function messages(): array
    {
        return [
            'client_name.required' => 'Informe seu nome.',
            'client_phone.required' => 'Informe seu telefone.',
            'service_id.required' => 'Escolha um serviço.',
            'service_id.exists' => 'Serviço inválido.',
            'appointment_date.required' => 'Escolha uma data.',
            'appointment_time.required' => 'Escolha um horário.',
            '*.date_format' => 'Formato inválido.',
        ];
    }
}

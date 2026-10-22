<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function store(Request $request)
    {
        Service::create($this->validated($request));

        return redirect()->back()->with('success', 'Serviço cadastrado com sucesso!');
    }

    public function update(Request $request, Service $service)
    {
        // Agendamentos já feitos mantêm o horário de término que foi calculado na hora da marcação
        $service->update($this->validated($request));

        return redirect()->back()->with('success', 'Serviço atualizado com sucesso!');
    }

    public function destroy(Service $service)
    {
        // Não exclui serviço com agendamentos: deixaria a agenda e o faturamento sem referência
        $total = $service->appointments()->count();

        if ($total > 0) {
            return redirect()->back()->withErrors([
                'service' => "Não dá para excluir \"{$service->name}\": existem {$total} agendamento(s) com ele. Cancele-os na agenda primeiro.",
            ]);
        }

        $service->delete();

        return redirect()->back()->with('success', 'Serviço excluído com sucesso!');
    }

    private function validated(Request $request): array
    {
        // Aceita preço digitado com vírgula (ex: 53,00)
        $request->merge(['price' => str_replace(',', '.', (string) $request->input('price'))]);

        return $request->validate([
            'name' => 'required|string|max:80',
            'duration_minutes' => 'required|integer|min:5|max:240',
            'price' => 'required|numeric|min:0|max:99999',
        ], [
            'name.required' => 'Informe o nome do serviço.',
            'duration_minutes.required' => 'Informe a duração em minutos.',
            'price.required' => 'Informe o preço do serviço.',
            'price.numeric' => 'O preço deve ser um número.',
        ]);
    }
}

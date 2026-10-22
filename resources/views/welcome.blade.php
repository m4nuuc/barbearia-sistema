<!DOCTYPE html>
<html lang="pt-BR" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>La Famiglia Barber | Agendamento online</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Oswald:wght@500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: { extend: {
                colors: { gold: '#d4af37', ink: '#0c0c0d', coal: '#161618', steel: '#2a2a2e' },
                fontFamily: { display: ['Oswald', 'sans-serif'], sans: ['Inter', 'sans-serif'] },
            } },
        };
    </script>
    <style>
        dialog::backdrop { background: rgba(0,0,0,.75); }
        /* Foto fixa atrás de tudo. O JS reduz a opacidade no scroll até o fundo cinza (#0c0c0d) aparecer. */
        #site-bg {
            position: fixed;
            inset: 0;
            z-index: 0;
            background: #0c0c0d url('{{ asset('images/espaco/fachada.jpg') }}') center / cover no-repeat;
            pointer-events: none;
            will-change: opacity;
        }
        #site-bg::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(180deg, rgba(12,12,13,.4) 0%, rgba(12,12,13,.72) 55%, #0c0c0d 100%);
        }
        .hero { background: transparent; }
    </style>
</head>
<body class="bg-ink text-zinc-300 font-sans antialiased">
<div id="site-bg" aria-hidden="true"></div>
<div class="relative z-10">

@php
    $input = 'w-full rounded-md bg-ink border border-steel px-3 py-2.5 text-sm text-white placeholder-zinc-600 focus:border-gold focus:outline-none focus:ring-1 focus:ring-gold';
    $label = 'block text-sm font-medium text-zinc-400 mb-1.5';
    $date->locale('pt_BR');
@endphp

{{-- Navbar fixa --}}
<nav class="sticky top-0 z-40 border-b border-steel bg-ink/90 backdrop-blur">
    <div class="mx-auto flex max-w-6xl items-center justify-between px-4 py-3">
        <a href="{{ route('appointments.index') }}" class="font-display text-2xl font-bold tracking-wide text-gold">
            LA FAMIGLIA <span class="font-sans text-xs font-normal text-zinc-500">barbearia</span>
        </a>
        <div class="flex items-center gap-1 text-sm sm:gap-5">
            <a href="#servicos" class="hidden px-2 py-1 hover:text-gold sm:block">Serviços</a>
            <a href="#espaco" class="hidden px-2 py-1 hover:text-gold sm:block">Espaço</a>
            <a href="#unidade" class="hidden px-2 py-1 hover:text-gold sm:block">Informações</a>
            @if($isAdmin)
                <a href="#agenda" class="hidden px-2 py-1 hover:text-gold sm:block">Agenda</a>
                <a href="#painel" class="hidden px-2 py-1 hover:text-gold sm:block">Painel</a>
                <form action="{{ route('admin.logout') }}" method="POST">
                    @csrf
                    <button class="px-2 py-1 text-zinc-500 hover:text-white">Sair</button>
                </form>
            @else
                <a href="{{ route('admin.login') }}" class="px-2 py-1 text-zinc-500 hover:text-white">Área do barbeiro</a>
            @endif
            <a href="#agendar" class="rounded-md bg-gold px-4 py-2 font-semibold text-ink hover:bg-yellow-500">Agendar horário</a>
        </div>
    </div>
</nav>

{{-- Hero --}}
<header class="hero border-b border-steel">
    <div class="mx-auto max-w-6xl px-4 py-24 sm:py-36">
        <h1 class="font-display text-5xl font-bold leading-tight text-white sm:text-6xl">
            Corte e barba,<br>sem trocar mensagem.
        </h1>
        <p class="mt-5 max-w-lg text-zinc-400">
            Escolha o serviço, veja os horários livres e confirme na hora. Atendemos de segunda a sábado, das 9h às 18h.
        </p>
        <a href="#agendar" class="mt-8 inline-block rounded-md border border-gold px-6 py-3 font-semibold text-gold hover:bg-gold hover:text-ink">Ver horários livres</a>
    </div>
</header>

<main class="mx-auto max-w-6xl space-y-20 px-4 py-16">

    {{-- Feedback --}}
    @if(session('success'))
        <div class="rounded-md border border-emerald-700 bg-emerald-950/50 px-4 py-3 text-sm text-emerald-300">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="space-y-1 rounded-md border border-red-800 bg-red-950/50 px-4 py-3 text-sm text-red-300">
            @foreach($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    {{-- Serviços --}}
    <section id="servicos" class="scroll-mt-24">
        <h2 class="font-display text-3xl font-semibold text-white">Serviços</h2>
        <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @forelse($services as $service)
                @php
                    $photo = match ($service->name) {
                        'Corte' => 'corte.jpg',
                        'Barba' => 'barba.jpg',
                        'Corte + Barba' => 'combo.jpg',
                        default => 'corte.jpg',
                    };
                @endphp
                <div class="flex items-center gap-4 rounded-lg border border-steel bg-coal/90 p-4">
                    <img src="{{ asset('images/servicos/'.$photo) }}" alt="{{ $service->name }}"
                         class="h-16 w-16 shrink-0 rounded-full object-cover ring-1 ring-steel">
                    <div class="min-w-0 flex-1">
                        <p class="truncate text-lg font-medium text-white">{{ $service->name }}</p>
                        <p class="text-sm text-zinc-500">R$ {{ number_format($service->price, 2, ',', '.') }} · {{ $service->duration_minutes }} min</p>
                    </div>
                    <a href="#agendar" onclick="pickService({{ $service->id }})"
                       class="shrink-0 rounded-md border border-gold px-3 py-1.5 text-sm font-semibold text-gold hover:bg-gold hover:text-ink">Agendar</a>
                </div>
            @empty
                <p class="text-zinc-500">Nenhum serviço cadastrado ainda. Cadastre o primeiro no painel abaixo.</p>
            @endforelse
        </div>
    </section>

    {{-- Carrossel do espaço (fachada e interior) --}}
    <section id="espaco" class="scroll-mt-24">
        <h2 class="font-display text-3xl font-semibold text-white">Conheça nosso espaço</h2>
        <p class="mt-1 text-sm text-zinc-500">Use as setas para ver a fachada e o interior da barbearia.</p>
        <div class="relative mt-6 overflow-hidden rounded-xl border border-steel bg-coal">
            <img id="galleryImg" src="{{ asset('images/espaco/fachada.jpg') }}" alt="Fachada da La Famiglia Barber"
                 class="aspect-[16/9] w-full object-cover">
            <button type="button" onclick="galleryStep(-1)" aria-label="Foto anterior"
                    class="absolute left-3 top-1/2 -translate-y-1/2 rounded-full border border-steel bg-ink/80 px-3 py-2 text-xl text-white hover:border-gold hover:text-gold">‹</button>
            <button type="button" onclick="galleryStep(1)" aria-label="Próxima foto"
                    class="absolute right-3 top-1/2 -translate-y-1/2 rounded-full border border-steel bg-ink/80 px-3 py-2 text-xl text-white hover:border-gold hover:text-gold">›</button>
            <p id="galleryCaption" class="absolute bottom-0 w-full bg-ink/70 px-4 py-3 text-sm text-zinc-200">Fachada</p>
        </div>
        <div id="galleryDots" class="mt-4 flex justify-center gap-2"></div>
    </section>

    {{-- Agendar --}}
    <section id="agendar" class="scroll-mt-24 rounded-xl border border-steel bg-coal p-6 sm:p-8">
        <h2 class="font-display text-3xl font-semibold text-white">Agende seu horário</h2>
        <p class="mt-1 text-sm text-zinc-500">Só aparecem horários livres para o serviço escolhido.</p>

        <form action="{{ route('appointments.store') }}" method="POST" class="mt-6 grid gap-5 md:grid-cols-2">
            @csrf
            <div>
                <label class="{{ $label }}" for="client_name">Seu nome</label>
                <input id="client_name" name="client_name" value="{{ old('client_name') }}" required class="{{ $input }}">
            </div>
            <div>
                <label class="{{ $label }}" for="client_phone">Telefone / WhatsApp</label>
                <input id="client_phone" name="client_phone" value="{{ old('client_phone') }}" placeholder="(51) 99999-9999" required class="{{ $input }}">
            </div>
            <div>
                <label class="{{ $label }}" for="service_id">Serviço</label>
                <select id="service_id" name="service_id" required class="{{ $input }}">
                    <option value="">Escolha um serviço</option>
                    @foreach($services as $service)
                        <option value="{{ $service->id }}" @selected(old('service_id') == $service->id)>
                            {{ $service->name }} — R$ {{ number_format($service->price, 2, ',', '.') }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="{{ $label }}" for="appointment_date">Data</label>
                    <input id="appointment_date" type="date" name="appointment_date" min="{{ today()->toDateString() }}" value="{{ old('appointment_date', $date->isPast() && ! $date->isToday() ? today()->toDateString() : $date->toDateString()) }}" required class="{{ $input }}">
                </div>
                <div>
                    <label class="{{ $label }}" for="appointment_time">Horário</label>
                    <select id="appointment_time" name="appointment_time" required class="{{ $input }}"></select>
                </div>
            </div>
            <button type="submit" class="md:col-span-2 rounded-md bg-gold py-3 font-semibold text-ink hover:bg-yellow-500">Confirmar agendamento</button>
        </form>
    </section>

    {{-- Informações da unidade --}}
    @php
        $info = config('barbearia.info');
        $hours = config('barbearia.hours');
        $dayNames = ['Domingo', 'Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta', 'Sábado'];
        $todayIdx = now()->dayOfWeek;
        $todayHours = $hours[$todayIdx];
        $openNow = $todayHours
            && now()->between(today()->setTimeFromTimeString($todayHours[0]), today()->setTimeFromTimeString($todayHours[1]));
    @endphp
    <section id="unidade" class="scroll-mt-24">
        <h2 class="font-display text-3xl font-semibold text-white">Sobre a barbearia</h2>
        <div class="mt-6 grid gap-4 lg:grid-cols-3">

            <div class="space-y-5 rounded-lg border border-steel bg-coal p-6">
                <h3 class="font-display text-xl text-gold">Onde estamos</h3>
                <div>
                    <p class="text-white">{{ $info['address'] }}</p>
                    <a href="https://www.google.com/maps/search/?api=1&query={{ urlencode($info['address']) }}" target="_blank" rel="noopener" class="mt-1 inline-block text-sm text-gold hover:underline">Abrir no mapa</a>
                </div>
                <div class="space-y-3 border-t border-steel pt-5 text-sm">
                    <p><span class="text-zinc-500">Telefone</span><br>
                        <a href="tel:+55{{ preg_replace('/\D/', '', $info['phone']) }}" class="text-white hover:text-gold">{{ $info['phone'] }}</a></p>
                    <p><span class="text-zinc-500">WhatsApp</span><br>
                        <a href="https://wa.me/55{{ preg_replace('/\D/', '', $info['whatsapp']) }}" target="_blank" rel="noopener" class="text-white hover:text-gold">{{ $info['whatsapp'] }}</a></p>
                    <p><span class="text-zinc-500">Instagram</span><br>
                        <a href="{{ $info['instagram_url'] }}" target="_blank" rel="noopener" class="text-white hover:text-gold">{{ $info['instagram'] }}</a></p>
                </div>
            </div>

            <div class="rounded-lg border border-steel bg-coal p-6">
                <div class="flex items-center justify-between">
                    <h3 class="font-display text-xl text-gold">Horários</h3>
                    <span class="rounded-full px-3 py-1 text-xs font-medium {{ $openNow ? 'bg-emerald-950 text-emerald-400' : 'bg-steel text-zinc-400' }}">
                        {{ $openNow ? 'Aberto agora' : 'Fechado agora' }}
                    </span>
                </div>
                <dl class="mt-5 space-y-2.5 text-sm">
                    @foreach([1, 2, 3, 4, 5, 6, 0] as $i)
                        <div class="flex justify-between {{ $i === $todayIdx ? 'font-medium text-gold' : '' }}">
                            <dt>{{ $dayNames[$i] }}{{ $i === $todayIdx ? ' (hoje)' : '' }}</dt>
                            <dd>{{ $hours[$i] ? $hours[$i][0] . ' – ' . $hours[$i][1] : 'Fechado' }}</dd>
                        </div>
                    @endforeach
                </dl>
            </div>

            <div class="space-y-5 rounded-lg border border-steel bg-coal p-6">
                <h3 class="font-display text-xl text-gold">Comodidades</h3>
                <ul class="grid gap-2 text-sm sm:grid-cols-2 lg:grid-cols-1">
                    @foreach($info['amenities'] as $item)
                        <li class="flex items-start gap-2"><span class="text-gold">✓</span>{{ $item }}</li>
                    @endforeach
                </ul>
                <div class="border-t border-steel pt-5">
                    <p class="mb-2 text-sm text-zinc-500">Formas de pagamento</p>
                    <div class="flex flex-wrap gap-2">
                        @foreach($info['payments'] as $payment)
                            <span class="rounded-md border border-steel bg-ink px-2.5 py-1 text-xs text-zinc-300">{{ $payment }}</span>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>

    @if($isAdmin)
    {{-- Agenda do dia --}}
    <section id="agenda" class="scroll-mt-24">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <h2 class="font-display text-3xl font-semibold text-white">Agenda do dia</h2>
                <p class="mt-1 text-sm capitalize text-zinc-500">{{ $date->translatedFormat('l, d \d\e F \d\e Y') }}</p>
            </div>
            <form method="GET" action="{{ route('appointments.index') }}#agenda" class="flex items-center gap-2">
                <label for="agenda_date" class="text-sm text-zinc-400">Ver outro dia</label>
                <input id="agenda_date" type="date" name="date" value="{{ $date->toDateString() }}" onchange="this.form.submit()" class="{{ $input }} !w-auto">
            </form>
        </div>

        <div class="mt-6 divide-y divide-steel overflow-hidden rounded-lg border border-steel bg-coal">
            @forelse($agenda as $item)
                <div class="flex flex-wrap items-center justify-between gap-4 p-4">
                    <div class="flex items-center gap-5">
                        <div class="w-28 font-display text-xl text-gold">
                            {{ $item->start_time->format('H:i') }}<span class="text-sm text-zinc-500"> – {{ $item->end_time->format('H:i') }}</span>
                        </div>
                        <div>
                            <p class="font-medium text-white">{{ $item->client_name }}</p>
                            <p class="text-sm text-zinc-500">{{ $item->service->name }} · {{ $item->client_phone }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 text-sm">
                        <a href="{{ $item->whatsappReminderUrl() }}" target="_blank" rel="noopener"
                           class="rounded-md border border-emerald-800 px-3 py-1.5 text-emerald-400 hover:bg-emerald-950">Enviar lembrete</a>
                        <button type="button"
                                data-action="{{ route('appointments.update', $item) }}"
                                data-id="{{ $item->id }}"
                                data-duration="{{ $item->service->duration_minutes }}"
                                data-date="{{ $item->start_time->toDateString() }}"
                                data-name="{{ $item->client_name }} · {{ $item->service->name }}"
                                onclick="openReschedule(this)"
                                class="rounded-md border border-steel px-3 py-1.5 text-zinc-300 hover:border-gold hover:text-gold">Remarcar</button>
                        <form action="{{ route('appointments.destroy', $item) }}" method="POST" onsubmit="return confirm({{ Js::from('Cancelar o agendamento de ' . $item->client_name . '?') }})">
                            @csrf @method('DELETE')
                            <button class="rounded-md border border-red-900 px-3 py-1.5 text-red-400 hover:bg-red-950">Cancelar</button>
                        </form>
                    </div>
                </div>
            @empty
                <p class="p-8 text-center text-zinc-500">Nenhum agendamento neste dia. Os horários estão livres.</p>
            @endforelse
        </div>
    </section>

    {{-- Painel: relatório + cadastro de serviço --}}
    <section id="painel" class="scroll-mt-24 space-y-6">
        <h2 class="font-display text-3xl font-semibold text-white">Painel do barbeiro</h2>

        <div class="grid gap-4 sm:grid-cols-3">
            <div class="rounded-lg border border-steel bg-coal p-5">
                <p class="text-sm text-zinc-500">Faturamento do dia</p>
                <p class="mt-1 font-display text-3xl text-gold">R$ {{ number_format($revenue, 2, ',', '.') }}</p>
            </div>
            <div class="rounded-lg border border-steel bg-coal p-5">
                <p class="text-sm text-zinc-500">Atendimentos agendados</p>
                <p class="mt-1 font-display text-3xl text-white">{{ $count }}</p>
            </div>
            <div class="rounded-lg border border-steel bg-coal p-5">
                <p class="text-sm text-zinc-500">Ticket médio</p>
                <p class="mt-1 font-display text-3xl text-white">R$ {{ number_format($average, 2, ',', '.') }}</p>
            </div>
        </div>
        <p class="text-xs text-zinc-600">Os números acompanham o dia escolhido na agenda ({{ $date->format('d/m/Y') }}).</p>

        <div class="overflow-hidden rounded-lg border border-steel bg-coal">
            <div class="border-b border-steel px-5 py-3 text-sm font-medium text-zinc-400">Serviços cadastrados ({{ $services->count() }})</div>
            <div class="divide-y divide-steel">
                @forelse($services as $service)
                    <div class="flex flex-wrap items-center justify-between gap-3 px-5 py-3">
                        <div>
                            <p class="font-medium text-white">{{ $service->name }}</p>
                            <p class="text-sm text-zinc-500">
                                {{ $service->duration_minutes }} min · R$ {{ number_format($service->price, 2, ',', '.') }} · {{ $service->appointments_count }} agendamento(s)
                            </p>
                        </div>
                        <div class="flex items-center gap-2">
                        <button type="button"
                                data-action="{{ route('services.update', $service) }}"
                                data-name="{{ $service->name }}"
                                data-duration="{{ $service->duration_minutes }}"
                                data-price="{{ number_format($service->price, 2, ',', '') }}"
                                onclick="openServiceEdit(this)"
                                class="rounded-md border border-steel px-3 py-1.5 text-sm text-zinc-300 hover:border-gold hover:text-gold">Editar</button>
                        <form action="{{ route('services.destroy', $service) }}" method="POST" onsubmit="return confirm({{ Js::from('Excluir o serviço ' . $service->name . '?') }})">
                            @csrf @method('DELETE')
                            <button class="rounded-md border border-red-900 px-3 py-1.5 text-sm text-red-400 hover:bg-red-950">Excluir</button>
                        </form>
                        </div>
                    </div>
                @empty
                    <p class="px-5 py-6 text-sm text-zinc-500">Nenhum serviço cadastrado ainda.</p>
                @endforelse
            </div>
        </div>

        <form action="{{ route('services.store') }}" method="POST" class="grid items-end gap-4 rounded-lg border border-dashed border-steel p-5 md:grid-cols-4">
            @csrf
            <div class="md:col-span-1">
                <label class="{{ $label }}" for="s_name">Novo serviço</label>
                <input id="s_name" name="name" placeholder="Ex: Sobrancelha" required class="{{ $input }}">
            </div>
            <div>
                <label class="{{ $label }}" for="s_dur">Duração (min)</label>
                <input id="s_dur" type="number" name="duration_minutes" min="5" max="240" step="5" required class="{{ $input }}">
            </div>
            <div>
                <label class="{{ $label }}" for="s_price">Preço (R$)</label>
                <input id="s_price" name="price" inputmode="decimal" placeholder="30,00" required class="{{ $input }}">
            </div>
            <button class="rounded-md border border-gold py-2.5 font-semibold text-gold hover:bg-gold hover:text-ink">Cadastrar serviço</button>
        </form>
    </section>
    @endif
</main>

<footer class="border-t border-steel py-8 text-center text-xs text-zinc-600">
    Desafio técnico Skala Code · Laravel + SQLite · Inspirado na estética da La Mafia Barbearia
</footer>

@if($isAdmin)
{{-- Modal de remarcação --}}
<dialog id="rescheduleDialog" class="w-full max-w-md rounded-xl border border-steel bg-coal p-6 text-zinc-300">
    <form id="rescheduleForm" method="POST" class="space-y-4">
        @csrf @method('PUT')
        <h3 class="font-display text-2xl text-white">Remarcar horário</h3>
        <p id="rescheduleInfo" class="text-sm text-zinc-500"></p>
        <div>
            <label class="{{ $label }}" for="r_date">Nova data</label>
            <input id="r_date" type="date" name="appointment_date" min="{{ today()->toDateString() }}" required class="{{ $input }}">
        </div>
        <div>
            <label class="{{ $label }}" for="r_time">Novo horário</label>
            <select id="r_time" name="appointment_time" required class="{{ $input }}"></select>
        </div>
        <div class="flex justify-end gap-2 pt-2">
            <button type="button" onclick="rescheduleDialog.close()" class="rounded-md border border-steel px-4 py-2 text-sm hover:text-white">Voltar</button>
            <button class="rounded-md bg-gold px-4 py-2 text-sm font-semibold text-ink hover:bg-yellow-500">Salvar remarcação</button>
        </div>
    </form>
</dialog>

{{-- Modal de edição de serviço --}}
<dialog id="serviceDialog" class="w-full max-w-md rounded-xl border border-steel bg-coal p-6 text-zinc-300">
    <form id="serviceForm" method="POST" class="space-y-4">
        @csrf @method('PUT')
        <h3 class="font-display text-2xl text-white">Editar serviço</h3>
        <div>
            <label class="{{ $label }}" for="e_name">Nome</label>
            <input id="e_name" name="name" required class="{{ $input }}">
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="{{ $label }}" for="e_dur">Duração (min)</label>
                <input id="e_dur" type="number" name="duration_minutes" min="5" max="240" step="5" required class="{{ $input }}">
            </div>
            <div>
                <label class="{{ $label }}" for="e_price">Preço (R$)</label>
                <input id="e_price" name="price" inputmode="decimal" required class="{{ $input }}">
            </div>
        </div>
        <div class="flex justify-end gap-2 pt-2">
            <button type="button" onclick="serviceDialog.close()" class="rounded-md border border-steel px-4 py-2 text-sm hover:text-white">Voltar</button>
            <button class="rounded-md bg-gold px-4 py-2 text-sm font-semibold text-ink hover:bg-yellow-500">Salvar alterações</button>
        </div>
    </form>
</dialog>
@endif

<script>
    // Ocupação futura vinda do servidor: { 'YYYY-MM-DD': [['10:00','10:40', id], ...] }
    const busy = @json($busy);
    const durations = @json($services->pluck('duration_minutes', 'id'));
    const STEP = 20;
    // Horário por dia da semana (0 = domingo), vindo de config/barbearia.php: [abre, fecha] ou null
    const hours = @json(config('barbearia.hours'));

    const toMin = t => { const [h, m] = t.split(':'); return +h * 60 + +m; };
    const fmt = m => String(Math.floor(m / 60)).padStart(2, '0') + ':' + String(m % 60).padStart(2, '0');

    // Horários livres para uma data/duração (ignora o próprio agendamento ao remarcar)
    function freeSlots(date, duration, ignoreId = null) {
        const out = [];
        const now = new Date();
        const today = now.toLocaleDateString('en-CA');
        const nowMin = now.getHours() * 60 + now.getMinutes();
        const day = hours[new Date(date + 'T00:00:00').getDay()];
        if (!day) return out; // fechado neste dia
        for (let s = toMin(day[0]); s + duration <= toMin(day[1]); s += STEP) {
            if (date === today && s <= nowMin) continue;
            const taken = (busy[date] || []).some(([a, b, id]) => id !== ignoreId && toMin(a) < s + duration && toMin(b) > s);
            if (!taken) out.push(fmt(s));
        }
        return out;
    }

    function fillSelect(select, slots, emptyText) {
        select.innerHTML = slots.length
            ? slots.map(t => `<option value="${t}">${t}</option>`).join('')
            : `<option value="">${emptyText}</option>`;
    }

    // Formulário principal
    const serviceEl = document.getElementById('service_id');
    const dateEl = document.getElementById('appointment_date');
    const timeEl = document.getElementById('appointment_time');

    function refreshMain() {
        const duration = durations[serviceEl.value];
        if (!duration || !dateEl.value) return fillSelect(timeEl, [], 'Escolha serviço e data');
        fillSelect(timeEl, freeSlots(dateEl.value, duration), 'Sem horários livres neste dia');
    }
    serviceEl.addEventListener('change', refreshMain);
    dateEl.addEventListener('change', refreshMain);
    refreshMain();

    @if($isAdmin)
    // Modal de remarcação
    const rDate = document.getElementById('r_date');
    const rTime = document.getElementById('r_time');
    let rDuration = 0, rId = null;

    function refreshReschedule() {
        fillSelect(rTime, rDate.value ? freeSlots(rDate.value, rDuration, rId) : [], 'Sem horários livres neste dia');
    }
    rDate.addEventListener('change', refreshReschedule);

    function openReschedule(btn) {
        rDuration = +btn.dataset.duration;
        rId = +btn.dataset.id;
        document.getElementById('rescheduleForm').action = btn.dataset.action;
        document.getElementById('rescheduleInfo').textContent = btn.dataset.name;
        rDate.value = btn.dataset.date;
        refreshReschedule();
        document.getElementById('rescheduleDialog').showModal();
    }

    function openServiceEdit(btn) {
        document.getElementById('serviceForm').action = btn.dataset.action;
        document.getElementById('e_name').value = btn.dataset.name;
        document.getElementById('e_dur').value = btn.dataset.duration;
        document.getElementById('e_price').value = btn.dataset.price;
        document.getElementById('serviceDialog').showModal();
    }
    @endif

    // Fundo: some conforme o scroll (fica invisível depois de ~uma tela)
    const siteBg = document.getElementById('site-bg');
    function fadeBackground() {
        const fadeEnd = Math.max(320, window.innerHeight * 0.85);
        const t = Math.min(1, window.scrollY / fadeEnd);
        siteBg.style.opacity = String(1 - t);
    }
    window.addEventListener('scroll', fadeBackground, { passive: true });
    fadeBackground();

    function pickService(id) {
        serviceEl.value = String(id);
        refreshMain();
    }

    // Carrossel: as 4 fotos do espaço, na pasta public/images/espaco
    const gallery = [
        { src: @json(asset('images/espaco/fachada.jpg')), caption: 'Fachada — La Famiglia Barber' },
        { src: @json(asset('images/espaco/salao.jpg')), caption: 'Salão principal' },
        { src: @json(asset('images/espaco/cadeiras.jpg')), caption: 'Cadeiras e atendimento' },
        { src: @json(asset('images/espaco/sinuca.jpg')), caption: 'Área de lazer' },
    ];
    let galleryIndex = 0;
    const galleryImg = document.getElementById('galleryImg');
    const galleryCaption = document.getElementById('galleryCaption');
    const galleryDots = document.getElementById('galleryDots');

    gallery.forEach((_, i) => {
        const dot = document.createElement('button');
        dot.type = 'button';
        dot.className = 'h-2.5 w-2.5 rounded-full bg-steel';
        dot.setAttribute('aria-label', 'Foto ' + (i + 1));
        dot.onclick = () => showGallery(i);
        galleryDots.appendChild(dot);
    });

    function showGallery(i) {
        galleryIndex = (i + gallery.length) % gallery.length;
        galleryImg.src = gallery[galleryIndex].src;
        galleryCaption.textContent = gallery[galleryIndex].caption;
        [...galleryDots.children].forEach((dot, n) => {
            dot.className = 'h-2.5 w-2.5 rounded-full ' + (n === galleryIndex ? 'bg-gold' : 'bg-steel');
        });
    }
    function galleryStep(delta) { showGallery(galleryIndex + delta); }
    showGallery(0);
</script>
</div>
</body>
</html>

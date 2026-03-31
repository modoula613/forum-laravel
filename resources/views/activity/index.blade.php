<x-app-layout>
    <x-slot name="header">
        <div class="max-w-3xl">
            <p class="section-kicker">Activite</p>
            <h2 class="mt-3 text-4xl font-semibold text-stone-950">Activite recente</h2>
            <p class="muted-copy mt-3 text-base leading-7">
                Les derniers sujets, les nouvelles reponses et les notifications recentes reunis au meme endroit.
            </p>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto grid max-w-6xl gap-6 px-4 sm:px-6 lg:grid-cols-3 lg:px-8">
            <section class="glass-panel rounded-[2rem] p-6">
                <p class="section-kicker">Sujets</p>
                <h3 class="mt-3 text-2xl font-semibold text-stone-950">Nouveaux sujets</h3>
                <div class="mt-5 space-y-4">
                    @forelse ($topics as $topic)
                        <a href="{{ route('topics.show', $topic) }}" class="block rounded-[1.5rem] bg-white/70 p-4 transition hover:bg-white">
                            <p class="font-semibold text-stone-900">{{ $topic->title }}</p>
                            <p class="mt-2 text-sm text-stone-500">{{ $topic->user->name }} · {{ $topic->created_at->format('d/m/Y H:i') }}</p>
                        </a>
                    @empty
                        <p class="text-sm text-stone-500">Aucun sujet recent.</p>
                    @endforelse
                </div>
            </section>

            <section class="glass-panel rounded-[2rem] p-6">
                <p class="section-kicker">Reponses</p>
                <h3 class="mt-3 text-2xl font-semibold text-stone-950">Dernieres reponses</h3>
                <div class="mt-5 space-y-4">
                    @forelse ($replies as $reply)
                        <a href="{{ route('topics.show', $reply->topic) }}" class="block rounded-[1.5rem] bg-white/70 p-4 transition hover:bg-white">
                            <p class="font-semibold text-stone-900">{{ $reply->topic->title }}</p>
                            <p class="mt-2 text-sm text-stone-500">{{ $reply->user->name }} · {{ $reply->created_at->format('d/m/Y H:i') }}</p>
                            <p class="mt-2 text-sm text-stone-600">{{ \Illuminate\Support\Str::limit($reply->content, 90) }}</p>
                        </a>
                    @empty
                        <p class="text-sm text-stone-500">Aucune reponse recente.</p>
                    @endforelse
                </div>
            </section>

            <section class="glass-panel rounded-[2rem] p-6">
                <p class="section-kicker">Notifications</p>
                <h3 class="mt-3 text-2xl font-semibold text-stone-950">Dernieres alertes</h3>
                <div class="mt-5 space-y-4">
                    @forelse ($notifications as $notification)
                        <a href="{{ $notification->data['url'] ?? route('notifications.index') }}" class="block rounded-[1.5rem] bg-white/70 p-4 transition hover:bg-white">
                            <p class="font-semibold text-stone-900">{{ $notification->data['title'] ?? 'Notification' }}</p>
                            <p class="mt-2 text-sm text-stone-600">{{ $notification->data['message'] ?? '' }}</p>
                            <p class="mt-2 text-sm text-stone-500">{{ $notification->created_at->format('d/m/Y H:i') }}</p>
                        </a>
                    @empty
                        <p class="text-sm text-stone-500">Aucune notification recente.</p>
                    @endforelse
                </div>
            </section>
        </div>
    </div>
</x-app-layout>

@props(['status'])

@if ($status)
    <div {{ $attributes->merge(['class' => 'rounded-[1.25rem] border border-emerald-200 bg-emerald-50/90 px-4 py-3 text-sm font-medium text-emerald-700']) }}>
        {{ $status }}
    </div>
@endif

@props(['used' => 0, 'limit' => 3, 'plan' => 'bronze', 'endDate' => null])

<div class="card pad" style="display:flex;justify-content:space-between;align-items:center">
    <div>
        <div class="muted" style="font-size:12px">Current Plan</div>
        <div class="h2" style="margin:0">
            {{ ucfirst($plan) }} — {{ $used }}/{{ $limit }} listings used
        </div>

        @if ($endDate)
            @php
                $remaining = now()->diffInDays(\Carbon\Carbon::parse($endDate), false);
            @endphp
            <div class="muted" style="font-size:12px;margin-top:4px">
                Berakhir: {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}
            </div>
        @endif
    </div>

    <button class="btn primary" type="button" data-open="upgrade-modal">Upgrade</button>
</div>

<!-- This is not "blade" -->

<div class="card">
    {{ @if (!empty($props->{"card-title"})): }}
        <div class="card-header">
            <h3 class="card-title">{{ $props->{"card-title"} }}</h3>
        </div>
    {{ @endif }}

    <div class="card-body m-3">
        {{ $props->children }}
    </div>

    {{ @if (!empty($props->{"card-footer"})): }}
        <div class="card-footer">
            {{ $props->{"card-footer"} }}
        </div>
    {{ @endif }}
</div>
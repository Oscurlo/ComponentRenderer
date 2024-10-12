<!-- This is not blade 😅 -->

<div class="card">
    {{ @if (!empty($props->title)): }}
        <div class="card-header">
            <h3 class="card-title">
                {{ $props->title }}
            </h3>
        </div>
    {{ @endif }}

    {{ @if (!empty($props->children)): }}
        <div class="card-body">
            {{ $props->children }}
        </div>
    {{ @endif }}

    {{ @if (!empty($props->footer)): }}
        <div class="card-footer">
            {{ $props->footer }}
        </div>
    {{ @endif }}
</div>
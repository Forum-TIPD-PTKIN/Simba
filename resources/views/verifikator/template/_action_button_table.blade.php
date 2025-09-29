<div class="btn-group btn-group-sm" role="group" aria-label="Button Action">
    @foreach ($buttons as $key => $button)
        <button type="button" class="{{ $button['btn-class'] }}"
            onclick="{{ $key }}Data('{{ !array_key_exists('encrypted_id', $button) ? $data->encrypted_id : $button['encrypted_id'] }}')"
            title="{{ $button['title'] }} {{ $button['title'] }}" {{ $button['is_disabled'] ? 'disabled' : '' }}>
            <i class="{{ $button['icon'] }}"></i>
            @if (!isset($showTitle) || $showTitle === true)
                {{ $button['title'] }}
            @endif
        </button>
    @endforeach
</div>

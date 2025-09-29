@foreach ($form as $key => $item)
    @if (!in_array($item->kode . '_' . $item->config->name, $exclude))
        <div class="form-group @if ($item->config->type == 'hidden') d-none @endif"
            id="form-group-{{ $item->kode . '_' . $item->config->name }}">
            <label for="id_{{ $item->kode . '_' . $item->config->name }}">{{ $item->config?->title }}</label>
            @if (in_array($item->config->type, ['text', 'hidden', 'password', 'email', 'number', 'date']))
                @if ($item->config->type == 'date')
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><span class="far fa-calendar-alt"></span></span>
                        </div>
                        <input class="form-control datepicker @if ($errors->has($item->kode . '_' . $item->config->name)) is-invalid @endif"
                            id="id_{{ $item->kode . '_' . $item->config->name }}" placeholder="Pilih tanggal"
                            type="text" value="{{ $item->value }}"
                            name="{{ $item->kode . '_' . $item->config->name }}"
                            @if (!$disabledVue) v-model="{{ $item->kode . '_' . $item->config->name }}" @endif
                            @disabled(isset($item->config->disabled) && $item->config->disabled) @readonly(isset($item->config->readonly) && $item->config->readonly)>
                    </div>
                @else
                    <input type="{{ $item->config->type }}" id="id_{{ $item->kode . '_' . $item->config->name }}"
                        value="{{ $item->value }}"
                        class="form-control @if ($errors->has($item->kode . '_' . $item->config->name)) is-invalid @endif"
                        name="{{ $item->kode . '_' . $item->config->name }}"
                        @if (!$disabledVue) v-model="{{ $item->kode . '_' . $item->config->name }}" @endif
                        @disabled(isset($item->config->disabled) && $item->config->disabled) @readonly(isset($item->config->readonly) && $item->config->readonly)>
                @endif
            @elseif($item->config->type == 'textarea')
                <textarea id="id_{{ $item->kode . '_' . $item->config->name }}"
                    class="form-control @if ($errors->has($item->kode . '_' . $item->config->name)) is-invalid @endif"
                    name="{{ $item->kode . '_' . $item->config->name }}"
                    @if (!$disabledVue) v-model="{{ $item->kode . '_' . $item->config->name }}" @endif
                    @disabled(isset($item->config->disabled) && $item->config->disabled) @readonly(isset($item->config->readonly) && $item->config->readonly)>{{ $item->value }}</textarea>
            @elseif($item->config->type == 'radio')
                @foreach ($item->config->option as $ky_opt => $opt)
                    <div class="form-check">
                        <input class="form-check-input @if ($errors->has($item->kode . '_' . $item->config->name)) is-invalid @endif"
                            id="id_{{ $item->kode . '_' . $item->config->name }}_{{ $ky_opt }}_{{ $key }}"
                            type="radio" name="{{ $item->kode . '_' . $item->config->name }}"
                            @if (!$disabledVue) v-model="{{ $item->kode . '_' . $item->config->name }}" @endif
                            @if ($item->value == $opt->value) checked @endif value="{{ $opt->value }}"
                            @disabled(isset($item->config->disabled) && $item->config->disabled) @readonly(isset($item->config->readonly) && $item->config->readonly)>
                        <label class="form-check-label"
                            for="id_{{ $item->kode . '_' . $item->config->name }}_{{ $ky_opt }}_{{ $key }}">
                            {{ $opt->text }}
                        </label>
                        @if ($loop->last && $errors->has($item->kode . '_' . $item->config->name))
                            <div class="invalid-feedback">
                                {{ $errors->first($item->kode . '_' . $item->config->name) }}
                            </div>
                        @endif
                    </div>
                @endforeach
            @elseif($item->config->type == 'checkbox')
                @foreach ($item->config->option as $ky_opt => $opt)
                    <div class="form-check">
                        <input class="form-check-input @if ($errors->has($item->kode . '_' . $item->config->name)) is-invalid @endif"
                            id="id_{{ $item->kode . '_' . $item->config->name }}_{{ $ky_opt }}_{{ $key }}"
                            type="checkbox" name="{{ $item->kode . '_' . $item->config->name }}[]"
                            @if (!$disabledVue) v-model="{{ $item->kode . '_' . $item->config->name }}" @endif
                            @if (old($item->kode . '_' . $item->config->name . '[]', request($item->kode . '_' . $item->config->name . '[]')) ==
                                    $opt->value) checked @endif value="{{ $opt->value }}"
                            @disabled(isset($item->config->disabled) && $item->config->disabled) @readonly(isset($item->config->readonly) && $item->config->readonly)>
                        <label class="form-check-label"
                            for="id_{{ $item->kode . '_' . $item->config->name }}_{{ $ky_opt }}_{{ $key }}">
                            {{ $opt->text }}
                        </label>
                        @if ($loop->last && $errors->has($item->kode . '_' . $item->config->name))
                            <div class="invalid-feedback">
                                {{ $errors->first($item->kode . '_' . $item->config->name) }}
                            </div>
                        @endif
                    </div>
                @endforeach
            @elseif($item->config->type == 'select')
                <select class="form-select my-1 mr-sm-2 @if ($errors->has($item->kode . '_' . $item->config->name)) is-invalid @endif"
                    id="id_{{ $item->kode . '_' . $item->config->name }}"
                    name="{{ $item->kode . '_' . $item->config->name }}"
                    @if (!$disabledVue) v-model="{{ $item->kode . '_' . $item->config->name }}" @endif
                    @disabled(isset($item->config->disabled) && $item->config->disabled) @readonly(isset($item->config->readonly) && $item->config->readonly)>
                    <option selected="" disabled>Pilih...</option>
                    @foreach ($item->config->option as $opt)
                        <option value="{{ $opt->value }}" @if ($item->value == $opt->value) selected @endif>
                            {{ $opt->text }}</option>
                    @endforeach
                </select>
            @elseif ($item->config->type == 'file')
                <input type="file" id="id_{{ $item->kode . '_' . $item->config->name }}"
                    class="form-control @if ($errors->has($item->kode . '_' . $item->config->name)) is-invalid @endif"
                    name="{{ $item->kode . '_' . $item->config->name }}"
                    @if (!$disabledVue) v-model="{{ $item->kode . '_' . $item->config->name }}" @endif
                    @disabled(isset($item->config->disabled) && $item->config->disabled) @readonly(isset($item->config->readonly) && $item->config->readonly) value="{{ $item->value }}" />
                @if ($item->value)
                    {!! $item->value !!}
                @endif
            @endif
            @if (
                $item->config->type != 'radio' &&
                    $item->config->type != 'checkbox' &&
                    $errors->has($item->kode . '_' . $item->config->name))
                <div class="invalid-feedback d-block">
                    {{ $errors->first($item->kode . '_' . $item->config->name) }}
                </div>
            @endif
            @if (
                $item->deskripsi &&
                    (!(isset($item->config->readonly) && $item->config->readonly) &&
                        !(isset($item->config->disabled) && $item->config->disabled)))
                <div class="small text-muted">{!! nl2br($item->deskripsi) !!}</div>
            @endif
        </div>
    @endif
@endforeach

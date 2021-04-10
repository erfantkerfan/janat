<div class="form-group row">
    <label for="{{ $key }}" class="col-md-4 col-form-label text-md-right">{{ $label }}</label>
    <div class="col-md-6">
        <input id="{{ $key }}"
               type="text"
               class="form-control @error($key) is-invalid @enderror"
               name="{{ $key }}"
               value="{{ old($key) }}"
               autocomplete="{{ $key }}"
               autofocus
        >
        @error($key)
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>
</div>

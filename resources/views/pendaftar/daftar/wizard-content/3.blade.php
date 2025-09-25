<h5 class="mb-3">Step 3: Pemberkasan</h5>
<div class="row">
    <div class="col-12">
        @foreach ($generated_form as $item)
            {!! $item['form'] !!}
        @endforeach
    </div>
</div>

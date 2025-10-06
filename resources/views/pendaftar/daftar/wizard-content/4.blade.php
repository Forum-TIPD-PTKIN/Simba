<!-- Step Content (contoh isi step 2) -->
<h5 class="mb-3">Step 4: Finalisai</h5>
<div class="row">
    <div class="col-12">
        Hello
    </div>
</div>

@push('script')


    @if (session()->has('error_register'))
        <script>
            Swal.fire({
                icon: "error",
                title: "Oops...",
                text: "{{ session()->get('error_register') }}",
            });
        </script>
    @endif
@endpush

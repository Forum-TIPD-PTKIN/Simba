@if (count($verifikator))
    <div class="table-responsive">
        <table class="table table-sm table-bordered text-center align-middle" id="pc-dt-simple">
            <thead class="bg-teal-100">
                <tr>
                    <th scope="col" width="5%">No</th>
                    <th scope="col">Nama Verifikator</th>
                    <th scope="col" width="25%">Total Verifikasi Data</th>
                </tr>
            </thead>
            <tbody class="table-group-divider">
                @foreach ($verifikator as $key => $item)
                    <tr>
                        <td scope="row">{{ $loop->iteration }}</td>
                        <td scope="row" class="text-start ps-3">{{ $item->verifikator }}</td>
                        <td scope="row">{{ $item->total }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@else
    <div class="alert alert-info text-center fs-3 fw-bold">Data tidak ditemukan</div>
@endif

<table class="table table-bordered text-center align-middle" id="pc-dt-simple">
    <thead>
        <tr>
            <th>Program Studi</th>
            <th>Status Pendaftaran Terakhir</th>
            <th>Jumlah</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($rekap_prodi as $key => $item)
            <tr>
                <td rowspan="{{ count($item) + 1 }}">{{ $key }}</td>
            </tr>
            @foreach ($item as $k => $i)
                <tr>
                    <td>{{ $i['status'] }}</td>
                    <td>{{ $i['jumlah'] }}</td>
                </tr>
            @endforeach
        @endforeach
    </tbody>
</table>

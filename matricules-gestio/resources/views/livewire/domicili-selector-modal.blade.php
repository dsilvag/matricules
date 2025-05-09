<div class="container">
    <table class="table table-striped">
        <thead>
            <tr>
                <th>DOMCOD</th>
                <th>CARSIG</th>
                <th>CARDESC</th>
                <th>DOMNUM</th>
                <th>DOMBIS</th>
                <th>DOMNUM2</th>
                <th>DOMBIS2</th>
                <th>DOMESC</th>
                <th>DOMPIS</th>
                <th>DOMPTA</th>
                <th>DOMBLOC</th>
                <th>DOMPTAL</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($dwellings as $item)
                <tr>
                    <td>{{ $item->DOMCOD }}</td>
                    <td>{{ $item->street->CARSIG }}</td>
                    <td>{{ $item->street->CARDESC }}</td>
                    <td>{{ $item->DOMNUM }}</td>
                    <td>{{ $item->DOMBIS }}</td>
                    <td>{{ $item->DOMNUM2 }}</td>
                    <td>{{ $item->DOMBIS2 }}</td>
                    <td>{{ $item->DOMESC }}</td>
                    <td>{{ $item->DOMPIS }}</td>
                    <td>{{ $item->DOMPTA }}</td>
                    <td>{{ $item->DOMBLOC }}</td>
                    <td>{{ $item->DOMPTAL }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Mostrar los links de paginación -->
    <x-filament::pagination :paginator="$dwellings" />
</div>       
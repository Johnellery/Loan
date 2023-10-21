
<div class="overflow-x-auto mt-5">
    <table id="transaction-table" class="min-w-full table-auto border-collapse rounded-lg overflow-hidden">
        <thead class="bg-blue-400">
            <tr>
                <th class="px-4 py-2">Bike name</th>
                <th class="px-4 py-2">Amount</th>
                <th class="px-4 py-2">CI status</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
                    @if ($loan->isEmpty())
                    <tr>
                        <td colspan="3" class="px-4 py-2 text-center">No loan found</td>
                    </tr>
                @else
                    @foreach($loan as $loan)
                    <tr>
                        <td class="px-4 py-2 text-center">{{ $loan->bike->name}}</td>
                        <td class="px-4 py-2 text-center">{{ number_format($loan->bike->price) }} PHP</td>
                        <td class="px-4 py-2 text-center">{{$loan->ci_status }}</td>
                    </tr>
                    @endforeach
                @endif
        </tbody>
    </table>
</div>

<script>
    $(document).ready(function() {
        $('#transaction-table').DataTable({
            searching: true,
            paging: true,
            ordering: true,
            order: [[2, 'desc']],
        });
    });
</script>

<div class="overflow-x-auto mt-5">
    <table id="transaction-table" class="min-w-full table-auto border-collapse rounded-lg overflow-hidden">
        <thead class="bg-blue-400">
            <tr>
                <th class="px-4 py-2">Transaction No.</th>
                <th class="px-4 py-2">Amount</th>
                <th class="px-4 py-2">Date</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @if (empty($billing))
                <tr>
                    <td colspan="3" class="px-4 py-2 text-center">No transactions found</td>
                </tr>
            @else
                @foreach($billing as $bill)
                <tr>
                    <td class="px-4 py-2 text-center">{{ $bill->transaction_number }}</td>
                    <td class="px-4 py-2 text-center">${{ number_format($bill->amount, 2) }}</td>
                    <td class="px-4 py-2 text-center">{{ \Carbon\Carbon::parse($bill->created_at)->format('F j, Y') }}</td>
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

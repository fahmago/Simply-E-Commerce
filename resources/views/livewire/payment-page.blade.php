<div class="container mx-auto mt-8">
    <h2 class="text-2xl font-semibold">Pembayaran E-Payment</h2>
    <p class="mt-4">Silakan lakukan pembayaran untuk menyelesaikan transaksi.</p>

        <button id="pay-button" type="submit"
            class="bg-green-500 mt-4 w-full p-3 rounded-lg text-lg text-white hover:bg-green-600">
            <span wire:loading.remove>Place Order</span>
        </button>

    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('services.midtrans.clientKey') }}"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    
    <script>
        document.getElementById('pay-button').addEventListener('click', function () {
            snap.pay('{{ session('snapToken') }}', {
                onSuccess: function (result) {
                    console.log(result);
                    window.location.href = "/success";
                },
                onPending: function (result) {
                    console.log(result);
                    alert('Payment is pending');
                },
                onError: function (result) {
                    console.error(result);
                    alert('Payment failed');
					window.location.href = "/cancel";
                }
            });
        });
    </script>
</div>
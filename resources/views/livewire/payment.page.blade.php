@extends('layouts.app')

@section('content')
<div class="container mx-auto mt-8">
    <h2 class="text-2xl font-semibold">Pembayaran E-Payment</h2>
    <p class="mt-4">Silakan lakukan pembayaran untuk menyelesaikan transaksi.</p>
    
    @if(session()->has('snapToken'))
    <button id="pay-button" class="bg-green-500 mt-4 w-full p-3 rounded-lg text-lg text-white hover:bg-green-600">
        Pembayaran dengan Midtrans
    </button>

    <script src="https://app.midtrans.com/snap/snap.js" data-client-key="{{ env('MIDTRANS_CLIENT_KEY') }}"></script>
    <script type="text/javascript">
        document.getElementById('pay-button').onclick = function () {
            var snapToken = "{{ session('snapToken') }}"; // Ambil snapToken dari session
            window.snap.pay(snapToken, {
                onSuccess: function(result) {
                    // Redirect ke halaman success setelah pembayaran berhasil
                    window.location.href = "{{ route('success') }}";
                },
                onPending: function(result) {
                    // Jika pembayaran masih pending
                    alert("Pembayaran masih pending");
                    window.location.href = "{{ route('cancel') }}";
                },
                onError: function(result) {
                    // Jika terjadi kesalahan
                    alert("Pembayaran gagal");
                    window.location.href = "{{ route('cancel') }}";
                }
            });
        };
    </script>
    @else
    <p>Token pembayaran tidak ditemukan.</p>
    @endif
</div>
@endsection

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Payment</title>
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('MIDTRANS_CLIENT_KEY') }}"></script>
</head>
<body>
    <h1>Proses Pembayaran</h1>
    <button id="pay-button">Bayar Sekarang</button>

    <script>
        document.getElementById('pay-button').addEventListener('click', function () {
            window.snap.pay('{{ $token }}', {
                onSuccess: function(result){
                    console.log(result);
                    window.location.href = '/success';
                },
                onPending: function(result){
                    console.log(result);
                    window.location.href = '/pending';
                },
                onError: function(result){
                    console.log(result);
                    window.location.href = '/failed';
                }
            });
        });
    </script>
</body>
</html>

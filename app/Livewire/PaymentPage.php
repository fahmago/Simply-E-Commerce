<?php

namespace App\Livewire;

use Midtrans\Snap;
use App\Models\Order;
use App\Models\Address;
use Livewire\Component;
use Midtrans\Notification;
use GuzzleHttp\Psr7\Request;
use App\Helpers\CartManagement;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class PaymentPage extends Component {

    public $first_name;
    public $last_name;
    public $phone;
    public $street_address;
    public $city;
    public $state;
    public $zip_code;
    public $payment_method;

    public function mount()
    {
        $cart_items = CartManagement::getCartItemsFromCookie();
        if (count($cart_items) == 0) {
            return redirect('/products');
        }
    }

    public function createTransaction() {

        $cart_items = CartManagement::getCartItemsFromCookie();

        $order = new Order();
        $order->user_id = Auth::user()->id;
        $order->grand_total = CartManagement::calculateGrandTotal($cart_items);
        $order->payment_method = $this->payment_method;
        $order->payment_status = 'pending';
        $order->status = 'new';
        $order->currency = 'idr';
        $order->shipping_amount = 0;
        $order->shipping_method = 'none';
        $order->notes = 'Order placed by ' . Auth::user()->name;

        $address = new Address();
        $address->first_name = $this->first_name;
        $address->last_name = $this->last_name;
        $address->phone = $this->phone;
        $address->street_address = $this->street_address;
        $address->city = $this->city;
        $address->state = $this->state;
        $address->zip_code = $this->zip_code;

        $line_items = [];
        

        $sessionCheckout = Session::create([
        'transaction_details' => [
            'order_id' => uniqid(), // ID transaksi unik
            'gross_amount' => $order->grand_total, // Total pembayaran
        ],
        'customer_details' => [
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => Auth::user()->email,
            'phone' => $this->phone,
        ],
        'item_details' => $line_items,
        'callbacks' => [
            'finish' => route('success'), // Halaman setelah pembayaran sukses
        ],
    ]);

    $snapToken = Snap::getSnapToken($sessionCheckout);

    return view('payment', compact('snapToken'));
}

public function handleNotification(Request $request)
{
    $notification = new Notification();

    if ($notification->transaction_status === 'capture') {
        // Tangani pembayaran berhasil
    } else if ($notification->transaction_status === 'pending') {
        // Tangani pembayaran pending
    } else if ($notification->transaction_status === 'deny') {
        // Tangani pembayaran gagal
    }
}


    public function create (Request $request){
        
    }

    public function render()
    {
        return view('livewire.payment-page');
    }
}

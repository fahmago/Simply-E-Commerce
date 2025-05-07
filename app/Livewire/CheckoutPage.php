<?php

namespace App\Livewire;

use Midtrans\Snap;
use Midtrans\Config;
use App\Models\Order;
use App\Models\Address;
use Livewire\Component;
use Livewire\Attributes\Title;
use App\Helpers\CartManagement;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

#[Title('Checkout')]
class CheckoutPage extends Component
{

    public $first_name;
    public $last_name;
    public $phone;
    public $street_address;
    public $city;
    public $state;
    public $zip_code;
    public $payment_method;
    public $snapToken;


    public function mount()
    {
        $cart_items = CartManagement::getCartItemsFromCookie();
        if (count($cart_items) == 0) {
            return redirect('/products');
        }
    }

    public function placeOrder()
    {
        $this->validate([
            'first_name' => 'required',
            'last_name' => 'required',
            'phone' => 'required',
            'street_address' => 'required',
            'city' => 'required',
            'state' => 'required',
            'zip_code' => 'required',
            'payment_method' => 'required',
        ]);
    

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
        $order->save();

        $address = new Address();
        $address->first_name = $this->first_name;
        $address->last_name = $this->last_name;
        $address->phone = $this->phone;
        $address->street_address = $this->street_address;
        $address->city = $this->city;
        $address->state = $this->state;
        $address->zip_code = $this->zip_code;
        $address->order_id = $order->id;
        $address->save();

        $order->items()->createMany($cart_items);


        if ($this->payment_method == 'midtrans') {

            Config::$serverKey = config('services.midtrans.serverKey');
            Config::$clientKey = config('services.midtrans.clientKey');
            Config::$isProduction = config('services.midtrans.isProduction', false);
            Config::$isSanitized = config('services.midtrans.isSanitized', true);
            Config::$is3ds = config('services.midtrans.is3ds', true);

            $itemDetails = []; // Definisikan $itemDetails di luar $payload
            foreach ($cart_items as $item) {
                $itemDetails[] = [
                    'id' => $item['product_id'],
                    'price' => $item['unit_amount'],
                    'quantity' => $item['quantity'],
                    'name' => $item['name'],
                ];
            }

            $payload = [
                'transaction_details' => [
                    'order_id' => uniqid(),
                    'gross_amount' => $order->grand_total,
                ],

                'customer_details' => [
                    'first_name' => $this->first_name,
                    'last_name' => $this->last_name,
                    'email' => Auth::user()->email,
                    'phone' => $this->phone,
                    'address' => $this->street_address,
                    'city' => $this->city,
                    'postal_code' => $this->zip_code,
                ],

                'item_details' => $itemDetails,

            ];


            try {
                $this->snapToken = Snap::getSnapToken($payload);
                session(['snapToken' => $this->snapToken]);
                return redirect()->route('payment'); // Redirect ke Payment Page
                CartManagement::clearCartItems();
            } catch (\Exception $e) {
                Log::error('Failed to generate Snap Token:', ['error' => $e->getMessage()]);
                session()->flash('error', 'Gagal mendapatkan Snap Token. Silakan coba lagi.');
                return redirect()->route('checkout');
            }
        } else {
            // Kosongkan keranjang
            return redirect()->route('success'); // Redirect ke Halaman Sukses
            CartManagement::clearCartItems();
        }

        $order->save();
        $address->order_id = $order->id;
        $address->save();
        $order->items()->createMany($cart_items);
        CartManagement::clearCartItems();
    }
    public function render()
    {
        $cart_items = CartManagement::getCartItemsFromCookie();
        $grand_total = CartManagement::calculateGrandTotal($cart_items);
        return view('livewire.checkout-page', [
            'cart_items' => $cart_items,
            'grand_total' => $grand_total
        ]);
    }
}

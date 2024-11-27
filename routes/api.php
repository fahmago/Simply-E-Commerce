<?php

use Illuminate\Http\Request;
use App\Livewire\CheckoutPage;
use Illuminate\Support\Facades\Route;

Route::post('/checkout', [CheckoutPage::class, 'create']);
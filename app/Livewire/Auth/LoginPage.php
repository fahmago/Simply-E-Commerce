<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Auth;

#[Title('Login')]
class LoginPage extends Component{

    public $email;
    public $password;

    public function login(){
        $this->validate([
            'email' => 'required|email|max:255|exists:users,email',
            'password' => 'required|min:6|max:255',
        ]);

        if(Auth::attempt(['email' => $this->email, 'password' => $this->password])) {
            session()->flash('error', 'Invalid credentials');
            return redirect()->intended();
        } else {
            session()->flash('error', 'Invalid credentials.');
        }

    }

    public function render(){
        return view('livewire.auth.login-page');
    }
}

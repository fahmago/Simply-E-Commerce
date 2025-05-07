<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Category;
use Livewire\Attributes\Title;


#[Title('Categories Page - JajananKita')]
class CategoriesPage extends Component
{
    
    public function render()
    {
        $categories = Category::where('is_active', 1)->get();
        return view('livewire.categories-page',[
            'categories' => $categories,
        ]);
    }
}

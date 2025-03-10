<?php
namespace App\Http\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class DeleteAccount extends Component
{
    public $confirmingUserDeletion = false;

    // Trigger the modal to confirm deletion
    public function confirmUserDeletion()
    {
        $this->confirmingUserDeletion = true;
    }

    // Handle account deletion
    public function deleteUser()
    {
        $user = Auth::user();
        
        // Delete the user account
        $user->delete();

        // Log out the user after deletion
        Auth::logout();

        // Redirect to home page or login page after deletion
        return redirect('/');
    }

    public function render()
    {
        return view('livewire.delete-account');
    }
}

<?php
namespace App\Http\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Actions\Jetstream\DeleteUser;

class DeleteAccount extends Component
{
    public $confirmingUserDeletion = false;
    
    public function confirmUserDeletion()
    {
        $this->confirmingUserDeletion = true;
    }
    
    // Funció per eliminar l'usuari sense demanar la contrasenya
    public function deleteUser()
    {
        try {
            // Utilitza la classe DeleteUser per eliminar l'usuari
            (new DeleteUser)->delete(Auth::user());

            // Tanca la sessió de l'usuari
            Auth::logout();

            // Redirigeix després d'eliminar l'usuari
            return redirect('/')->with('success', 'El teu compte ha estat eliminat correctament.');
        } catch (\Exception $e) {
            session()->flash('error', 'Error en eliminar el compte: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.delete-account');
    }
}
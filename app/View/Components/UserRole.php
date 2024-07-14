<?php

namespace App\View\Components;

use App\Models\User;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use Spatie\Permission\Models\Role;

class UserRole extends Component
{

    public $userId;
    public $roles;
    public $selectedRoles;

    /**
     * Create a new component instance.
     */
    public function __construct($userId)
    {
        $this->userId = $userId;
        $this->roles = Role::all();
        $user = User::find($this->userId);
        $this->selectedRoles = $user->roles->pluck('name')->toArray();

    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.user-role');
    }
}

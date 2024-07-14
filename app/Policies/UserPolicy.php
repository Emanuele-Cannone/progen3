<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

class UserPolicy
{

    /**
     * @param User $impersonator
     * @return Response
     */
    public function take(User $impersonator): Response
    {
        if($impersonator->can('impersonate')){
            return Response::allow();
        }

        return Response::deny('Unauthorized');
    }

    /**
     * @return Response
     */
    public function leave(): Response
    {
        if(session()->has('impersonate')){
            return Response::allow();
        }

        return Response::deny('Unauthorized');

    }
}

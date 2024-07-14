<?php

namespace App\Services;

use App\Mail\impersonateUser;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class ImpersonateService
{

    use Queueable;
    /**
     * @param User $user
     * @return void
     * @throws \Throwable
     */
    public function impersonate(User $user): void
    {
        throw_if(auth()->user()->cannot('impersonate'), new \Exception());

        session([
            'impersonate' => true,
            'impersonator' => Auth::id(),
            'impersonateToken' => Carbon::now()->addMinutes(config('impersonate.duration')),
        ]);

        Auth::loginUsingId($user->id);

        Mail::to($user->email)->queue(new impersonateUser());

    }


    public function leave(): void
    {
        throw_if(session()->missing('impersonate'), new \Exception());

        $user = User::findOrFail(session('impersonator'));

        session()->invalidate();

        Auth::loginUsingId($user->id);
    }

}

<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\ImpersonateService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;


class ImpersonateController extends Controller
{

    /**
     * @param ImpersonateService $impersonateService
     */
    public function __construct(private readonly ImpersonateService $impersonateService)
    {
    }

    /**
     * @param User $user
     * @return RedirectResponse
     * @throws \Throwable
     */
    public function takeOver(User $user): RedirectResponse
    {
        // $this->authorize('take', $user);
        $this->impersonateService->impersonate($user);

        return redirect()->route('dashboard');
    }

    /**
     * @return RedirectResponse
     * @throws \Throwable
     */
    public function leave(): RedirectResponse
    {
        // $this->authorize('leave', Auth::user());
        $this->impersonateService->leave();

        return redirect()->route('users.index');
    }

}

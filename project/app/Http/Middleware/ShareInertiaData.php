<?php

namespace App\Http\Middleware;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Inertia\Inertia;
use Laravel\Fortify\Features;
use Laravel\Jetstream\Jetstream;

class ShareInertiaData
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  callable  $next
     * @return \Illuminate\Http\Response
     */
    public function handle($request, $next)
    {
        Inertia::share(array_filter([
            'jetstream' => function () use ($request) {
                return [
                    'canManageTwoFactorAuthentication' => Features::canManageTwoFactorAuthentication(),
                    'canUpdatePassword' => Features::enabled(Features::updatePasswords()),
                    'canUpdateProfileInformation' => Features::canUpdateProfileInformation(),
                    'hasApiFeatures' => Jetstream::hasApiFeatures(),
                    'managesProfilePhotos' => Jetstream::managesProfilePhotos(),
                    'flash' => $request->session()
                        ->get('flash', []),
                ];
            },

            'auth' => [
                'user' => function () use ($request) {
                    if (!$request->user()) {
                        return;
                    }

                    return array_merge(
                        $request->user()
                            ->toArray(),
                        ['two_factor_enabled' => !is_null($request->user()->two_factor_secret)]
                    );
                },
            ],

            'errorBags' => function () {
                return collect(optional(Session::get('errors'))->getBags() ?: [])
                    ->mapWithKeys(function ($bag, $key) {
                        return [$key => $bag->messages()];
                    })
                    ->all();
            },

            'currentRouteName' => Route::currentRouteName(),
            'csrfToken' => csrf_token(),
        ]));

        return $next($request);
    }
}

<?php

namespace App\Providers;

use App\Events\ClaimCompleted;
use App\Events\PaymentConfirmed;
use App\Events\PaymentExpired;
use App\Listeners\SendClaimCompletedNotification;
use App\Listeners\SendPaymentConfirmedNotification;
use App\Listeners\SendPaymentExpiredNotification;
use App\Services\BookingService;
use App\Services\PaymentService;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(PaymentService::class);
        $this->app->singleton(BookingService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // ── Event → Listener bindings ─────────────────────────────────────
        Event::listen(PaymentConfirmed::class, SendPaymentConfirmedNotification::class);
        Event::listen(PaymentExpired::class,   SendPaymentExpiredNotification::class);
        Event::listen(ClaimCompleted::class,   SendClaimCompletedNotification::class);

        // ── Scheduler ─────────────────────────────────────────────────────
        $this->callAfterResolving(Schedule::class, function (Schedule $schedule) {
            /** @var PaymentService $svc */
            $svc = $this->app->make(PaymentService::class);

            // Every minute: expire unpaid claims
            $schedule->call(function () use ($svc) {
                $count = $svc->expireOverdueClaims();
                if ($count) {
                    logger()->info("[Scheduler] Expired {$count} overdue claim(s).");
                }
            })->everyMinute()->name('expire-overdue-claims')->withoutOverlapping();

            // Every 5 minutes: auto-complete claims past pickup_deadline
            $schedule->call(function () use ($svc) {
                $count = $svc->autoCompleteOverduePickups();
                if ($count) {
                    logger()->info("[Scheduler] Auto-completed {$count} overdue pickup(s).");
                }
            })->everyFiveMinutes()->name('auto-complete-pickups')->withoutOverlapping();
        });
    }
}

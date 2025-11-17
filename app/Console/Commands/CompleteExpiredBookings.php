<?php

namespace App\Console\Commands;

use App\Models\Booking;
use App\Models\Notification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CompleteExpiredBookings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bookings:complete-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Complete bookings that have passed their checkout date and restore room capacity';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for expired bookings...');

        // Find all active or approved bookings that have passed their checkout date
        $expiredBookings = Booking::whereIn('status', [
            Booking::STATUS_ACTIVE,
            Booking::STATUS_APPROVED
        ])
        ->whereNotNull('check_out_date')
        ->where('check_out_date', '<', now())
        ->with(['room', 'property', 'tenant'])
        ->get();

        if ($expiredBookings->isEmpty()) {
            $this->info('No expired bookings found.');
            return Command::SUCCESS;
        }

        $count = 0;
        foreach ($expiredBookings as $booking) {
            try {
                // Complete the booking (this will also restore room availability)
                $booking->complete();

                // Notify tenant that their booking has been completed
                Notification::create([
                    'user_id' => $booking->user_id,
                    'type' => Notification::TYPE_BOOKING_COMPLETED,
                    'title' => 'Booking Completed',
                    'message' => 'Your booking for "' . $booking->property->title . '" has been completed. Thank you for staying with us!',
                    'data' => [
                        'booking_id' => $booking->id,
                        'property_id' => $booking->property_id,
                        'check_out_date' => $booking->check_out_date ? $booking->check_out_date->format('Y-m-d') : null
                    ],
                    'action_url' => route('bookings.index')
                ]);

                // Notify landlord that the booking has been completed
                Notification::create([
                    'user_id' => $booking->property->user_id,
                    'type' => Notification::TYPE_BOOKING_COMPLETED,
                    'title' => 'Booking Completed',
                    'message' => 'The booking by ' . $booking->tenant->name . ' for "' . $booking->property->title . '" has been completed. The room is now available.',
                    'data' => [
                        'booking_id' => $booking->id,
                        'property_id' => $booking->property_id,
                        'tenant_name' => $booking->tenant->name,
                        'check_out_date' => $booking->check_out_date ? $booking->check_out_date->format('Y-m-d') : null
                    ],
                    'action_url' => route('bookings.index')
                ]);

                $this->info("Completed booking #{$booking->id} for {$booking->property->title}");
                Log::info("Auto-completed expired booking", [
                    'booking_id' => $booking->id,
                    'property_id' => $booking->property_id,
                    'room_id' => $booking->room_id,
                    'check_out_date' => $booking->check_out_date
                ]);

                $count++;
            } catch (\Exception $e) {
                $this->error("Failed to complete booking #{$booking->id}: {$e->getMessage()}");
                Log::error("Failed to auto-complete expired booking", [
                    'booking_id' => $booking->id,
                    'error' => $e->getMessage()
                ]);
            }
        }

        $this->info("Successfully completed {$count} expired booking(s).");
        return Command::SUCCESS;
    }
}

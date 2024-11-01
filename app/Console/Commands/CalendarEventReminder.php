<?php

namespace App\Console\Commands;

use App\Models\Event;
use App\Notifications\CalendarEvent;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CalendarEventReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifications:calendar-event-reminder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send Calendar Event Reminder';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $events = Event::where('start', Carbon::now()->format('Y-m-d'))->get();
        foreach ($events as $event) {
            $event->user->notify(new CalendarEvent($event->user, $event));
        }

        \Log::info("Calendar Event Reminder");
    }
}

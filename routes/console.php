<?php

use Illuminate\Support\Facades\Schedule;

// Schedule the reminders command daily at 8am
Schedule::command('todos:reminders')->dailyAt('08:00');
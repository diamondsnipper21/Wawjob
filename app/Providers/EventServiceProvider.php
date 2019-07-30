<?php

namespace iJobDesk\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

use DB;
use Log;
use Illuminate\Http\Request;

use iJobDesk\Models\User;
use iJobDesk\Models\File;
use iJobDesk\Models\Project;
use iJobDesk\Models\Contract;
use iJobDesk\Models\Ticket;
use iJobDesk\Models\Todo;
use iJobDesk\Models\ContractFeedback;
use iJobDesk\Models\ProjectOffer;
use iJobDesk\Models\Settings;
use iJobDesk\Models\TicketComment;
use iJobDesk\Models\TicketCommentIDVerification;
use iJobDesk\Models\AdminMessage;
use iJobDesk\Models\UserPoint;

use iJobDesk\Observers\UserObserver;
use iJobDesk\Observers\ProjectObserver;
use iJobDesk\Observers\ContractObserver;
use iJobDesk\Observers\TicketObserver;
use iJobDesk\Observers\TodoObserver;
use iJobDesk\Observers\FileObserver;
use iJobDesk\Observers\ContractFeedbackObserver;
use iJobDesk\Observers\ProjectOfferObserver;
use iJobDesk\Observers\SettingsObserver;
use iJobDesk\Observers\TicketCommentObserver;
use iJobDesk\Observers\TicketCommentIDVerificationObserver;
use iJobDesk\Observers\AdminMessageObserver;
use iJobDesk\Observers\UserPointObserver;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'iJobDesk\Events\Event' => [
            'iJobDesk\Listeners\EventListener',
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        User::observe(new UserObserver());
        Contract::observe(new ContractObserver());
        Project::observe(new ProjectObserver());
        Ticket::observe(new TicketObserver());
        Todo::observe(new TodoObserver());

        File::observe(new FileObserver());
        // File Observers
        foreach (array_pluck(File::getOptions(), 'class') as $class_name) {
            $class = 'iJobDesk\\Models\\'.$class_name;
            $class::observe(new FileObserver());
        }

        ContractFeedback::observe(new ContractFeedbackObserver());
        ProjectOffer::observe(new ProjectOfferObserver());
        Settings::observe(new SettingsObserver());
        TicketComment::observe(new TicketCommentObserver());
        TicketCommentIDVerification::observe(new TicketCommentIDVerificationObserver());
        AdminMessage::observe(new AdminMessageObserver());

        UserPoint::observe(new UserPointObserver());

        // DB::listen(
        //     function ($sql, $bindings, $time) {
        //         Log::debug($sql);
        //     }
        // );   
    }
}

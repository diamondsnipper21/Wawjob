<?php

namespace iJobDesk\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        'iJobDesk\Console\Commands\GenerateHourlyLogMaps',
        'iJobDesk\Console\Commands\ProcessReviewTransactions',
        'iJobDesk\Console\Commands\ProcessPendingTransactions',
        'iJobDesk\Console\Commands\ReviewLastWeek',
        'iJobDesk\Console\Commands\ProcessJobs',
        'iJobDesk\Console\Commands\ProcessContracts',
        'iJobDesk\Console\Commands\ProcessUserStats',
        'iJobDesk\Console\Commands\ProcessUserSkillPoints',
        'iJobDesk\Console\Commands\ProcessUserPoints',
        'iJobDesk\Console\Commands\ProcessUserConnections',
        'iJobDesk\Console\Commands\ProcessAffiliates',
        'iJobDesk\Console\Commands\ProcessDeposits',
        'iJobDesk\Console\Commands\CheckWithdraws',
        'iJobDesk\Console\Commands\CheckTransactions',
        'iJobDesk\Console\Commands\ProcessWithdraws',
        'iJobDesk\Console\Commands\ProcessSiteWithdraws',
        'iJobDesk\Console\Commands\ProcessUserPaymentMethods',
        'iJobDesk\Console\Commands\ProcessUserCreditCards',
        'iJobDesk\Console\Commands\ProcessAffiliateTransactions',
        'iJobDesk\Console\Commands\JobRecommendation',
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // crontab -e
        // * * * * * php /path-to-your-project/artisan schedule:run >> /dev/null 2>&1

        $schedule->command('JobRecommendation:run')->everyFifteenMinutes();
        
        $schedule->command('GenerateHourlyLogMaps:run')->hourly();
        $schedule->command('ProcessAffiliates:run')->hourlyAt(20);
        $schedule->command('ProcessDeposits:run')->hourlyAt(30);
        //$schedule->command('ProcessWithdraws:run')->hourlyAt(30);
        //$schedule->command('ProcessSiteWithdraws:run')->hourlyAt(30);
        $schedule->command('ProcessUserProjects:run')->hourlyAt(40);

        $schedule->command('CheckWithdraws:run')->daily();
        $schedule->command('ProcessPendingTransactions:run')->dailyAt('1:00');
        $schedule->command('ProcessUserPaymentMethods:run')->dailyAt('2:00');
        // $schedule->command('ProcessAffiliateTransactions:run')->dailyAt('3:00');
        $schedule->command('ProcessJobs:run')->dailyAt('4:00');
        $schedule->command('ProcessUserConnections:run')->dailyAt('22:00');
        $schedule->command('ProcessUserPoints:run')->dailyAt('23:00');
        $schedule->command('CheckTransactions:run')->dailyAt('23:30');

        $schedule->command('ReviewLastWeek:run')->weekly()->mondays()->at('0:10');
        $schedule->command('ProcessContracts:run')->weekly()->mondays()->at('0:20');
        $schedule->command('ProcessReviewTransactions:run')->weekly()->saturdays()->at('0:00');
        $schedule->command('ProcessUserStats:run')->weekly()->sundays()->at('0:00');

        // $schedule->command('ProcessUserSkillPoints:run')->monthlyOn(1, '01:00');
        // $schedule->command('ProcessUserCreditCards:run')->monthlyOn(1, '00:10');

        // Backup Server
        $schedule->command('backup:run')->daily();

        // Remove unnessary or expired files.
        $schedule->command('RemoveExpiredFiles:run')->hourly();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}

<?php

namespace App\Console\Commands;

use App\Modules\Cms\Models\Menu;
use App\Modules\Cms\Models\MenuItem;
use App\Modules\Cms\Models\MenuItemTranslation;
use App\Modules\Cms\Models\MenuTranslation;
use App\Modules\Payments\Models\Transaction;
use App\Modules\Users\Models\Permission;
use App\Modules\Users\Models\PermissionTranslation;
use App\Modules\Users\Models\Role;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class MigrateTransactions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'forlearn:migrate-transactions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'MigrateTransactions';

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
        dd('Are you sure you want to do this?');

        $transactions = Transaction::get();

        $transactions->each(static function ($transaction) {
            $transaction->article_request()
                ->attach($transaction->article_request_id, ['value' => $transaction->value]);
        });
    }
}

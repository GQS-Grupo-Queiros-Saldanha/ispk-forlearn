<?php

namespace App\Console\Commands;

use App\Modules\Cms\Models\Menu;
use App\Modules\Cms\Models\MenuItem;
use App\Modules\Cms\Models\MenuItemTranslation;
use App\Modules\Cms\Models\MenuTranslation;
use App\Modules\Users\Models\Permission;
use App\Modules\Users\Models\PermissionTranslation;
use App\Modules\Users\Models\Role;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class ScaffoldPaymentsModule extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'forlearn:scaffold-payments-module';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scaffolds all database modifications needed for the Payments Module';

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

        // drop old payments table
        Schema::drop('payments');
        echo 'dropped payments table' . PHP_EOL;

        // create new tables from migrations
        Artisan::call('migrate --path=database/migrations/2019_11_02_170000_create_articles_table.php');
        Artisan::call('migrate --path=database/migrations/2019_11_02_170001_create_article_translations_table.php');
        Artisan::call('migrate --path=database/migrations/2019_11_02_170002_create_payments_table.php');
        Artisan::call('migrate --path=database/migrations/2019_11_02_180431_create_article_monthly_charges_table.php');
        Artisan::call('migrate --path=database/migrations/2019_11_02_180446_create_article_extra_fees_table.php');
        echo 'ran payments migrations' . PHP_EOL;

        // seed articles
        // Artisan::call('db:seed --class=ArticlesTableSeeder');
        // echo 'seeded articles' . PHP_EOL;

        // create new permissions
        $manageArticlesId = $this->createPermission('manage-articles', 'Gerir emolumentos / propinas');
        $managePaymentsId = $this->createPermission('manage-payments', 'Gerir pagamentos');
        $managePaymentsOthersId = $this->createPermission('manage-payments-others', 'Gerir pagamentos por outros utilizadores');
        $manageManualPaymentsId = $this->createPermission('manage-manual-payments', 'Marcar manualmente pagamento como pago');
        $createRequestsSelfId = $this->createPermission('create-requests-self', 'Criar próprios requerimentos');
        $createRequestsOthersId = $this->createPermission('create-requests-others', 'Criar requerimentos por outros utlilizadores');
        echo 'created new permissions' . PHP_EOL;

        // give new permissions to superadmin
        $superAdminUpdatedPermissions = array_merge(
            DB::table('role_has_permissions')
                ->where('role_id', '=', '2')
                ->pluck('permission_id')
                ->all(),
            [
                $manageArticlesId,
                $managePaymentsId,
                $managePaymentsOthersId,
                $manageManualPaymentsId,
                $createRequestsSelfId,
                $createRequestsOthersId
            ]);
        $role = Role::whereId(2)->firstOrFail();
        $role->syncPermissions($superAdminUpdatedPermissions);
        echo 'added created permissions to superadmin role' . PHP_EOL;

        // sortMenus
        $lastPosition = $this->sortMenus();
        echo 'added order values to existing menus' . PHP_EOL;

        // create new menu
        $menuPaymentsId = $this->createMenu('menu-payments', $lastPosition - 1, 'Menu Pagamentos');
        echo 'created new menu' . PHP_EOL;

        // create new menu items
        $menuItemPaymentsId = $this->createMenuItem('payment', null, $menuPaymentsId, null, 0, 'Pagamentos', [$managePaymentsId]);
        $this->createMenuItem('articles', '/payments/articles', $menuPaymentsId, $menuItemPaymentsId, 1, 'Emolumentos / Propinas', [$manageArticlesId]);
        $this->createMenuItem('account', '/payments/account', $menuPaymentsId, $menuItemPaymentsId, 2, 'Conta Corrente', [$managePaymentsId]);
        echo 'created new menu items' . PHP_EOL;

        echo 'Payments scaffolding done!' . PHP_EOL;
        return true;
    }

    protected function createPermission($code, $display_name)
    {
        DB::beginTransaction();

        $permission = Permission::create([
            'name' => $code,
            'guard_name' => 'web',
            'created_by' => 1
        ]);

        $permission->save();

        PermissionTranslation::create([
            'permission_id' => $permission->id,
            'language_id' => 1,
            'display_name' => $display_name,
            'description' => $display_name,
            'created_at' => Carbon::now(),
            'version' => 1,
            'active' => true
        ]);

        DB::commit();

        return $permission->id;
    }

    protected function createMenu($code, $order, $display_name)
    {
        DB::beginTransaction();

        // Create
        $menu = Menu::create([
            'code' => $code,
            'order' => $order,
            'created_by' => 1
        ]);

        $menu->save();

        MenuTranslation::create([
            'menus_id' => $menu->id,
            'language_id' => 1,
            'display_name' => $display_name,
            'description' => $display_name,
            'created_at' => Carbon::now(),
            'version' => 1,
            'active' => true
        ]);

        DB::commit();

        return $menu->id;
    }

    protected function createMenuItem($code, $link, $menu, $parent, $position, $display_name, $permissions)
    {
        DB::beginTransaction();

        // Create
        $menu_item = MenuItem::create([
            'code' => $code,
            'external_link' => $link,
            'menus_id' => $menu,
            'parent_id' => $parent,
            'position' => $position,
            'created_by' => 1
        ]);

        $menu_item->save();

        MenuItemTranslation::create([
            'menu_items_id' => $menu_item->id,
            'language_id' => 1,
            'display_name' => $display_name,
            'description' => $display_name,
            'created_at' => Carbon::now(),
            'version' => 1,
            'active' => true
        ]);

        $menu_item->syncPermissions($permissions);

        DB::commit();

        return $menu_item->id;
    }

    protected function sortMenus()
    {
        $menus = Menu::orderBy('order')->orderBy('id')->get();

        $lastIndex = $menus->count() - 1;
        $order = 0;
        $menus->each(function ($menu, $idx) use (&$order, $lastIndex) {
            if ($menu->order === 0) {
                // skip one position for the last one
                $menu->order = $lastIndex === $idx ? ++$order : $order++;
                $menu->save();
            }
        });

        return $order - 1;
    }
}

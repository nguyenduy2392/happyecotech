<?php

namespace App\Console\Commands;

use App\Models\AuditLog;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class SetupSuperAdmin extends Command
{
    protected $signature = 'he:superadmin
        {--email= : Email (mặc định lấy HE_ADMIN_EMAIL)}
        {--password= : Mật khẩu mới (mặc định lấy HE_ADMIN_PASSWORD)}
        {--name= : Tên hiển thị (mặc định lấy HE_ADMIN_NAME)}';

    protected $description = 'Tạo mới hoặc ĐẶT LẠI tài khoản SuperAdmin từ tham số hoặc biến môi trường.';

    public function handle(): int
    {
        $email = $this->option('email') ?: env('HE_ADMIN_EMAIL', 'admin@happyecotech.com');
        $password = $this->option('password') ?: env('HE_ADMIN_PASSWORD');
        $name = $this->option('name') ?: env('HE_ADMIN_NAME', 'Super Admin');

        if (blank($password)) {
            $this->error('Chưa có mật khẩu. Đặt HE_ADMIN_PASSWORD trong .env hoặc dùng --password=...');

            return self::FAILURE;
        }

        // Đảm bảo role/permission đã tồn tại.
        if (! Role::where('name', 'SuperAdmin')->exists()) {
            $this->info('Chưa có role SuperAdmin — chạy RolePermissionSeeder...');
            $this->callSilent('db:seed', ['--class' => RolePermissionSeeder::class, '--force' => true]);
        }

        $user = User::firstOrNew(['email' => $email]);
        $existed = $user->exists;

        $user->name = $name;
        $user->password = Hash::make($password); // LUÔN đặt lại mật khẩu (khác với seeder).
        $user->is_active = true;
        $user->email_verified_at ??= now();
        $user->save();
        $user->syncRoles(['SuperAdmin']);

        AuditLog::record($existed ? 'updated' : 'created', $user,
            ($existed ? 'Đặt lại' : 'Tạo').' tài khoản SuperAdmin qua CLI', [], $user->id);

        $this->info(($existed ? 'Đã ĐẶT LẠI' : 'Đã TẠO').' SuperAdmin: '.$email);
        $this->line('Hãy đăng nhập và đổi mật khẩu ngay sau đó.');

        return self::SUCCESS;
    }
}

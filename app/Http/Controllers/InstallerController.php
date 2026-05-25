<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class InstallerController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Welcome Page
    |--------------------------------------------------------------------------
    */

    public function welcome()
    {
        return view('installer.welcome');
    }

    /*
    |--------------------------------------------------------------------------
    | Requirements Check
    |--------------------------------------------------------------------------
    */

    public function requirements()
    {
        $requirements = [
            'PHP >= 8.3'               => version_compare(PHP_VERSION, '8.3', '>='),
            'OpenSSL Extension'        => extension_loaded('openssl'),
            'PDO Extension'            => extension_loaded('pdo'),
            'Mbstring Extension'       => extension_loaded('mbstring'),
            'Tokenizer Extension'      => extension_loaded('tokenizer'),
            'JSON Extension'           => extension_loaded('json'),
            'CURL Extension'           => extension_loaded('curl'),
            'GD Extension'             => extension_loaded('gd'),
            'Fileinfo Extension'       => extension_loaded('fileinfo'),
            'XML Extension'            => extension_loaded('xml'),
            'Zip Extension'            => extension_loaded('zip'),
            'PostgreSQL Extension'     => extension_loaded('pgsql'),
        ];

        $allPassed = !in_array(false, $requirements, true);

        return view('installer.requirements', compact(
            'requirements',
            'allPassed'
        ));
    }

    /*
    |--------------------------------------------------------------------------
    | Environment Form
    |--------------------------------------------------------------------------
    */

    public function environment()
    {
        return view('installer.environment');
    }

    /*
    |--------------------------------------------------------------------------
    | Process Installation
    |--------------------------------------------------------------------------
    */

    public function process(Request $request)
    {
        // Buat log file sederhana
        $log = storage_path('install_debug.log');

        file_put_contents($log, "=== START INSTALL ===\n", FILE_APPEND);
        file_put_contents($log, "Time: " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
        file_put_contents($log, "POST Data: " . json_encode($request->all()) . "\n", FILE_APPEND);

        try {
            file_put_contents($log, "Step 1: Validasi dimulai\n", FILE_APPEND);

            $request->validate([
                'admin_name' => 'required|string|max:100',
                'admin_email' => 'required|email',
                'admin_password' => 'required|min:8|confirmed',
                'admin_nik' => 'required|string|size:16',
                'admin_phone' => 'required|string|max:15',
            ]);

            file_put_contents($log, "Step 2: Validasi OK\n", FILE_APPEND);

            // Test database connection
            file_put_contents($log, "Step 3: Test DB Connection\n", FILE_APPEND);
            try {
                DB::connection()->getPdo();
                file_put_contents($log, "Step 4: DB Connection OK\n", FILE_APPEND);
            } catch (\Exception $e) {
                file_put_contents($log, "Step 4 ERROR: " . $e->getMessage() . "\n", FILE_APPEND);
                throw $e;
            }

            // Jalankan migrasi
            file_put_contents($log, "Step 5: Running migration\n", FILE_APPEND);
            Artisan::call('migrate:fresh', ['--force' => true]);
            file_put_contents($log, "Step 6: Migration done\n", FILE_APPEND);

            // Buat user
            file_put_contents($log, "Step 7: Creating user\n", FILE_APPEND);
            $user = User::create([
                'name' => $request->admin_name,
                'email' => $request->admin_email,
                'password' => Hash::make($request->admin_password),
                'nik' => $request->admin_nik,
                'no_hp' => $request->admin_phone,
            ]);
            file_put_contents($log, "Step 8: User created ID: " . ($user->id ?? 'null') . "\n", FILE_APPEND);

            // Buat file installed
            file_put_contents($log, "Step 9: Creating installed file\n", FILE_APPEND);
            File::put(storage_path('installed'), date('Y-m-d H:i:s'));
            file_put_contents($log, "Step 10: DONE!\n", FILE_APPEND);

            return redirect('/install/final')->with('success', 'Instalasi berhasil!');
        } catch (\Exception $e) {
            file_put_contents($log, "ERROR: " . $e->getMessage() . "\n", FILE_APPEND);
            file_put_contents($log, "File: " . $e->getFile() . ":" . $e->getLine() . "\n", FILE_APPEND);
            file_put_contents($log, "Trace: " . $e->getTraceAsString() . "\n", FILE_APPEND);

            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Final Page
    |--------------------------------------------------------------------------
    */

    public function final()
    {
        return view('installer.final');
    }

    /*
    |--------------------------------------------------------------------------
    | Update .env File
    |--------------------------------------------------------------------------
    */
    private function updateEnvFile(array $data): void
    {
        $path = base_path('.env');

        if (!File::exists($path)) {
            throw new \Exception('.env file tidak ditemukan');
        }

        $content = File::get($path);

        foreach ($data as $key => $value) {

            $value = trim((string) $value);

            /*
        |--------------------------------------------------------------------------
        | Quote value jika mengandung whitespace/special chars
        |--------------------------------------------------------------------------
        */

            if (preg_match('/\s/', $value)) {
                $value = '"' . addslashes($value) . '"';
            }

            $pattern = "/^{$key}=.*/m";

            $line = "{$key}={$value}";

            if (preg_match($pattern, $content)) {

                $content = preg_replace(
                    $pattern,
                    $line,
                    $content
                );
            } else {

                $content .= PHP_EOL . $line;
            }
        }

        File::put($path, $content);
    }
}

<?php

namespace Tests\Feature;

use Symfony\Component\Process\Process;
use Tests\TestCase;

class DeployTerminalTest extends TestCase
{
    public function test_deploy_script_file_exists_in_public(): void
    {
        $this->assertFileExists(public_path('deploy.php'));
    }

    public function test_deploy_script_blocks_login_when_deploy_key_is_not_configured(): void
    {
        $process = new Process([
            PHP_BINARY,
            '-r',
            '$_SERVER["REQUEST_METHOD"] = "GET"; $_SERVER["REQUEST_URI"] = "/deploy.php"; $_SERVER["REMOTE_ADDR"] = "127.0.0.1"; ob_start(); require "public/deploy.php"; $out = ob_get_clean(); echo str_contains($out, "DEPLOY_KEY Belum Diatur") ? "SECURE_BLOCKED" : "VULNERABLE";'
        ], base_path());

        $process->run();
        $this->assertStringContainsString('SECURE_BLOCKED', $process->getOutput());
    }

    public function test_deploy_script_contains_security_headers_and_anti_injection(): void
    {
        $content = file_get_contents(public_path('deploy.php'));
        
        $this->assertStringContainsString('X-Robots-Tag', $content);
        $this->assertStringContainsString('X-Frame-Options', $content);
        $this->assertStringContainsString('hash_equals', $content);
        $this->assertStringContainsString('session_regenerate_id', $content);
        $this->assertStringContainsString('escapeshellarg', $content);
        $this->assertStringContainsString('git_pull', $content);
        $this->assertStringContainsString('git_force_sync', $content);
        $this->assertStringContainsString('db_migrate', $content);
        $this->assertStringContainsString('storage_fix_permissions', $content);
        $this->assertStringContainsString('maintenance_toggle', $content);
    }

    public function test_deploy_script_authenticates_with_valid_key_and_renders_dashboard(): void
    {
        // Simulasi session dengan kunci yang valid
        $testCode = '
            $_SERVER["REQUEST_METHOD"] = "GET";
            $_SERVER["REQUEST_URI"] = "/deploy.php";
            $_SERVER["REMOTE_ADDR"] = "127.0.0.1";
            session_start();
            $_SESSION["gurukuu_devops_auth"] = true;
            $_SESSION["gurukuu_devops_login_time"] = time();
            $_SESSION["gurukuu_devops_csrf"] = "test_csrf_token_1234567890";
            ob_start();
            require "public/deploy.php";
            $out = ob_get_clean();
            if (str_contains($out, "Otomasi Git Deployment") && str_contains($out, "Manajemen Database & Migrasi")) {
                echo "DASHBOARD_RENDERED";
            } else {
                echo "FAILED_RENDER";
            }
        ';

        $process = new Process([PHP_BINARY, '-r', $testCode], base_path());
        $process->run();
        $this->assertStringContainsString('DASHBOARD_RENDERED', $process->getOutput());
    }
}

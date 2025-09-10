<?php

namespace Tests;

use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\TestCase as BaseTestCase;

abstract class DuskTestCase extends BaseTestCase
{
    use CreatesApplication, DatabaseMigrations;

    /**
     * Prepare for Dusk test execution.
     *
     * @beforeClass
     */
    public static function prepare(): void
    {
        // Skip starting ChromeDriver - we'll handle it differently
    }

    /**
     * Create the RemoteWebDriver instance.
     */
    protected function driver(): RemoteWebDriver
    {
        $options = (new ChromeOptions)->addArguments([
            '--headless',
            '--no-sandbox',
            '--disable-dev-shm-usage',
            '--disable-extensions',
            '--disable-web-security',
            '--disable-gpu',
            '--window-size=1920,1080',
            '--remote-debugging-port=9222',
        ]);

        // Try to start ChromeDriver if not running
        $this->ensureChromeDriverIsRunning();

        return RemoteWebDriver::create(
            $_ENV['DUSK_DRIVER_URL'] ?? env('DUSK_DRIVER_URL') ?? 'http://localhost:9515',
            DesiredCapabilities::chrome()->setCapability(
                ChromeOptions::CAPABILITY, $options
            )
        );
    }

    /**
     * Ensure ChromeDriver is running.
     */
    protected function ensureChromeDriverIsRunning(): void
    {
        // Try to start ChromeDriver in the background
        $chromeDriverPath = base_path('vendor/laravel/dusk/bin/chromedriver-mac-arm64');

        if (file_exists($chromeDriverPath)) {
            // Make it executable
            chmod($chromeDriverPath, 0755);

            // Start ChromeDriver in background if not already running
            $output = shell_exec('lsof -ti:9515');
            if (empty(trim($output))) {
                shell_exec("nohup {$chromeDriverPath} --port=9515 > /dev/null 2>&1 & echo \$!");
                sleep(2); // Give it time to start
            }
        }
    }

    /**
     * Determine whether the Dusk command has disabled headless mode.
     */
    protected function hasHeadlessDisabled(): bool
    {
        return isset($_SERVER['DUSK_HEADLESS_DISABLED']) ||
               isset($_ENV['DUSK_HEADLESS_DISABLED']);
    }

    /**
     * Determine if the browser window should start maximized.
     */
    protected function shouldStartMaximized(): bool
    {
        return isset($_SERVER['DUSK_START_MAXIMIZED']) ||
               isset($_ENV['DUSK_START_MAXIMIZED']);
    }
}

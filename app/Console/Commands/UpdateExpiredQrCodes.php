<?php

namespace App\Console\Commands;

use App\Models\QrCode;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class UpdateExpiredQrCodes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'qrcode:update-expired {--force : Force update even if no expired codes found}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update QR codes that have passed their expiry date to EXPIRED status';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Checking for expired QR codes...');

        try {
            // Get count of QR codes that need to be updated
            $expiredCount = QrCode::where('status', QrCode::STATUS_ACTIVE)
                ->where('expiry_date', '<=', now())
                ->count();

            if ($expiredCount === 0 && !$this->option('force')) {
                $this->info('No expired QR codes found.');
                return self::SUCCESS;
            }

            // Update expired QR codes
            $updatedCount = QrCode::updateExpiredQrCodes();

            if ($updatedCount > 0) {
                $this->info("Successfully updated {$updatedCount} expired QR codes to EXPIRED status.");

                // Log the operation
                Log::info('Batch QR code expiry update completed', [
                    'updated_count' => $updatedCount,
                    'executed_at' => now(),
                    'executed_by' => 'artisan_command'
                ]);
            } else {
                $this->info('No QR codes needed updating.');
            }

            // Show summary statistics
            $this->showStatistics();

            return self::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Failed to update expired QR codes: ' . $e->getMessage());

            Log::error('QR code expiry update command failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return self::FAILURE;
        }
    }

    /**
     * Show QR code statistics after update
     */
    private function showStatistics(): void
    {
        $statistics = [
            'Active' => QrCode::where('status', QrCode::STATUS_ACTIVE)->count(),
            'Expired' => QrCode::where('status', QrCode::STATUS_EXPIRED)->count(),
            'Used' => QrCode::where('status', QrCode::STATUS_USED)->count(),
            'Invalid' => QrCode::where('status', QrCode::STATUS_INVALID)->count(),
        ];

        $this->newLine();
        $this->info('Current QR Code Statistics:');
        $this->table(
            ['Status', 'Count'],
            collect($statistics)->map(function ($count, $status) {
                return [$status, $count];
            })->toArray()
        );

        // Show expiring soon count
        $expiringSoon = QrCode::where('status', QrCode::STATUS_ACTIVE)
            ->whereBetween('expiry_date', [now(), now()->addHours(24)])
            ->count();

        if ($expiringSoon > 0) {
            $this->warn("⚠️  {$expiringSoon} QR codes will expire within the next 24 hours.");
        }
    }
}

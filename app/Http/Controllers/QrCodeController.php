<?php

namespace App\Http\Controllers;

use App\Models\QrCode;
use App\Models\Vendor;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use Illuminate\Support\Str;
use chillerlan\QRCode\QRCode as QRGenerator;
use chillerlan\QRCode\QROptions;
use Illuminate\Support\Facades\Redirect;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class QrCodeController extends Controller
{
    /**
     * QR Code configuration constants
     */
    private const QR_SIZE = 400;
    private const QR_MARGIN = 20;
    private const TEMPLATE_WIDTH = 600;
    private const TEMPLATE_HEIGHT = 800;

    /**
     * Default expiry hours for QR codes
     */
    private const DEFAULT_EXPIRY_HOURS = 24;
    private const MAX_EXPIRY_HOURS = 168; // 7 days

    /**
     * Display a listing of QR codes with filtering.
     */
    public function index(Request $request): View
    {
        // First, update all expired QR codes
        QrCode::updateExpiredQrCodes();

        $query = QrCode::with(['vendor.user', 'service'])
            ->when($request->status, function ($q, $status) {
                return $q->where('status', $status);
            })
            ->when($request->vendor_id, function ($q, $vendorId) {
                return $q->where('vendor_id', $vendorId);
            })
            ->when($request->expired === '1', function ($q) {
                return $q->where('expiry_date', '<=', now());
            })
            ->when($request->expiring_soon === '1', function ($q) {
                return $q->where('status', 'ACTIVE')
                    ->whereBetween('expiry_date', [now(), now()->addHours(24)]);
            });

        $qrCodes = $query->orderBy('status')
            ->orderBy('expiry_date', 'desc')
            ->paginate(20);

        $vendors = Vendor::with('user')->orderBy('business_name')->get();
        $statistics = $this->getQrCodeStatistics();

        return view('pages.qrcode.qrcode', compact('qrCodes', 'vendors', 'statistics'));
    }

    /**
     * Show the form for creating a new QR code.
     */
    public function create(): View
    {
        $vendors = Vendor::with(['user', 'services' => function ($query) {
            $query->where('is_available', true)->orderBy('name');
        }])->orderBy('business_name')->get();

        $services = Service::with('vendor')->where('is_available', true)->get();
        $templates = $this->getAvailableTemplates();

        return view('pages.qrcode.create-qrcode', compact('vendors', 'services', 'templates'));
    }

    /**
     * Store a newly created QR code.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateQrCodeData($request);

        DB::beginTransaction();

        try {
            $service = Service::with('vendor')->findOrFail($validated['service_id']);

            // Generate unique QR code
            $uniqueCode = $this->generateUniqueCode();
            $generatedDate = Carbon::now();

            // Fix: Cast expiry_hours to integer
            $expiryHours = (int) $validated['expiry_hours'];
            $expiryDate = $generatedDate->copy()->addHours($expiryHours);

            // Prepare service details
            $serviceDetails = $this->prepareServiceDetails($service);

            // Create QR code record
            $qrCode = QrCode::create([
                'code' => $uniqueCode,
                'service_details' => $serviceDetails,
                'generated_date' => $generatedDate,
                'expiry_date' => $expiryDate,
                'status' => QrCode::STATUS_ACTIVE,
                'vendor_id' => $validated['vendor_id'],
                'service_id' => $validated['service_id']
            ]);

            // Generate QR code image and save to storage with proper error handling
            $this->generateAndSaveQrCodeImage($qrCode, $validated['template'] ?? 'default');

            // Log QR code generation
            $this->logQrCodeActivity($qrCode, 'generated');

            DB::commit();

            return redirect()
                ->route('qrcode.show', $qrCode->id)
                ->with('success', 'QR Code generated successfully!');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('QR Code generation failed: ' . $e->getMessage(), [
                'request_data' => $request->all(),
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['error' => 'Failed to generate QR code: ' . $e->getMessage()]);
        }
    }

    /**
     * Display the specified QR code.
     */
    public function show($id): View|RedirectResponse
    {
        try {
            $qrCode = QrCode::with(['vendor.user', 'service', 'transactions'])->findOrFail($id);

            if (!$qrCode || !$qrCode->exists) {
                abort(404, 'QR Code not found');
            }

            $imageExists = $this->checkQrCodeImageExists($qrCode);
            $usageStats = $this->getQrCodeUsageStats($qrCode);

            return view('pages.qrcode.show-qrcode', compact('qrCode', 'imageExists', 'usageStats'));
        } catch (\Exception $e) {
            Log::error('QR Code show failed: ' . $e->getMessage(), [
                'qr_code_id' => $id,
                'exception' => $e
            ]);

            return redirect()
                ->route('qrcode.index')
                ->withErrors(['error' => 'QR Code not found or could not be loaded.']);
        }
    }

    /**
     * Show the form for editing the specified QR code.
     */
    public function edit($id): View|RedirectResponse
    {
        try {
            $qrCode = QrCode::with(['vendor.user', 'service'])->findOrFail($id);

            if (!$qrCode || !$qrCode->exists) {
                abort(404, 'QR Code not found');
            }

            return view('pages.qrcode.edit-qrcode', compact('qrCode'));
        } catch (\Exception $e) {
            Log::error('QR Code edit failed: ' . $e->getMessage(), [
                'qr_code_id' => $id,
                'exception' => $e
            ]);

            return redirect()
                ->route('qrcode.index')
                ->withErrors(['error' => 'QR Code not found or could not be loaded.']);
        }
    }

    /**
     * Update the specified QR code.
     * FIXED: Now properly updates expiry date and status
     */
    public function update(Request $request, $id): RedirectResponse
    {
        // Find the QR code explicitly with proper error handling
        try {
            $qrCode = QrCode::findOrFail($id);
        } catch (\Exception $e) {
            Log::error('QR Code not found for update', [
                'id' => $id,
                'error' => $e->getMessage()
            ]);

            return redirect()
                ->route('qrcode.index')
                ->withErrors(['error' => 'QR Code not found.']);
        }

        $validated = $this->validateQrCodeUpdate($request);

        // Debug logging
        Log::info('QR Code update attempt', [
            'qr_code_id' => $qrCode->id,
            'current_status' => $qrCode->status,
            'current_expiry' => $qrCode->expiry_date,
            'current_generated' => $qrCode->generated_date,
            'new_status' => $validated['status'],
            'new_expiry_hours' => $validated['expiry_hours']
        ]);

        DB::beginTransaction();

        try {
            $oldStatus = $qrCode->status;
            $oldExpiryDate = $qrCode->expiry_date;

            // Get the generated date, use current time if null
            $generatedDate = $qrCode->generated_date ?
                Carbon::parse($qrCode->generated_date) :
                Carbon::now();

            // Prepare update data
            $updateData = [];

            // If generated_date was null, update it
            if (!$qrCode->generated_date) {
                $updateData['generated_date'] = $generatedDate;
                Log::info('QR Code generated_date was null, setting to current time', [
                    'qr_code_id' => $qrCode->id,
                    'new_generated_date' => $generatedDate
                ]);
            }

            // Handle reactivation logic
            if ($validated['status'] === 'ACTIVE' && $oldStatus !== 'ACTIVE') {
                // For reactivation, use current time as base for expiry calculation
                $baseTime = Carbon::now();
                Log::info('Reactivating QR code, using current time as base', [
                    'qr_code_id' => $qrCode->id,
                    'base_time' => $baseTime,
                    'old_status' => $oldStatus
                ]);
            } else {
                // For normal updates, use the original generated date
                $baseTime = $generatedDate;
            }

            // Calculate new expiry date
            $expiryHours = (int) $validated['expiry_hours'];
            $newExpiryDate = $baseTime->copy()->addHours($expiryHours);

            // Update the QR code data
            $updateData['status'] = $validated['status'];
            $updateData['expiry_date'] = $newExpiryDate;

            // For reactivation, also update the generated_date to current time
            if ($validated['status'] === 'ACTIVE' && $oldStatus !== 'ACTIVE') {
                $updateData['generated_date'] = Carbon::now();
                Log::info('Updating generated_date for reactivation', [
                    'qr_code_id' => $qrCode->id,
                    'new_generated_date' => $updateData['generated_date']
                ]);
            }

            // Final validation: If setting to ACTIVE and expiry is still in the past, prevent it
            if ($validated['status'] === 'ACTIVE' && $newExpiryDate <= now()) {
                DB::rollback();
                Log::warning('Cannot activate QR code with past expiry date', [
                    'qr_code_id' => $qrCode->id,
                    'new_expiry_date' => $newExpiryDate,
                    'current_time' => now()
                ]);

                return redirect()
                    ->back()
                    ->withInput()
                    ->withErrors(['error' => 'Cannot activate QR code with expiry date in the past. Please select a longer validity period.']);
            }

            // Perform the update
            $qrCode->update($updateData);

            // Log changes
            $this->logQrCodeActivity($qrCode, 'updated', [
                'old_status' => $oldStatus,
                'new_status' => $updateData['status'],
                'old_expiry_date' => $oldExpiryDate ? $oldExpiryDate->toISOString() : 'null',
                'new_expiry_date' => $newExpiryDate->toISOString(),
                'expiry_hours' => $expiryHours,
                'was_reactivation' => ($validated['status'] === 'ACTIVE' && $oldStatus !== 'ACTIVE'),
                'base_time_used' => $baseTime->toISOString(),
                'generated_date_updated' => isset($updateData['generated_date'])
            ]);

            DB::commit();

            // Build success message
            $message = 'QR Code updated successfully!';

            if ($validated['status'] === 'ACTIVE' && $oldStatus !== 'ACTIVE') {
                $message = 'QR Code reactivated successfully! New expiry: ' . $newExpiryDate->format('d M Y, H:i');
            }

            if (!$qrCode->getOriginal('generated_date')) {
                $message .= ' Generated date was set to current time.';
            }

            Log::info('QR Code update completed successfully', [
                'qr_code_id' => $qrCode->id,
                'final_status' => $qrCode->fresh()->status,
                'final_expiry' => $qrCode->fresh()->expiry_date
            ]);

            return redirect()
                ->route('qrcode.show', $qrCode->id)
                ->with('success', $message);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('QR Code update failed: ' . $e->getMessage(), [
                'qr_code_id' => $qrCode->id,
                'validated_data' => $validated,
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['error' => 'Failed to update QR code: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified QR code.
     * FIXED: Now allows deletion even with completed transactions (cascade delete)
     */
    public function destroy(QrCode $qrcode): RedirectResponse
    {
        try {
            // Log deletion attempt
            Log::info('Attempting to delete QR Code', [
                'qr_code_id' => $qrcode->id,
                'qr_code_code' => $qrcode->code,
                'vendor_id' => $qrcode->vendor_id,
                'service_id' => $qrcode->service_id
            ]);

            DB::beginTransaction();

            // Delete QR code image file first (if exists)
            $this->deleteQrCodeImage($qrcode);

            // Use Eloquent to handle cascade deletion properly
            // First, delete related transactions
            $qrcode->transactions()->delete();

            // Then delete the QR code itself
            $qrcode->delete();

            DB::commit();

            Log::info('QR Code deleted successfully', [
                'qr_code_id' => $qrcode->id,
                'method' => 'eloquent_with_cascade'
            ]);

            return redirect()
                ->route('qrcode.index')
                ->with('success', 'QR Code deleted successfully!');
        } catch (\Exception $e) {
            DB::rollback();

            Log::error('QR Code deletion failed with exception: ' . $e->getMessage(), [
                'qr_code_id' => $qrcode->id ?? 'unknown',
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()
                ->back()
                ->withErrors(['error' => 'Failed to delete QR code: ' . $e->getMessage()]);
        }
    }

    /**
     * Download QR code as PNG image.
     */
    public function download(QrCode $qrCode): \Symfony\Component\HttpFoundation\StreamedResponse|RedirectResponse
    {
        try {
            $imagePath = $this->getQrCodeImagePath($qrCode);

            if (!Storage::exists($imagePath)) {
                // Regenerate image if it doesn't exist
                $this->generateAndSaveQrCodeImage($qrCode, 'default');
            }

            $fileName = $this->generateQrCodeFileName($qrCode);

            return Storage::download($imagePath, $fileName, [
                'Content-Type' => 'image/png',
                'Content-Disposition' => 'attachment; filename="' . $fileName . '"'
            ]);
        } catch (\Exception $e) {
            Log::error('QR Code download failed: ' . $e->getMessage(), [
                'qr_code_id' => $qrCode->id,
                'exception' => $e
            ]);

            return redirect()
                ->back()
                ->withErrors(['error' => 'Failed to download QR code. Please try again.']);
        }
    }

    /**
     * Display QR code image inline.
     */
    public function image(QrCode $qrCode): \Illuminate\Http\Response|RedirectResponse
    {
        try {
            $imagePath = $this->getQrCodeImagePath($qrCode);

            if (!Storage::exists($imagePath)) {
                // Regenerate image if it doesn't exist
                $this->generateAndSaveQrCodeImage($qrCode, 'default');
            }

            $imageContent = Storage::get($imagePath);

            return Response::make($imageContent, 200, [
                'Content-Type' => 'image/png',
                'Cache-Control' => 'public, max-age=3600',
                'Expires' => gmdate('D, d M Y H:i:s', time() + 3600) . ' GMT'
            ]);
        } catch (\Exception $e) {
            Log::error('QR Code image display failed: ' . $e->getMessage(), [
                'qr_code_id' => $qrCode->id,
                'exception' => $e
            ]);

            return redirect()
                ->back()
                ->withErrors(['error' => 'Failed to display QR code image.']);
        }
    }

    /**
     * Get services for a specific vendor (AJAX endpoint).
     */
    public function getVendorServices(Vendor $vendor): \Illuminate\Http\JsonResponse
    {
        try {
            $services = $vendor->services()
                ->where('is_available', true)
                ->orderBy('name')
                ->get(['id', 'name', 'price', 'category']);

            return response()->json([
                'success' => true,
                'services' => $services
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to get vendor services: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to load services.'
            ], 500);
        }
    }

    /**
     * Bulk expire QR codes.
     */
    public function bulkExpire(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'qr_code_ids' => 'required|array|min:1',
            'qr_code_ids.*' => 'exists:qr_codes,id'
        ]);

        try {
            $updated = QrCode::whereIn('id', $validated['qr_code_ids'])
                ->where('status', QrCode::STATUS_ACTIVE)
                ->update(['status' => QrCode::STATUS_EXPIRED]);

            return redirect()
                ->route('qrcode.index')
                ->with('success', "Successfully expired {$updated} QR codes.");
        } catch (\Exception $e) {
            Log::error('Bulk expire failed: ' . $e->getMessage());

            return redirect()
                ->back()
                ->withErrors(['error' => 'Failed to expire QR codes. Please try again.']);
        }
    }

    /**
     * Validate QR code data for creation.
     */
    private function validateQrCodeData(Request $request): array
    {
        return $request->validate([
            'vendor_id' => 'required|exists:vendors,id',
            'service_id' => 'required|exists:services,id',
            'expiry_hours' => [
                'required',
                'integer',
                'min:1',
                'max:' . self::MAX_EXPIRY_HOURS
            ],
            'template' => 'nullable|string|in:default,modern,elegant,minimal'
        ], [
            'expiry_hours.max' => 'Expiry time cannot exceed 7 days (168 hours).',
        ]);
    }

    /**
     * Validate QR code data for updates.
     */
    private function validateQrCodeUpdate(Request $request): array
    {
        return $request->validate([
            'status' => [
                'required',
                'string',
                Rule::in(['ACTIVE', 'EXPIRED', 'USED', 'INVALID'])
            ],
            'expiry_hours' => [
                'required',
                'integer',
                'min:1',
                'max:' . self::MAX_EXPIRY_HOURS
            ]
        ], [
            'status.required' => 'Status is required.',
            'status.in' => 'Invalid status selected.',
            'expiry_hours.required' => 'Expiry hours is required.',
            'expiry_hours.integer' => 'Expiry hours must be a number.',
            'expiry_hours.min' => 'Expiry hours must be at least 1 hour.',
            'expiry_hours.max' => 'Expiry time cannot exceed 7 days (168 hours).',
        ]);
    }

    /**
     * Generate unique QR code.
     */
    private function generateUniqueCode(): string
    {
        do {
            $code = strtoupper(Str::random(16));
        } while (QrCode::where('code', $code)->exists());

        return $code;
    }

    /**
     * Prepare service details for QR code.
     */
    private function prepareServiceDetails(Service $service): array
    {
        return [
            'service_id' => $service->id,
            'service_name' => $service->name,
            'service_description' => $service->description,
            'price' => $service->price,
            'category' => $service->category,
            'vendor_id' => $service->vendor_id,
            'vendor_name' => $service->vendor->business_name,
            'vendor_category' => $service->vendor->service_category,
            'generated_at' => now()->toISOString(),
            'expires_at' => now()->addHours(self::DEFAULT_EXPIRY_HOURS)->toISOString()
        ];
    }

    /**
     * Generate and save QR code image with template.
     */
    private function generateAndSaveQrCodeImage(QrCode $qrCode, string $template = 'default'): void
    {
        try {
            Log::info('Starting QR code image generation', [
                'qr_code_id' => $qrCode->id,
                'template' => $template
            ]);

            // Check if GD extension is loaded
            if (!extension_loaded('gd')) {
                throw new \Exception('GD extension is not loaded');
            }

            // Generate basic QR code with error handling
            $qrImage = $this->generateBasicQrCode($qrCode->code);

            if (!$qrImage) {
                throw new \Exception('Failed to generate basic QR code');
            }

            Log::info('Basic QR code generated successfully');

            // Apply template with simplified approach
            $finalImage = $this->applySimpleTemplate($qrImage, $qrCode, $template);

            // Ensure storage directory exists
            $imagePath = $this->getQrCodeImagePath($qrCode);
            $directory = dirname(storage_path('app/' . $imagePath));

            if (!file_exists($directory)) {
                mkdir($directory, 0755, true);
            }

            // Save to storage
            Storage::put($imagePath, $finalImage);

            Log::info('QR code image saved successfully', [
                'path' => $imagePath,
                'size' => strlen($finalImage)
            ]);
        } catch (\Exception $e) {
            Log::error('QR code image generation failed', [
                'qr_code_id' => $qrCode->id,
                'template' => $template,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Create a simple fallback QR code without template
            $this->generateFallbackQrCode($qrCode);
        }
    }

    /**
     * Generate basic QR code image.
     */
    private function generateBasicQrCode(string $code): string
    {
        try {
            // More robust QR code options
            $options = new QROptions([
                'version' => 5,
                'outputType' => 'png',
                'eccLevel' => 1,
                'scale' => 10,
                'imageBase64' => false,
                'moduleValues' => [
                    1536 => [0, 0, 0], // Black
                    6 => [255, 255, 255], // White
                ]
            ]);

            $qrGenerator = new QRGenerator($options);
            $qrImage = $qrGenerator->render($code);

            if (!$qrImage) {
                throw new \Exception('QR code generation returned empty result');
            }

            return $qrImage;
        } catch (\Exception $e) {
            Log::error('Basic QR code generation failed', [
                'code' => $code,
                'error' => $e->getMessage()
            ]);

            // Fallback to simple QR code
            return $this->generateSimpleQrCode($code);
        }
    }

    /**
     * Generate simple QR code as fallback.
     */
    private function generateSimpleQrCode(string $code): string
    {
        try {
            $options = new QROptions([
                'version' => 5,
                'outputType' => 'png',
                'eccLevel' => 1,
                'scale' => 10,
                'imageBase64' => false,
                'moduleValues' => [
                    1536 => [0, 0, 0], // Black
                    6 => [255, 255, 255], // White
                ]
            ]);

            $qrGenerator = new QRGenerator($options);
            return $qrGenerator->render($code);
        } catch (\Exception $e) {
            Log::error('Simple QR code generation also failed', [
                'error' => $e->getMessage()
            ]);
            throw new \Exception('All QR code generation methods failed: ' . $e->getMessage());
        }
    }

    /**
     * Simplified template application that avoids complex image manipulation.
     */
    private function applySimpleTemplate(string $qrImage, QrCode $qrCode, string $template): string
    {
        try {
            // If Intervention Image fails, return the basic QR code
            if (!class_exists('\Intervention\Image\ImageManager')) {
                Log::warning('Intervention Image not available, returning basic QR code');
                return $qrImage;
            }

            $manager = new ImageManager(new Driver());

            // Try to create the template image
            $templateImage = $manager->create(self::TEMPLATE_WIDTH, self::TEMPLATE_HEIGHT);
            $templateImage->fill('ffffff'); // White background

            // Load and resize QR code
            $qrCodeImage = $manager->read($qrImage);
            $qrCodeImage->resize(self::QR_SIZE, self::QR_SIZE);

            // Place QR code in center
            $x = (self::TEMPLATE_WIDTH - self::QR_SIZE) / 2;
            $y = (self::TEMPLATE_HEIGHT - self::QR_SIZE) / 2;
            $templateImage->place($qrCodeImage, 'top-left', $x, $y);

            // Add simple text without custom fonts
            $this->addSimpleText($templateImage, $qrCode);

            return $templateImage->toPng();
        } catch (\Exception $e) {
            Log::warning('Template application failed, returning basic QR code', [
                'error' => $e->getMessage()
            ]);

            // Return the basic QR code if template fails
            return $qrImage;
        }
    }

    /**
     * Add simple text without custom fonts.
     */
    private function addSimpleText($templateImage, QrCode $qrCode): void
    {
        try {
            // Add title
            $templateImage->text('Pay Sabil Al-Hikmah', self::TEMPLATE_WIDTH / 2, 50, function ($font) {
                $font->size(24);
                $font->color('000000');
                $font->align('center');
                $font->valign('middle');
            });

            // Add vendor name
            $templateImage->text($qrCode->vendor->business_name, self::TEMPLATE_WIDTH / 2, 100, function ($font) {
                $font->size(18);
                $font->color('333333');
                $font->align('center');
                $font->valign('middle');
            });

            // Add service name
            $templateImage->text($qrCode->service->name, self::TEMPLATE_WIDTH / 2, 130, function ($font) {
                $font->size(16);
                $font->color('666666');
                $font->align('center');
                $font->valign('middle');
            });

            // Add price
            $templateImage->text('RM ' . number_format($qrCode->service->price, 2), self::TEMPLATE_WIDTH / 2, 650, function ($font) {
                $font->size(20);
                $font->color('e74c3c');
                $font->align('center');
                $font->valign('middle');
            });

            // Add expiry
            $templateImage->text('Valid until: ' . $qrCode->expiry_date->format('d M Y, H:i'), self::TEMPLATE_WIDTH / 2, 700, function ($font) {
                $font->size(14);
                $font->color('999999');
                $font->align('center');
                $font->valign('middle');
            });
        } catch (\Exception $e) {
            Log::warning('Text addition failed, continuing without text', [
                'error' => $e->getMessage()
            ]);
            // Continue without text if it fails
        }
    }

    /**
     * Generate fallback QR code without any template.
     */
    private function generateFallbackQrCode(QrCode $qrCode): void
    {
        try {
            Log::info('Generating fallback QR code without template');

            $qrImage = $this->generateBasicQrCode($qrCode->code);
            $imagePath = $this->getQrCodeImagePath($qrCode);

            Storage::put($imagePath, $qrImage);

            Log::info('Fallback QR code generated successfully');
        } catch (\Exception $e) {
            Log::error('Even fallback QR code generation failed', [
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Get available templates.
     */
    private function getAvailableTemplates(): array
    {
        return [
            'default' => [
                'name' => 'Default',
                'description' => 'Clean and professional design',
                'preview' => '/images/templates/default.png'
            ],
            'modern' => [
                'name' => 'Modern',
                'description' => 'Gradient background with modern styling',
                'preview' => '/images/templates/modern.png'
            ],
            'elegant' => [
                'name' => 'Elegant',
                'description' => 'Sophisticated design with gold accents',
                'preview' => '/images/templates/elegant.png'
            ],
            'minimal' => [
                'name' => 'Minimal',
                'description' => 'Clean and minimal design',
                'preview' => '/images/templates/minimal.png'
            ]
        ];
    }

    /**
     * Get QR code image path.
     */
    private function getQrCodeImagePath(QrCode $qrCode): string
    {
        return 'public/qr_codes/' . $qrCode->id . '.png';
    }

    /**
     * Generate QR code file name for download.
     */
    private function generateQrCodeFileName(QrCode $qrCode): string
    {
        $vendorName = Str::slug($qrCode->vendor->business_name);
        $serviceName = Str::slug($qrCode->service->name);
        $date = $qrCode->generated_date->format('Y-m-d');

        return "qr_code_{$vendorName}_{$serviceName}_{$date}.png";
    }

    /**
     * Check if QR code image exists.
     */
    private function checkQrCodeImageExists(QrCode $qrCode): bool
    {
        return Storage::exists($this->getQrCodeImagePath($qrCode));
    }

    /**
     * Delete QR code image.
     */
    private function deleteQrCodeImage(QrCode $qrCode): bool
    {
        $imagePath = $this->getQrCodeImagePath($qrCode);
        return Storage::delete($imagePath);
    }

    /**
     * Get QR code statistics.
     */
    private function getQrCodeStatistics(): array
    {
        // Update expired QR codes before getting statistics
        QrCode::updateExpiredQrCodes();

        return [
            'total' => QrCode::count(),
            'active' => QrCode::where('status', 'ACTIVE')->count(),
            'expired' => QrCode::where('status', 'EXPIRED')->count(),
            'used' => QrCode::where('status', 'USED')->count(),
            'expiring_soon' => QrCode::where('status', 'ACTIVE')
                ->whereBetween('expiry_date', [now(), now()->addHours(24)])
                ->count(),
            'generated_today' => QrCode::whereDate('generated_date', today())->count(),
            'generated_this_month' => QrCode::whereMonth('generated_date', now()->month)->count(),
        ];
    }

    /**
     * Get QR code usage statistics.
     */
    private function getQrCodeUsageStats(QrCode $qrCode): array
    {
        // Use the loaded transactions collection instead of the query builder
        $transactions = $qrCode->transactions;

        // Filter completed transactions
        $completedTransactions = $transactions->where('status', 'COMPLETED');

        // Filter failed transactions
        $failedTransactions = $transactions->where('status', 'FAILED');

        return [
            'total_scans' => $transactions->count(),
            'successful_scans' => $completedTransactions->count(),
            'failed_scans' => $failedTransactions->count(),
            'total_revenue' => $completedTransactions->sum('amount'),
            'unique_users' => $transactions->pluck('student_id')->unique()->count(),
            'first_scan' => $transactions->min('transaction_date'),
            'last_scan' => $transactions->max('transaction_date'),
        ];
    }

    /**
     * Log QR code activity.
     */
    private function logQrCodeActivity(QrCode $qrCode, string $action, array $details = []): void
    {
        Log::info("QR Code {$action}", [
            'qr_code_id' => $qrCode->id,
            'vendor_id' => $qrCode->vendor_id,
            'service_id' => $qrCode->service_id,
            'action' => $action,
            'details' => $details,
            'timestamp' => now()
        ]);
    }
}

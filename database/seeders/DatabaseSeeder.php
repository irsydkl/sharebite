<?php

namespace Database\Seeders;

use App\Models\ApprovalLog;
use App\Models\DonorProfile;
use App\Models\Food;
use App\Models\FoodClaim;
use App\Models\Notification;
use App\Models\Payment;
use App\Models\Payout;
use App\Models\ProductCategory;
use App\Models\Rating;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    // ------------------------------------------------------------------ //
    // Realistic food data for Indonesian context
    // ------------------------------------------------------------------ //
    private array $foodData = [
        ['title' => 'Nasi Rames Campur', 'category' => 'Makanan Berat', 'unit' => 'porsi', 'price' => 25000, 'qty' => 20],
        ['title' => 'Roti Tawar Gandum', 'category' => 'Roti & Kue', 'unit' => 'bungkus', 'price' => 18000, 'qty' => 15],
        ['title' => 'Nasi Padang Komplit', 'category' => 'Makanan Berat', 'unit' => 'porsi', 'price' => 32000, 'qty' => 10],
        ['title' => 'Kue Bolu Coklat', 'category' => 'Roti & Kue', 'unit' => 'potong', 'price' => 10000, 'qty' => 30],
        ['title' => 'Bakso Sapi Isi 10', 'category' => 'Makanan Berat', 'unit' => 'mangkuk', 'price' => 22000, 'qty' => 12],
        ['title' => 'Croissant Butter', 'category' => 'Roti & Kue', 'unit' => 'pcs', 'price' => 15000, 'qty' => 25],
        ['title' => 'Mie Goreng Spesial', 'category' => 'Makanan Berat', 'unit' => 'porsi', 'price' => 20000, 'qty' => 18],
        ['title' => 'Pisang Cavendish', 'category' => 'Buah & Sayur', 'unit' => 'sisir', 'price' => 12000, 'qty' => 8],
        ['title' => 'Ayam Goreng Krispi', 'category' => 'Lauk Pauk', 'unit' => 'potong', 'price' => 16000, 'qty' => 22],
        ['title' => 'Salad Sayur Segar', 'category' => 'Buah & Sayur', 'unit' => 'box', 'price' => 28000, 'qty' => 10],
        ['title' => 'Lumpia Goreng', 'category' => 'Jajanan', 'unit' => 'pcs', 'price' => 8000, 'qty' => 40],
        ['title' => 'Donat Gula Halus', 'category' => 'Roti & Kue', 'unit' => 'pcs', 'price' => 7000, 'qty' => 35],
        ['title' => 'Tahu Tempe Goreng', 'category' => 'Lauk Pauk', 'unit' => 'porsi', 'price' => 12000, 'qty' => 20],
        ['title' => 'Sup Ayam Bening', 'category' => 'Makanan Berat', 'unit' => 'mangkuk', 'price' => 18000, 'qty' => 15],
        ['title' => 'Jus Alpukat Susu', 'category' => 'Minuman', 'unit' => 'cup', 'price' => 20000, 'qty' => 25],
        ['title' => 'Martabak Manis Coklat', 'category' => 'Jajanan', 'unit' => 'pcs', 'price' => 35000, 'qty' => 8],
        ['title' => 'Nasi Goreng Spesial', 'category' => 'Makanan Berat', 'unit' => 'porsi', 'price' => 25000, 'qty' => 14],
        ['title' => 'Roti Bakar Keju', 'category' => 'Roti & Kue', 'unit' => 'pcs', 'price' => 14000, 'qty' => 20],
        ['title' => 'Siomay Bandung', 'category' => 'Jajanan', 'unit' => 'porsi', 'price' => 18000, 'qty' => 12],
        ['title' => 'Kolak Pisang Ubi', 'category' => 'Minuman', 'unit' => 'cup', 'price' => 10000, 'qty' => 30],
    ];

    private array $categories = [
        'Makanan Berat', 'Roti & Kue', 'Jajanan', 'Lauk Pauk',
        'Buah & Sayur', 'Minuman', 'Dessert', 'Camilan',
    ];

    public function run(): void
    {
        // ---------------------------------------------------------------- //
        // 1. Product Categories
        // ---------------------------------------------------------------- //
        $this->command->info('Seeding product categories...');
        $categoryMap = [];
        foreach ($this->categories as $catName) {
            $cat = ProductCategory::updateOrCreate(
                ['name' => $catName],
                ['description' => "Kategori untuk {$catName}."]
            );
            $categoryMap[$catName] = $cat->id;
        }

        // ---------------------------------------------------------------- //
        // 2. Admin Account
        // ---------------------------------------------------------------- //
        $this->command->info('Seeding admin account...');
        $admin = User::updateOrCreate(
            ['email' => 'admin@sharebite.test'],
            [
                'name' => 'Admin ShareBite',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'phone' => '081200000001',
                'address' => 'Kantor ShareBite, Jakarta Pusat',
                'latitude' => -6.2088,
                'longitude' => 106.8456,
                'is_active' => true,
                'is_verified' => true,
                'verified_at' => now(),
                'email_verified_at' => now(),
            ]
        );

        // ---------------------------------------------------------------- //
        // 3. Donatur Accounts — 1 fixed + 4 random
        // ---------------------------------------------------------------- //
        $this->command->info('Seeding donatur accounts...');

        // Fixed donatur (easy login for testing)
        $donatur1 = User::updateOrCreate(
            ['email' => 'donatur@sharebite.test'],
            [
                'name' => 'Budi Santoso',
                'password' => Hash::make('password'),
                'role' => 'donatur',
                'phone' => '081200000002',
                'address' => 'Jl. Sudirman No. 5, Jakarta Selatan',
                'latitude' => -6.2150,
                'longitude' => 106.8120,
                'is_active' => true,
                'is_verified' => true,
                'verified_at' => now()->subDays(10),
                'email_verified_at' => now(),
                'balance' => 250000,
            ]
        );

        DonorProfile::updateOrCreate(
            ['user_id' => $donatur1->id],
            [
                'store_name' => 'Warung Nasi Bu Sari',
                'store_description' => 'Menyediakan nasi rames, lauk pauk segar, dan berbagai hidangan rumahan berkualitas setiap hari.',
                'store_address' => 'Jl. Sudirman No. 5, Jakarta Selatan',
                'latitude' => -6.2150,
                'longitude' => 106.8120,
                'is_verified' => true,
                'location_verified' => true,
                'verified_by' => $admin->id,
                'verified_at' => now()->subDays(10),
                'approval_status' => 'approved',
            ]
        );

        // Fixed donatur 2 (for variety)
        $donatur2 = User::updateOrCreate(
            ['email' => 'donatur2@sharebite.test'],
            [
                'name' => 'Siti Rahayu',
                'password' => Hash::make('password'),
                'role' => 'donatur',
                'phone' => '081200000003',
                'address' => 'Jl. Gatot Subroto No. 22, Jakarta',
                'latitude' => -6.2350,
                'longitude' => 106.8230,
                'is_active' => true,
                'is_verified' => true,
                'verified_at' => now()->subDays(7),
                'email_verified_at' => now(),
                'balance' => 180000,
            ]
        );

        DonorProfile::updateOrCreate(
            ['user_id' => $donatur2->id],
            [
                'store_name' => 'Bakery Roti Enak',
                'store_description' => 'Spesialisasi roti, kue, dan pastri segar yang dibuat setiap pagi. Ideal untuk sarapan bergizi.',
                'store_address' => 'Jl. Gatot Subroto No. 22, Jakarta',
                'latitude' => -6.2350,
                'longitude' => 106.8230,
                'is_verified' => true,
                'location_verified' => true,
                'verified_by' => $admin->id,
                'verified_at' => now()->subDays(7),
                'approval_status' => 'approved',
            ]
        );

        // Pending donatur (for admin verification testing)
        $donaturPending = User::updateOrCreate(
            ['email' => 'donatur.pending@sharebite.test'],
            [
                'name' => 'Agus Prasetyo',
                'password' => Hash::make('password'),
                'role' => 'donatur',
                'phone' => '081200000004',
                'address' => 'Jl. MT Haryono No. 9, Jakarta',
                'latitude' => -6.2480,
                'longitude' => 106.8500,
                'is_active' => true,
                'is_verified' => false,
                'email_verified_at' => now(),
            ]
        );

        DonorProfile::updateOrCreate(
            ['user_id' => $donaturPending->id],
            [
                'store_name' => 'Dapur Sehat Alami',
                'store_description' => 'Masakan rumahan sehat dari bahan-bahan organik pilihan.',
                'store_address' => 'Jl. MT Haryono No. 9, Jakarta',
                'latitude' => -6.2480,
                'longitude' => 106.8500,
                'is_verified' => false,
                'location_verified' => false,
                'approval_status' => 'pending',
            ]
        );

        $allDonaturs = [$donatur1, $donatur2];

        // ---------------------------------------------------------------- //
        // 4. User (Penerima) Accounts — 1 fixed + several random
        // ---------------------------------------------------------------- //
        $this->command->info('Seeding user accounts...');

        $user1 = User::updateOrCreate(
            ['email' => 'user@sharebite.test'],
            [
                'name' => 'Andi Wijaya',
                'password' => Hash::make('password'),
                'role' => 'user',
                'phone' => '081200000010',
                'address' => 'Jl. Casablanca No. 88, Jakarta Selatan',
                'latitude' => -6.2200,
                'longitude' => 106.8350,
                'is_active' => true,
                'email_verified_at' => now(),
                'balance' => 50000,
            ]
        );

        $user2 = User::updateOrCreate(
            ['email' => 'user2@sharebite.test'],
            [
                'name' => 'Dewi Kusuma',
                'password' => Hash::make('password'),
                'role' => 'user',
                'phone' => '081200000011',
                'address' => 'Jl. Tebet Barat No. 12, Jakarta',
                'latitude' => -6.2260,
                'longitude' => 106.8390,
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        $allUsers = [$user1, $user2];

        // ---------------------------------------------------------------- //
        // 5. Foods — mix of approved/pending across both donaturs
        // ---------------------------------------------------------------- //
        $this->command->info('Seeding foods...');

        $now = now();
        $createdFoods = [];

        foreach ($this->foodData as $i => $data) {
            $donatur = $allDonaturs[$i % count($allDonaturs)];
            $profile = $donatur->donorProfile;

            $originalPrice = $data['price'];
            $serviceFee = round($originalPrice * 0.1, 2);
            $finalPrice = $originalPrice + $serviceFee;
            $qty = $data['qty'];

            // Spread pickup times across today (some past, some future for variety)
            $pickupStart = $now->copy()->addHours(($i % 6) + 1);
            $pickupEnd = $pickupStart->copy()->addHours(3);
            $pickupDeadline = $pickupEnd->copy()->subMinutes(30);

            // Last 4 foods are pending for admin testing
            $approvalStatus = ($i >= count($this->foodData) - 4) ? 'pending' : 'approved';
            $status = $approvalStatus === 'approved' ? 'available' : 'available';
            $remainingQty = $qty;

            $food = Food::updateOrCreate(
                [
                    'donor_id' => $donatur->id,
                    'title' => $data['title'],
                ],
                [
                    'category_id' => $categoryMap[$data['category']] ?? $categoryMap['Makanan Berat'],
                    'description' => "Tersedia {$qty} {$data['unit']} {$data['title']} segar dan berkualitas. Ambil sebelum tenggat waktu!",
                    'quantity' => $qty,
                    'remaining_quantity' => $remainingQty,
                    'unit' => $data['unit'],
                    'original_price' => $originalPrice,
                    'service_fee' => $serviceFee,
                    'final_price' => $finalPrice,
                    'pickup_address' => $profile->store_address,
                    'latitude' => $profile->latitude,
                    'longitude' => $profile->longitude,
                    'pickup_start' => $pickupStart,
                    'pickup_end' => $pickupEnd,
                    'pickup_deadline' => $pickupDeadline,
                    'pickup_duration_minutes' => 180,
                    'approval_status' => $approvalStatus,
                    'approved_by' => $approvalStatus === 'approved' ? $admin->id : null,
                    'status' => $status,
                ]
            );

            $createdFoods[] = $food;
        }

        // ---------------------------------------------------------------- //
        // 6. Food Claims — mix of statuses for testing
        // ---------------------------------------------------------------- //
        $this->command->info('Seeding food claims...');

        $approvedFoods = collect($createdFoods)->filter(
            fn ($f) => $f->approval_status === 'approved'
        )->values();

        $claimScenarios = [
            // [food_index, user, claim_status, payment_status]
            [0, $user1, 'completed',      'paid'],
            [1, $user2, 'completed',      'paid'],
            [2, $user1, 'picked_up',      'paid'],
            [3, $user2, 'ready_pickup',   'paid'],
            [4, $user1, 'waiting_payment', null],
            [5, $user2, 'cancelled',      null],
            [6, $user1, 'completed',      'paid'],
            [7, $user2, 'ready_pickup',   'paid'],
        ];

        $createdClaims = [];
        foreach ($claimScenarios as [$foodIdx, $claimUser, $claimStatus, $payStatus]) {
            if (! isset($approvedFoods[$foodIdx])) {
                continue;
            }

            $food = $approvedFoods[$foodIdx];
            $qtyClaimed = min(rand(1, 3), $food->remaining_quantity ?: 1);
            $unitPrice = $food->original_price / max($food->quantity, 1);
            $subtotal = round($unitPrice * $qtyClaimed, 2);
            $svcFee = round($subtotal * 0.1, 2);
            $totalPrice = $subtotal + $svcFee;
            $bookingCode = 'BK-'.strtoupper(Str::random(8));

            $claimData = [
                'food_id' => $food->id,
                'user_id' => $claimUser->id,
                'quantity_claimed' => $qtyClaimed,
                'subtotal_price' => $subtotal,
                'service_fee' => $svcFee,
                'total_price' => $totalPrice,
                'booking_code' => $bookingCode,
                'payment_expired_at' => now()->addMinutes(30),
                'pickup_deadline' => $food->pickup_deadline,
                'claim_status' => $claimStatus,
                'picked_up_at' => in_array($claimStatus, ['picked_up', 'completed']) ? now()->subHours(2) : null,
            ];

            $claim = FoodClaim::create($claimData);
            $createdClaims[] = ['claim' => $claim, 'food' => $food, 'payStatus' => $payStatus];
        }

        // ---------------------------------------------------------------- //
        // 7. Payments
        // ---------------------------------------------------------------- //
        $this->command->info('Seeding payments...');

        $paidClaims = [];
        foreach ($createdClaims as $item) {
            ['claim' => $claim, 'food' => $food, 'payStatus' => $payStatus] = $item;

            if ($payStatus !== 'paid') {
                continue;
            }

            $payment = Payment::create([
                'claim_id' => $claim->id,
                'user_id' => $claim->user_id,
                'amount' => $claim->total_price,
                'service_fee' => $claim->service_fee,
                'donor_amount' => $claim->subtotal_price,
                'payment_method' => collect(['qris', 'bank_transfer', 'ewallet'])->random(),
                'payment_status' => 'paid',
                'transaction_reference' => 'TX-'.strtoupper(Str::random(12)),
                'paid_at' => now()->subHours(rand(1, 48)),
            ]);

            $paidClaims[] = ['claim' => $claim, 'food' => $food, 'payment' => $payment];
        }

        // ---------------------------------------------------------------- //
        // 8. Payouts (for completed claims)
        // ---------------------------------------------------------------- //
        $this->command->info('Seeding payouts...');

        foreach ($paidClaims as $item) {
            ['claim' => $claim, 'food' => $food, 'payment' => $payment] = $item;

            if (! in_array($claim->claim_status, ['completed', 'picked_up'])) {
                continue;
            }

            $existsPayout = Payout::where('payment_id', $payment->id)->exists();
            if ($existsPayout) {
                continue;
            }

            $payout = Payout::create([
                'payment_id' => $payment->id,
                'donor_id' => $food->donor_id,
                'amount' => $payment->donor_amount,
                'status' => 'completed',
                'sent_at' => now()->subHours(rand(1, 24)),
            ]);

            // Update donor balance
            $donor = User::find($food->donor_id);
            if ($donor) {
                $donor->balance += $payout->amount;
                $donor->save();
            }
        }

        // ---------------------------------------------------------------- //
        // 9. Ratings (for completed claims)
        // ---------------------------------------------------------------- //
        $this->command->info('Seeding ratings...');

        $positiveReviews = [
            'Makanan sangat enak dan segar! Terima kasih donaturnya.',
            'Pelayanannya ramah, makanan masih hangat saat diambil.',
            'Sangat membantu, makanannya lezat dan bergizi.',
            'Prosesnya mudah dan cepat. Makanan berkualitas bagus.',
            'Terima kasih banyak, sangat bermanfaat bagi kami.',
            'Makanan dalam kondisi baik dan rasa enak. Recommended!',
        ];

        foreach ($createdClaims as $item) {
            ['claim' => $claim, 'food' => $food] = $item;

            if ($claim->claim_status !== 'completed') {
                continue;
            }

            $exists = Rating::where('claim_id', $claim->id)->exists();
            if ($exists) {
                continue;
            }

            Rating::create([
                'food_id' => $food->id,
                'claim_id' => $claim->id,
                'user_id' => $claim->user_id,
                'rating' => rand(4, 5),
                'review' => collect($positiveReviews)->random(),
            ]);
        }

        // ---------------------------------------------------------------- //
        // 10. Approval Logs
        // ---------------------------------------------------------------- //
        $this->command->info('Seeding approval logs...');

        foreach ($createdFoods as $food) {
            if ($food->approval_status === 'approved') {
                ApprovalLog::updateOrCreate(
                    ['food_id' => $food->id, 'admin_id' => $admin->id],
                    [
                        'donor_profile_id' => null,
                        'status' => 'approved',
                        'notes' => 'Makanan telah diperiksa dan disetujui.',
                    ]
                );
            }
        }

        // Log for the fixed approved donatur profiles
        foreach ([$donatur1, $donatur2] as $donatur) {
            $profile = $donatur->donorProfile;
            if ($profile) {
                ApprovalLog::updateOrCreate(
                    ['donor_profile_id' => $profile->id, 'admin_id' => $admin->id],
                    [
                        'food_id' => null,
                        'status' => 'approved',
                        'notes' => 'Dokumen lengkap dan lokasi terverifikasi.',
                    ]
                );
            }
        }

        // ---------------------------------------------------------------- //
        // 11. Notifications
        // ---------------------------------------------------------------- //
        $this->command->info('Seeding notifications...');

        $notifData = [
            // Admin
            [$admin->id, 'Makanan Baru Perlu Verifikasi', 'Ada 4 listing makanan baru yang menunggu persetujuan Anda.', 'info'],
            [$admin->id, 'Donatur Baru Mendaftar', 'Agus Prasetyo mendaftarkan diri sebagai donatur.', 'info'],

            // Donatur 1
            [$donatur1->id, 'Pendaftaran Donatur Disetujui', 'Akun Donatur Anda telah disetujui! Anda sekarang dapat mengunggah makanan.', 'success'],
            [$donatur1->id, 'Makanan Diklaim', "Nasi Rames Campur telah diklaim oleh {$user1->name}.", 'info'],
            [$donatur1->id, 'Pembayaran Diterima', 'Pembayaran untuk Nasi Rames Campur telah diselesaikan.', 'success'],
            [$donatur1->id, 'Pencairan Dana Berhasil', 'Dana sebesar Rp 25.000 telah ditambahkan ke saldo Anda.', 'success'],
            [$donatur1->id, 'Ulasan Baru Diterima', "Pengguna memberikan ulasan bintang 5 untuk 'Nasi Rames Campur'.", 'success'],

            // Donatur 2
            [$donatur2->id, 'Pendaftaran Donatur Disetujui', 'Akun Donatur Anda telah disetujui! Anda sekarang dapat mengunggah makanan.', 'success'],
            [$donatur2->id, 'Makanan Baru Disetujui', "Roti Tawar Gandum' Anda telah disetujui dan tersedia untuk diklaim.", 'success'],

            // User 1
            [$user1->id, 'Klaim Berhasil Dibuat', 'Klaim untuk Nasi Rames Campur berhasil dibuat. Silakan lakukan pembayaran.', 'info'],
            [$user1->id, 'Pembayaran Berhasil', 'Pembayaran untuk kode booking BK-XXXXXX diterima.', 'success'],
            [$user1->id, 'Makanan Telah Diambil', 'Makanan untuk booking BK-XXXXXX telah selesai diambil. Berikan ulasan Anda!', 'success'],

            // User 2
            [$user2->id, 'Klaim Berhasil Dibuat', 'Klaim untuk Roti Tawar Gandum berhasil dibuat. Silakan lakukan pembayaran.', 'info'],
            [$user2->id, 'Pembayaran Berhasil', 'Pembayaran berhasil diproses. Silakan ambil makanan Anda.', 'success'],
        ];

        foreach ($notifData as [$userId, $title, $message, $type]) {
            Notification::create([
                'user_id' => $userId,
                'title' => $title,
                'message' => $message,
                'type' => $type,
                'is_read' => false,
            ]);
        }

        // ---------------------------------------------------------------- //
        // Summary
        // ---------------------------------------------------------------- //
        $this->command->newLine();
        $this->command->info('✅ Seeding selesai! Berikut akun yang tersedia:');
        $this->command->table(
            ['Role', 'Email', 'Password', 'Keterangan'],
            [
                ['Admin',           'admin@sharebite.test',          'password', 'Akun admin utama'],
                ['Donatur',         'donatur@sharebite.test',        'password', 'Donatur verified (Warung Nasi)'],
                ['Donatur',         'donatur2@sharebite.test',       'password', 'Donatur verified (Bakery)'],
                ['Donatur Pending', 'donatur.pending@sharebite.test', 'password', 'Menunggu verifikasi admin'],
                ['User/Penerima',   'user@sharebite.test',           'password', 'Penerima utama'],
                ['User/Penerima',   'user2@sharebite.test',          'password', 'Penerima ke-2'],
            ]
        );
    }
}

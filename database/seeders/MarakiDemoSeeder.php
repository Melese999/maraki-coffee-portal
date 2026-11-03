<?php

namespace Database\Seeders;

use App\Enums\InvestmentType;
use App\Enums\PaymentStatus;
use App\Models\Customer;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\Investment;
use App\Models\Investor;
use App\Models\MenuItem;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class MarakiDemoSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::query()->updateOrCreate(
            ['email' => 'admin@maraki.local'],
            [
                'name' => 'ማራኪ አስተዳዳሪ',
                'password' => Hash::make('password'),
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );
        $admin->assignRole('Admin');

        $manager = User::query()->updateOrCreate(
            ['email' => 'manager@maraki.local'],
            [
                'name' => 'የቤት ሥራ አስኪያጅ',
                'password' => Hash::make('password'),
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );
        $manager->assignRole('Manager');

        $cashier = User::query()->updateOrCreate(
            ['email' => 'cashier@maraki.local'],
            [
                'name' => 'የፊት ገንዘብ ተቀባይ',
                'password' => Hash::make('password'),
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );
        $cashier->assignRole('Cashier');

        $categories = [
            'የቡና ፍሬ',
            'ኪራይ',
            'መገልገያዎች',
            'ደመወዝ',
            'ወተት',
            'ማሸጊያ',
            'መጓጓዣ',
            'ሌላ',
        ];

        foreach ($categories as $name) {
            ExpenseCategory::query()->updateOrCreate(
                ['name' => $name],
                ['is_active' => true]
            );
        }

        $menu = [
            ['name' => 'ቡና (ባህላዊ)', 'price' => 40, 'unit' => 'ኩባያ', 'sort_order' => 1],
            ['name' => 'ማኪያቶ', 'price' => 55, 'unit' => 'ኩባያ', 'sort_order' => 2],
            ['name' => 'ኤስፕሬሶ', 'price' => 45, 'unit' => 'ኩባያ', 'sort_order' => 3],
            ['name' => 'ሻይ', 'price' => 30, 'unit' => 'ኩባያ', 'sort_order' => 4],
            ['name' => 'ትኩስ ጭማቂ', 'price' => 70, 'unit' => 'ብርጭቆ', 'sort_order' => 5],
            ['name' => 'ፓስትሪ', 'price' => 50, 'unit' => 'ቁራጭ', 'sort_order' => 6],
        ];

        foreach ($menu as $item) {
            MenuItem::query()->updateOrCreate(
                ['name' => $item['name']],
                [
                    'price' => $item['price'],
                    'unit' => $item['unit'],
                    'sort_order' => $item['sort_order'],
                    'is_active' => true,
                ]
            );
        }

        $customer = Customer::query()->updateOrCreate(
            ['phone' => '0911000000'],
            ['name' => 'አበበ ከበደ', 'notes' => 'መደበኛ የጠዋት እንግዳ']
        );

        $bunna = MenuItem::query()->where('name', 'ቡና (ባህላዊ)')->firstOrFail();
        $macchiato = MenuItem::query()->where('name', 'ማኪያቶ')->firstOrFail();

        $paidSale = Sale::query()->create([
            'recorded_by' => $cashier->id,
            'sale_date' => today(),
            'payment_status' => PaymentStatus::Paid,
            'total' => 95,
            'amount_paid' => 95,
            'notes' => 'የጠዋት ጫና',
        ]);

        SaleItem::query()->create([
            'sale_id' => $paidSale->id,
            'menu_item_id' => $bunna->id,
            'item_name' => $bunna->name,
            'unit_price' => $bunna->price,
            'quantity' => 1,
            'line_total' => $bunna->price,
        ]);

        SaleItem::query()->create([
            'sale_id' => $paidSale->id,
            'menu_item_id' => $macchiato->id,
            'item_name' => $macchiato->name,
            'unit_price' => $macchiato->price,
            'quantity' => 1,
            'line_total' => $macchiato->price,
        ]);

        $creditSale = Sale::query()->create([
            'recorded_by' => $cashier->id,
            'customer_id' => $customer->id,
            'sale_date' => today(),
            'payment_status' => PaymentStatus::Credit,
            'total' => 110,
            'amount_paid' => 0,
            'notes' => 'በብድር',
        ]);

        SaleItem::query()->create([
            'sale_id' => $creditSale->id,
            'menu_item_id' => $macchiato->id,
            'item_name' => $macchiato->name,
            'unit_price' => $macchiato->price,
            'quantity' => 2,
            'line_total' => 110,
        ]);

        Expense::query()->create([
            'expense_category_id' => ExpenseCategory::query()->where('name', 'የቡና ፍሬ')->value('id'),
            'recorded_by' => $manager->id,
            'expense_date' => today(),
            'amount' => 500,
            'note' => 'የይርጋቸፌ ቡና ፍሬ',
        ]);

        Expense::query()->create([
            'expense_category_id' => ExpenseCategory::query()->where('name', 'ኪራይ')->value('id'),
            'recorded_by' => $admin->id,
            'expense_date' => today()->startOfMonth(),
            'amount' => 15000,
            'note' => 'የሱቅ ኪራይ',
        ]);

        $investor = Investor::query()->updateOrCreate(
            ['name' => 'ሰላማዊት ኃይሌ'],
            ['phone' => '0911222333', 'notes' => 'የጅምር ድጋፍ']
        );

        Investment::query()->create([
            'investor_id' => $investor->id,
            'recorded_by' => $admin->id,
            'type' => InvestmentType::Loan,
            'amount' => 50000,
            'invested_on' => today()->subMonths(2),
            'is_paid' => false,
            'notes' => 'የመክፈቻ ካፒታል ብድር',
        ]);

        Investment::query()->create([
            'investor_id' => $investor->id,
            'recorded_by' => $admin->id,
            'type' => InvestmentType::Equity,
            'amount' => 25000,
            'invested_on' => today()->subMonths(3),
            'is_paid' => false,
            'notes' => 'የባለቤት ድርሻ',
        ]);
    }
}

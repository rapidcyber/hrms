<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Deduction;

class DeductionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $deductions = [
            // Tax Deductions
            [
                'name' => 'PAYE Tax',
                'type' => 'tax',
                'amount' => 0,
                'calculation_type' => 'percentage',
                'is_active' => true,
                'description' => 'Pay As You Earn income tax'
            ],
            [
                'name' => 'Social Security',
                'type' => 'tax',
                'amount' => 5.5,
                'calculation_type' => 'percentage',
                'is_active' => true,
                'description' => 'Social security contributions'
            ],

            // Statutory Deductions
            [
                'name' => 'Pension Contribution',
                'type' => 'statutory',
                'amount' => 5,
                'calculation_type' => 'percentage',
                'is_active' => true,
                'description' => 'Employee pension contribution'
            ],
            [
                'name' => 'Health Insurance',
                'type' => 'statutory',
                'amount' => 200,
                'calculation_type' => 'fixed',
                'is_active' => true,
                'description' => 'Monthly health insurance premium'
            ],
            [
                'name' => 'Union Dues',
                'type' => 'statutory',
                'amount' => 50,
                'calculation_type' => 'fixed',
                'is_active' => true,
                'description' => 'Monthly union membership fees'
            ],

            // Voluntary Deductions
            [
                'name' => 'Staff Loan Repayment',
                'type' => 'voluntary',
                'amount' => 0,
                'calculation_type' => 'fixed',
                'is_active' => true,
                'description' => 'Employee loan repayment'
            ],
            [
                'name' => 'Charity Donation',
                'type' => 'voluntary',
                'amount' => 20,
                'calculation_type' => 'fixed',
                'is_active' => true,
                'description' => 'Monthly charity contribution'
            ],

            // Custom Deductions
            [
                'name' => 'Uniform Fee',
                'type' => 'custom',
                'amount' => 100,
                'calculation_type' => 'fixed',
                'is_active' => true,
                'description' => 'Company uniform maintenance'
            ],
            [
                'name' => 'Training Fee',
                'type' => 'custom',
                'amount' => 0,
                'calculation_type' => 'fixed',
                'is_active' => true,
                'description' => 'Specialized training costs'
            ]
        ];

        foreach ($deductions as $deduction) {
            Deduction::create($deduction);
        }
    }
}

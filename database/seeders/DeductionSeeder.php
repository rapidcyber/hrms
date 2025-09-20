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
            // [
            //     'name' => 'PAYE Tax',
            //     'type' => 'tax',
            //     'default_amount' => 0,
            //     'calculation_type' => 'percentage',
            //     'description' => 'Pay As You Earn income tax',
            //     'created_by' => 1,
            //     'updated_by' => 1,
            //     'effective_from' => now(),
            //     'effective_until' => null,
            // // ],
            // [
            //     'name' => 'Social Security',
            //     'type' => 'tax',
            //     'default_amount' => 5.5,
            //     'calculation_type' => 'percentage',
            //     'description' => 'Social security contributions',
            //     'created_by' => 1,
            //     'updated_by' => 1,
            //     'effective_from' => now(),
            //     'effective_until' => null,
            // ],

            // Benefits Deductions
            // [
            //     'name' => 'Pension Contribution',
            //     'type' => 'benefits',
            //     'default_amount' => 5,
            //     'calculation_type' => 'percentage',
            //     'description' => 'Employee pension contribution',
            //     'created_by' => 1,
            //     'updated_by' => 1,
            //     'effective_from' => now(),
            //     'effective_until' => null,
            // ],
            // [
            //     'name' => 'Health Insurance',
            //     'type' => 'benefits',
            //     'default_amount' => 200,
            //     'calculation_type' => 'fixed',
            //     'description' => 'Monthly health insurance premium',
            //     'created_by' => 1,
            //     'updated_by' => 1,
            //     'effective_from' => now(),
            //     'effective_until' => null,
            // ],
            // [
            //     'name' => 'Union Dues',
            //     'type' => 'benefits',
            //     'default_amount' => 50,
            //     'calculation_type' => 'fixed',
            //     'description' => 'Monthly union membership fees',
            //     'created_by' => 1,
            //     'updated_by' => 1,
            //     'effective_from' => now(),
            //     'effective_until' => null,
            // ],

            // Voluntary Deductions
            // [
            //     'name' => 'Staff Loan Repayment',
            //     'type' => 'voluntary',
            //     'default_amount' => 0,
            //     'calculation_type' => 'fixed',
            //     'description' => 'Employee loan repayment',
            //     'created_by' => 1,
            //     'updated_by' => 1,
            //     'effective_from' => now(),
            //     'effective_until' => null,
            // ],
            // [
            //     'name' => 'Charity Donation',
            //     'type' => 'voluntary',
            //     'default_amount' => 20,
            //     'calculation_type' => 'fixed',
            //     'description' => 'Monthly charity contribution',
            //     'created_by' => 1,
            //     'updated_by' => 1,
            //     'effective_from' => now(),
            //     'effective_until' => null,
            // ],

            // Custom Deductions
            // [
            //     'name' => 'Uniform Fee',
            //     'type' => 'custom',
            //     'default_amount' => 100,
            //     'calculation_type' => 'fixed',
            //     'description' => 'Company uniform maintenance',
            //     'created_by' => 1,
            //     'updated_by' => 1,
            //     'effective_from' => now(),
            //     'effective_until' => null,
            // ],
            // [
            //     'name' => 'Training Fee',
            //     'type' => 'custom',
            //     'default_amount' => 0,
            //     'calculation_type' => 'fixed',
            //     'description' => 'Specialized training costs',
            //     'created_by' => 1,
            //     'updated_by' => 1,
            //     'effective_from' => now(),
            //     'effective_until' => null,
            // ]
            [
                'name' => 'Cash Advance',
                'type' => 'custom',
                'default_amount' => 0,
                'calculation_type' => 'fixed',
                'description' => 'Cash Advance: one-time payment',
                'created_by' => 1,
                'updated_by' => 1,
                'effective_from' => now(),
                'effective_until' => null,
            ],
                        [
                'name' => 'Salary Loan',
                'type' => 'loan',
                'default_amount' => 0,
                'calculation_type' => 'fixed',
                'description' => 'Cash Advance: one-time payment',
                'created_by' => 1,
                'updated_by' => 1,
                'effective_from' => now(),
                'effective_until' => null,
            ]
        ];

        foreach ($deductions as $deduction) {
            Deduction::create($deduction);
        }
    }
}

<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int|null $user_id
 * @property string $action
 * @property string|null $description
 * @property string|null $subject_type
 * @property int|null $subject_id
 * @property array<array-key, mixed>|null $properties
 * @property string|null $ip_address
 * @property string|null $user_agent
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent|null $subject
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityLog query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityLog whereAction($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityLog whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityLog whereIpAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityLog whereProperties($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityLog whereSubjectId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityLog whereSubjectType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityLog whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityLog whereUserAgent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityLog whereUserId($value)
 */
	class ActivityLog extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $employee_id
 * @property string $effective_date
 * @property string $amount
 * @property string $type
 * @property string|null $description
 * @property string|null $metadata
 * @property int $created_by
 * @property int $updated_by
 * @property string|null $deleted_at
 * @property int $is_active
 * @property int $is_taxable
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdditionalPay newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdditionalPay newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdditionalPay query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdditionalPay whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdditionalPay whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdditionalPay whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdditionalPay whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdditionalPay whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdditionalPay whereEffectiveDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdditionalPay whereEmployeeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdditionalPay whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdditionalPay whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdditionalPay whereIsTaxable($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdditionalPay whereMetadata($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdditionalPay whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdditionalPay whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdditionalPay whereUpdatedBy($value)
 */
	class AdditionalPay extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $employee_id
 * @property string $date
 * @property string|null $in_1
 * @property string|null $out_1
 * @property string|null $in_2
 * @property string|null $out_2
 * @property string|null $in_3
 * @property string|null $out_3
 * @property string $hours_worked
 * @property string|null $status
 * @property string|null $remarks
 * @property string|null $source
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Employee $employee
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance absent()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance between($startDate, $endDate)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance biometric()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance checkIn()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance checkOut()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance dateRange($startDate, $endDate)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance earlyLeave()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance filter(array $filters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance lastMonth()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance lastWeek()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance lastYear()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance late()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance manual()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance present()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance thisMonth()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance thisWeek()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance thisYear()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance today()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance todayCheckIn()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance todayCheckOut()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance whereEmployeeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance whereHoursWorked($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance whereIn1($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance whereIn2($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance whereIn3($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance whereOut1($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance whereOut2($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance whereOut3($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance whereRemarks($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance whereSource($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance whereUpdatedAt($value)
 */
	class Attendance extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $employee_id
 * @property string $effective_date
 * @property string $amount
 * @property string $type
 * @property string|null $description
 * @property string|null $metadata
 * @property int $created_by
 * @property int $updated_by
 * @property string|null $deleted_at
 * @property int $is_active
 * @property int $is_taxable
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Compensation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Compensation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Compensation query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Compensation whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Compensation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Compensation whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Compensation whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Compensation whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Compensation whereEffectiveDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Compensation whereEmployeeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Compensation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Compensation whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Compensation whereIsTaxable($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Compensation whereMetadata($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Compensation whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Compensation whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Compensation whereUpdatedBy($value)
 */
	class Compensation extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string $start_date
 * @property string|null $end_date
 * @property string $frequency
 * @property int $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cutoff newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cutoff newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cutoff query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cutoff whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cutoff whereEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cutoff whereFrequency($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cutoff whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cutoff whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cutoff whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cutoff whereStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cutoff whereUpdatedAt($value)
 */
	class Cutoff extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $employee_id
 * @property string $type
 * @property string $amount
 * @property string|null $description
 * @property string|null $metadata
 * @property string $effective_date
 * @property int $created_by
 * @property int $updated_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Payroll> $payrolls
 * @property-read int|null $payrolls_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Deduction newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Deduction newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Deduction query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Deduction whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Deduction whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Deduction whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Deduction whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Deduction whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Deduction whereEffectiveDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Deduction whereEmployeeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Deduction whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Deduction whereMetadata($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Deduction whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Deduction whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Deduction whereUpdatedBy($value)
 */
	class Deduction extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string|null $code
 * @property string|null $description
 * @property int|null $manager_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Employee> $employees
 * @property-read int|null $employees_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department whereManagerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department whereUpdatedAt($value)
 */
	class Department extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $employee_id
 * @property string $first_name
 * @property string $last_name
 * @property string $email
 * @property string $phone
 * @property string $date_of_birth
 * @property string $hire_date
 * @property string $base_salary
 * @property int|null $department_id
 * @property int|null $position_id
 * @property int|null $shift_id
 * @property string|null $rest_days
 * @property string|null $biometric_id
 * @property string|null $photo
 * @property string|null $address
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Attendance> $attendances
 * @property-read int|null $attendances_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Deduction> $deductions
 * @property-read int|null $deductions_count
 * @property-read \App\Models\Department|null $department
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Payroll> $payrolls
 * @property-read int|null $payrolls_count
 * @property-read \App\Models\Position|null $position
 * @property-read \App\Models\Shift|null $shift
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ThirteenthPay> $thirteenthPays
 * @property-read int|null $thirteenth_pays_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Employee active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Employee department($departmentId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Employee filter(array $filters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Employee inactive()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Employee newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Employee newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Employee position($positionId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Employee query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Employee whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Employee whereBaseSalary($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Employee whereBiometricId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Employee whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Employee whereDateOfBirth($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Employee whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Employee whereDepartmentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Employee whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Employee whereEmployeeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Employee whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Employee whereHireDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Employee whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Employee whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Employee wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Employee wherePhoto($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Employee wherePositionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Employee whereRestDays($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Employee whereShiftId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Employee whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Employee withAttendance()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Employee withDeductions()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Employee withPayroll()
 */
	class Employee extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string $date
 * @property string $type
 * @property int $is_recurring
 * @property string|null $description
 * @property int $created_by
 * @property int $updated_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Holiday newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Holiday newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Holiday query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Holiday whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Holiday whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Holiday whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Holiday whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Holiday whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Holiday whereIsRecurring($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Holiday whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Holiday whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Holiday whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Holiday whereUpdatedBy($value)
 */
	class Holiday extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $employee_id
 * @property \Illuminate\Support\Carbon $start_date
 * @property \Illuminate\Support\Carbon $end_date
 * @property string $type
 * @property string $status
 * @property string|null $reason
 * @property int|null $approved_by
 * @property int $created_by
 * @property int|null $updated_by
 * @property string|null $metadata
 * @property string|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Employee $employee
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Leave newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Leave newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Leave query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Leave whereApprovedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Leave whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Leave whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Leave whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Leave whereEmployeeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Leave whereEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Leave whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Leave whereMetadata($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Leave whereReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Leave whereStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Leave whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Leave whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Leave whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Leave whereUpdatedBy($value)
 */
	class Leave extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $employee_id
 * @property string $period_start
 * @property string $period_end
 * @property string $gross_salary
 * @property string $total_deductions
 * @property string $net_salary
 * @property string|null $bonus
 * @property string|null $overtime_pay
 * @property string $status
 * @property string|null $payment_date
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Deduction> $deductions
 * @property-read int|null $deductions_count
 * @property-read \App\Models\Employee $employee
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payroll newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payroll newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payroll query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payroll whereBonus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payroll whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payroll whereEmployeeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payroll whereGrossSalary($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payroll whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payroll whereNetSalary($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payroll whereOvertimePay($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payroll wherePaymentDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payroll wherePeriodEnd($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payroll wherePeriodStart($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payroll whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payroll whereTotalDeductions($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payroll whereUpdatedAt($value)
 */
	class Payroll extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property int $level
 * @property string|null $description
 * @property string|null $salary_min
 * @property string|null $salary_max
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Employee> $employees
 * @property-read int|null $employees_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Position newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Position newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Position query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Position whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Position whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Position whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Position whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Position whereLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Position whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Position whereSalaryMax($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Position whereSalaryMin($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Position whereUpdatedAt($value)
 */
	class Position extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read int|null $users_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role whereUpdatedAt($value)
 */
	class Role extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string $time_in
 * @property string $time_out
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Employee> $employee
 * @property-read int|null $employee_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shift newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shift newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shift query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shift whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shift whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shift whereTimeIn($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shift whereTimeOut($value)
 */
	class Shift extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property-read \App\Models\Employee|null $employee
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ThirteenthPay newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ThirteenthPay newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ThirteenthPay query()
 */
	class ThirteenthPay extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Role> $roles
 * @property-read int|null $roles_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 */
	class User extends \Eloquent {}
}


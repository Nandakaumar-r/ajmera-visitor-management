@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h2 class="mb-4" style="font-size:22px; font-weight:600;">Create Salary Breakup</h2>
    <form action="{{ route('salary_breakups.import') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="mb-3">
            <label for="file" class="form-label">Upload Salary Excel Sheet (.xlsx)</label>
            <input type="file" name="file" class="form-control" required accept=".xlsx,.xls">
        </div>
        <button type="submit" class="btn btn-primary">Upload</button>
    </form>
</div>

<!-- <div class="container mt-5">
    <h2 class="mb-4" style="font-size:22px; font-weight:600;">Create Salary Breakup</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form action="" method="POST">
        @csrf

        <div class="row">
            @php
                $fields = [
                    'basic' => 'Basic',
                    'hra' => 'HRA',
                    'statutory_bonus' => 'Statutory Bonus',
                    'shift_allowance' => 'Shift Allowance',
                    'internet_allowance' => 'Internet Allowance',
                    'special_allowance' => 'Special Allowance',
                    'lta' => 'LTA',
                    'gross_pay' => 'Gross Pay',
                    'empl_pf' => 'Employee PF',
                    'empl_esi' => 'Employee ESI',
                    'pt' => 'Professional Tax',
                    'lwf' => 'LWF',
                    'take_home' => 'Take Home',
                    'empr_pf' => 'Employer PF',
                    'empr_esi' => 'Employer ESI',
                    'medical_insurance' => 'Medical Insurance',
                    'gratuity' => 'Gratuity',
                    'empr_lwf' => 'Employer LWF',
                    'joining_bonus' => 'Retention/Joining Bonus',
                    'ctc' => 'Cost to Company'
                ];
            @endphp

            @foreach($fields as $field => $label)
                <div class="col-md-6 mb-3">
                    <label>{{ $label }} (Per Month)</label>
                    <input 
                        type="number" 
                        step="0.01" 
                        name="{{ $field }}_month" 
                        class="form-control calc-month {{ $field === 'gross_pay' ? 'readonly' : '' }}" 
                        id="{{ $field }}_month" 
                        value="{{ old("{$field}_month", '0.00') }}" 
                        data-target="{{ $field }}_annual" 
                        {{ $field === 'gross_pay' ? 'readonly' : '' }}
                    >
                </div>
                <div class="col-md-6 mb-3">
                    <label>{{ $label }} (Per Annum)</label>
                    <input 
                        type="number" 
                        step="0.01" 
                        name="{{ $field }}_annual" 
                        class="form-control" 
                        id="{{ $field }}_annual" 
                        value="{{ old("{$field}_annual", '0.00') }}" 
                        readonly
                    >
                </div>
                
            @endforeach
        </div>

        <button type="submit" class="btn btn-primary mt-3">Save</button>
    </form>
</div> -->

<!-- <script>
    document.addEventListener('DOMContentLoaded', function () {
        const calcFields = document.querySelectorAll('.calc-month');

        function calculateAnnual(field) {
            const monthVal = parseFloat(field.value) || 0;
            const targetId = field.dataset.target;
            const targetInput = document.getElementById(targetId);
            if (targetInput) targetInput.value = (monthVal * 12).toFixed(2);
        }

        function updateGrossPay() {
            const ids = [
                'basic_month',
                'hra_month',
                'statutory_bonus_month',
                'shift_allowance_month',
                'internet_allowance_month',
                'special_allowance_month'
            ];

            let total = 0;
            ids.forEach(id => {
                const input = document.getElementById(id);
                total += parseFloat(input?.value || 0);
            });

            document.getElementById('gross_pay_month').value = total.toFixed(2);
            document.getElementById('gross_pay_annual').value = (total * 12).toFixed(2);
        }

        function updateTakeHome() {
            const gross = parseFloat(document.getElementById('gross_pay_month')?.value || 0);
            const deductions = [
                'empl_pf_month',
                'empl_esi_month',
                'pt_month',
                'lwf_month'
            ].reduce((sum, id) => {
                return sum + (parseFloat(document.getElementById(id)?.value) || 0);
            }, 0);

            const takeHomeMonth = gross - deductions;
            const takeHomeAnnual = takeHomeMonth * 12;

            document.getElementById('take_home_month').value = takeHomeMonth.toFixed(2);
            document.getElementById('take_home_annual').value = takeHomeAnnual.toFixed(2);
        }

        function updateHRAAndBonus() {
            const basicInput = document.getElementById('basic_month');
            const basic = parseFloat(basicInput?.value || 0);

            const hraInput = document.getElementById('hra_month');
            const hraAnnualInput = document.getElementById('hra_annual');
            const hraPercentage = 0.4; // Change to 0.5 for 50%

            const hraValue = (basic * hraPercentage).toFixed(2);
            hraInput.value = hraValue;
            hraAnnualInput.value = (hraValue * 12).toFixed(2);

            const bonusInput = document.getElementById('lta_month');
            const bonusAnnualInput = document.getElementById('lta_annual');
            const bonusValue = (basic * 0.0833).toFixed(2);
            bonusInput.value = bonusValue;
            bonusAnnualInput.value = (bonusValue * 12).toFixed(2);
        }

        function fullUpdate() {
            updateHRAAndBonus();
            calcFields.forEach(field => calculateAnnual(field));
            updateGrossPay();
            updateTakeHome();
        }

        calcFields.forEach(field => {
            field.addEventListener('input', fullUpdate);
            calculateAnnual(field);
        });

        fullUpdate();
    });
</script> -->




@endsection
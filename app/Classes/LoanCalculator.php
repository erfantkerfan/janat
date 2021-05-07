<?php


namespace App\Classes;


use App\AllocatedLoan;
use App\Setting;

class LoanCalculator
{
    public function getInterestRate($loanAmount, $loanInterestPerMonth, $numberOfInstallments) {
        $interestAmount = $loanInterestPerMonth*$numberOfInstallments;
        $interestRate = ($interestAmount/$loanAmount) * 100;
        return $interestRate;
    }

    public function getInterestAmount($loanInterestPerMonth, $numberOfInstallments) {
        $interestAmount = $loanInterestPerMonth*$numberOfInstallments;
        return $interestAmount;
    }

    public function getInterestRate_newType($loanAmount, $interestRate, $numberOfInstallments) {
        $calc1 = $interestRate/1200;
        $calc2 = (pow((1+$calc1), $numberOfInstallments));
        $installmentRate = ($loanAmount*$calc1*$calc2)/($calc2-1);
        $interestAmount = (($installmentRate*$numberOfInstallments) - $loanAmount);
        return $interestAmount;
    }

    public function getInterestRate_oldType($loanAmount, $interestRate, $numberOfInstallments) {
        $interestAmount = ($loanAmount*$interestRate*($numberOfInstallments+1))/2400;
        return $interestAmount;
    }

    public function getRoundedInstallmentsRate($loanAmount, $interestAmount, $numberOfInstallments, $typeOfLoanInterestPayment) {
        $padding = 100;
        if ($typeOfLoanInterestPayment === 'monthly_payment') {
            $payableAmount = $loanAmount + $interestAmount;
        } else if ($typeOfLoanInterestPayment === 'paid_at_first') {
            $payableAmount = $loanAmount;
        } else {
            $payableAmount = $loanAmount + $interestAmount;
        }
        $installmentsRateWithoutRound = $payableAmount / $numberOfInstallments;

        return floor($installmentsRateWithoutRound / $padding) * $padding;
    }

    public function isTimeToPayLastInstallment(AllocatedLoan $allocatedLoan) {
        $remainingPayableAmount = $allocatedLoan->remaining_payable_amount;
        $installmentRate = $allocatedLoan->installment_rate;
        if (
        ($remainingPayableAmount > (1 * $installmentRate)) &&
        ($remainingPayableAmount < (2 * $installmentRate))
        ) {
            return true;
        } else {
            return false;
        }
    }

    public function getLastInstallmentRate(AllocatedLoan $allocatedLoan) {
        $remainingPayableAmount = $allocatedLoan->remaining_payable_amount;
        return $remainingPayableAmount;
    }

    public function prepareLoanData($loanAmount, $numberOfInstallments) {
        $loanInterestPerMonth = Setting::where('name', 'loan_interest_per_month')->first()->value;
        $typeOfLoanInterestPayment = Setting::where('name', 'type_of_loan_interest_payment')->first()->value;
        $interestRate = $this->getInterestRate(
            $loanAmount,
            $loanInterestPerMonth,
            $numberOfInstallments
        );
        $interestAmount = $this->getInterestAmount($loanInterestPerMonth, $numberOfInstallments);
        $roundedInstallmentsRate = $this->getRoundedInstallmentsRate(
            $loanAmount,
            $interestAmount,
            $numberOfInstallments,
            $typeOfLoanInterestPayment
        );

        return [
            $roundedInstallmentsRate,
            $interestAmount,
            $interestRate,
        ];
    }
}

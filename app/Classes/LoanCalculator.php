<?php


namespace App\Classes;


use App\AllocatedLoan;

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

    public function getRoundedInstallmentsRate($loanAmount, $interestAmount, $numberOfInstallments) {
        $padding = 100;
        $payableAmount = $loanAmount + $interestAmount;
        $installmentsRateWithoutRound = $payableAmount / $numberOfInstallments;
        $roundedInstallmentsRate = floor($installmentsRateWithoutRound / $padding) * $padding;

        return $roundedInstallmentsRate;
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
}

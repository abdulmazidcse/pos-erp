<?php 

namespace App\Contracts;

interface PaymentCollectionInterface
{
    public function customerLedger();

    public function sale($amount);

    public function voucher($amount);

    public function voucherDrTransaction();

    public function voucherCrTransaction();

}

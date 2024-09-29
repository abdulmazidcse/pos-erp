<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use phpDocumentor\Reflection\Types\Self_;

class AccountVoucherTransaction extends Model
{
    use SoftDeletes;
    use HasFactory;

    public $table   = 'account_voucher_transactions';

    protected $dates = ['deleted_at'];

//    protected $guarded = [];
    public $fillable    = [
        'company_id',
        'voucher_id',
        'cost_center_id',
        'ledger_id',
        'ledger_code',
        'vaccount_type',
        'vtransaction_type',
        'debit',
        'credit',
        'reference_id',
        'transaction_sl',
        'voucher_note',
        'transaction_date'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        //
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [

    ];

    // Relations
    public function account_vouchers()
    {
        return $this->belongsTo(AccountVoucher::class, 'voucher_id', 'id');
    }

    public function account_ledgers()
    {
        return $this->belongsTo(AccountLedger::class, 'ledger_id', 'id');
    }

    public function cost_centers()
    {
        return $this->belongsTo(CostCenter::class, 'cost_center_id', 'id');
    }

    public function recursion($voucher_id,$reference_id,$vaccount_type, $recursion_type, $ledger_code)
    {
        if($recursion_type == "SL") {
            $transactions = self::where('voucher_id', $voucher_id)
                ->where('reference_id', $reference_id)
                ->where('vaccount_type', '!=', $vaccount_type)
                ->get();
        }else{
            $transactions = self::where('voucher_id', $voucher_id)
                ->where('ledger_code', $reference_id)
                ->where('vaccount_type', '!=', $vaccount_type)
                ->get();
        }

        $data_array = [];
        foreach ($transactions as $transaction) {

            if($transaction->debit > 0) {
                $btype = "D";
                if($recursion_type == "OL") {
                    $tran_ledger_data = self::where('voucher_id', $voucher_id)
                        ->where('ledger_code', $ledger_code)->first();

                    $amount = $tran_ledger_data->credit;
                }else {
                    $amount = $transaction->debit;
                }
            }else{

                $btype = "C";
                if($recursion_type == "OL") {
                    $tran_ledger_data = self::where('voucher_id', $voucher_id)
                        ->where('ledger_code', $ledger_code)->first();

                    $amount = $tran_ledger_data->debit;
                }else {
                    $amount = $transaction->credit;
                }
            }

            if($transaction->account_vouchers->cheque_no) {
                $cheque_no  = "cheque no: ".$transaction->account_vouchers->cheque_no;
                $cheque_date = "cheque date: ".$transaction->account_vouchers->cheque_date;
            }else{
                $cheque_no = "";
                $cheque_date = "";
            }

            $account_note = ($transaction->voucher_note != '') ? $transaction->voucher_note : $transaction->account_vouchers->global_note;

            $ledger_details = $transaction->ledger_code.' - '.$transaction->account_ledgers->ledger_name.' TK: '.$amount. ' '.$cheque_no.' '.$cheque_date.' '.$account_note;

            $data_array[] = [
                'id'    => $transaction->id,
                'voucher_id'    => $transaction->voucher_id,
                'cost_center_id'    => $transaction->cost_center_id,
                'ledger_id' => $transaction->ledger_id,
                'ledger_code'   => $transaction->ledger_code,
                'ledger_name'   => $transaction->account_ledgers->ledger_name ?? "",
                'ledger_details'   =>  $ledger_details,
                'vaccount_type' => $transaction->vaccount_type,
                'vtransaction_type' => $transaction->vtransaction_type,
                'debit' => ($btype == 'D') ? $amount : 0,
                'credit'    => ($btype == 'C') ? $amount : 0,
                'reference_id'  => $transaction->reference_id,
                'transaction_sl'    => $transaction->transaction_sl,
                'voucher_note'  => $account_note,
                'created_at'    => $transaction->created_at->format("Y-m-d H:i:s"),
                'updated_at'    => $transaction->updated_at->format("Y-m-d H:i:s"),

            ];
        }

        return $data_array;

    }

}

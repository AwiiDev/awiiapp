<?php

namespace App\Transformers\Payment;

use App\Transformers\Transformer;
use App\Models\Payment\OwnerWalletHistory;
use App\Base\Constants\Setting\Settings;

class OwnerWalletHistoryTransformer extends Transformer
{
    /**
     * Resources that can be included if requested.
     *
     * @var array
     */
    protected array $availableIncludes = [

    ];

    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(OwnerWalletHistory $wallet_history)
    {
        $owner = auth()->user();
        $remarks = custom_remarks_trans($wallet_history->remarks,[],$owner->lang ?? 'en') ;
        $params = [
            'id' => $wallet_history->id,
            'user_id' => $wallet_history->user_id,
            'card_id' => $wallet_history->card_id,
            'transaction_id' => $wallet_history->transaction_id,
            'amount' => $wallet_history->amount,
            'conversion' => $wallet_history->conversion,
            'merchant' => $wallet_history->merchant,
            'remarks'=>$remarks,
            'is_credit'=>$wallet_history->is_credit,
            'created_at' => $wallet_history->converted_created_at,
            'updated_at' => $wallet_history->converted_updated_at,
            'currency_code'=>$wallet_history->ownerDetail->user->countryDetail->currency_code,
            'currency_symbol'=>$wallet_history->ownerDetail->user->countryDetail->currency_symbol,
        ];

        return $params;
    }
}

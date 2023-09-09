<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ChartOfAccountExportResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'name' => $this->name,
            'account_type' => $this->account_type,
            'account_type_name' => $this->account_type_name,
            'children' => $this->transformAccounts($this->children) ,
        ];
    }

    protected function transformAccounts($accounts)
    {
        $result = [];

        foreach ($accounts as $account) {
            $children = isset($account['children']) ? $this->transformAccounts($account['children']) : [];
            
            $result[] = [
                'id' => $account['id'],
                'code' => $account['code'],
                'name' => $account['name'],
                'account_type' => $account['account_type'],
                'account_type_name' => $account['account_type_name'],
                'children' => $children,
            ];
        }

        return $result;
    }
}

<?php

namespace App\Http\Resources;

use App\Models\Category;
use App\Models\OrderProduct;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;

class DriverOrderDetail extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {

        $user = User::find($this->user_id);

        $categories =  OrderProduct::where('order_id', $this->id)->groupBy('category_id')->pluck('category_id');
        $product_data = [];
        $product_array = [];
        $category_name = '';
        foreach ($categories as $key => $category) {
            $category_name =  Category::where('id', $category)->value('name');
            $cart_data  =  OrderProduct::where('order_id', $this->id)->where('category_id', $category)->get();
            foreach ($cart_data as $key1 => $cart) {
                $p_data = Product::find($cart->product_id);
                // $add_on_service_ids  = AddOnServiceMappingInCart::where(['cart_id' => $cart->id])->pluck('add_on_service_id')->join(',');

                // $add_on_service_arr  = AddOnService::select('id', 'price', 'title')->whereIn('id', explode(',', $add_on_service_ids))->get()->toArray();
                // $add_on_service_price = array_sum(array_column($add_on_service_arr, 'price'));
                $p_image        =  asset('storage/app/public/product_images/' . $p_data->image);
                $product_data[$key1] = ['product_name' => $p_data->name, 'product_image' => $p_image, 'product_quantity' => $cart->product_quantity, 'category_id' => $category,];
            }
            $product_array[$key]['category_name'] = $category_name;
            $product_array[$key]['products'] = $product_data;
        }

        return [
            'id'                     => $this->id,
            'order_id'               => $this->order_number,
            'total_amount'           => $this->order_amount,
            'order_date'             => date('d/m/Y, h:i A', strtotime($this->created_at)),
            'delivery_date'          => date('d/m/Y', strtotime($this->delivery_date)),
            'pickup_date'            => date('d/m/Y', strtotime($this->pickup_date)),
            'pickup_time_slot_id'    => $this->pickup_time_slot_id,
            'delivery_time_slot_id'  => $this->delivery_time_slot_id,
            'delivery_time'          => $this->delivery_time,
            'pickup_time'            => $this->pickup_time,
            'order_delivery_status'  => $this->order_delivery_status,
            'payment_method'         => $this->payment_method,
            'username'               => $user->name,
            'mobile'                 => $user->mobile,
            'remark'                 => $this->remark ?? '',
            'address'                => new AddressCollection($this->addresses),
            'product_data'           => $product_array
        ];


        $product_name = OrderProduct::where('order_id', $this->id)->pluck('product_name')->join(',');
        return
            [
                'id'                     => $this->id,
                'order_id'               => $this->order_number,
                'name'                   => $this->addresses->name,
                'product_name'           => $product_name,
                'order_date'             => date('d/m/Y', strtotime($this->created_at)),
                'order_delivery_status'  => $this->order_delivery_status,
                'total_amount'           => $this->order_amount,
                'payment_method'         => $this->payment_method,
                'address'                => new AddressCollection($this->addresses),
            ];
    }
}

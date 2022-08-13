<?php

namespace App\Exports;

use App\Models\Order;
use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\FromQuery;

use DateTime;

class NewOrderExport implements WithHeadings, WithMapping, FromQuery
{
    // public  $start_date;
    public  $orders;
    public function __construct( $orders)
    {
        // $this->start_date = $start_date;
        $this->orders = $orders;
    }

    public function query()
    {
        return $this->orders;
       
    
    }
    public function headings(): array
    {
        return [
            'Order Id',
            'User Name',
            'Email',
            'Mobile',
            'Order Amount',
            'Payment Method',
            'Order  Date',
            'Deliver  Date',
            'Pickup  Date',
            'Pickup  Time',
            'Deliver  Time',
            'Order  Status',
            'Payment  Status',
            'Location',
            'Address',
            'Pincode',
            'Driver'
        ];
    }

    public function map($val): array
    {
        return [
            $val->order_number,
            $val->users->name,
            $val->users->email,
            $val->users->mobile,
            $val->order_amount,
            $val->payment_method,
            $val->created_at,
            date('d/m/Y', strtotime($val->delivery_date)),
            date('d/m/Y', strtotime($val->pickup_date)),
            $val->pickup_time,
            $val->delivery_time,
            $val->order_delivery_status,
            $val->payment_status,
            isset($val->addresses->location) ? $val->addresses->location : '',
            isset($val->addresses->address) ? $val->addresses->address : '',
            isset($val->addresses->pincode) ? $val->addresses->pincode : '',
            $val->drivers->name ?? ''
        ];
    }
}

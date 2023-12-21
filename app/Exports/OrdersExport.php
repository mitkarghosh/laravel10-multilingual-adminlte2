<?php

namespace App\Exports;

use App\Order;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\FromArray;   

class OrdersExport implements FromArray, WithHeadings, ShouldAutoSize, WithEvents
{
    private $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

	/*
        * Function name : headings
        * Purpose       : To add heading
        * Author        : 
        * Created Date  : 
        * Modified Date : 
        * Input Params  : 
        * Return Value  : 
    */
    public function headings(): array
    {
        return [
            trans('custom_admin.lab_order_id'),
            trans('custom_admin.lab_order_customer_name'),
            trans('custom_admin.lab_payment_method'),
            trans('custom_admin.lab_order_payment_status'),
            trans('custom_admin.lab_order_delivery_time'),
            trans('custom_admin.label_delivery_type'),
            trans('custom_admin.lab_order_total'),
            trans('custom_admin.lab_order_delivery_status')
        ];
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    // public function collection(): array
    // {
    //     return $this->data;
    // }

    /**
    * @return \Illuminate\Support\Array
    */
    public function array(): array
    {
        return $this->data;
    }

    /**
     * @return array
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class    => function(AfterSheet $event) {
                $cellRange = 'A1:Z1';   // All headers
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setSize(12);
            },
        ];
    }

}

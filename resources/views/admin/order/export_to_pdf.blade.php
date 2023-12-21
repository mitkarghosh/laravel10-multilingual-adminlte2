<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Schiff Binningen</title>
    <style>
    .page-break {
        page-break-after: always;
    }
    </style>
</head>
<body>
    <table width="100%" style="width: 100%; border-spacing: 0; margin: 0; padding: 0; border: none; font-family: Arial, Helvetica, sans-serif; font-size: 14px; line-height: 20px;">
        <tbody>
            <tr>
                <td style="padding: 15px 15px 30px; margin: 0;">
                    <table width="100%">
                        <tbody>
                            <tr>
                                <td style="font-size: 12px; line-height: 20px;">
                                    <div style="font-size: 16px; line-height: 20px; margin-bottom: 10px;"><strong>Schiff Binningen</strong></div>
                                    <div>{!! $siteSettings->address !!}</div>
                                @if ($siteSettings->phone_no)
                                    <div>{!! $siteSettings->phone_no !!} </div>
                                @endif
                                @if ($siteSettings->mwst_number != null)
                                    <div>{!! $siteSettings->mwst_number !!}</div>
                                @endif
                                    <div><strong>@lang('custom_admin.label_created_date'):</strong> {{date('d.m.Y')}}</div>
                                    <div><strong>@lang('custom_admin.label_range_of_date_for_the_receipts'):</strong> {{$otherPayments['date_range']}}</div>
                                </td>
                                <td style="text-align: right;">
                                    <img src="{{asset('images/site/logo.png')}}" alt="" style="width: 100px;">
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
            <tr>
                <td style="padding: 15px 15px 30px; margin: 0;">
                    <table width="100%">
                        <tbody>
                            <tr>
                                <td>
                                    <div style="color: #d7a626; font-size: 12px; margin: 0 0 10px;"><strong>@lang('custom_admin.dashboard_total_orders_print')</strong></div>
                                </td>
                                <td>
                                    <div style="color: #d7a626; font-size: 12px; margin: 0 0 10px;"><strong>@lang('custom_admin.lab_order_total_print')</strong></div>
                                </td>
                                <td>
                                    <div style="color: #d7a626; font-size: 12px; margin: 0 0 10px;"><strong>@lang('custom_admin.label_need_pay_cash_print')</strong></div>
                                </td>
                                <td>
                                    <div style="color: #d7a626; font-size: 12px; margin: 0 0 10px;"><strong>@lang('custom_admin.label_pay_online_print')</strong></div>
                                </td>
                                <td>
                                    <div style="color: #d7a626; font-size: 12px; margin: 0 0 10px;"><strong>@lang('custom_admin.label_card_on_door_print')</strong></div>
                                </td>
                                <td>
                                    <div style="color: #d7a626; font-size: 12px; margin: 0 0 10px;"><strong>@lang('custom_admin.label_cancelled_print')</strong></div>
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <div style="font-size: 11px; margin: 0 0 10px;">
                                        {{ $otherPayments['total_orders_count'] }}
                                    </div>
                                </td>
                                <td>
                                    <div style="font-size: 11px; margin: 0 0 10px;">
                                        {{ $otherPayments['order_total_amount'] }}
                                    </div>
                                </td>
                                <td>
                                    <div style="font-size: 11px; margin: 0 0 10px;">
                                        {{ $otherPayments['cash_payment_amount'] }}
                                    </div>
                                </td>
                                <td>
                                    <div style="font-size: 11px; margin: 0 0 10px;">
                                        {{ $otherPayments['online_payment_amount'] }}
                                    </div>
                                </td>
                                <td>
                                    <div style="font-size: 11px; margin: 0 0 10px;">
                                        {{ $otherPayments['card_payment_amount'] }}
                                    </div>
                                </td>
                                <td>
                                    <div style="font-size: 11px; margin: 0 0 10px;">
                                        {{ $otherPayments['cancelled_payment_amount'] }}
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
        </tbody>
    </table>
    <table width="100%" style="width: 100%; border-spacing: 0; margin: 0; padding: 0; border: none; font-family: Arial, Helvetica, sans-serif; font-size: 14px; line-height: 20px;">
        <tbody>
            <tr>
                <td style="padding: 15px; margin: 0;">
                    <table width="100%">
                        <tbody>
                            <tr>
                                <td>
                                    <div style="color: #d7a626; font-size: 12px; margin: 0 0 10px;"><strong>@lang('custom_admin.lab_order_id')</strong></div>
                                </td>
                                <td>
                                    <div style="color: #d7a626; font-size: 12px; margin: 0 0 10px;"><strong>@lang('custom_admin.label_pdf_ordered_date_and_time')</strong></div>
                                </td>
                                <td>
                                    <div style="color: #d7a626; font-size: 12px; margin: 0 0 10px;"><strong>@lang('custom_admin.label_zip_code')</strong></div>
                                </td>
                                <td>
                                    <div style="color: #d7a626; font-size: 12px; margin: 0 0 10px;"><strong>@lang('custom_admin.label_total_amount')</strong></div>
                                </td>
                                <td>
                                    <div style="color: #d7a626; font-size: 12px; margin: 0 0 10px;"><strong>@lang('custom_admin.lab_payment_method')</strong></div>
                                </td>
                            </tr>
                        @php $counterToPageBreak = 1; $page = 1; @endphp
                        @foreach ($dataToPrint as $keyData => $valData)
                            <tr>
                                <td>
                                    <div style="font-size: 11px; margin: 0 0 10px;">
                                        {{ $valData['unique_order_id'] }}
                                    </div>
                                </td>
                                <td>
                                    <div style="font-size: 11px; margin: 0 0 10px;">
                                        {{$valData['order_on']}}
                                    </div>
                                </td>
                                <td>
                                    <div style="font-size: 11px; margin: 0 0 10px;">
                                        {{ $valData['post_code'] }}
                                    </div>
                                </td>
                                <td>
                                    <div style="font-size: 11px; margin: 0 0 10px;">
                                        {{ $valData['order_total'] }}
                                    </div>
                                </td>
                                <td>
                                    <div style="font-size: 11px; margin: 0 0 10px;">
                                        {{ $valData['payment_method'] }}
                                    </div>
                                </td>
                            </tr>
                            @if ($page == 1)
                                @if ($counterToPageBreak % 28 == 0)
                                    @php $counterToPageBreak = 1; @endphp
                                    <div class="page-break"></div>
                                @else
                                    @php $counterToPageBreak++; @endphp
                                @endif
                            @else
                                @if ($counterToPageBreak % 29 == 0)
                                    @php $counterToPageBreak = 1; @endphp
                                    <div class="page-break"></div>
                                    <tr>
                                        <td colspan="5"></td>
                                    </tr>
                                @else
                                    @php $counterToPageBreak++; @endphp
                                @endif
                            @endif

                            @php $page++; @endphp
                        @endforeach
                        </tbody>
                    </table>
                </td>
            </tr>
        </tbody>
    </table>
</body>
</html>
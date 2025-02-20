<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
    <head>
        <!-- meta tags -->
        <meta http-equiv="Cache-control" content="no-cache">
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

        <!-- lang supports inclusion -->
        <style type="text/css">
            /* @font-face {
                font-family: 'Hind';
                src: url({{ asset('vendor/webkul/ui/assets/fonts/Hind/Hind-Regular.ttf') }}) format('truetype');
            } */

            @font-face {
                font-family: 'Roboto';
                src: url({{ asset('vendor/webkul/ui/assets/fonts/Noto/NotoSans-Regular.ttf') }}) format('truetype');
            }
        </style>

        @php
            /* main font will be set on locale based */
            $mainFontFamily = app()->getLocale() === 'lv' ? 'DejaVu Sans' : 'Noto Sans';
        @endphp

        <!-- main css -->
        <style type="text/css">
            * {
                font-family: '{{ $mainFontFamily }}';
            }

            body, th, td, h5 {
                font-size: 12px;
                color: #000;
            }

            .container {
                padding: 20px;
                display: block;
            }

            .invoice-summary {
                margin-bottom: 20px;
            }

            .table {
                margin: 20px 6px 0px 6px;
                border-spacing: 0px 0px 15px 0px;
            }

            .table table {
                width: 100%;
                border-collapse: collapse;
                text-align: left;
                table-layout: fixed;
            }

            .table thead th {
                font-weight: 700;
                border-top: solid 1px #d3d3d3;
                border-bottom: solid 1px #d3d3d3;
                border-left: solid 1px #d3d3d3;
                padding: 5px 12px;
                background: #005aff0d;
            }

            .table thead th:last-child {
                border-right: solid 1px #d3d3d3;
            }

            .table tbody td {
                padding: 5px 10px;
                color: #3A3A3A;
                vertical-align: middle;
                border-bottom: solid 1px #d3d3d3;
            }

            .table tbody td, p {
                margin: 0;
                color: #000;
            }

            .table tbody td .excl-tax {
                font-size: 10px;
                color: #000;
                display: block;
            }

            .table tbody td .excl-tax span {
                font-weight: 600;
            }

            .sale-summary {
                margin-top: 20px;
                float: right;
                background-color: #005aff0d;
            }

            .sale-summary tr td {
                padding: 3px 5px;
            }

            .sale-summary tr.bold {
                font-weight: 700;
            }

            .label {
                color: #000;
                font-weight: bold;
            }

            .logo {
                height: 70px;
                width: 70px;
            }

            .merchant-details {
                margin-bottom: 5px;
            }

            .merchant-details-title {
                font-weight: bold;
            }

            .text-center {
                text-align: center;
            }

            .col-6 {
                width: 42%;
                display: inline-block;
                vertical-align: top;
                margin: 0px 5px;
            }

            .table-header {
                color: #0041FF;
            }

            .align-left {
                text-align: left;
            }

            .invoice-text {
                font-size: 40px; 
                color: #3c41ff; 
                font-weight: bold;
                position: absolute; 
                width: 100%; 
                left: 0;
                text-align: center;
                top: -6px;
            }

            .without_logo {
                height: 35px;
                width: 35px;
            }
            
            .header {
                padding: 0px 2px;
                width: 100%;
                position: relative;
                border-bottom: solid 1px #d3d3d3;
                padding-bottom: 20px;
            }
        </style>
    </head>

    <body style="background-image: none; background-color: #fff;">

        <div class="container">
            <div class="row">
                <div class="col-12 header">
                    @if (core()->getConfigData('sales.invoice_settings.invoice_slip_design.logo'))
                        <div class="image" style="display:inline-block; vertical-align: middle; padding-top:8px">
                            <img class="logo" src="{{ Storage::url(core()->getConfigData('sales.invoice_settings.invoice_slip_design.logo')) }}" alt=""/>
                        </div>
                    @else
                        <div class="without_logo" style="display:inline-block; vertical-align: middle; padding-top:8px">
                        </div>
                    @endif
                    <div class="invoice-text">
                        <span>{{ __('admin::app.sales.invoices.invoice-pdf.invoice') }}</span>
                    </div>
                </div>
            </div>
            <div class="row" style="padding: 5px">
                <div class="col-12">
                    <div class="col-6">
                        <div class="merchant-details">
                            <div class="row">
                                <span class="label">@lang('admin::app.sales.invoices.invoice-pdf.invoice-id'): </span>
                                <span class="value">#{{ $invoice->increment_id ?? $invoice->id }}</span>
                            </div>

                            <div class="row">
                                <span class="label">@lang('admin::app.sales.invoices.invoice-pdf.date'): </span>
                                <span class="value">{{ core()->formatDate($invoice->created_at, 'd-m-Y') }}</span>
                            </div>

                            <div style="padding-top: 20px">
                                <span class="merchant-details-title">{{ core()->getConfigData('sales.shipping.origin.store_name') ? core()->getConfigData('sales.shipping.origin.store_name') : '' }}</span>
                            </div>

                            <div>{{ core()->getConfigData('sales.shipping.origin.address') ?? '' }}</div>

                            <div>
                                <span>{{ core()->getConfigData('sales.shipping.origin.zipcode') ?? '' }}</span>
                                <span>{{ core()->getConfigData('sales.shipping.origin.city') ?? '' }}</span>
                            </div>

                            <div>{{ core()->getConfigData('sales.shipping.origin.state') ?? '' }}</div>

                            <div>{{ core()->getConfigData('sales.shipping.origin.country') ?? '' }}</div>
                        </div>
                        <div class="merchant-details">
                            @if (core()->getConfigData('sales.shipping.origin.contact'))
                                <div><span class="merchant-details-title">@lang('admin::app.sales.invoices.invoice-pdf.contact-number'): </span> {{ core()->getConfigData('sales.shipping.origin.contact') }}</div>
                            @endif

                            @if (core()->getConfigData('sales.shipping.origin.vat_number'))
                                <div><span class="merchant-details-title">@lang('admin::app.sales.invoices.invoice-pdf.vat-number'): </span> {{ core()->getConfigData('sales.shipping.origin.vat_number') }}</div>
                            @endif
                        </div>
                    </div>

                    <div class="col-6" style="padding-left: 80px">
                        <div class="row">
                            <span class="label">@lang('admin::app.sales.invoices.invoice-pdf.order-id'): </span>
                            <span class="value">#{{ $invoice->order->increment_id }}</span>
                        </div>
                        
                        <div class="row">
                            <span class="label">@lang('admin::app.sales.invoices.invoice-pdf.order-date'): </span>
                            <span class="value">{{ core()->formatDate($invoice->order->created_at, 'd-m-Y') }}</span>
                        </div>

                        @if ($invoice->hasPaymentTerm())
                            <div class="row">
                                <span class="label">@lang('admin::app.sales.invoices.invoice-pdf.payment-terms') -</span>
                                <span class="value">{{ $invoice->getFormattedPaymentTerm() }}</span>
                            </div>
                        @endif

                        @if (core()->getConfigData('sales.shipping.origin.bank_details'))
                            <div class="row" style="padding-top: 20px">
                                <span class="merchant-details-title">
                                    @lang('admin::app.sales.invoices.invoice-pdf.bank-details'):
                                </span> 
                                <div>{{ core()->getConfigData('sales.shipping.origin.bank_details') }}</div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="invoice-summary">
                <!-- Billing & Shipping Address Details --> 
                <div class="address table">
                    <table>
                        <thead>
                            <tr>
                                <th class="table-header align-left" style="width: 50%;">
                                    {{ ucwords(trans('admin::app.sales.invoices.invoice-pdf.bill-to')) }}
                                </th>

                                @if ($invoice->order->shipping_address)
                                    <th class="table-header align-left">
                                        {{ ucwords(trans('admin::app.sales.invoices.invoice-pdf.ship-to')) }}
                                    </th>
                                @endif
                            </tr>
                        </thead>

                        <tbody>
                            <tr>
                                @foreach (['billing_address', 'shipping_address'] as $addressType)
                                    @if ($invoice->order->$addressType)
                                        <td>
                                            <p>{{ $invoice->order->$addressType->company_name ?? '' }}</p>

                                            <p>{{ $invoice->order->$addressType->registration_number ?? '' }}</p>

                                            <p>{{ $invoice->order->$addressType->name }}</p>

                                            <p>{{ $invoice->order->$addressType->address }}</p>

                                            <p>{{ $invoice->order->$addressType->postcode . ' ' . $invoice->order->$addressType->city }}</p>

                                            <p>{{ $invoice->order->$addressType->state }}</p>

                                            <p>{{ core()->country_name($invoice->order->$addressType->country) }}</p>

                                            @lang('admin::app.sales.invoices.invoice-pdf.contact') : {{ $invoice->order->$addressType->phone }}
                                        </td>
                                    @endif
                                @endforeach
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Payment & Shipping Methods -->
                <div class="payment-shipment table">
                    <table>
                        <thead>
                            <tr>
                                <th class="table-header align-left" style="width: 50%;">@lang('admin::app.sales.invoices.invoice-pdf.payment-method')</th>

                                @if ($invoice->order->shipping_address)
                                    <th class="table-header align-left">@lang('admin::app.sales.invoices.invoice-pdf.shipping-method')</th>
                                @endif
                            </tr>
                        </thead>

                        <tbody>
                            <tr>
                                <td>
                                    {{ core()->getConfigData('sales.payment_methods.' . $invoice->order->payment->method . '.title') }}

                                    @php $additionalDetails = \Webkul\Payment\Payment::getAdditionalDetails($invoice->order->payment->method); @endphp

                                    @if (! empty($additionalDetails))
                                        <div>
                                            <label class="label">{{ $additionalDetails['title'] }}:</label>
                                            <p class="value">{{ $additionalDetails['value'] }}</p>
                                        </div>
                                    @endif
                                </td>

                                @if ($invoice->order->shipping_address)
                                    <td>
                                        {{ $invoice->order->shipping_title }}
                                    </td>
                                @endif
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="items table">
                    <table>
                        <thead>
                            <tr>
                                @foreach (['sku', 'product-name', 'price', 'qty', 'subtotal'] as $item)
                                    <th class="table-header text-center">@lang('admin::app.sales.invoices.invoice-pdf.' . $item)</th>
                                @endforeach
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($invoice->order->items as $item)
                                <tr>
                                    <td class="text-center">{{ $item->getTypeInstance()->getOrderedItem($item)->sku }}</td>

                                    <td class="text-center">
                                        {{ $item->name }}

                                        @if (isset($item->additional['attributes']))
                                            <div class="item-options">

                                                @foreach ($item->additional['attributes'] as $attribute)
                                                    <b>{{ $attribute['attribute_name'] }} : </b>{{ $attribute['option_label'] }}</br>
                                                @endforeach

                                            </div>
                                        @endif
                                    </td>

                                    <td class="flex flex-col text-center">
                                        @if (core()->getConfigData('sales.taxes.sales.display_prices') == 'including_tax')
                                            {!! core()->formatBasePrice($item->base_price_incl_tax, true) !!}
                                        @elseif (core()->getConfigData('sales.taxes.sales.display_prices') == 'both')
                                            {!! core()->formatBasePrice($item->base_price_incl_tax, true) !!}
                                            
                                            <span class="excl-tax">
                                                @lang('admin::app.sales.invoices.invoice-pdf.excl-tax')
                                                
                                                <span>
                                                    {{ core()->formatPrice($item->base_price) }}
                                                </span>
                                            </span>
                                        @else
                                            {!! core()->formatBasePrice($item->base_price, true) !!}
                                        @endif
                                    </td>

                                    <td class="text-center">
                                        {{ $item->qty_ordered }}
                                    </td>


                                    <td class="text-center">
                                        @if (core()->getConfigData('sales.taxes.sales.display_subtotal') == 'including_tax')
                                            {!! core()->formatBasePrice($item->base_total_incl_tax, true) !!}
                                        @elseif (core()->getConfigData('sales.taxes.sales.display_subtotal') == 'both')
                                            {!! core()->formatBasePrice($item->base_total_incl_tax, true) !!}
                                            
                                            <span class="excl-tax">
                                                @lang('admin::app.sales.invoices.invoice-pdf.excl-tax')
                                                
                                                <span>
                                                    {{ core()->formatPrice($item->base_total) }}
                                                </span>
                                            </span>
                                        @else
                                            {!! core()->formatBasePrice($item->base_total, true) !!}
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="5" style="padding: 0; border: none;">
                                        <div style="padding: 10px; background-color: #F9F9F9; border-radius: 4px;">
                                            <b>@lang('shop::app.emails.orders.print-name-position'):</b>
                                                {{ $item->print_name  }}<br/>
                                            @if (floatval($item->print_single_price) != 0)
                                                <b>@lang('shop::app.emails.orders.print-single-price'):</b> 
                                                {{ core()->formatPrice(floatval($item->print_single_price) * $item->qty_ordered, $invoice->order->order_currency_code) }}<br/>
                                            
                                                <b>@lang('shop::app.emails.orders.print-setup'):</b> 
                                                {{ core()->formatPrice(floatval(str_replace(',', '.', $item->print_setup)), $invoice->order->order_currency_code) }}<br/>
                                            
                                                <b>@lang('shop::app.emails.orders.print-manipulation'):</b> 
                                                {{ core()->formatPrice(floatval($item->print_manipulation_cost) * $item->qty_ordered, $invoice->order->order_currency_code) }}<br/>
                                            
                                                <b>@lang('shop::app.emails.orders.print-price'):</b> 
                                                {{ core()->formatPrice(floatval($item->print_price), $invoice->order->order_currency_code) }}<br/>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Sale Summary -->
                <table class="sale-summary">
                    @if (!is_null($invoice->order->print_price) && $invoice->order->print_price != '0.00')
                    <tr>
                        <td style="text-align: left;">
                            @lang('admin::app.sales.invoices.invoice-pdf.print-total')
                        </td>
                        <td>-</td>
                        <td style="text-align: right;">
                            {{ core()->formatBasePrice(floatval($invoice->order->print_price), $invoice->order->order_currency_code) }}
                        </td>
                    </tr>
                    @endif
                    <tr>
                        <td style="text-align: left;">
                            @lang('admin::app.sales.invoices.invoice-pdf.product-price')
                        </td>
                        <td>-</td>
                        <td style="text-align: right;">
                            {{ core()->formatBasePrice(floatval($invoice->order->base_sub_total), $invoice->order->order_currency_code) }}
                        </td>
                    </tr>
                    @if (core()->getConfigData('sales.taxes.sales.display_subtotal') == 'including_tax')
                        <tr>
                            <td>@lang('admin::app.sales.invoices.invoice-pdf.subtotal')</td>
                            <td>-</td>

                            <td>{!! core()->formatBasePrice($invoice->order->base_sub_total_incl_tax, true) !!}</td>
                        </tr>
                    @elseif (core()->getConfigData('sales.taxes.sales.display_subtotal') == 'both')
                        <tr>
                            <td>@lang('admin::app.sales.invoices.invoice-pdf.subtotal-incl-tax')</td>
                            <td>-</td>
                            <td>{!! core()->formatBasePrice($invoice->order->base_sub_total_incl_tax, true) !!}</td>
                        </tr>
                        <tr>
                            <td>@lang('admin::app.sales.invoices.invoice-pdf.subtotal-excl-tax')</td>
                            <td>-</td>
                            <td>{!! core()->formatBasePrice($invoice->order->sub_total, true) !!}</td>
                        </tr>
                    @else
                        <tr>
                            <td>@lang('admin::app.sales.invoices.invoice-pdf.subtotal')</td>
                            <td>-</td>
                            <td>{!! core()->formatBasePrice($invoice->order->sub_total, true) !!}</td>
                        </tr>
                    @endif

                    @if (core()->getConfigData('sales.taxes.sales.display_shipping_amount') == 'including_tax')
                        <tr>
                            <td>@lang('admin::app.sales.invoices.invoice-pdf.shipping-handling')</td>
                            <td>-</td>
                            <td>{!! core()->formatBasePrice($invoice->base_shipping_amount_incl_tax, true) !!}</td>
                        </tr>
                    @elseif (core()->getConfigData('sales.taxes.sales.display_shipping_amount') == 'both')
                        <tr>
                            <td>@lang('admin::app.sales.invoices.invoice-pdf.shipping-handling-incl-tax')</td>
                            <td>-</td>
                            <td>{!! core()->formatBasePrice($invoice->base_shipping_amount_incl_tax, true) !!}</td>
                        </tr>
                        
                        <tr>
                            <td>@lang('admin::app.sales.invoices.invoice-pdf.shipping-handling-excl-tax')</td>
                            <td>-</td>
                            <td>{!! core()->formatBasePrice($invoice->order->base_shipping_amount, true) !!}</td>
                        </tr>
                    @else
                        <tr>
                            <td>@lang('admin::app.sales.invoices.invoice-pdf.shipping-handling')</td>
                            <td>-</td>
                            <td>{!! core()->formatBasePrice($invoice->base_shipping_amount, true) !!}</td>
                        </tr>
                    @endif

                    <tr>
                        <td>@lang('admin::app.sales.invoices.invoice-pdf.tax')</td>
                        <td>-</td>
                        <td>{!! core()->formatBasePrice($invoice->order->tax_amount, true) !!}</td>
                    </tr>

                    <tr>
                        <td>@lang('admin::app.sales.invoices.invoice-pdf.discount')</td>
                        <td>-</td>
                        <td>{!! core()->formatBasePrice($invoice->base_discount_amount, true) !!}</td>
                    </tr>

                    <tr>
                        <td colspan="3">
                            <hr>
                        </td>
                    </tr>

                    <tr>
                        <td>@lang('admin::app.sales.invoices.invoice-pdf.grand-total')</td>
                        <td>-</td>
                        <td>{!! core()->formatBasePrice($invoice->order->grand_total, true) !!}</td>
                    </tr>
                </table>
            </div>
        </div>
    </body>
</html>


<div id="receipt_wrapper">
    <style>
        .tg  {border-collapse:collapse;border-spacing:0;width: 100%;max-height: 13.9cm;table-layout: fixed; margin: 0; padding: 0;}
        .tg td{border-color:white;border-style:solid;border-width:1px; padding-left: 0.1cm;}
        .tg th{border-color:white;border-style:solid;border-width:1px; padding-left: 0.1cm;}
        .hist {
            height: 100%;
            column-width: 135px;
            -webkit-column-width: 135px;
            -moz-column-width: 135px;
            font-size: 7px;
            white-space: nowrap;
            overflow: hidden;
        }
        .items {
            margin-top: 0;
            margin-bottom: 0;
            margin-left: 0;
            margin-right: 0;
            padding: 0;
            border-collapse:collapse;
            border-spacing:0;
            font-family: monospace;
            height: 5.5cm;
            width: 100%;
            line-height: 10px;
            padding-left: 0.1cm;
            padding-right: 0.1cm;
        }

        .items th {
            border-bottom:1px solid #555555;
        }

        .idBen {
            padding-left: 2.5cm;
        }
        .timestamp {
            padding-left: 1cm;
        }
        .name {
            padding-left: 1.5cm;
            width: 100%;
            height: 0.95cm;
            overflow: hidden;
        }
        .text-right {
            padding-right: 0.1cm;
        }
        .focus {
            font-size: 1.5em;
        }
    </style>
    <table class="tg">
        <thead>
        <tr>
            <td style="width: 2cm" rowspan="6"></td>
            <td style="height: 1.8cm" colspan="5"></td>
        </tr>
        <tr>
            <td style="height: 0.95cm" colspan="3">
                <div id="sale_id" class="idBen focus"><?php echo str_pad($customer_id,5,0, STR_PAD_LEFT) . "/" . str_pad($sale_id_num, 3,0, STR_PAD_LEFT); ?></div>
            </td>
            <td colspan="2">
                <div id="sale_time" class="timestamp focus"><?php echo $transaction_time ?></div>
            </td>
        </tr>
        <tr>
            <td class="name focus" colspan="5">
                <?php
                if(isset($customer))
                {
                    echo $last_name . ' ' . $first_name . ' / ' . $customer_address . ', ' . $customer_location;
                }
                ?>
            </td>
        </tr>
        <tr>
            <td colspan="5">
                <table class="items">
                    <tr>
                        <th style="width:2.6cm;"></th>
                        <th style="width:7.5cm;"></th>
                        <th style="width:1cm;">Cant.</th>
                        <th style="width:2cm;">Unit.</th>
                        <th style="width:4.5cm;"></th>
                        <?php
                        if($this->config->item('receipt_show_tax_ind'))
                        {
                            ?>
                            <th style="width:20%;"></th>
                            <?php
                        }
                        ?>
                    </tr>
                    <?php
                    foreach($cart as $line=>$item)
                    {
                        if($item['print_option'] == PRINT_YES)
                        {
                            ?>
                            <tr>
                                <td class="text-right"><?php echo ucfirst($item['attribute_values']); ?></td>
                                <td><?php
                                    echo ucfirst($item['name']);
                                    if($this->config->item('receipt_show_description') && $item['description'] != '')
                                    {
                                        echo ' (' . $item['description'] . ')';
                                    }
                                    ?></td>
                                <td class="text-right"><?php echo to_quantity_decimals($item['quantity']); ?></td>
                                <td class="text-right"><?php echo to_currency($item['price']); ?></td>
                                <td class="text-right"><?php echo to_currency($item[($this->config->item('receipt_show_total_discount') ? 'total' : 'discounted_total')]); ?></td>
                                <?php
                                if($this->config->item('receipt_show_tax_ind'))
                                {
                                    ?>
                                    <td><?php echo $item['taxed_flag'] ?></td>
                                    <?php
                                }
                                ?>
                            </tr>
                            <?php
                            if($this->config->item('receipt_show_serialnumber'))
                            {
                                ?>
                            <tr>
                                <td></td>
                                <td></td>
                                <td>

                                    <?php echo $item['serialnumber']; ?>

                                </td>
                            </tr>
                                <?php
                            }
                            ?>
                            <?php
                            if($item['discount'] > 0)
                            {
                                ?>
                                <tr>
                                    <?php
                                    if($item['discount_type'] == FIXED)
                                    {
                                        ?>
                                        <td colspan="3" class="discount"><?php echo to_currency($item['discount']) . " " . $this->lang->line("sales_discount") ?></td>
                                        <?php
                                    }
                                    elseif($item['discount_type'] == PERCENT)
                                    {
                                        ?>
                                        <td colspan="3" class="discount"><?php echo to_decimals($item['discount']) . " " . $this->lang->line("sales_discount_included") ?></td>
                                        <?php
                                    }
                                    ?>
                                    <td class="text-right"><?php echo to_currency($item['discounted_total']); ?></td>
                                </tr>
                                <?php
                            }
                        }
                    }
                    ?>

                    <?php
                    if($this->config->item('receipt_show_total_discount') && $discount > 0)
                    {
                        ?>
                        <tr>
                            <td colspan="3" style='text-align:right;border-top:2px solid #000000;'><?php echo $this->lang->line('sales_sub_total'); ?></td>
                            <td style='text-align:right;border-top:2px solid #000000;'><?php echo to_currency($prediscount_subtotal); ?></td>
                        </tr>
                        <tr>
                            <td colspan="3" class="text-right"><?php echo $this->lang->line('sales_customer_discount'); ?>:</td>
                            <td class="text-right"><?php echo to_currency($discount * -1); ?></td>
                        </tr>
                        <?php
                    }
                    ?>
                    <tr>
                        <td colspan="3"></td>
                        <td></td>
                        <td class="text-right" style="width: 5.3cm">
                            Importe Cuenta: <?php echo to_currency($total); ?>
                        </td>
                    </tr>
                    <?php
                        $only_sale_check = FALSE;
                        $show_giftcard_remainder = FALSE;
                        $sale_due = 0;
                        $total_payments = count($payments);
                        if(count($payments) > 60) {
                            $payments = array_slice($payments, -60, 60);
                        }
                    ?>
                    <tr>
                        <th colspan="5">
                            Historial de Pagos
                        </th>
                    </tr>
                    <tr>
                        <td colspan="5">
                            <p class="hist">
                    <?php
                    $current_payments = 0;
                    foreach($payments as $payment_id=>$payment)
                    {
                        if(strpos($payment_id, $this->lang->line('sales_due'))) {
                            $sale_due = $payment['payment_amount'];
                            $total_payments--;
                            continue;
                        }
                        if(strpos($payment_id, date_format(date_create($transaction_time), 'Y-m-d')) === 0) {
                            $current_payments += $payment['payment_amount'];
                        }
                        $only_sale_check |= $payment['payment_type'] == $this->lang->line('sales_check');
                        $splitpayment = explode(':', $payment['payment_type']);
                        $show_giftcard_remainder |= $splitpayment[0] == $this->lang->line('sales_giftcard');
                        if($payment['payment_amount'] > 0) {
                        ?>
                            &#8811; <?php echo substr($splitpayment[0], 0, 19) . " " . to_currency( ($sale_due > 0 ? $payment['payment_amount'] : $total) * -1); ?><br>
                        <?php
                        }
                    }
                    ?>
                            </p>
                        </td>
                    </tr>

                    <?php
                    if(isset($cur_giftcard_value) && $show_giftcard_remainder)
                    {
                        ?>
                        <tr>
                            <td colspan="3" style="text-align:right;"><?php echo $this->lang->line('sales_giftcard_balance'); ?></td>
                            <td class="text-right"><?php echo to_currency($cur_giftcard_value); ?></td>
                        </tr>
                        <?php
                    }
                    ?>
                    <tr>
                        <td colspan="5" class="text-right">
                            Total Abonos <?php echo '(' . $total_payments .'): ' . to_currency($total - $sale_due); ?>
                            <?php
                            if($sale_due > 0) {
                                echo '&nbsp;&nbsp;&nbsp; Saldo Deudor: ' . to_currency($sale_due);
                            }
                            ?>
                        </td>
                    </tr>

                </table>
            </td>

        </tr>
        <tr>
            <td style="height: 0.95cm"colspan="3"></td>
            <td ></td>
            <td class="text-right focus" style="width: 5.3cm">
                <?php
                    if($sale_due === 0) {
                        echo 'Liquidación: ';
                    } else {
                        echo 'Abono: ';
                    }
                    echo to_currency(($sale_due > 0 ? $payment['payment_amount'] : $total));
                ?>
            </td>
        </tr>
        <tr style="width: 5.3cm">
            <td colspan="3">
                <div id="employee"><?php echo "Cajero: ".$employee; ?></div>
            </td>
            <td style="height: 1.1cm; width: 8.1cm" colspan="2">
                <div id="barcode" style="vertical-align: top">
                    <img src='data:image/png;base64,<?php echo $barcode; ?>' /><br>
                </div>
            </td>
        </tr>
        </thead>
    </table>
</div>

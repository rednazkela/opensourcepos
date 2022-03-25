<style>
    @media (min-width: 768px)
    {
        .modal-dlg .modal-dialog
        {
            width: 750px !important;
        }
    }
</style>
<table id="suspended_sales_table" class="table table-striped table-hover">
    <thead>
    <tr bgcolor="#CCC">
        <th><?php echo $this->lang->line('sales_suspended_doc_id'); ?></th>
        <th><?php echo $this->lang->line('sales_date'); ?></th>
        <?php
        if($this->config->item('dinner_table_enable') == TRUE)
        {
            ?>
            <th><?php echo $this->lang->line('sales_table'); ?></th>
            <?php
        }
        ?>
        <th><?php echo $this->lang->line('sales_customer'); ?></th>
        <th><?php echo $this->lang->line('sales_employee'); ?></th>
        <th><?php echo $this->lang->line('sales_comments'); ?></th>
        <th><?php echo $this->lang->line('sales_unsuspend_and_delete'); ?></th>
    </tr>
    </thead>
    <tbody>
    <?php
    foreach($dued_sales as $dued_sale)
    {
        ?>
        <tr>
            <td><?php echo $dued_sale['doc_id']; ?></td>
            <td><?php echo date($this->config->item('dateformat'), strtotime($dued_sale['sale_time'])); ?></td>
            <?php
            if($this->config->item('dinner_table_enable') == TRUE)
            {
                ?>
                <td><?php echo $this->Dinner_table->get_name($dued_sale['dinner_table_id']); ?></td>
                <?php
            }
            ?>
            <td>
                <?php
                if(isset($dued_sale['customer_id']))
                {
                    $customer = $this->Customer->get_info($dued_sale['customer_id']);
                    echo $customer->first_name . ' ' . $customer->last_name;
                }
                else
                {
                    ?>
                    &nbsp;
                    <?php
                }
                ?>
            </td>
            <td>
                <?php
                if(isset($dued_sale['employee_id']))
                {
                    $employee = $this->Employee->get_info($dued_sale['employee_id']);
                    echo $employee->first_name . ' ' . $employee->last_name;
                }
                else
                {
                    ?>
                    &nbsp;
                    <?php
                }
                ?>
            </td>
            <td><?php echo $dued_sale['comment']; ?></td>
            <td>
                <?php echo form_open('sales/unsuspend'); ?>
                <?php echo form_hidden('suspended_sale_id', $dued_sale['sale_id']); ?>
                <input type="submit" name="submit" value="<?php echo $this->lang->line('sales_unsuspend'); ?>" id="submit" class="btn btn-primary btn-xs pull-right">
                <?php echo form_close(); ?>
            </td>
        </tr>
        <?php
    }
    ?>
    </tbody>
</table>

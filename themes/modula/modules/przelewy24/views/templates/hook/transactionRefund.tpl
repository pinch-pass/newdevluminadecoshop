<div class="tab-content panel">
    <div class="panel-heading">
        {l s='Refunds to Przelewy24' mod='przelewy24'}
    </div>

    {if $amount > 0}
        {assign var="amountToRefund" value=$amount/100}

        <p>
            {l s='Here you can send a refund to the customer. The amount of the refund may not exceed the value of the transaction and the amount of funds available in your account.' mod='przelewy24'}
        </p>
        <p>{l s='Amount to refund' mod='przelewy24'}: {$amountToRefund} zł</p>
        <form class="form-horizontal hidden-print" method="post">
            <div class="form-group">
                <div class="col-lg-2">
                    <label for="amountToRefund">{l s='Amount' mod='przelewy24'}</label>
                    <input class="form-control" id="amountToRefund" type="number" name="amountToRefund"
                           value="{$amountToRefund}" min="0.01" max="{$amountToRefund}" step="0.01"/>
                    <input onclick="return confirm('{l s='This will generate outgoing transfer. Can you confirm the operation?' mod='przelewy24'}')"
                           class="btn btn-primary pull-right" type="submit" name="submitRefund" value="{l s='Send' mod='przelewy24'}">
                </div>
            </div>
        </form>
    {else}
        <p>{l s='The payment has already been fully refunded - no funds to make further returns.' mod='przelewy24'}</p>
    {/if}

    {if $refunds}
        <div class="table-responsive">
            <table class="table">
                <thead>
                <tr>
                    <th>
                        <span class="title_box">
                            {l s='Amount refunded' mod='przelewy24'}
                        </span>
                    </th>
                    <th>
                        <span class="title_box">
                            {l s='Date of refund' mod='przelewy24'}
                        </span>
                    </th>
                    <th>
                        <span class="title_box">
                            Status
                        </span>
                    </th>
                </tr>
                </thead>
                <tbody>
                {foreach from=$refunds item=refund}
                    <tr>
                        <td>
                            {$refund['amount_refunded']/100} zł
                        </td>
                        <td>
                            {$refund['created']}
                        </td>
                        <td>
                            {$refund['status']}
                        </td>
                    </tr>
                {/foreach}
                </tbody>
            </table>
        </div>
    {/if}
</div>


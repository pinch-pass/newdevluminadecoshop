{$notIn=$notIn|default:array()}
{if !empty($bank_name) && !in_array($bank_id, $notIn)}
    <div class="bank-element">
        <div class="bank-logo bank-logo-{$bank_id}" >
            {if $text}<span>{$text}</span>{/if}
        </div>
        <strong>{$bank_name}</strong>
    </div>
{/if}
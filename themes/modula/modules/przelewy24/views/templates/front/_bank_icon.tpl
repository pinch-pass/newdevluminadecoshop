{$notIn=$notIn|default:array()}
{$cc_id=$cc_id|default:""}
{$onclick=$onclick|default:""}
{$class=$class|default:""}
{$text=$text|default:""}
{if !empty($bank_name) && !in_array($bank_id, $notIn)}
<a class="bank-box {$class}" data-id="{$bank_id}" data-cc="{$cc_id}" onclick="{$onclick}"><div class="bank-logo bank-logo-{$bank_id}" >{if $text}<span>{$text}</span>{/if}</div><div class="bank-name">{$bank_name}</div></a>
{/if}

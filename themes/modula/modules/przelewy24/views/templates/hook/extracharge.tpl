<script>
    $(document).ready(function() {
        $('#total_shipping').after(
                '<tr id="extracharge">' +
                    '<td class="text-right">{$extracharge_text}</td>' +
                    '<td class="amount text-right nowrap">{$extracharge}</td>' +
            '</tr>');
    });
</script>

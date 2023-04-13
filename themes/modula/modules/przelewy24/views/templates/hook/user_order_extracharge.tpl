<script>
    $(document).ready(function() {
        $('.totalprice.item').before(
                '<tr class="item" id="extracharge">' +
                    '<td colspan="1"><strong>{$extracharge_text}</strong></td>' +
                    '<td colspan="4">{$extracharge}</td>' +
            '</tr>');
    });
</script>

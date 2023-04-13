{*
*   Stworzeone przez SEIGI.eu
*
*
*
*
*
*}
<picture>
    <source type="image/webp" data-srcset="{$img_url|replace:'.jpg':'.webp'}">
    <img class="img-responsive lazy"
         data-src="{$img_url}"
         alt="{$img_alt|escape:'htmlall':'UTF-8'}" {$img_attributes} />
</picture>
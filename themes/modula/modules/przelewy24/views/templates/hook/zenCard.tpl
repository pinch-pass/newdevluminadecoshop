{* ZENCARD *}

{if $mode_zencard}
    <div id="p24-zen-loader" class="loader-bg">
        <div class="loader center"></div>
    </div>
    <div style="clear:both;" id="zenCards"></div>
    <div id="zenDiscountWrapper">
        <div style="clear:both;" id="zenDiscount"></div>
        <div id="zen-discount-is-loading">
            Wczytywanie kuponu do koszyka...
        </div>
    </div>
    <div style="display: none" id="p24_zencard_products_with_tax">
        {$p24_zencard_products_with_tax}
    </div>
    <div style="clear:both;" id="zenTotal"></div>
    <hr/>
    <style>
        @keyframes pulse {
            from {
                opacity: 1;
            }
            to {
                opacity: 0.1;
            }
        }

        #zen-discount-is-loading {
            clear: both;
            display: none;
            color: red;
            font-size: 17px;
            text-align: center;
            font-weight: bold;
            animation: pulse 1.5s infinite alternate;
        }

        .loader-bg {
            height: 140px;
            background-color: rgba(0, 0, 0, 0.09);
            position: relative;
        }

        .loader {
            width: 40px;
            height: 40px;
            background: transparent;
            border: 5px inset rgb(255, 255, 255);
            border-radius: 100px;

            -webkit-animation: spin 2s infinite linear;
            -moz-animation: spin 2s infinite linear;
            animation: spin 2s infinite linear;
        }

        #p24-zen-loader .loader {
            position: absolute;
        }

        .center {
            left: 0;
            right: 0;
            top: 0;
            bottom: 0;
            margin: auto;
        }

        #p24-zen-update-cart {
            position: relative;
            width: 100%;
        }

        @-moz-keyframes spin {
            from {
                -moz-transform: rotate(0deg);
            }
            to {
                -moz-transform: rotate(360deg);
            }
        }

        @-webkit-keyframes spin {
            from {
                -webkit-transform: rotate(0deg);
            }
            to {
                -webkit-transform: rotate(360deg);
            }
        }

        @keyframes spin {
            from {
                transform: rotate(0deg);
            }
            to {
                transform: rotate(360deg);
            }
        }

        .zen-card-small-description {
            color: #777;
            font-size: 13px;
            white-space: nowrap;
            font-weight: normal;
        }
    </style>
{/if}

{* /ZENCARD *}

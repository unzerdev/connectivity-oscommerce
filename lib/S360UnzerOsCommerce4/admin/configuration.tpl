<div class="s360-osc4-admin">

    <h2>{constant("MODULE_PAYMENT_S360_UNZER_OSC4_TEXT_PAYMENT_METHODS")}</h2>

    <table class="table table-hover table-responsive responsive table-bordered table-colored no-footer dtr-inline s360-osc4-table-payment-methods">
        <thead>
        <tr>
            <th class="sorting_disabled" rowspan="1"
                colspan="1">{constant("MODULE_PAYMENT_S360_UNZER_OSC4_TEXT_METHOD")}</th>
            <th class="sorting_disabled" rowspan="1"
                colspan="1">{constant("MODULE_PAYMENT_S360_UNZER_OSC4_TEXT_STATE")}</th>
            <th class="sorting_disabled" rowspan="1"
                colspan="1">{constant("MODULE_PAYMENT_S360_UNZER_OSC4_TEXT_TM")}</th>
        </tr>
        </thead>
        <tbody>
        {if count($paymentMethods)}
            {foreach item=paymentMethod from=$paymentMethods}
                <tr class="{cycle values="odd,even"}">
                    <td class="dtr-control">{$paymentMethod.label}</td>
                    <td>
                        <select name="unzerPaymentMethod[{$paymentMethod.type}][state]">
                            <option value="0"
                                    {if $paymentMethod.config.state == 0}selected{/if}>{constant("MODULE_PAYMENT_S360_UNZER_OSC4_TEXT_DISABLED")}</option>
                            <option value="1"
                                    {if $paymentMethod.config.state == 1}selected{/if}>{constant("MODULE_PAYMENT_S360_UNZER_OSC4_TEXT_ENABLED")}</option>
                        </select>
                    </td>
                    <td>
                        {if $paymentMethod.canChangeTransactionMode}
                            <select name="unzerPaymentMethod[{$paymentMethod.type}][transaction_mode]">
                                <option value="2"
                                        {if $paymentMethod.config.transaction_mode == 2}selected{/if}>{constant("MODULE_PAYMENT_S360_UNZER_OSC4_TEXT_CHARGE")}</option>
                                <option value="1"
                                        {if $paymentMethod.config.transaction_mode == 1}selected{/if}>{constant("MODULE_PAYMENT_S360_UNZER_OSC4_TEXT_AUTHORIZE")}</option>
                            </select>
                        {else}
                            {$payment.transcationModus}
                            Charge
                        {/if}
                    </td>
                </tr>
            {/foreach}
        {else}
            <tr class="odd">
                <td colspan="4">
                    {constant("MODULE_PAYMENT_S360_UNZER_OSC4_TEXT_NO_PAYMENT_METHODS")}
                </td>
            </tr>
        {/if}
        </tbody>
    </table>

    <h2>{constant("MODULE_PAYMENT_S360_UNZER_OSC4_TEXT_CUSTOMIZATION")}</h2>

    <div class="row">
        <div class="col-md-4" style="margin-bottom: 15px">
            <label for="MODULE_PAYMENT_S360_UNZER_OSC4_HEADER_BACKGROUND_COLOR">{constant("MODULE_PAYMENT_S360_UNZER_OSC4_TEXT_HEADER_BACKGROUND_COLOR")}</label>
            <br/>
            <input type="text" name="MODULE_PAYMENT_S360_UNZER_OSC4_HEADER_BACKGROUND_COLOR"
                   id="MODULE_PAYMENT_S360_UNZER_OSC4_HEADER_BACKGROUND_COLOR"
                   value="{$MODULE_PAYMENT_S360_UNZER_OSC4_HEADER_BACKGROUND_COLOR}" maxlength="7"/>
        </div>
        <div class="col-md-4" style="margin-bottom: 15px">
            <label for="MODULE_PAYMENT_S360_UNZER_OSC4_HEADER_FONT_COLOR">{constant("MODULE_PAYMENT_S360_UNZER_OSC4_TEXT_HEADER_FONT_COLOR")}</label>
            <br/>
            <input type="text" name="MODULE_PAYMENT_S360_UNZER_OSC4_HEADER_FONT_COLOR"
                   id="MODULE_PAYMENT_S360_UNZER_OSC4_HEADER_FONT_COLOR"
                   value="{$MODULE_PAYMENT_S360_UNZER_OSC4_HEADER_FONT_COLOR}" maxlength="7"/>
        </div>
        <div class="col-md-4" style="margin-bottom: 15px">
            <label for="MODULE_PAYMENT_S360_UNZER_OSC4_HEADER_FONT_SIZE">{constant("MODULE_PAYMENT_S360_UNZER_OSC4_TEXT_HEADER_FONT_SIZE")}</label>
            <br/>
            <input type="text" name="MODULE_PAYMENT_S360_UNZER_OSC4_HEADER_FONT_SIZE"
                   id="MODULE_PAYMENT_S360_UNZER_OSC4_HEADER_FONT_SIZE"
                   value="{$MODULE_PAYMENT_S360_UNZER_OSC4_HEADER_FONT_SIZE}" maxlength="5"/>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4" style="margin-bottom: 15px">
            <label for="MODULE_PAYMENT_S360_UNZER_OSC4_SHOP_NAME_BACKGROUND_COLOR">{constant("MODULE_PAYMENT_S360_UNZER_OSC4_TEXT_SHOP_NAME_BACKGROUND_COLOR")}</label>
            <br/>
            <input type="text" name="MODULE_PAYMENT_S360_UNZER_OSC4_SHOP_NAME_BACKGROUND_COLOR"
                   id="MODULE_PAYMENT_S360_UNZER_OSC4_SHOP_NAME_BACKGROUND_COLOR"
                   value="{$MODULE_PAYMENT_S360_UNZER_OSC4_SHOP_NAME_BACKGROUND_COLOR}" maxlength="7"/>
        </div>
        <div class="col-md-4" style="margin-bottom: 15px">
            <label for="MODULE_PAYMENT_S360_UNZER_OSC4_SHOP_NAME_FONT_COLOR">{constant("MODULE_PAYMENT_S360_UNZER_OSC4_TEXT_SHOP_NAME_FONT_COLOR")}</label>
            <br/>
            <input type="text" name="MODULE_PAYMENT_S360_UNZER_OSC4_SHOP_NAME_FONT_COLOR"
                   id="MODULE_PAYMENT_S360_UNZER_OSC4_SHOP_NAME_FONT_COLOR"
                   value="{$MODULE_PAYMENT_S360_UNZER_OSC4_SHOP_NAME_FONT_COLOR}" maxlength="7"/>
        </div>
        <div class="col-md-4" style="margin-bottom: 15px">
            <label for="MODULE_PAYMENT_S360_UNZER_OSC4_SHOP_NAME_FONT_SIZE">{constant("MODULE_PAYMENT_S360_UNZER_OSC4_TEXT_SHOP_NAME_FONT_SIZE")}</label>
            <br/>
            <input type="text" name="MODULE_PAYMENT_S360_UNZER_OSC4_SHOP_NAME_FONT_SIZE"
                   id="MODULE_PAYMENT_S360_UNZER_OSC4_SHOP_NAME_FONT_SIZE"
                   value="{$MODULE_PAYMENT_S360_UNZER_OSC4_SHOP_NAME_FONT_SIZE}" maxlength="5"/>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4" style="margin-bottom: 15px">
            <label for="MODULE_PAYMENT_S360_UNZER_OSC4_TAGLINE_BACKGROUND_COLOR">{constant("MODULE_PAYMENT_S360_UNZER_OSC4_TEXT_TAGLINE_BACKGROUND_COLOR")}</label>
            <br/>
            <input type="text" name="MODULE_PAYMENT_S360_UNZER_OSC4_TAGLINE_BACKGROUND_COLOR"
                   id="MODULE_PAYMENT_S360_UNZER_OSC4_TAGLINE_BACKGROUND_COLOR"
                   value="{$MODULE_PAYMENT_S360_UNZER_OSC4_TAGLINE_BACKGROUND_COLOR}" maxlength="7"/>
        </div>
        <div class="col-md-4" style="margin-bottom: 15px">
            <label for="MODULE_PAYMENT_S360_UNZER_OSC4_TAGLINE_FONT_COLOR">{constant("MODULE_PAYMENT_S360_UNZER_OSC4_TEXT_TAGLINE_FONT_COLOR")}</label>
            <br/>
            <input type="text" name="MODULE_PAYMENT_S360_UNZER_OSC4_TAGLINE_FONT_COLOR"
                   id="MODULE_PAYMENT_S360_UNZER_OSC4_TAGLINE_FONT_COLOR"
                   value="{$MODULE_PAYMENT_S360_UNZER_OSC4_TAGLINE_FONT_COLOR}" maxlength="7"/>
        </div>
        <div class="col-md-4" style="margin-bottom: 15px">
            <label for="MODULE_PAYMENT_S360_UNZER_OSC4_TAGLINE_FONT_SIZE">{constant("MODULE_PAYMENT_S360_UNZER_OSC4_TEXT_TAGLINE_FONT_SIZE")}</label>
            <br/>
            <input type="text" name="MODULE_PAYMENT_S360_UNZER_OSC4_TAGLINE_FONT_SIZE"
                   id="MODULE_PAYMENT_S360_UNZER_OSC4_TAGLINE_FONT_SIZE"
                   value="{$MODULE_PAYMENT_S360_UNZER_OSC4_TAGLINE_FONT_SIZE}" maxlength="5"/>
        </div>
    </div>

    <h2>{constant("MODULE_PAYMENT_S360_UNZER_OSC4_TEXT_WEBHOOK_MANAGEMENT")}</h2>

    <table class="table table-hover table-responsive responsive table-bordered table-colored no-footer dtr-inline s360-osc4-table-webhooks">
        <thead>
        <tr>
            <th class="sorting_disabled" width="200">ID</th>
            <th class="sorting_disabled">Event</th>
            <th class="sorting_disabled">For Platform</th>
            <th class="sorting_disabled">Url
                <button class="btn btn-right"
                        onclick="s360UnzerOsCommerceInitWebhooks(); return false;">{constant("MODULE_PAYMENT_S360_UNZER_OSC4_REGISTER_WEBHOOKS")}</button>
            </th>
        </tr>
        </thead>
        <tbody>
        {if count($webhooks)}
            {foreach item=webhook from=$webhooks}
                <tr class="{cycle values="odd,even"}">
                    <td class="dtr-control">{$webhook.webhook_id}</td>
                    <td>{$webhook.event}</td>
                    <td align="center">{if $webhook.assignedToPlatform}
                            <span class="icon icon-2x icon-check"></span>
                        {/if}</td>
                    <td>
                        {$webhook.url}
                        <button class="btn btn-delete btn-right"
                                onclick="s360UnzerOsCommerceDeleteWebhook('{$webhook.webhook_id}'); return false;"></button>
                    </td>
                </tr>
            {/foreach}
        {else}
            <tr class="odd">
                <td colspan="4">
                    {constant("MODULE_PAYMENT_S360_UNZER_OSC4_TEXT_NO_WEBHOOKS")}
                </td>
            </tr>
        {/if}
        </tbody>
    </table>
</div>

<script>
    function s360UnzerOsCommerceDeleteWebhook(webhookId) {
        if (confirm("{constant("MODULE_PAYMENT_S360_UNZER_OSC4_TEXT_CONFIRM_DELETE_WEBHOOK")}")) {
            var formData = new FormData();
            formData.append("webhookId", webhookId);

            fetch('{$deleteWebhookUrl}', {
                method: 'POST',
                body: formData
            })
                .then(response => {
                    location.reload();
                })
                .catch(error => {
                    alert(error);
                });
        }
    }

    function s360UnzerOsCommerceInitWebhooks() {
        var formData = new FormData();
        formData.append("action", "initWebhooks");

        fetch('{$initWebhookUrl}', {
            method: 'POST',
            body: formData
        })
            .then(response => {
                location.reload();
            })
            .catch(error => {
                alert(error);
            });
    }
</script>

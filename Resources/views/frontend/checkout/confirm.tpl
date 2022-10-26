{extends file="parent:frontend/checkout/confirm.tpl"}

{* Terms of service *}
{block name='frontend_checkout_confirm_agb'}
    {$smarty.block.parent}
    {if $sPayment["name"] == 'axytos_kauf_auf_rechnung'}
        <li class="block-group row--tos">
            <span class="block column--checkbox">
                <input type="checkbox" required aria-required="true" id="axytos_conditions" name="axytos_conditions" />
            </span>
            {* AGB label *}
            <span class="block column--label">
                <label for="axytos_conditions">{s name="CreditCheckAgreementText"}I agree that a credit check can be carried out on my person. I can revoke my consent at any time for the future. I am aware that a revocation has no effect on the lawfulness of the processing that has taken place up to the revocation and that in the event of a revocation not all payment methods can be offered to me.{/s}</label>
                <br>
                <a id="axytosCreditCheckLink">{s name="CreditCheckAgreementInfoLinkText"}More information on the credit check.{/s}</a>
            </span>
        </li>
    {/if}
{/block}
{block name="frontend_index_content"}
    {$smarty.block.parent}
    <div id="axytosCreditCheck" class="js--modal sizing--content">
        <div class="header">
            <div class="title">{s name="CreditCheckInfoModalTitle"}Credit Check Info Text{/s}</div>
        </div>
        <div class="content" style="padding: 10px;">
            {$creditCheckAgreementInfo}
        </div>

        <a id="axytosCreditCheckCloseButton" class="btn icon--cross is--small btn--grey modal--close">
        </a>
    </div>

    <style>
        #axytosCreditCheck {
            display: none;
            opacity: 1;
            width: 100%;
            max-width: 600px;
            height: 600px;
            min-height: min(400px, 100%);
            position: fixed;
            top: 50%;
            left: 50%;
            right: unset;
            transform: translate(-50%, -50%);
        }

        #axytosCreditCheck.js--modal .content {
            height: unset;
        }

        #axytosCreditCheckLink {
            cursor: pointer;
            user-select: none;
        }
    </style>
    <script>
        const axytosCreditCheck = document.getElementById("axytosCreditCheck");

        const axytosCreditCheckLink = document.getElementById("axytosCreditCheckLink");
        axytosCreditCheckLink.addEventListener("click", () => {
            axytosCreditCheck.style.display = 'block';
        });

        const axytosCreditCheckCloseButton = document.getElementById("axytosCreditCheckCloseButton");
        axytosCreditCheckCloseButton.addEventListener("click", () => {
            axytosCreditCheck.style.display = 'none';
        });
    </script>
{/block}
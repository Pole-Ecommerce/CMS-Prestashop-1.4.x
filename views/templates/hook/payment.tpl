{*
 * E-Transactions PrestaShop Module
 *
 * Feel free to contact E-Transactions at support@e-transactions.fr for any
 * question.
 *
 * LICENSE: This source file is subject to the version 3.0 of the Open
 * Software License (OSL-3.0) that is available through the world-wide-web
 * at the following URI: http://opensource.org/licenses/OSL-3.0. If
 * you did not receive a copy of the OSL-3.0 license and are unable 
 * to obtain it through the web, please send a note to
 * support@e-transactions.fr so we can mail you a copy immediately.
 *
 * @author Olivier - BM Services (http://www.bm-services.com)
 * @copyright 2012-2015 E-Transactions
 * @license http://opensource.org/licenses/OSL-3.0
 * @link http://www.e-transactions.fr/
 * @since 2
 *}
{if $ETransactionsReason == 'cancel'}
<div class="ETRANS_epayment_error" style="margin-left:15px;">
	{l s='Payment canceled.' mod='ETransactionsEpayment'}
</div>
{/if}

{if $ETransactionsReason == 'error'}
<div class="ETRANS_epayment_error" style="margin-left:15px;">
	{l s='Payment refused by ETransactions.' mod='ETransactionsEpayment'}
</div>
{/if}

{if !$ETransactionsProduction}
<div class="ETRANS_epayment_error" style="margin-left:15px;">
	{l s='The payment ETransactions is in test mode.' mod='ETransactionsEpayment'}
</div>

{/if}
{* Standard payment *}
{foreach from=$ETransactionsCards item=card name=cards}
<p class="payment_module ETRANS_epayment_module">
	<a href="{$card.url|escape:'html'}">
		<img src="{$card.image}" alt="{$card.card}" title="{$card.card}" />
		{l s='Pay by' mod='ETransactionsEpayment'} {$card.label}
	</a>
</p>
{/foreach}

{* Recurring payment *}
{if !empty($ETransactionsRecurring)}
<p class="payment_module ETRANS_epayment_module">
	<img src="{$ETransactionsImagePath}/Paiement_3X.png"/>
	{foreach from=$ETransactionsRecurring item=card name=cards}
		<a href="{$card.url|escape:'html'}&amp;recurring=1">
			<img src="{$card.image}" alt="{$card.card}" title="{$card.card}" />
		</a>
	{/foreach}
	{l s='Pay by' mod='ETransactionsEpayment'} {l s='card 3 times without fees' mod='ETransactionsEpayment'}
</p>
{/if}

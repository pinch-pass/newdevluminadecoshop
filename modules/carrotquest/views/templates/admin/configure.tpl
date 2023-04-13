{*
* 2017-2019 Carrot quest
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* @author Carrot quest <support@carrotquest.io>
* @copyright 2017-2019 Carrot quest
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*}


{if isset($confirmation)}
    <div class="alert alert-success">{$confirmation|escape:'html':'UTF-8'}</div>
{/if}

<div class="panel">
    <h3>
        <i class="icon icon-credit-card"></i> {l s='Carrot quest is a customer service, combining all instruments for marketing automation, sales and communications for your web app. Goal is to increase first and second sales.' mod='carrotquest'}
    </h3>
    <p>
        <strong>{l s='Overview' mod='carrotquest'}</strong>
        <br/>
        {l s='The module integrates your store with SaaS Carrot quest for perfect marketing automation and user support' mod='carrotquest'}
    </p>
    <p>
        <strong>{l s='What this product does for you' mod='carrotquest'}</strong>
    <ul>
        <li>{l s='Collect your user data in real time for perfect relationships' mod='carrotquest'}</li>
        <li>{l s='Boost your conversion with marketing automation and triggered messages' mod='carrotquest'}</li>
        <li>{l s='Use pop-ups and online chat for supporting user' mod='carrotquest'}</li>
        <li>{l s='Do marketing research with funnels, A/B-test and reports' mod='carrotquest'}</li>
        <li>{l s='Use tens of integrations (email, Google Analytics, Facebook, Zendesk, Viber and many others)' mod='carrotquest'}</li>
    </ul>
    </p>
    <p>
        <strong>{l s='Features' mod='carrotquest'}</strong>
    <ul>
        <li>{l s='Collecting user data (events and props)' mod='carrotquest'}</li>
        <li>{l s='eCRM' mod='carrotquest'}</li>
        <li>{l s='Online-chat' mod='carrotquest'}</li>
        <li>{l s='Pop-ups' mod='carrotquest'}</li>
        <li>{l s='Email-marketing' mod='carrotquest'}</li>
        <li>{l s='Drip campaigns' mod='carrotquest'}</li>
        <li>{l s='Marketing automation' mod='carrotquest'}</li>
        <li>{l s='A/B-tests' mod='carrotquest'}</li>
        <li>{l s='Sales funnels' mod='carrotquest'}</li>
    </ul>
    </p>
</div>

<div class="panel">
    <h3><i class="icon icon-tags"></i>{l s='Install' mod='carrotquest'}</h3>
    <p>
        <!-- &raquo; -->
        <strong>{l s='Installing Module' mod='carrotquest'}</strong>
    <ul>
        <li>{l s='In Carrot quest go to Settings > API Keys and copy API Key, API Secret and User Auth Key' mod='carrotquest'}</li>
        <li>{l s='Insert API Key, API Secret and User Auth Key in the corresponding fields in the module settings' mod='carrotquest'}</li>
        <li>{l s='If you want to send User ID to Carrot quest turn on the corresponding switch' mod='carrotquest'}</li>
        <li>{l s='Save module' mod='carrotquest'}</li>
    </ul>
    {l s='Visit your site to see that the Carrot quest widget appeared on your site. Now Carrot quest will collect user data (events and props). Just enjoy your marketing.' mod='carrotquest'}
    </p>
</div>

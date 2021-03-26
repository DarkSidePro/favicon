{*
* 2007-2019 PrestaShop
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
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2019 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
{if isset($favicons)}
    <link rel="apple-touch-icon-precomposed" sizes="57x57" href="{$urls.base_url}{$favicons.57}" />
    <link rel="apple-touch-icon-precomposed" sizes="114x114" href="{$urls.base_url}{$favicons.114}" />
    <link rel="apple-touch-icon-precomposed" sizes="72x72" href="{$urls.base_url}{$favicons.72}" />
    <link rel="apple-touch-icon-precomposed" sizes="144x144" href="{$urls.base_url}{$favicons.144}" />
    <link rel="apple-touch-icon-precomposed" sizes="60x60" href="{$urls.base_url}{$favicons.60}" />
    <link rel="apple-touch-icon-precomposed" sizes="120x120" href="{$urls.base_url}{$favicons.120}" />
    <link rel="apple-touch-icon-precomposed" sizes="76x76" href="{$urls.base_url}{$favicons.76}" />
    <link rel="apple-touch-icon-precomposed" sizes="152x152" href="{$urls.base_url}{$favicons.152}" />
    <link rel="icon" type="image/png" href="{$urls.base_url}{$favicons.196}" sizes="196x196" />
    <link rel="icon" type="image/png" href="{$urls.base_url}{$favicons.96}" sizes="96x96" />
    <link rel="icon" type="image/png" href="{$urls.base_url}{$favicons.32}" sizes="32x32" />
    <link rel="icon" type="image/png" href="{$urls.base_url}{$favicons.16}" sizes="16x16" />
    <link rel="icon" type="image/png" href="{$urls.base_url}{$favicons.128}" sizes="128x128" />
    <meta name="msapplication-TileImage" content="{$urls.base_url}{$favicons.144}" />
    <meta name="msapplication-square70x70logo" content="{$urls.base_url}{$favicons.70}" />
    <meta name="msapplication-square150x150logo" content="{$urls.base_url}{$favicons.150}" />
    <meta name="msapplication-square310x310logo" content="{$urls.base_url}{$favicons.310}" />
{/if}

{if isset($favLogo)}
    <meta name="msapplication-wide310x150logo" content="{$urls.base_url}{$favLogo}" />
{/if}
<meta name="application-name" content="{$shop.name}"/>
<meta name="msapplication-TileColor" content="{$favicolor}" />


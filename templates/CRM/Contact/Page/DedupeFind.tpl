{*
 +--------------------------------------------------------------------+
 | CiviCRM version 4.5                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2014                                |
 +--------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                    |
 |                                                                    |
 | CiviCRM is free software; you can copy, modify, and distribute it  |
 | under the terms of the GNU Affero General Public License           |
 | Version 3, 19 November 2007 and the CiviCRM Licensing Exception.   |
 |                                                                    |
 | CiviCRM is distributed in the hope that it will be useful, but     |
 | WITHOUT ANY WARRANTY; without even the implied warranty of         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
 | See the GNU Affero General Public License for more details.        |
 |                                                                    |
 | You should have received a copy of the GNU Affero General Public   |
 | License and the CiviCRM Licensing Exception along                  |
 | with this program; if not, contact CiviCRM LLC                     |
 | at info[AT]civicrm[DOT]org. If you have questions about the        |
 | GNU Affero General Public License or the licensing of CiviCRM,     |
 | see the CiviCRM license FAQ at http://civicrm.org/licensing        |
 +--------------------------------------------------------------------+
*}
{if $action eq 2 || $action eq 16}
<div class="form-item">
  <div class="crm-accordion-wrapper crm-search_filters-accordion">
    <div class="crm-accordion-header">
    {ts}Filter Contacts{/ts}</a>
    </div><!-- /.crm-accordion-header -->
    <div class="crm-accordion-body">
      <table class="no-border form-layout-compressed" id="searchOptions" style="width:100%;">
          <tr>
            <td class="crm-contact-form-block-first_name" colspan="2"><label for="first_name">{ts}{$form.first_name.label}{/ts}</label><br />
              {$form.first_name.html}
            </td>
            <td class="crm-contact-form-block-last_name"><label for="last_name">{$form.last_name.label}</label><br />
              {$form.last_name.html}
            </td>
          </tr>
          <tr>
            <td class="crm-contact-form-block-email email medium crm-form-text" colspan="2"><label for="email">{ts}{$form.email.label}{/ts}</label><br />
              {$form.email.html}
            </td>
            <td class="crm-contact-form-block-postal_code crm_postal_code crm-form-text" colspan="2"><label for="email">{ts}{$form.postal_code.label}{/ts}</label><br />
              {$form.postal_code.html}
            </td>
            <td style="vertical-align: bottom;">
              <span class="crm-button"><input id="search-filter" class="crm-form-submit default" name="_qf_Basic_refresh" value="Search" type="button" /></span>
            </td>
          </tr>
      </table>
    </div><!-- /.crm-accordion-body -->
  </div><!-- /.crm-accordion-wrapper -->
  <table id="dupePairs" class='pagerDisplay'>
    <thead>
<!--    <tr class="columnheader"><th class="sortable">{ts}Contact{/ts} 1</th><th id="sortable">{ts}Contact{/ts} 2 ({ts}Duplicate{/ts})</th><th id="sortable">{ts}Threshold{/ts}</th><th id="sortable">&nbsp;</th></tr>-->
      <tr class="columnheader">
        <th class="crm-contact">{ts}Contact{/ts} 1</th>
        <th class="crm-contact-duplicate">{ts}Contact{/ts} 2 ({ts}Duplicate{/ts})</th>
        <th class="crm-threshold">{ts}Threshold{/ts}</th>
        <th class="crm-empty">&nbsp;</th>
      </tr>
    </thead>
  </table>
  {*include file="CRM/common/jsortable.tpl" sourceUrl=$sourceUrl useAjax=1*}
  {if $cid}
    <table style="width: 45%; float: left; margin: 10px;">
      <tr class="columnheader"><th colspan="2">{ts 1=$main_contacts[$cid]}Merge %1 with{/ts}</th></tr>
      {foreach from=$dupe_contacts[$cid] item=dupe_name key=dupe_id}
        {if $dupe_name}
          {capture assign=link}<a href="{crmURL p='civicrm/contact/view' q="reset=1&cid=$dupe_id"}">{$dupe_name}</a>{/capture}
          {capture assign=merge}<a href="{crmURL p='civicrm/contact/merge' q="reset=1&cid=$cid&oid=$dupe_id"}">{ts}merge{/ts}</a>{/capture}
          <tr class="{cycle values="odd-row,even-row"}">
      <td>{$link}</td>
      <td style="text-align: right">{$merge}</td>
      <td style="text-align: right"><a id='notDuplicate' href="#" title={ts}not a duplicate{/ts} onClick="processDupes( {$main.srcID}, {$main.dstID}, 'dupe-nondupe' );return false;">{ts}not a duplicate{/ts}</a></td>
      </tr>
        {/if}
      {/foreach}
    </table>
  {/if}
</div>

{if $context eq 'search'}
   <a href="{$backURL}" class="button"><span>{ts}Done{/ts}</span></a>
{else}
   {if $gid}
      {capture assign=backURL}{crmURL p="civicrm/contact/dedupefind" q="reset=1&rgid=`$rgid`&gid=`$gid`&action=renew" a=1}{/capture}
   {else}
      {capture assign=backURL}{crmURL p="civicrm/contact/dedupefind" q="reset=1&rgid=`$rgid`&action=renew" a=1}{/capture}
   {/if}
   <a href="{$backURL}" title="{ts}Refresh List of Duplicates{/ts}" onclick="return confirm('{ts escape="js"}This will refresh the duplicates list. Click OK to proceed.{/ts}');" class="button"><span>{ts}Refresh Duplicates{/ts}</span></a>

   {if $gid}
      {capture assign=backURL}{crmURL p="civicrm/contact/dedupefind" q="reset=1&rgid=`$rgid`&gid=`$gid`&action=map" a=1}{/capture}
   {else}
      {capture assign=backURL}{crmURL p="civicrm/contact/dedupefind" q="reset=1&rgid=`$rgid`&action=map" a=1}{/capture}
   {/if}
   <a href="{$backURL}" title="{ts}Batch Merge Duplicate Contacts{/ts}" onclick="return confirm('{ts escape="js"}This will run the batch merge process on the listed duplicates. The operation will run in safe mode - only records with no direct data conflicts will be merged. Click OK to proceed if you are sure you wish to run this operation.{/ts}');" class="button"><span>{ts}Batch Merge Duplicates{/ts}</span></a>

   {capture assign=backURL}{crmURL p="civicrm/contact/deduperules" q="reset=1" a=1}{/capture}
  <a href="{$backURL}" class="button crm-button-type-cancel"><span>{ts}Done{/ts}</span></a>
{/if}
<div style="clear: both;"></div>
{else}
{include file="CRM/Contact/Form/DedupeFind.tpl"}
{/if}

{* process the dupe contacts *}
{include file='CRM/common/dedupe.tpl'}
{literal}
<script type="text/javascript">
CRM.$(function($) {
  buildDedupeContacts(false);

  $( "#search-filter" ).click(function() {
    buildDedupeContacts(true);
  });

  function buildDedupeContacts(filterSearch) {
    if (filterSearch) {
      oTable.fnDestroy();
    }

    var columns = '';
    var count = 0;
    var sourceUrl = {/literal}'{$sourceUrl}'{literal};
    console.log(sourceUrl);

    var ZeroRecordText = {/literal}'{ts escape="js"}No matches found{/ts}'{literal};

    oTable = $('#dupePairs').dataTable({
      "multipleSelection": true,
      "bFilter"    : false,
      "bAutoWidth" : false,
      "aaSorting"  : [],
      "aoColumns"  : [
        {sClass:'crm-contact'},
        {sClass:'crm-contact-duplicate'},
        {sClass:'crm-threshold'},
        {sClass:'crm-empty', bSortable:false}
      ],
      "bProcessing": true,
      "sPaginationType": "full_numbers",
      "sDom"       : '<"crm-datatable-pager-top"lfp>rt<"crm-datatable-pager-bottom"ip>',
      "bServerSide": true,
      "bJQueryUI": true,
      "sAjaxSource": sourceUrl,
      "iDisplayLength": 10,
      "oLanguage": {
        "sZeroRecords":  ZeroRecordText,
        "sProcessing":   {/literal}"{ts escape='js'}Processing...{/ts}"{literal},
        "sLengthMenu":   {/literal}"{ts escape='js'}Show _MENU_ entries{/ts}"{literal},
        "sInfo":         {/literal}"{ts escape='js'}Showing _START_ to _END_ of _TOTAL_ entries{/ts}"{literal},
        "sInfoEmpty":    {/literal}"{ts escape='js'}Showing 0 to 0 of 0 entries{/ts}"{literal},
        "sInfoFiltered": {/literal}"{ts escape='js'}(filtered from _MAX_ total entries){/ts}"{literal},
        "sSearch":       {/literal}"{ts escape='js'}Search:{/ts}"{literal},
        "oPaginate": {
          "sFirst":    {/literal}"{ts escape='js'}First{/ts}"{literal},
          "sPrevious": {/literal}"{ts escape='js'}Previous{/ts}"{literal},
          "sNext":     {/literal}"{ts escape='js'}Next{/ts}"{literal},
          "sLast":     {/literal}"{ts escape='js'}Last{/ts}"{literal}
        }
      },
      "fnDrawCallback": function() { setSelectorClass(); },
      "fnServerData": function ( sSource, aoData, fnCallback ) {
        if ( filterSearch ) {
          aoData.push(
            {name:'firstName', value: CRM.$("#first_name").val()},
            {name:'lastName', value: CRM.$("#last_name").val()}
          );
        }
        $.ajax( {
          "dataType": 'json',
          "type": "POST",
          "url": sSource,
          "data": aoData,
          "success": fnCallback,
          // CRM-10244
          "dataFilter": function(data, type) { return data.replace(/[\n\v\t]/g, " "); }
        });
      }
    });
  }

  function setSelectorClass( ) {
    $('#dupePairs td:last-child').each( function( ) {
      $(this).parent().addClass($(this).text() );
    });
  }
});
</script>
{/literal}

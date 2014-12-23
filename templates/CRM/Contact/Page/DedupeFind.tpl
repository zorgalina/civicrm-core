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
  <table id="dupePairs" class="display">
    <thead>
<!--    <tr class="columnheader"><th class="sortable">{ts}Contact{/ts} 1</th><th id="sortable">{ts}Contact{/ts} 2 ({ts}Duplicate{/ts})</th><th id="sortable">{ts}Threshold{/ts}</th><th id="sortable">&nbsp;</th></tr>-->
      <tr> 
        <th class="crm-dedupe-merge">&nbsp;</th>
        <th class="crm-contact">{ts}Contact{/ts} 1</th>
        <th class="crm-contact">{ts}Email{/ts} 1</th>
        <th class="crm-contact">{ts}Street Address{/ts} 1</th>
        <th class="crm-contact">{ts}Postcode{/ts} 1</th>
        <th class="crm-contact-duplicate">{ts}Contact{/ts} 2 ({ts}Duplicate{/ts})</th>
        <th class="crm-contact-duplicate">{ts}Email{/ts} 2 ({ts}Duplicate{/ts})</th>
        <th class="crm-contact-duplicate">{ts}Street Address{/ts} 2 ({ts}Duplicate{/ts})</th>
        <th class="crm-contact-duplicate">{ts}Postcode{/ts} 2 ({ts}Duplicate{/ts})</th>
        <th class="crm-threshold">{ts}Threshold{/ts}</th>
        <th class="crm-empty">&nbsp;</th>
      </tr>
    </thead>
    <tfoot>
      <tr class="columnfooter">
        <th class="crm-dedupe-merge">&nbsp;</th>
        <th class="crm-contact">{ts}Contact{/ts} 1</th>
        <th class="crm-contact">{ts}Email{/ts} 1</th>
        <th class="crm-contact">{ts}Street Address{/ts} 1</th>
        <th class="crm-contact">{ts}Postcode{/ts} 1</th>
        <th class="crm-contact-duplicate">{ts}Contact{/ts} 2 ({ts}Duplicate{/ts})</th>
        <th class="crm-contact-duplicate">{ts}Email{/ts} 2 ({ts}Duplicate{/ts})</th>
        <th class="crm-contact-duplicate">{ts}Street Address{/ts} 2 ({ts}Duplicate{/ts})</th>
        <th class="crm-contact-duplicate">{ts}Postcode{/ts} 2 ({ts}Duplicate{/ts})</th>
        <th class="crm-threshold">{ts}Threshold{/ts}</th>
        <th class="crm-empty">&nbsp;</th>
      </tr>
    </tfoot>
  </table>
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
  var sourceUrl = {/literal}'{$sourceUrl}'{literal};
  $('#dupePairs').dataTable({
    "ajax": sourceUrl,
    "columns"  : [
      {data: "is_selected_input"},
      {data: "src"},
      {data: "src_email"},
      {data: "src_street"},
      {data: "src_postcode"},
      {data: "dst"},
      {data: "dst_email"},
      {data: "dst_street"},
      {data: "dst_postcode"},
      {data: "weight"},
      {data: "actions"},
    ],
    "columnDefs": [ {
      "targets": [0, 4],
      "orderable": false
    }],
    rowCallback: function (row, data) {
      // Set the checked state of the checkbox in the table
      $('input.crm-dedupe-select', row).prop('checked', data.is_selected == 1);
      if (data.is_selected == 1) {
      $(row).toggleClass('selected');
      }
    }
  });

  // inline search boxes placed in tfoot
  $('#dupePairs tfoot th').each( function () {
    var title = $('#dupePairs thead th').eq($(this).index()).text();
    if (title.length > 1) {
      $(this).html( '<input type="text" placeholder="Search '+title+'" />' );
    }
  });

  // apply dataTable
  var table = $('#dupePairs').DataTable();
  
  // apply the search
  $('#dupePairs').DataTable().columns().eq(0).each(function (colIdx) {
    $( 'input', table.column(colIdx).footer()).on( 'keyup change', function () {
      table
        .column(colIdx)
        .search(this.value)
        .draw();
    });
  });

  // apply selected class on click of a row
  $('#dupePairs tbody').on('click', 'tr', function() {
    $(this).toggleClass('selected');
    $('input.crm-dedupe-select', this).prop('checked', $(this).hasClass("selected"));
    var sth = $('input.crm-dedupe-select', this);
    console.log(sth);
    toggleDedupeSelect(sth);
  });
});

function toggleDedupeSelect(element) {
  var is_selected = CRM.$(element).prop('checked') ? 1: 0;
  var id = CRM.$(element).prop('name').substr(5);

  var dataUrl = {/literal}"{crmURL p='civicrm/ajax/toggleDedupeSelect' h=0 q='snippet=4'}"{literal};
  var rgid = {/literal}"{$rgid}"{literal};
  var gid = {/literal}"{$gid}"{literal};

  rgid = rgid.length > 0 ? rgid : 0;
  gid  = gid.length > 0 ? gid : 0;
  
  CRM.$.post(dataUrl, {pnid: id, rgid: rgid, gid: gid, is_selected: is_selected}, function (data) {
    console.log(data);
  }, 'json');
}
</script>
{/literal}

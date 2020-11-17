<!--{include file="../../../templates/spinner.tpl"}-->

<div id="confirm_xhrDialog" style="visibility: hidden">
<form id="confirm_record" enctype="multipart/form-data" action="javascript:void(0);">
    <div style="background-color: #feffd1; border: 1px solid black">
        <div id="confirm_xhr" style="width: 400px; height: 120px; padding: 16px; overflow: auto"></div>
        <div style="position: absolute; left: 10px; font-size: 140%"><button class="buttonNorm" id="confirm_button_cancelchange"><img src="../../libs/dynicons/?img=edit-undo.svg&amp;w=32" alt="cancel" /> No</button></div>
        <div style="text-align: right; padding-right: 6px"><button class="buttonNorm" id="confirm_button_save"><img src="../../libs/dynicons/?img=media-floppy.svg&amp;w=32" alt="save" /><span id="confirm_saveBtnText"> Yes</span></button></div><br />
    </div>
</form>
</div>
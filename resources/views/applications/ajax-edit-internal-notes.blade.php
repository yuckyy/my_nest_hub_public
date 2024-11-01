<div class="inRowComment">
    <i class="fal fa-info-circle"></i> Internal Notes are not visible to the tenant
</div>
<textarea title="Internal Notes" class="form-control" id="internalNotesField" name="internal_notes" maxlength="4000">{{ $application->internal_notes }}</textarea>

<input type="hidden" name="updateInternalNotes" value="1">

<div class="inRowComment">
    <i class="fal fa-info-circle"></i> Notes are visible for the landlord and property manager
</div>
<textarea title="Notes" class="form-control" id="notesField" name="notes" maxlength="4000">{{ $application->notes }}</textarea>

<input type="hidden" name="updateNotes" value="1">

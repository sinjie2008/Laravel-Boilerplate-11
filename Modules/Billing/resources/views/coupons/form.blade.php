<div class="mb-3">
    <label class="form-label">Code</label>
    <input type="text" name="code" class="form-control" value="{{ old('code', $coupon->code ?? '') }}">
</div>
<div class="mb-3">
    <label class="form-label">Amount Off</label>
    <input type="number" step="0.01" name="amount_off" class="form-control" value="{{ old('amount_off', $coupon->amount_off ?? '') }}">
</div>
<div class="mb-3">
    <label class="form-label">Percent Off</label>
    <input type="number" step="0.01" name="percent_off" class="form-control" value="{{ old('percent_off', $coupon->percent_off ?? '') }}">
</div>
<div class="mb-3">
    <label class="form-label">Duration</label>
    <input type="text" name="duration" class="form-control" value="{{ old('duration', $coupon->duration ?? '') }}">
</div>
<div class="mb-3">
    <label class="form-label">Applies To</label>
    <input type="text" name="applies_to" class="form-control" value="{{ old('applies_to', $coupon->applies_to ?? '') }}">
</div>

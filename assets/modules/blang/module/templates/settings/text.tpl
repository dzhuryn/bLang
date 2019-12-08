<div class="form-group row">
    <label for="settings_[+name+]" class="col-md-3 col-form-label">[+caption+]</label>
    <div class="col-md-9">
        <input id="settings_[+name+]" name="[+name+]" type="text" value="[+value+]" class="inputBox">
    </div>
    [[if? &is=`[+description+]:_:!empty` &separator=`:_:` &then=`<div class="col-md-12">[+description+]</div>`]]
</div>
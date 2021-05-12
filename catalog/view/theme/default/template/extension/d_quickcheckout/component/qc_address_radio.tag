<qc_address_radio>
    <h3 class="uk-text-bold uk-text-yellow uk-h4 font">آدرس ارسال سفارش</h3>
    <p class="uk-hidden">{getLanguage().general.text_address_existing}</p>
    <div class="uk-margin-bottom" each={address in getSession().addresses } if={address}>
        <label class="uk-flex uk-flex-middle checkoutLabel uk-button uk-button-default uk-padding-small uk-border-rounded uk-lineheight-normal uk-text-right {(parent.opts.address_id == address.address_id) ? 'selected':''}" for="{ parent.opts.step }_address_id_{address.address_id}">
        <input
          type="radio"
          class="uk-radio uk-margin-left"
          id="{ parent.opts.step }_address_id_{address.address_id}"
          name="{ parent.opts.step }[address_id]"
          value={ address.address_id}
          checked={parent.opts.address_id == address.address_id}
          onclick={change}/>
          <div class="uk-display-inline-block fnum">
            <span class="uk-text-secondary font uk-text-small">{address.zone} ، {address.city}</span><br>
            <span class="uk-text-muted font uk-text-tiny">آدرس تحویل : {address.address_1} ، پلاک {address.company} ، واحد {address.address_2}</span><br>
            <span class="uk-text-muted font uk-text-tiny">کد پستی : {address.postcode}</span><br>
            <span class="uk-text-muted font uk-text-tiny">تحویل گیرنده : {address.firstname} {address.lastname}</span>
          </div>
      </label>
    </div>

    <div class="uk-margin-bottom">
        <label class="uk-flex uk-flex-middle checkoutLabel uk-button uk-button-default uk-padding-small uk-border-rounded uk-lineheight-normal uk-text-right {(opts.address_id == '0') ? 'selected':''}"  for="{ opts.step }_address_id_0">
        <input
          type="radio"
          class="uk-radio uk-margin-left"
          id="{ opts.step }_address_id_0"
          name="{ opts.step }[address_id]"
          value="0"
          checked={opts.address_id == '0'}
          onclick={change}/>
          <div class="uk-display-inline-block fnum">
            <span class="uk-text-secondary font uk-text-small">درج آدرس جدید</span><br>
            <span class="uk-text-muted font uk-text-tiny">می خواهم سفارش به آدرس جدیدی ارسال شود.</span><br>
          </div>
      </label>
    </div>
    
    <script>
        this.mixin({store:d_quickcheckout_store});
        change(e){
            this.store.dispatch(this.opts.step+'/update', $(e.currentTarget).serializeJSON());
        }
    </script>
</qc_address_radio>

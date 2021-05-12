<d_quickcheckout>
    <div if={!getSession().status}>
      <h1>{getLanguage().general.text_cart_title}</h1>
      <p>{getLanguage().general.text_cart_empty}</p>
    </div>

    <div if={getSession().status}>
        <div class="qc-loader uk-flex uk-flex-center uk-flex-middle uk-position-cover uk-position-fixed" style="display:none">
            <div data-uk-spinner></div>
        </div>
        <qc_layout></qc_layout>
    </div>

    <script>
        this.mixin({store:d_quickcheckout_store});
    </script>
</d_quickcheckout>
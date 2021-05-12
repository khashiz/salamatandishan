<qc_account>
    <div class="step qc-account">

        <qc_account_setting if={riot.util.tags.selectTags().search('"qc_account_setting"') && getState().edit} step="{opts.step}"></qc_account_setting>

        <qc_pro_label if={ riot.util.tags.selectTags().search('"qc_account_setting"') < 0 && getState().edit}></qc_pro_label>

        <!-- Step -->
        <div if={ (getState().session.account != 'logged') && (getConfig().account.display == 1) } >
            <div if={ getState().config.guest.account.option.guest.display == 1 || getState().config.guest.account.option.register.display == 1 || (getState().config.guest.account.login_popup == 1 && getState().config.guest.account.option.login.display == 1 )}>
                <div data-toggle="buttons" class="uk-margin-bottom uk-child-width-1-3 uk-child-width-auto@m tabsColor" data-uk-tab>
                  <label if={getState().config.guest.account.option.register.display == 1} class="d-vis { getAccount() == 'register' ?  'active uk-active' : '' }" onclick={changeAccount}>
                    <input class="uk-hidden" type="radio" name="account" value="register" id="register" autocomplete="off" checked={ getAccount() == 'register' }>
                    <a class="font">
                        <span class="uk-visible@m">ساخت حساب کاربری</span>
                        <span class="uk-hidden@m">ثبت نام</span>
                    </a>
                  </label>
                  <label if={getState().config.guest.account.option.guest.display == 1} class="d-vis { getAccount() == 'guest' ?  'active uk-active' : '' }" onclick={changeAccount}>
                    <input class="uk-hidden" type="radio" name="account" value="guest" id="guest" autocomplete="off" checked={ getAccount() == 'guest' }>
                    <a class="font">
                        <span class="uk-visible@m">خرید بصورت مهمان</span>
                        <span class="uk-hidden@m">مهمان</span>
                    </a>
                  </label>
                  <div>
                    <a class="font" onclick={openLoginPopup} if={getState().config.guest.account.login_popup == 1 && getState().config.guest.account.option.login.display == 1 }>{getLanguage().account.entry_login}</a>
                  </div>
                </div>
            </div>
            
            <!-- style card -->
            <div class="ve-card" if={getState().config.guest.account.login_popup != 1 && getState().config.guest.account.option.login.display == 1 && getState().config.guest.account.style == 'card' }>
                <div class="ve-card__header">
                    <h4 class="ve-h4">
                        <span class="icon">
                            <i class="{ getConfig().account.icon }"></i>
                        </span>
                        { getLanguage().account.heading_title }
                    </h4>
                    <p class="ve-p" if={getLanguage().account.text_description}>{  getLanguage().account.text_description } </p>
                </div>
                <div class="ve-card__section">
                    <div if={getError() && getError().account && getError().account.login } class="alert alert-danger">
                        {getError().account.login}
                    </div>
                    <div if={getState().config.guest.account.social_login.display == 1 && getState().config.guest.account.social_login.value} class="qc-row form-group d-vis clearfix qc-social-login">
                        <div class="ve-col-md-12">
                            <qc_raw content="{getState().config.guest.account.social_login.value}"></qc_raw>
                        </div>
                    </div>
                    <form>
                        <div class="ve-field d-vis clearfix">
                            <div class="ve-row">
                                <label class="ve-col-md-5">{getLanguage().account.entry_email}</label>
                                <div class="ve-col-md-7">
                                    <input ref="email" type="text" autocomplete="email" class="ve-input" name="email" value="">
                                </div>
                            </div>
                        </div>
                        <div class="ve-field d-vis clearfix">
                            <div class="ve-row">
                                <label class="ve-col-md-5">{getLanguage().account.entry_password}</label>
                                <div class="ve-col-md-7">
                                    <input ref="password" type="password" autocomplete="current-password" class="ve-input" name="password" value="">
                                </div>
                            </div>
                        </div>

                        <div class="ve-field d-vis clearfix">
                            <div class="ve-row">
                                <div class="ve-col-md-7 ve-col-offset-5">
                                    <button class="ve-btn d-vis ve-btn--primary" onclick={login}>{getLanguage().account.button_login}</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- style clear -->
            <div if={getState().config.guest.account.login_popup != 1 && getState().config.guest.account.option.login.display == 1 && getState().config.guest.account.style == 'clear' }>
                <p if={getLanguage().account.text_description}>{  getLanguage().account.text_description } </p>
            
                <div if={getError() && getError().account && getError().account.login } class="alert alert-danger">
                    {getError().account.login}
                </div>
                <div if={getState().config.guest.account.social_login.display == 1 && getState().config.guest.account.social_login.value} class="qc-row form-group d-vis clearfix qc-social-login">
                    <div>
                        <qc_raw content="{getState().config.guest.account.social_login.value}"></qc_raw>
                    </div>
                </div>
                <form>
                    <div class="d-vis">
                        <div>
                            <label>{getLanguage().account.entry_email}</label>
                            <div class="ve-col-md-7">
                                <input ref="email" type="text" autocomplete="email" class="ve-input" name="email" value="">
                            </div>
                        </div>
                    </div>
                    <div class="ve-field d-vis clearfix">
                        <div class="ve-row">
                            <label class="ve-col-md-5">{getLanguage().account.entry_password}</label>
                            <div class="ve-col-md-7">
                                <input ref="password" type="password" autocomplete="current-password" class="ve-input" name="password" value="">
                            </div>
                        </div>
                    </div>

                    <div class="ve-field d-vis clearfix">
                        <div class="ve-row">
                            <div class="ve-col-md-7 ve-col-offset-5">
                                <button class="ve-btn d-vis ve-btn--primary" onclick={login}>{getLanguage().account.button_login}</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div data-uk-modal id="login_popup" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true">
                <div class="uk-modal-dialog uk-border-rounded uk-box-shadow-small">
                    <div>
                        <form class="saForm uk-margin-remove">
                            <div class="uk-hidden">
                                <h4>{getLanguage().account.entry_login}</h4>
                                <p class="ve-p" if={getLanguage().account.text_description}>{  getLanguage().account.text_description } </p>
                            </div>
                            <div class="uk-modal-body uk-padding">
                            <div class="uk-grid-medium uk-child-width-1-1 uk-child-width-1-2@m" data-uk-grid>
                                <div if={getError() && getError().account && getError().account.login } class="ve-alert ve-alert--danger">
                                    {getError().account.login}
                                </div>
                                <div if={getState().config.guest.account.social_login.display == 1 && getState().config.guest.account.social_login.value} class="form-group d-vis clearfix qc-social-login">
                                    <qc_raw content="{getState().config.guest.account.social_login.value}"></qc_raw>
                                </div>
                                <div class="d-vis">
                                    <label class="uk-form-label uk-margin-small-bottom uk-text-secondary uk-display-block uk-text-bold font">{getLanguage().account.entry_email}</label>
                                    <input ref="email" type="text" autocomplete="email" class="uk-input uk-border-rounded uk-width-1-1 uk-text-left ltr uk-text-secondary font ve-inpu" name="email" value="">
                                </div>
                                <div class="d-vis">
                                    <label class="uk-form-label uk-margin-small-bottom uk-text-secondary uk-display-block uk-text-bold font">{getLanguage().account.entry_password}</label>
                                    <input ref="password" type="password" autocomplete="current-password" class="uk-input uk-border-rounded uk-width-1-1 uk-text-left ltr uk-text-secondary font ve-inpu" name="password" value="">
                                </div>
                                <div class="uk-width-1-1">
                                    <button class="uk-button uk-button-primary uk-width-1-1 uk-border-rounded font d-vis" onclick={login}>{getLanguage().account.button_login}</button>
                                </div>
                            </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>

        <!-- Hidden Step -->
        <div show={(getConfig().account.display != 1 || getState().session.account == 'logged') && getState().edit}>
            <div class="ve-card" style="opacity: 0.5">
                <div class="ve-card__header">{ getLanguage().account.heading_title } <div class="ve-pull-right"><span class="ve-badge ve-badge--warning">{getLanguage().general.text_hidden}<span></div></div>
            </div>
        </div>
    </div>

    <script>
        this.mixin({store:d_quickcheckout_store});

        var tag = this;

        login(e){
            this.store.dispatch('account/login', $(e.currentTarget).parents('form').serializeJSON());
            e.preventDefault();
        }

        changeAccount(e){
            this.store.dispatch('account/update', { account: $(e.currentTarget).find('input').val()});
        }

        openLoginPopup(e){
            UIkit.modal('#login_popup').show();
        }

        this.on('mount', function(){
            $(this.root).find('#login_popup').appendTo('body');
        })

        this.store.subscribe('account/updated', function(data) {
            if(data.session.account == 'logged'){
                $('.modal-backdrop').remove();
                alert('accountLogin');

                //bugfix: required to close the model window.
                UIkit.modal('#login_popup').hide();
            }
        });

    </script>
</qc_account>
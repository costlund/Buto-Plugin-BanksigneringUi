function PluginBanksigneringUi(){
  this.btn = null;
  this.plugin_banksignering_ui_timeout = null;
  this.auth = function(btn){
    this.btn = btn;
    PluginWfBootstrapjs.modal({id: 'modal_banksignering_auth', label: btn.innerHTML, url: '/banksignering/auth'});
  }
  this.sign = function(btn){
    this.btn = btn;
    PluginWfBootstrapjs.modal({id: 'modal_banksignering_sign', label: btn.innerHTML, url: '/banksignering/sign'});
  }
  this.success = function(response){
    if(response=='auth'){
      $('#modal_banksignering_auth').modal('hide');
      this.account(this.btn);
    }else if(response=='sign'){
      $('#modal_banksignering_sign').modal('hide');
      PluginWfBootstrapjs.modal({id: 'modal_banksignering_sign_success', label: this.btn.innerHTML, url: '/banksignering/sign_success', fade: false});
    }
  }
  this.account = function(btn){
    PluginWfBootstrapjs.modal({id: 'modal_banksignering_account', label: btn.innerHTML, url: '/banksignering/account'});
  }
  this.account_click = function(btn){
    PluginWfAjax.load('modal_banksignering_account_body', '/banksignering/account_click?id='+btn.getAttribute('data-id'));
  }
  this.account_click_if_one = function(btn){
    var items = document.getElementById('banksignering_account_items').getElementsByTagName('a');
    if(items.length==1){
      items[0].click();
    }
  }
  this.method = function(btn){
    /**
     * disable buttons
     */
    $('#banksignering_div_buttons_personal_number_method').attr('disabled', 'disabled');
    $('#banksignering_div_buttons_personal_number_method').addClass('disabled');
    $('#banksignering_div_buttons_personal_number_pid').attr('disabled', 'disabled');
    $('#banksignering_div_buttons_personal_number_pid').addClass('disabled');
    /**
     * 
     */
    $.get( '/banksignering/method?method=qr', function( data ) {
      PluginWfAjax.update('div_banksignering');
    });
  }
  this.capture_method = function(){
    PluginWfAjax.update('div_banksignering');
  }
  this.try_again = function(btn){
    $(btn).attr('disabled', 'disabled');
    $(btn).addClass('disabled');
    PluginWfAjax.update('div_banksignering')
  }
  this.auth_as_webadmin = function(btn){
    this.btn = btn;
    PluginWfBootstrapjs.modal({id: 'modal_banksignering_auth_as_webadmin', label: btn.innerHTML, url: '/banksignering/auth_as_webadmin'});
  }
  this.auth_as_webadmin_capture = function(){
    this.account(this.btn);
  }
}
var PluginBanksigneringUi = new PluginBanksigneringUi();

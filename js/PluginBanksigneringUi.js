function PluginBanksigneringUi(){
  this.btn = null;
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
  this.method = function(){
    $.get( '/banksignering/method?method=qr', function( data ) {
      PluginWfAjax.update('div_banksignering');
    });
  }
  this.capture_method = function(){
    PluginWfAjax.update('div_banksignering');
  }
}
var PluginBanksigneringUi = new PluginBanksigneringUi();

# Buto-Plugin-BanksigneringUi
- Using Banksignering.se service.
- Sign up with them and they provide you with details for testing.

## Theme settings
```
plugin_modules:
  banksignering:
    plugin: banksignering/ui
```
```
plugin:
  banksignering:
    ui:
      enabled: true
      data: 'yml:/../buto_data/theme/[theme]/banksignering.yml'
```
banksignering.yml
```
mode: test
apiUser: _provided_by_Banksignering.se_
password: _provided_by_Banksignering.se_
companyApiGuid: _provided_by_Banksignering.se_
mysql: 'yml:/../buto_data/_mysql.yml'
```


## Widget include
Include in page head section.
```
type: widget
data:
  plugin: banksignering/ui
  method: include          
```

## Database
Add tables to database.
```
/plugin/banksignering/ui/sql/schema.yml
```

## Button auth
Using Javascript or a widget.
### Javascript
```
type: a
attribute:
  onclick: "PluginBanksigneringUi.auth(this);"
innerHTML: 'BankID sign in'
```
### Widget
```
type: widget
data:
  plugin: banksignering/ui
  method: auth_button
```

## Button sign
When this button is render data is set in session.
```
type: widget
data:
  plugin: banksignering/ui
  method: sign_button
  data:
    text: 'Sign of document test.pdf'
    before:
      method:
        plugin: any/plugin
        method: bankid_sign_before
    success:
      method:
        plugin: any/plugin
        method: bankid_sign
      script: console.log('Script to run after method!')
```
Before method (optional).
```
public function bankid_sign_before($data){
  $data = new PluginWfArray($data);
  $data->set('data/text', 'Change text param...');
  return $data->get();
}
```
Success method.
```
public function bankid_sign($data){
  $session_data =  wfUser::getSession()->get('plugin/banksignering/ui/sign_button');
  /**
    * Run some data when bankid sign success.
    */
  return __CLASS__.' says: success in method '.__FUNCTION__.'!';
}
```

## Button account
Anywhere on a page. This modal will show up automatic on sign in success but could be used later to shift account.
```
type: a
attribute:
  onclick: "PluginBanksigneringUi.account(this);"
innerHTML: 'BankID accounts'
```

## Sign in flow
- User click on Sign in button to open modal with qr code.
- On success modal Account appear with all accounts where pid has a match.
- User click on a account and are signed in.

## Log
Log files are created per day basis.
```
/../buto_data/theme/[theme]/plugin/banksignering/ui/YYYY-MM-DD.yml
```

## PID
Test pids.
- 202201012396
- 202201022387
- 202201032394
- 199912312395



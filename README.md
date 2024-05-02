# Buto-Plugin-BanksigneringUi

<ul>
<li>Using Banksignering.se service.</li>
<li>Sign up with them and they provide you with details for testing.</li>
</ul>

<a name="key_0"></a>

## Sign in flow

<ul>
<li>User click on Sign in button to open modal with qr code.</li>
<li>On success modal Account appear with all accounts where pid has a match (if no account exist with current PID it will be created).</li>
<li>User click on a account and are signed in (if only one account this is automatic).</li>
</ul>

<a name="key_1"></a>

## Log

<p>Log files are created per day basis.</p>
<pre><code>/../buto_data/theme/[theme]/plugin/banksignering/ui/YYYY-MM-DD.yml</code></pre>

<a name="key_2"></a>

## PIDs

<p>Test pids.</p>
<ul>
<li>202201012396</li>
<li>202201022387</li>
<li>202201032394</li>
<li>199912312395</li>
</ul>

<a name="key_3"></a>

## Settings

<pre><code>plugin_modules:
  banksignering:
    plugin: banksignering/ui</code></pre>
<pre><code>plugin:
  banksignering:
    ui:
      enabled: true
      data: 'yml:/../buto_data/theme/[theme]/banksignering.yml'</code></pre>
<p>Run methods on success.</p>
<pre><code>      auth:
        success:
          methods:
            -
              plugin: my/plugin
              method: my_method</code></pre>
<p>Run methods on signin.
This happends after BankID auth and the user is signed in to an account.</p>
<pre><code>      signin:
        success:
          methods:
            -
              plugin: marknadsurval/db
              method: set_user</code></pre>
<p>banksignering.yml</p>
<pre><code>mode: test
apiUser: _provided_by_Banksignering.se_
password: _provided_by_Banksignering.se_
companyApiGuid: _provided_by_Banksignering.se_
mysql: 'yml:/../buto_data/_mysql.yml'</code></pre>

<a name="key_4"></a>

## Usage



<a name="key_4_0"></a>

### Schema

<p>Add tables to database.</p>
<pre><code>/plugin/banksignering/ui/sql/schema.yml</code></pre>

<a name="key_4_1"></a>

### Button auth

<p>Using Javascript or widget_auth_button.</p>
<pre><code>type: a
attribute:
  onclick: "PluginBanksigneringUi.auth(this);"
innerHTML: 'BankID sign in'</code></pre>

<a name="key_4_2"></a>

### Button account

<p>Anywhere on a page. This modal will show up automatic on sign in success but could be used later to shift account.</p>
<pre><code>type: a
attribute:
  onclick: "PluginBanksigneringUi.account(this);"
innerHTML: 'BankID accounts'</code></pre>

<a name="key_4_3"></a>

### Button Auth as Webadmin

<p>Anywhere on a page. A modal where user with role webadmin can provide any pid for authorisation.</p>
<pre><code>type: a
attribute:
  onclick: "PluginBanksigneringUi.auth_as_webadmin(this)"
innerHTML: 'BankID accounts'</code></pre>

<a name="key_5"></a>

## Pages



<a name="key_5_0"></a>

### page_account



<a name="key_5_1"></a>

### page_account_click



<a name="key_5_2"></a>

### page_auth



<a name="key_5_3"></a>

### page_auth_check



<a name="key_5_4"></a>

### page_method



<a name="key_5_5"></a>

### page_sign



<a name="key_5_6"></a>

### page_sign_check



<a name="key_5_7"></a>

### page_sign_success



<a name="key_6"></a>

## Widgets



<a name="key_6_0"></a>

### widget_auth_button

<pre><code>type: widget
data:
  plugin: banksignering/ui
  method: auth_button</code></pre>

<a name="key_6_1"></a>

### widget_include

<p>Include in page head section.</p>
<pre><code>type: widget
data:
  plugin: banksignering/ui
  method: include          </code></pre>

<a name="key_6_2"></a>

### widget_sign_button

<p>When this button is render data is set in session.</p>
<pre><code>type: widget
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
      script: console.log('Script to run after method!')</code></pre>
<p>Before method (optional).</p>
<pre><code>public function bankid_sign_before($data){
  $data = new PluginWfArray($data);
  $data-&gt;set('data/text', 'Change text param...');
  return $data-&gt;get();
}</code></pre>
<p>Success method.</p>
<pre><code>public function bankid_sign($data){
  $session_data =  wfUser::getSession()-&gt;get('plugin/banksignering/ui/sign_button');
  /**
    * Run some data when bankid sign success.
    */
  return __CLASS__.' says: success in method '.__FUNCTION__.'!';
}</code></pre>
<p>Method PluginBanksigneringUi.sign is called when click on sign button. To override onclick one could use script below.</p>
<pre><code>document.getElementById('plugin_banksignering_ui_sign_button').onclick = function(){
  if(confirm('Ok?')){
    PluginBanksigneringUi.sign(this);
  }else{
    return false;
  }
}</code></pre>

<a name="key_7"></a>

## Event



<a name="key_8"></a>

## Construct



<a name="key_8_0"></a>

### __construct



<a name="key_9"></a>

## Methods



<a name="key_9_0"></a>

### unset_session



<a name="key_9_1"></a>

### render_method



<a name="key_9_2"></a>

### capture_method



<a name="key_9_3"></a>

### db_account_get_available_username



<a name="key_9_4"></a>

### db_account_select_by_pid



<a name="key_9_5"></a>

### db_banksignering_ui_auth_insert



<a name="key_9_6"></a>

### db_banksignering_ui_sign_insert



<a name="key_9_7"></a>

### db_account_set_pid




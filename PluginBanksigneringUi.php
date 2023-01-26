<?php
class PluginBanksigneringUi{
  private $data = null;
  private $mysql = null;
  function __construct() {
    $this->data = wfPlugin::getPluginSettings('banksignering/ui', true);
    /**
     * 
     */
    wfPlugin::includeonce('wf/mysql');
    $this->mysql =new PluginWfMysql();
    /**
     * 
     */
    wfPlugin::includeonce('wf/yml');
  }
  public function widget_include(){
    wfPlugin::enable('wf/embed');
    $widget = wfDocument::createWidget('wf/embed', 'embed', array('type' => 'script', 'file' => '/plugin/banksignering/ui/js/'.__CLASS__.'.js'));
    wfDocument::renderElement(array($widget));
  }
  public function widget_sign_button($data){
    $data = new PluginWfArray($data);
    /**
     * 
     */
    if($data->get('data/before/method')){
      wfPlugin::includeonce($data->get('data/before/method/plugin'));
      $obj = wfSettings::getPluginObj($data->get('data/before/method/plugin'));
      $method = $data->get('data/before/method/method');
      $data = $obj->$method($data->get());
      $data = new PluginWfArray($data);
    }
    /**
     * 
     */
    if(!$data->get('data/success/method')){
      echo('<i>'.__CLASS__.' says: Param data/success/method is not set.</i>');
    }
    if(!$data->get('data/success/script')){
      echo('<i>'.__CLASS__.' says: Param data/success/script is not set.</i>');
    }
    /**
     * 
     */
    wfUser::setSession('plugin/banksignering/ui/sign_button/data', $data->get('data'));
    $element = new PluginWfYml(__DIR__.'/element/'.__FUNCTION__.'.yml');
    wfDocument::renderElement($element);
  }
  public function widget_auth_button($data){
    $element = new PluginWfYml(__DIR__.'/element/'.__FUNCTION__.'.yml');
    wfDocument::renderElement($element);
  }
  private function unset_session($param = null){
    wfUser::unsetSession('plugin/banksignering/api');
    if(!$param){
      $sign_button = wfUser::getSession()->get('plugin/banksignering/ui/sign_button');
      wfUser::unsetSession('plugin/banksignering/ui');
      wfUser::setSession('plugin/banksignering/ui/sign_button', $sign_button);
    }else{
      wfUser::unsetSession('plugin/banksignering/ui/'.$param);
    }
  }
  public function page_auth(){
    $this->unset_session();
    $element = new PluginWfYml(__DIR__.'/element/page_auth.yml');
    wfDocument::renderElement($element);
  }
  public function page_sign(){
    $this->unset_session();
    $element = new PluginWfYml(__DIR__.'/element/page_sign.yml');
    wfDocument::renderElement($element);
  }
  public function page_method(){
    if(!wfRequest::isPost()){
      $method = wfRequest::get('method');
      if($method=='qr'){
        wfUser::setSession('plugin/banksignering/ui/method', 'qr');
      }
    }else{
      $element = new PluginWfYml(__DIR__.'/element/page_method_capture.yml');
      wfDocument::renderElement($element);
    }
  }
  public function render_method($v){
    $v = new PluginWfArray($v);
    if(wfHelp::isLocalhost()){
      $v->set('items/personalNumber/default', '199912312395');
    }
    return $v->get();
  }
  public function capture_method(){
    wfUser::setSession('plugin/banksignering/ui/method', 'personalNumber');
    wfUser::setSession('plugin/banksignering/ui/pid', wfRequest::get('personalNumber'));
    return array("PluginBanksigneringUi.capture_method()");
  }
  public function page_auth_check(){
    /**
     * 
     */
    if(wfUser::hasRole('client')){
      exit('Your are already signed in!');
    }
    if(!wfUser::getSession()->get('plugin/banksignering/ui/method')){
      $element = new PluginWfYml(__DIR__.'/element/page_method_set.yml');
      wfDocument::renderElement($element);
    }else{
      /**
       * 
       */
      wfPlugin::includeonce('banksignering/api');
      $api = new PluginBanksigneringApi($this->data->get('data/mode'));
      $api->set_data($this->data->get('data'));
      /**
       * 
       */
      $auth = $api->get_auth();
      if(!$auth){
        $api->auth(wfUser::getSession()->get('plugin/banksignering/ui/pid'));
      }
      /**
       * 
       */
      $api->collectstatus();
      /**
       * 
       */
      $element = new PluginWfYml(__DIR__.'/element/page_auth_check.yml');
      $element->setByTag(array('src' => $api->get_qr_image()));
      $element->setByTag($api->get_session()->get('response/auth_data'));
      $element->setByTag($api->get_session()->get('response'), 'response');
      wfDocument::renderElement($element);
      /**
       * log
       */
      $key = $api->get_session()->get('response/auth_data/date_time').' ('. session_id().')';
      $log = new PluginWfYml(wfGlobals::getAppDir().'/../buto_data/theme/[theme]/plugin/banksignering/ui/'.$api->get_session()->get('response/auth_data/date').'.yml');
      $log->set($key , array('data' => $this->data->get(), 'session' => $api->get_session()->get()));
      $log->save();
      /**
       * 
       */
      if(!$api->continue()){
        /**
         * success
         */
        if($api->success()){
          /**
           * db
           */
          $this->db_banksignering_ui_auth_insert($key);
          /**
           * session
           */
          wfUser::setSession('plugin/banksignering/ui/pid', $api->get_session()->get('response/collectstatus/apiCallResponse/Response/CompletionData/user/personalNumber'));
        }
        $api->unset_session();
        $this->unset_session('method');
      }
    }
  }
  public function page_sign_check(){
    if(!wfUser::getSession()->get('plugin/banksignering/ui/sign_button/data')){
      throw new Exception(__CLASS__.' says: Sign data is not set!');
    }
    /**
     * 
     */
    if(false){
      wfHelp::print(wfUser::getSession()->get('plugin/banksignering/ui'));
    }
    /**
     * 
     */
    if(!wfUser::getSession()->get('plugin/banksignering/ui/method')){
      $element = new PluginWfYml(__DIR__.'/element/page_method_set.yml');
      wfDocument::renderElement($element);
    }else{
      /**
       * 
       */
      wfPlugin::includeonce('banksignering/api');
      $api = new PluginBanksigneringApi($this->data->get('data/mode'));
      $api->set_data($this->data->get('data'));
      /**
       * 
       */
      $sign = $api->get_sign();
      if(!$sign){
        $api->sign(wfUser::getSession()->get('plugin/banksignering/ui/sign_button/data/text'), wfUser::getSession()->get('plugin/banksignering/ui/pid'));
      }
      /**
       * 
       */
      $api->collectstatus('sign');
      /**
       * 
       */
      $element = new PluginWfYml(__DIR__.'/element/page_sign_check.yml');
      $element->setByTag(array('src' => $api->get_qr_image()));
      $element->setByTag($api->get_session()->get('response/sign_data'));
      $element->setByTag($api->get_session()->get('response'), 'response');
      $element->setByTag(wfUser::getSession()->get('plugin/banksignering/ui/sign_button/data'), 'sign_button');
      wfDocument::renderElement($element);
      /**
       * log
       */
      $key = $api->get_session()->get('response/sign_data/date_time').' ('. session_id().')';
      $log = new PluginWfYml(wfGlobals::getAppDir().'/../buto_data/theme/[theme]/plugin/banksignering/ui/'.$api->get_session()->get('response/sign_data/date').'.yml');
      $log->set($key , array('data' => $this->data->get(), 'session' => $api->get_session()->get()));
      $log->save();
      /**
       * 
       */
      if(!$api->continue()){
        /**
         * success
         */
        if($api->success()){
          /**
           * run method
           */
          wfPlugin::includeonce(wfUser::getSession()->get('plugin/banksignering/ui/sign_button/data/success/method/plugin'));
          $obj = wfSettings::getPluginObj(wfUser::getSession()->get('plugin/banksignering/ui/sign_button/data/success/method/plugin'));
          $method = wfUser::getSession()->get('plugin/banksignering/ui/sign_button/data/success/method/method');
          $data = $obj->$method(wfUser::getSession()->get('plugin/banksignering/ui/sign_button/data'));
          /**
           * db
           */
          $this->db_banksignering_ui_sign_insert($key);
          $this->db_account_set_pid($api);
          /**
           * session
           */
          wfUser::setSession('plugin/banksignering/ui/sign_button/data/success/result', $data);
          wfUser::setSession('plugin/banksignering/ui/sign_button/data/success/user', $api->get_session()->get('response/collectstatus/apiCallResponse/Response/CompletionData/user'));
        }
        $api->unset_session();
        $this->unset_session('method');
        $this->unset_session('pid');
      }
    }
  }
  public function page_sign_success(){
    $element = new PluginWfYml(__DIR__.'/element/'.__FUNCTION__.'.yml');
    $element->setByTag(wfUser::getSession()->get('plugin/banksignering/ui/sign_button/data/success'), 'success');
    wfDocument::renderElement($element);
  }
  public function page_account(){
    /**
     * 
     */
    if(!strlen(wfUser::getSession()->get('plugin/banksignering/ui/account/count'))){
      wfUser::setSession('plugin/banksignering/ui/account/count', 0);
    }else{
      wfUser::setSession('plugin/banksignering/ui/account/count', 1 + wfUser::getSession()->get('plugin/banksignering/ui/account/count'));
    }
    /**
     * 
     */
    $rs = $this->db_account_select_by_pid();
    $items = array();
    foreach($rs as $v){
      $item = new PluginWfYml(__DIR__.'/element/page_account_item.yml');
      $item->setByTag($v);
      $items[] = $item->get();
    }
    /**
     * 
     */
    $element = new PluginWfYml(__DIR__.'/element/page_account.yml');
    $element->setByTag(array('items' => $items));
    wfDocument::renderElement($element);
  }
  public function page_account_click(){
    $id = wfRequest::get('id');
    $rs = $this->db_account_select_by_pid();
    $success = false;
    foreach($rs as $v){
      $i = new PluginWfArray($v);
      if($i->get('id')==$id){
        $success = true;
        break;
      }
    }
    if(!$success){
      throw new Exception(__CLASS__." says: No match on id $id!");
    }
    /**
     * 
     */
    $session_data = wfUser::getSession()->get('plugin/banksignering/ui');
    /**
     * 
     */
    wfPlugin::includeonce('wf/account2');
    $obj = new PluginWfAccount2();
    $obj->sign_in_external($id, 'banksignering');      
    /**
     * 
     */
    wfUser::setSession('plugin/banksignering/ui', $session_data);
    /**
     * 
     */
    $element = new PluginWfYml(__DIR__.'/element/page_account_click.yml');
    wfDocument::renderElement($element);
  }
  private function db_account_select_by_pid(){
    $this->mysql->open($this->data->get('data/mysql'));
    $sql = new PluginWfYml(__DIR__.'/sql/sql.yml', 'account_select_by_pid');
    $sql->setByTag(wfUser::getSession()->get('plugin/banksignering/ui'));
    $this->mysql->execute($sql->get());
    return $this->mysql->getMany();
  }
  private function db_banksignering_ui_auth_insert($key){
    $this->mysql->open($this->data->get('data/mysql'));
    $sql = new PluginWfYml(__DIR__.'/sql/sql.yml', __FUNCTION__);
    $sql->setByTag(array('id' => $key));
    $sql->setByTag(wfUser::getSession()->get('plugin/banksignering/api/response/collectstatus/apiCallResponse/Response/CompletionData/user'));
    $this->mysql->execute($sql->get());
    return null;
  }
  private function db_banksignering_ui_sign_insert($key){
    $this->mysql->open($this->data->get('data/mysql'));
    $sql = new PluginWfYml(__DIR__.'/sql/sql.yml', __FUNCTION__);
    $sql->setByTag(array('id' => $key));
    $sql->setByTag(wfUser::getSession()->get('plugin/banksignering/api/response/collectstatus/apiCallResponse/Response/CompletionData/user'));
    $sql->setByTag(wfUser::getSession()->get('plugin/banksignering/api/endpoint/sign'));
    $this->mysql->execute($sql->get());
    return null;
  }
  private function db_account_set_pid($api){
    $this->mysql->open($this->data->get('data/mysql'));
    $sql = new PluginWfYml(__DIR__.'/sql/sql.yml', __FUNCTION__);
    $sql->setByTag(array('pid' => $api->get_session()->get('response/collectstatus/apiCallResponse/Response/CompletionData/user/personalNumber')));
    $this->mysql->execute($sql->get());
    return null;
  }
}

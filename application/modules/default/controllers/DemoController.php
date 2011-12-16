<?php
class DemoController extends Et_Controller_Action
{
    public function treeAction()
    {
        Model_Node::registerType('department', null, 'Model_Department');
        Model_Node::registerType('user', 'Model_Table_User', 'Model_User');
        Model_Node::registerType('tree', 'Model_Table_Tree', 'Model_Tree');
        if($this->_getParam('pop'))
        {
            if(Model_Node::resource()->count())
            {
                Model_Node::resource()->truncate();
            }
            if(Model_Node::resource('user')->count())
            {
                Model_Node::resource('user')->truncate();
            }
             if(Model_Node::resource('tree')->count())
            {
                Model_Node::resource('tree')->truncate();
            }

            // 插入测试数据
            $company = Model_Node::create(array('name'=>'Simon Company', 'code'=> 'dept_some_company'), 'department');
            $company->markAsRoot();
            $cd_office = Model_Node::create(array('name'=>'ChengDu Office', 'code'=> 'dept_cd_office'), 'department');
            $sh_office = Model_Node::create(array('name'=>'ShangHai Office', 'code'=> 'dept_sh_office'), 'department');
            $company->addChild($cd_office);
            $company->addChild($sh_office);

            $ceo = Model_User::create(array('title'=>'CEO', 'name'=>'CEO unknown', 'pass'=>'changeitlater'));
            $cto = Model_User::create(array('title'=>'CTO', 'name'=>'CTO unknown', 'pass'=>'changeitlater'));
            $staff_se = Model_User::create(array('title'=>'Staff SE', 'name'=>'Staff Software Engineer', 'pass'=>'changeitlater'));
            $senior_se = Model_User::create(array('title'=>'Senior SE', 'name'=>'senior Software Engineer', 'pass'=>'changeitlater'));
            $se = Model_User::create(array('title'=>'SE', 'name'=>'software Engineer', 'pass'=>'changeitlater'));
            $se->language='zh';
            $se->save();
            $company->addChild($ceo);
            $sh_office->addChild($cto);
            $cd_office->addChildren($staff_se, $senior_se, $se);
            ->select()->where();
            $article = Model_Node::create(array('name'=>'About', 'lang'=>'en', 'description'=>'this is an article to introduce our company', 'code'=> 'about_us'), 'article');
        }
        // 返回 json数据供参考
        $department_root = Model_Tree::find('department');
        $response = array();
        $response['root']['name'] = $department_root->name;
        if($department_root->hasChildren('department') || $department_root->hasChildren('user'))
        {
            $response['root']['isExpandable'] = true;
        }
        foreach($department_root->getChildren('department') as $department)
        {
            $depart_a = array('name'=>$department->name);
            if($department->hasChildren('department') || $department->hasChildren('user'))
            {
                $depart_a['isExpandable'] = true;
                foreach($department->getChildren('department') as $sub_department)
                {
                    $depart_a['departments'][] = array('name'=>$sub_department->name);
                }
                foreach($department->getChildren('user') as $department_user)
                {
                    $depart_a['users'][] = array('name'=>$department_user->name);
                }
            }
            $response['root']['departments'][]=$depart_a;
        }
        $response['root']['users']=array();
        foreach($department_root->getChildren('user') as $user)
        {
            $response['root']['users'] []= array('name'=>$user->name);
        }
        $this->renderJson($response);
    }
    public function logAction()
    {
        echo '<pre>';
        var_dump($this->_getAllParams());
        die();
        $this->log('DEBUG Message', Zend_Log::DEBUG);
        $this->log('Info Message', Zend_Log::INFO);
        $this->log('Notice Message', Zend_Log::NOTICE);
        $this->log('WARN Message', Zend_Log::WARN);
        $this->log('ERR Message', Zend_Log::ERR);
        $this->log('CRIT Message', Zend_Log::CRIT);
        $this->log('ALERT Message', Zend_Log::ALERT);
        $this->log('EMERG Message', Zend_Log::EMERG);
        $this->renderText('Please Use Firefox and FirePHP extension to view the log message!');
    }
    public function formAction()
    {
        //        $this->_helper->layout->setLayout('layout');
        $form = new Zend_Form();
        $price_decorators = array(
        'ViewHelper',
        array('Description', array('tag' => 'span', 'class' => 'help-inline')),
        array('HtmlTag', array('tag' => 'div')),
        'Label',  array(
                'decorator' => array('wrapper' => 'HtmlTag'),
                'options' => array('tag' => 'div', 'class' => 'clearfix')));
        $form->addElement('text', 'public_price', array(
        'label' => 'Market Price',
        'decorators' => $price_decorators));
        $form->addElement('text', 'min_price', array(
        'label' => 'Min Price',
        'decorators' => $price_decorators));
        $form->addElement('text', 'max_price', array(
        'label' => 'Max Price',
        'decorators' => $price_decorators));
        $form->addElement('text', 'default_price', array(
        'label' => 'Price',
        'decorators' => $price_decorators));
        $form->addDisplayGroup(array(
        'public_price',
        'min_price',
        'max_price',
        'default_price'), 'pricing', array(
        'legend' => 'pricing',
        'decorators' => array('FormElements', 'Fieldset')));
        $form->createElement('text', 'username');
        // Create and configure username element:
        $username = $form->createElement('text', 'username');
        $username->addValidator('alnum')
            ->addValidator('regex', false, array('/^[a-z]+/'))
            ->addValidator('stringLength', false, array(6, 20))
            ->setRequired(true)
            ->addFilter('StringToLower');
        // Create and configure password element:
        $password = $form->createElement('password', 'password');
        $password->addValidator('StringLength', false, array(6))->setRequired(true);
        // Add elements to form:
        $form->addElement($username)
            ->addElement($password)
            ->addElement('submit', 'login', array('label' => 'Login'));
        if($this->getRequest()->isPost())
        {
            if($form->isValid($this->getRequest()
                ->getPost()))
            {
                Et_Utils::edump($form->getValues());
                $this->flash('comment saved', 'success', 'index');
            }
        }
        $this->view->form = $form;
    }
}
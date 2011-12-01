<?php

/**
 * 封装dynatree树形插件
 *
 * @see http://wwwendt.de/tech/dynatree/index.html
 * @author Administrator
 * @uses Zend_Form_Element
 *
 */
class Et_Form_Element_Tree extends Zend_Form_Element
{

    /** @var int Show checkboxes */
    public static $isLoad = false;

    /** @var String jquery文件路径 */
    protected $_jqueryScriptDir;

    /** @var String dynatree文件路径 */
    protected $_dynatreeScriptDir;

    /** @var int 选择模式，1:single, 2:multi, 3:multi-hier */
    protected $_selectMode = 1;

    /** @var int Show checkboxes */
    protected $_checkbox = false;

    /** Cookie */
    protected $_persist = false;

    /** @var String 子节点数据，json格式 */
    protected $_children;

    /** @var String ajax请求地址 */
    protected $_url;

    /** @var String 点击事件 */
    protected $_onClick;

	/** @var Int String 编辑事件 */
	protected $_editType;
	protected $_editContents;

    /** @var String 选择节点事件 */
    protected $_onSelect;

    /** @var String 节点渲染后回调函数 */
    protected $_onRender;

    /** @var String 节点渲染前回调函数 */
    protected $_onCustomRender;

    /** @var int 标题截取长度 */
    protected $_titleLength = 0;

    /**
     * Default view helper to use
     * @var string
     */
    public $helper = 'formHidden';

    /**
     * Constructor
     *
     * $spec may be:
     * - string: name of element
     * - array: options with which to configure element
     * - Zend_Config: Zend_Config with options for configuring element
     *
     * @param  string|array|Zend_Config $spec
     * @param  array|Zend_Config $options
     * @return void
     * @throws Zend_Form_Exception if no element name after initialization
     */
    public function __construct($spec, $options = null)
    {
        parent::__construct($spec, $options);
        $this->_dynatreeScriptDir = Rt_Util::public_url('/js/dynatree');
        $this->_jqueryScriptDir = Rt_Util::public_url('/js/jquery');
        $this->removeDecorator('Errors');
        $this->removeDecorator('HtmlTag');
        $this->removeDecorator('Label');
    }

    /**
     * 加载dynatree
     *
     * @access protected
     * @return String|Bool
     */
    protected function getScript()
    {
        $string = '';
        if(!self::$isLoad){
            self::$isLoad = true;

			$string = "
            <link href=\"{$this->_dynatreeScriptDir}/skin-vista/ui.dynatree.css\" rel=\"stylesheet\" type=\"text/css\">
           	<script type=\"text/javascript\" src=\"{$this->_dynatreeScriptDir}/jquery.dynatree.min.js\"></script>";
        }
        return $string;
    }

    /**
     * 设置选择模式
     * 1:single, 2:multi, 3:multi-hier
     *
     * @access public
     * @param int $_selectMode
     * @return void
     */
    public function setSelectMode($_selectMode)
    {
        $this->_selectMode = $_selectMode;
    }

    /**
     * 是否显示checkbox
     *
     * @access public
     * @param bool $_checkbox
     * @return void
     */
    public function setCheckbox($_checkbox)
    {
        $this->_checkbox = $_checkbox ? true : false;
    }

    public function setPersist($_persist)
    {
    	$this->_persist = $_persist ? true : false;
    }

    /**
     * 子节点数据请求地址
     *
     * @access public
     * @param string $_url
     * @return void
     */
    public function setUrl($_url)
    {
        $this->_url = $_url;
    }

    public function setTitleLength($_length)
    {
        $this->_titleLength = $_length;
    }

    /**
     * 子节点数据
     *
     * @access public
     * @param array $_children
     * @return void
     */
    public function setChildren(array $_children)
    {
        $this->_children = Zend_Json::encode($_children);
    }

    public function setOnClick($_onClick)
    {
        $this->_onClick = $_onClick;
    }

	public function setEditType($_editType)
	{
		$this->_editType = $_editType;
	}

	public function setEditContents($_editContents)
	{
		$this->_editContents = $_editContents;
	}

    public function setOnSelect($_onSelect)
    {
        $this->_onSelect = $_onSelect;
    }

    public function setOnRender($_onRender)
    {
        $this->_onRender = $_onRender;
    }

    public function setOnCustomRender($_onCustomSelect)
    {
        $this->_onCustomSelect = $_onCustomSelect;
    }

    /**
     * 获取树形控件js配置信息
     * 优先加载通过set方法设置的属性
     *
     * @access protected
     * @return String
     */
    protected function getTreeConfig()
    {
        $config = "";
        if($this->_checkbox){
            $config .= "checkbox: {$this->_checkbox},";
        }
        // 设置成单选按钮
        if($this->_checkbox && $this->_selectMode == 1){
        	$config .= "classNames: {checkbox: 'dynatree-radio'},";
        }

        if($this->_persist){
        	$config .= "persist: true,";
        }

        if($this->_children){
            $config .= "children: {$this->_children},";
        }elseif($this->_url){
            $config .= "initAjax: {
                	url: '{$this->_url}'
            	},
            	onLazyRead: function(node){
            		node.appendAjax({
            			url:'{$this->_url}',
           				data: {
           					'id': node.data.key,
           					'mode': 'all'
            			}
                    });
				},";
        }

        if($this->_onClick){
            $config .= "onClick: function(node) {
                {$this->_onClick}
        	},";
        }

        if($this->_titleLength){
			/* 类别树 编辑事件 */
			if($this->_editContents && $this->_editType){
				$subTitleMethod = "
					var tempHtml = \"<a title='\"+node.data.title+\"'>\"+node.data.title+\"</a>\";
					if(node.data.type == {$this->_editType}){
						tempHtml +=\"&nbsp;&nbsp;<span style='color:#069;'>\"+node.data.content+\"</span>&nbsp;\";
						tempHtml += \"&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|<a href='#' onClick='editTheContent(\"+node.data.key+\")' title='Edit'>Edit</a>|<a href='#' onClick='deleteTheContent(\"+node.data.key+\")' title='Delete'>Delete</a>|\";
					}else if(node.data.code){
						tempHtml +=\"&nbsp;&nbsp;<span style='color:#069;'>cdp(\"+node.data.code+\", null)</span>&nbsp;\";
					}
					return tempHtml;
				";
			}else{
				$subTitleMethod = "
				if(node.data.title.length>{$this->_titleLength}){
					return \"<a href='#' title='\"+node.data.title+\"'>\"+node.data.title.substring(0, {$this->_titleLength})+\"...</a>&nbsp;&nbsp;&nbsp;&nbsp;\";
				}else{
					return \"<a href='#' title='\"+node.data.title+\"'>\"+node.data.title+\"</a>&nbsp;&nbsp;&nbsp;&nbsp;\";
				}";
			}
            $this->_onCustomRender = $this->_onCustomRender . $subTitleMethod;
        }

        if ($this->_onCustomRender) {
            $config .= "onCustomRender: function(node, nodeSpan) {
                {$this->_onCustomRender}
    		},";
        }

        $config .= "
        	onSelect: function(select, dtnode) {
        		" . $this->_onSelect . "
        		var nodes = dtnode.tree.getSelectedNodes();
				var ids = $.map(nodes, function(node){
   					return String(node.data.key);
                });
                $('#{$this->_name}').val(ids.join(','));
			},
			onRender: function(node, nodeSpan) {
				" . $this->_onRender . "
				if ( node.isSelected() == true) {
					var val = $('#{$this->_name}').val();
					val = val ? val + ',' + String(node.data.key) : String(node.data.key);
					$('#{$this->_name}').val(val);
    			}
			},
			selectMode: {$this->_selectMode}";

        $config = "{" . $config . "}";

        return $config;
    }

    /**
     * 实例化dynatree
     *
     * @access protected
     * @return String
     */
    protected function initTree()
    {
        $script = "
            <script type=\"text/javascript\">
            	$(function(){
        			$('#dynatree_{$this->_name}').dynatree(" . $this->getTreeConfig() . ");
        		});
            </script>
            <div id='dynatree_{$this->_name}'></div>";
        $script = preg_replace('/[\r\n]/', '', $script);
        $script = preg_replace('/\s{2,}/', ' ', $script);
        $script = preg_replace('/\/\*.*?\*\//', '', $script);
        return $script;
    }

    /**
     * Render form element
     *
     * @param  Zend_View_Interface $view
     * @return string
     */
    public function render(Zend_View_Interface $view = null)
    {
        $render = parent::render($view);
        $dynatreeScript = $this->getScript() . $this->initTree();
        return $dynatreeScript . $render;
    }

}

?>
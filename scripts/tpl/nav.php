<li><a href="[?php echo $this->url(array('action'=>'index', 'controller'=>'<?php echo $mvc_filter->filter($table_name);;?>'))?]">[?php echo $this->translate('List %1$s', '<?php echo $cls;?>')?]</a></li>
<li><a href="[?php echo $this->url(array('action'=>'create', 'controller'=>'<?php echo $mvc_filter->filter($table_name);;?>'))?]">[?php echo $this->translate('Create %1$s', '<?php echo $cls;?>')?]</a></li>

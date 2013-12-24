<div class="view">
	<dl><?php $i = 0; $class = ' class="altrow"';?>
		<dt<?php if ($i % 2 == 0) echo $class;?>>WEB</dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $this->Html->link('WebExplorer admin shared', '/admin/webexplorer'); ?><br />
			<?php echo $this->Html->link('WebExplorer per user', '/webexplorer'); ?><br />
      <?php echo $this->Html->link('WebExplorer TP sent-in', '/admin/webexplorer/rendu'); ?><br />
			&nbsp;
		</dd>
	</dl>
</div>
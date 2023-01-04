<?php

//add_action( 'widgets_init', 'thegem_diagram_register_widgets' );
function thegem_diagram_register_widgets() {
	if ( !is_blog_installed() )
		return;
	register_widget('The_Gem_Widget_Diagram');
}

class The_Gem_Widget_Diagram extends WP_Widget {


	function __construct() {
		$widget_ops = array('diagram' => 'widget_diagram', 'description' => __('diagram', 'thegem'));
		parent::__construct('diagram', __('diagram', 'thegem'), $widget_ops);
	}



	function widget( $args, $instance ) {
		extract($args);
		$style = '';
		$title = apply_filters('widget_title', empty($instance['title']) ? '' : $instance['title'], $instance, $this->id_base);
		if (!isset($instance['effects_enabled']))
			$instance['effects_enabled'] = false;
		echo $before_widget;
		echo '<div class="diagram-item">';
		echo '<div class="diagram-wrapper">';
		thegem_build_diagram($title, $instance['summary'], $instance['type'], $instance['skills'], true, $instance['effects_enabled'], $style);
		echo '</div>';
		echo '</div>';
		echo $after_widget;
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['summary'] = strip_tags($new_instance['summary']);
		$instance['type'] = strip_tags($new_instance['type']);
		$instance['skills'] = $new_instance['skills'];
		$instance['effects_enabled'] = (bool) $new_instance['effects_enabled'];
		;
		return $instance;
	}

	function get_skill_code($id, $data=null) {
		if (!is_array($data))
			$data = array(
				'title' => '',
				'amount' => '',
				'color' => '',
			);
		?>
		<fieldset style="border: 1px dashed #DFDFDF; padding: 0 5px; margin: 0 0 10px 0;">
			<p><label for="skill_<?php echo esc_attr($id.'_'.$this->get_field_id('title')); ?>"><?php _e('Title:', 'thegem'); ?></label>
				<input class="widefat" id="skill_<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('skills').'['.$id.'][title]'); ?>" type="text" value="<?php echo esc_attr($data['title']); ?>" /></p>
			<p><label for="skill_<?php echo esc_attr($id.'_'.$this->get_field_id('amount')); ?>"><?php _e('Amount (in percents):', 'thegem'); ?></label>
				<input class="widefat" id="skill_<?php echo esc_attr($this->get_field_id('amount')); ?>" name="<?php echo esc_attr($this->get_field_name('skills').'['.$id.'][amount]'); ?>" type="text" value="<?php echo esc_attr($data['amount']); ?>" /></p>
			<div><label for="skill_<?php echo esc_attr($id.'_'.$this->get_field_id('color')); ?>"><?php _e('Color:', 'thegem'); ?></label>
				<div><input class="widefat color-select" style="float: left;" id="skill_<?php echo esc_attr($this->get_field_id('color')); ?>" name="<?php echo esc_attr($this->get_field_name('skills').'['.$id.'][color]'); ?>" type="text" value="<?php echo esc_attr($data['color']); ?>" /></div><br style="clear: both;"></div>
			<div class="widget-control-actions"><a href="#" class="diagram-delete-skill">Delete skill</a></div>
		</fieldset>
		<?php
	}

	function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => '', 'summary' => '', 'type' => '', 'skills' => array(), 'effects_enabled' => false ) );
		$title = strip_tags($instance['title']);
		$summary = strip_tags($instance['summary']);
		$type = strip_tags($instance['type']);
		$skills = $instance['skills'];
		$effects_enabled = (bool) $instance['effects_enabled'];
		?>
		<script class="diagram-edit-skill-template" type="text/template">
			<?php $this->get_skill_code('%INDEX%'); ?>
		</script>
		<p><label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php _e('Title:', 'thegem'); ?></label>
			<input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text" value="<?php echo esc_attr($title); ?>" /></p>

		<p><label for="<?php echo esc_attr($this->get_field_id('summary')); ?>"><?php _e('Summary:', 'thegem'); ?></label>
			<textarea class="widefat" id="<?php echo esc_attr($this->get_field_id('summary')); ?>" name="<?php echo esc_attr($this->get_field_name('summary')); ?>"><?php echo esc_textarea($summary); ?></textarea></p>

		<p><label for="<?php echo esc_attr($this->get_field_id('type')); ?>"><?php _e('Type:', 'thegem'); ?></label>
			<select class="widefat" id="<?php echo esc_attr($this->get_field_id('type')); ?>" name="<?php echo esc_attr($this->get_field_name('type')); ?>">
				<option <?php if ($type == 'circle') echo 'selected="selected"'; ?> value="circle"><?php _e('Circle', 'thegem'); ?></option>
				<option <?php if ($type == 'line') echo 'selected="selected"'; ?> value="line"><?php _e('Lines', 'thegem'); ?></option>
			</select></p>

		<p>
			<input type="checkbox" name="<?php echo esc_attr($this->get_field_name('effects_enabled')); ?>" id="<?php echo esc_attr($this->get_field_id('effects_enabled')); ?>" value="1" <?php checked($effects_enabled, 1); ?> />
			<label for="<?php echo esc_attr($this->get_field_id('effects_enabled')); ?>"><?php _e('Lazy loading enabled', 'thegem'); ?></label>
		</p>

		<p><?php _e('Skills', 'thegem'); ?>:</p>
		<div class="diagram-edit-skills">
			<?php
			if (count($skills) > 0)
				foreach ($skills as $key=>$skill)
					$this->get_skill_code($key, $skill);
			else
				$this->get_skill_code(0);
			?>
			<div class="widget-control-actions" style="margin-top: 10px;"><a class="diagram-edit-add-skill" href="#">Add skill</a></div>
		</div>
		<script type="text/javascript">
			if (thegem_init_diagram_edit != undefined)
				thegem_init_diagram_edit();
		</script>
		<?php
	}
}

/*add_action( 'admin_enqueue_scripts', 'thegem_admin_diagram_scripts_init' );
function thegem_admin_diagram_scripts_init() {
	wp_enqueue_script('thegem-diagram-edit', get_template_directory_uri() . '/js/diagram_edit.js', array('jquery'));
}*/

function thegem_build_diagram($title, $summary, $type, $skills, $is_widget, $effects_enabled, $style, $background = '', $title_color = '', $summary_color = '', $line_color = '') {
	if ($type == 'line') {
		thegem_build_line_diagram($title, $summary, $skills, $is_widget, $effects_enabled, $style, $background, $title_color, $summary_color, $line_color );
	}
	if ($type == 'circle') {
		thegem_build_circle_diagram($title, $summary, $skills, $is_widget, $effects_enabled, $style);
	}
}

function thegem_build_line_diagram($title, $summary, $skills, $is_widget, $effects_enabled, $style, $background, $title_color, $summary_color, $line_color ) {
	wp_enqueue_script('thegem-diagram-line');
	if ($effects_enabled) {
		thegem_lazy_loading_enqueue();
	}
	?>
	<?php  if (!empty($title)): ?>

		<h3 <?php if ($is_widget): ?>class="widget-title"<?php endif; ?><?php echo ($title_color ? ' style="color: '.esc_attr($title_color).'"' : ''); ?>><?php echo $title; ?></h3>
	<?php endif; ?>
	<?php if (!empty($summary)): ?>
		<div <?php if ($is_widget): ?>class="diagram-summary diagram-summary-skill-line"<?php else: ?>class="diagram-summary-text"<?php endif; ?><?php echo ($summary_color ? ' style="color: '.esc_attr($summary_color).'"' : ''); ?>><?php echo $summary; ?></div>
	<?php endif; ?>
	<div class="<?php if($effects_enabled): ?>lazy-loading lazy-loading-not-hide<?php endif; ?>" <?php if($effects_enabled): ?>data-ll-item-delay="0"<?php endif; ?>>
		<div class="digram-line-box <?php if($effects_enabled): ?>lazy-loading-item<?php endif; ?>" <?php if($effects_enabled): ?>data-ll-effect="action" data-ll-action-func="thegem_start_line_digram"<?php endif; ?><?php echo ($background ? ' style="background-color: '.esc_attr($background).'"' : ''); ?>>
			<?php foreach ($skills as $skill): ?>
				<div class="skill-element">
					<div class="skill-title"<?php echo (isset($skill['title_color']) ? ' style="color: '.esc_attr($skill['title_color']).'"' : ''); ?>><?php echo $skill['title'];?> <?php if ($style == "style-3") echo '<span'.(isset($skill['title_color']) ? ' style="color: '.esc_attr($skill['title_color']).'"' : '').'>' .$skill['amount']. '%</span>';  ?></div>
					<div class="clearfix">
						<div style="color:<?php echo $skill['color']; ?>" class="skill-amount">0%</div>
						<div class="skill-line"<?php echo ($line_color ? ' style="background-color: '.esc_attr($line_color).'"' : ''); ?>><div data-amount="<?php echo esc_attr($skill['amount']); ?>" style="width: 0; background: <?php echo $skill['color']; ?>;"></div></div>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
	<?php
}

function thegem_build_circle_diagram($title, $summary, $skills, $is_widget, $effects_enabled, $style) {
	wp_enqueue_script('thegem-diagram-circle');
	$max_font_size = 0;
	if(thegem_get_option('h5_font_size')) {
		$max_font_size = thegem_get_option('h5_font_size');
	} else if (thegem_get_option('body_font_size')) {
		$max_font_size = thegem_get_option('body_font_size');
	}
	if ($effects_enabled) {
		thegem_lazy_loading_enqueue();
	}
	?>
	<div class="diagram-circle clearfix" <?php if ($max_font_size != 0): ?>data-max-font-size="<?php echo esc_attr($max_font_size); ?>"<?php endif; ?> data-title="<?php echo esc_attr(htmlspecialchars($title)); ?>" data-summary="<?php echo esc_attr(htmlspecialchars($summary)); ?>">
		<div class="box-wrapper">
			<div class="box"></div>
		</div>
		<div class="skills">
			<?php foreach ($skills as $skill): ?>
				<div class="skill-arc">
					<span class="title"><?php echo $skill['title']; ?></span>
					<input type="hidden" class="percent" value="<?php echo esc_attr($skill['amount']); ?>" />
					<input type="hidden" class="color" value="<?php echo esc_attr($skill['color']); ?>" />
					<input type="hidden" class="title_color" value="<?php echo esc_attr($skill['title_color']); ?>" />
				</div>
			<?php endforeach; ?>
		</div>
	</div>
	<?php
}

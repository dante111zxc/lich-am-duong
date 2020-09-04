<?php
class LAD_Widget extends WP_Widget {
    function __construct() {

        parent::__construct(
            'lich-am-duong',  // Base ID
            'Lịch Âm Dương'   // Name
        );

        add_action( 'widgets_init', function() {
            register_widget( 'LAD_Widget' );
        });

    }

    public $args = array(
        'before_title'  => '<div class="jeg_block_heading jeg_block_heading_6"><h3 class="jeg_block_title">',
        'after_title'   => '</h3></div>',
        'before_widget' => '<div class="widget">',
        'after_widget'  => '</div>'
    );

    public function widget( $args, $instance ) {

        echo $args['before_widget'];
        if ( ! empty( $instance['title'] ) ) {
            echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
        }
        include LAD__DIR__ . 'form.php';
        echo $args['after_widget'];

    }

    public function form( $instance ) {
        $title = ! empty( $instance['title'] ) ? $instance['title'] : esc_html__( '', 'text_domain' );
        ?>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php echo esc_html__( 'Title:', 'text_domain' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
        </p>
        <?php

    }
    public function update( $new_instance, $old_instance ) {
        $instance = array();
        $instance['title'] = ( !empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
        return $instance;
    }

}
$my_widget = new LAD_Widget();
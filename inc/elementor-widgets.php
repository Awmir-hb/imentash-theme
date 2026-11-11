<?php
/**
 * ویجت‌های المنتور برای سکشن‌های صفحه اصلی (گروه «ایمن تاش» در پنل المنتور).
 *
 * هر ویجت همان template-part قالب را چاپ می‌کند، پس طراحی و انیمیشن یکی است.
 * کنترل‌ها خودکار از فیلدهای Customizer همان سکشن ساخته می‌شوند: خالی = مقدار پنل،
 * پر = فقط برای همین ویجت جایگزین می‌شود (ementash_override_push در template-tags.php).
 * این فایل فقط از هوک elementor/widgets/register بارگذاری می‌شود (inc/elementor.php).
 *
 * @package ementash
 */

defined( 'ABSPATH' ) || exit;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;

/**
 * پایه همه ویجت‌های سکشن. زیرکلاس فقط $section را تعیین می‌کند.
 */
abstract class Ementash_Section_Widget extends Widget_Base {

	/** @var string نام template-part در template-parts/home (کلید ementash_home_sections). */
	protected $section = '';

	/**
	 * بخش‌های Customizer که فیلدهایشان در این ویجت قابل بازنویسی است.
	 */
	protected function schema_sections() {
		$sections = array( 'home_' . str_replace( '-', '_', $this->section ) );
		if ( 'about' === $this->section ) {
			$sections[] = 'stats'; // آمار داخل سکشن درباره ما چاپ می‌شود.
		}
		return $sections;
	}

	/**
	 * فیلدهای قابل بازنویسی: کلید ← تعریف. روشن/خاموش، برگه، ایمیل و فایل در پنل می‌مانند.
	 */
	protected function fields() {
		$schema = ementash_customizer_schema();
		$fields = array();
		foreach ( $this->schema_sections() as $id ) {
			if ( isset( $schema[ $id ]['fields'] ) ) {
				$fields += $schema[ $id ]['fields'];
			}
		}
		return array_filter(
			$fields,
			function ( $field ) {
				return in_array( $field['type'], array( 'text', 'textarea', 'lines', 'url', 'select', 'image', 'video', 'media' ), true );
			}
		);
	}

	public function get_name() {
		return 'ementash-' . $this->section;
	}

	public function get_title() {
		$labels = ementash_home_sections();
		/* translators: %s: نام سکشن */
		return sprintf( __( 'ایمن تاش: %s', 'ementash' ), isset( $labels[ $this->section ] ) ? $labels[ $this->section ] : $this->section );
	}

	public function get_icon() {
		return 'eicon-section';
	}

	public function get_categories() {
		return array( 'ementash' );
	}

	public function get_style_depends() {
		return 'honors' === $this->section ? array( 'ementash-honors' ) : array( 'ementash-home' );
	}

	public function get_script_depends() {
		return 'honors' === $this->section ? array( 'ementash-honors' ) : array( 'ementash-home' );
	}

	protected function register_controls() {
		$this->start_controls_section(
			'ementash_content',
			array(
				'label' => __( 'محتوا', 'ementash' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'ementash_note',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => esc_html__( 'فیلد خالی = همان مقدار پنل «ایمن تاش» در Customizer. هر چیزی اینجا بنویسید فقط در همین صفحه جایگزین می‌شود.', 'ementash' ),
				'content_classes' => 'elementor-descriptor',
			)
		);

		$media_types = array(
			'image' => array( 'image' ),
			'video' => array( 'video' ),
			'media' => array( 'image', 'video' ),
		);

		foreach ( $this->fields() as $key => $field ) {
			$args = array(
				'label'       => $field['label'],
				'description' => isset( $field['description'] ) ? $field['description'] : '',
				'label_block' => true,
			);
			switch ( $field['type'] ) {
				case 'textarea':
				case 'lines':
					$args['type']        = Controls_Manager::TEXTAREA;
					$args['rows']        = 'lines' === $field['type'] ? 4 : 3;
					$args['placeholder'] = (string) $field['default'];
					break;
				case 'select':
					$args['type']    = Controls_Manager::SELECT;
					$args['options'] = array( '' => __( '(همان مقدار پنل)', 'ementash' ) ) + $field['choices'];
					$args['default'] = '';
					break;
				case 'image':
				case 'video':
				case 'media':
					$args['type']        = Controls_Manager::MEDIA;
					$args['media_types'] = $media_types[ $field['type'] ];
					$args['default']     = array(
						'id'  => 0,
						'url' => '',
					);
					break;
				default: // text و url
					$args['type']        = Controls_Manager::TEXT;
					$args['input_type']  = 'url' === $field['type'] ? 'url' : 'text';
					$args['placeholder'] = (string) $field['default'];
			}
			$this->add_control( $key, $args );
		}

		$this->end_controls_section();
	}

	/**
	 * مقادیر پرشده ویجت، با همان پاک‌سازی Customizer.
	 */
	protected function overrides() {
		$settings = $this->get_settings_for_display();
		$values   = array();
		foreach ( $this->fields() as $key => $field ) {
			if ( ! isset( $settings[ $key ] ) ) {
				continue;
			}
			$value = $settings[ $key ];
			if ( is_array( $value ) ) {
				$value = isset( $value['id'] ) ? $value['id'] : 0;
			}
			if ( '' === $value || null === $value || 0 === absint( $value ) && in_array( $field['type'], array( 'image', 'video', 'media' ), true ) ) {
				continue;
			}
			$values[ $key ] = call_user_func( ementash_customizer_sanitizer( $field['type'] ), $value );
			if ( 'select' === $field['type'] && '' === $values[ $key ] ) {
				unset( $values[ $key ] );
			}
		}
		return $values;
	}

	protected function render() {
		ementash_override_push( $this->overrides() );
		get_template_part( 'template-parts/home/' . $this->section );
		ementash_override_pop();
	}
}

// یک کلاس کوچک برای هر سکشن (المنتور هر نوع ویجت را با کلاس خودش می‌سازد).
// phpcs:disable Generic.Files.OneObjectStructurePerFile.MultipleFound
class Ementash_Widget_Hero extends Ementash_Section_Widget {
	protected $section = 'hero';
}
class Ementash_Widget_About extends Ementash_Section_Widget {
	protected $section = 'about';
}
class Ementash_Widget_Honors extends Ementash_Section_Widget {
	protected $section = 'honors';
}
class Ementash_Widget_Industries extends Ementash_Section_Widget {
	protected $section = 'industries';
}
class Ementash_Widget_Clients extends Ementash_Section_Widget {
	protected $section = 'clients';
}
class Ementash_Widget_Services extends Ementash_Section_Widget {
	protected $section = 'services';
}
class Ementash_Widget_Products extends Ementash_Section_Widget {
	protected $section = 'products';
}
class Ementash_Widget_Production_Line extends Ementash_Section_Widget {
	protected $section = 'production-line';
}
class Ementash_Widget_Gallery extends Ementash_Section_Widget {
	protected $section = 'gallery';
}
class Ementash_Widget_Textile extends Ementash_Section_Widget {
	protected $section = 'textile';
}
class Ementash_Widget_Blog extends Ementash_Section_Widget {
	protected $section = 'blog';
}
// phpcs:enable

/**
 * کلاس ویجت هر سکشن؛ سکشنی که کلاس ندارد ویجت هم ندارد.
 */
function ementash_widget_classes() {
	return array(
		'hero'            => 'Ementash_Widget_Hero',
		'about'           => 'Ementash_Widget_About',
		'honors'          => 'Ementash_Widget_Honors',
		'industries'      => 'Ementash_Widget_Industries',
		'clients'         => 'Ementash_Widget_Clients',
		'services'        => 'Ementash_Widget_Services',
		'products'        => 'Ementash_Widget_Products',
		'production-line' => 'Ementash_Widget_Production_Line',
		'gallery'         => 'Ementash_Widget_Gallery',
		'textile'         => 'Ementash_Widget_Textile',
		'blog'            => 'Ementash_Widget_Blog',
	);
}
